<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use League\OAuth2\Client\Provider\Google;

/**
 * "Continue with Google" for the storefront.
 *
 * Uses the standard authorization-code flow. The `state` parameter is stored in
 * the session and compared on return — without that check, an attacker could
 * feed a victim a callback URL and sign them into the attacker's account.
 */
class GoogleAuth extends BaseController
{
    /** GET /auth/google — hand off to Google. */
    public function redirectToGoogle()
    {
        $config = config('Google');

        if (!$config->isConfigured()) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Google sign-in is not configured yet.');
        }

        if (session()->get('customerId')) {
            return redirect()->to(base_url('account'));
        }

        $provider = $this->provider();

        $url = $provider->getAuthorizationUrl([
            'scope' => ['openid', 'email', 'profile'],
        ]);

        // Bind this attempt to this session.
        session()->set('googleState', $provider->getState());

        return redirect()->to($url);
    }

    /** GET /auth/google/callback */
    public function callback()
    {
        $config  = config('Google');
        $session = session();

        if (!$config->isConfigured()) {
            return $this->fail('Google sign-in is not configured yet.');
        }

        $state    = (string) $this->request->getGet('state');
        $expected = (string) $session->get('googleState');
        $session->remove('googleState');

        // Reject anything that did not start from our own redirect above.
        if ($state === '' || $expected === '' || !hash_equals($expected, $state)) {
            return $this->fail('That sign-in link has expired. Please try again.');
        }

        if ($this->request->getGet('error')) {
            return $this->fail('Google sign-in was cancelled.');
        }

        $code = (string) $this->request->getGet('code');
        if ($code === '') {
            return $this->fail('Google did not return a sign-in code.');
        }

        try {
            $provider = $this->provider();
            $token    = $provider->getAccessToken('authorization_code', ['code' => $code]);
            /** @var \League\OAuth2\Client\Provider\GoogleUser $owner */
            $owner = $provider->getResourceOwner($token);
            $raw   = $owner->toArray();
        } catch (\Throwable $e) {
            log_message('error', 'Google OAuth failed: ' . $e->getMessage());

            return $this->fail('Could not complete Google sign-in. Please try again.');
        }

        $googleId = (string) ($raw['sub'] ?? $owner->getId() ?? '');
        $email    = strtolower(trim((string) ($raw['email'] ?? $owner->getEmail() ?? '')));

        if ($googleId === '' || $email === '') {
            return $this->fail('Google did not share an email address.');
        }

        // Only trust an address Google itself has verified — otherwise someone
        // could claim an address they do not own and take over that account.
        if (isset($raw['email_verified']) && $raw['email_verified'] === false) {
            return $this->fail('That Google account has an unverified email address.');
        }

        $customer = $this->findOrCreate($googleId, $email, $raw, $owner);

        if ($customer === null) {
            return $this->fail('Could not create your account. Please try again.');
        }

        $model = new CustomerModel();
        $model->markLogin((int) $customer['id']);

        $session->regenerate(true); // new id on privilege change
        $session->set([
            'customerId'       => (int) $customer['id'],
            'customerName'     => $customer['first_name'],
            'customerLastName' => $customer['last_name'],
            'customerEmail'    => $customer['email'],
            'customerPhone'    => $customer['phone'],
        ]);

        return redirect()->to(base_url('account'));
    }

    // ------------------------------------------------------------------

    private function provider(): Google
    {
        $config = config('Google');

        return new Google([
            'clientId'     => $config->clientId,
            'clientSecret' => $config->clientSecret,
            'redirectUri'  => $config->callbackUrl(),
        ]);
    }

    /**
     * Match on the Google subject id first, then fall back to the verified
     * email so an existing password account is linked rather than duplicated.
     */
    private function findOrCreate(string $googleId, string $email, array $raw, $owner): ?array
    {
        $model = new CustomerModel();

        $byGoogle = $model->where('google_id', $googleId)->first();
        if ($byGoogle) {
            return $byGoogle;
        }

        $first = trim((string) ($raw['given_name'] ?? '')) ?: (explode(' ', (string) $owner->getName())[0] ?? 'Friend');
        $last  = trim((string) ($raw['family_name'] ?? '')) ?: null;
        $photo = (string) ($raw['picture'] ?? '');

        $byEmail = $model->findByEmail($email);
        if ($byEmail) {
            // Google verified this address, so linking is safe. The existing
            // password is left untouched — both routes keep working.
            $model->protect(false)->update($byEmail['id'], [
                'google_id'      => $googleId,
                'avatar_url'     => $photo ?: $byEmail['avatar_url'],
                'email_verified' => 1,
            ]);

            return $model->find($byEmail['id']);
        }

        $id = $model->insert([
            'first_name'     => mb_substr($first, 0, 80),
            'last_name'      => $last !== null ? mb_substr($last, 0, 80) : null,
            'email'          => $email,
            'email_verified' => 1,
        ], true);

        if (!$id) {
            log_message('error', 'Google signup insert failed: ' . implode(' ', $model->errors()));

            return null;
        }

        // google_id and avatar are outside allowedFields on purpose.
        $model->protect(false)->update($id, [
            'google_id'  => $googleId,
            'avatar_url' => $photo ?: null,
        ]);

        return $model->find($id);
    }

    private function fail(string $message)
    {
        return redirect()->to(base_url('login'))->with('error', $message);
    }
}
