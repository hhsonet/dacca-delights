<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

/**
 * JSON auth endpoints for the storefront.
 *
 * The signup/login UI is client-rendered, so these return JSON (field-keyed
 * errors matching the form's own `err` object) rather than redirecting.
 */
class Auth extends BaseController
{
    private const MIN_PASSWORD = 8;

    /** GET /signup — the auth page, opened in register mode. */
    public function signupPage()
    {
        if (session()->get('customerId')) {
            return redirect()->to(base_url('account'));
        }

        return view('storefront/auth', [
            'page'     => 'auth',
            'authMode' => 'register',
        ]);
    }

    /** POST /auth/signup */
    public function signup(): ResponseInterface
    {
        $in    = $this->input();
        $model = new CustomerModel();

        $name  = trim((string) ($in['name'] ?? ''));
        $email = strtolower(trim((string) ($in['email'] ?? '')));
        $phone = trim((string) ($in['phone'] ?? ''));
        $pw    = (string) ($in['pw'] ?? '');
        $pw2   = (string) ($in['pw2'] ?? '');
        $terms = !empty($in['terms']);

        // Server-side validation. The client validates too, but that is a
        // convenience — this is the check that actually counts.
        $err = [];

        if (mb_strlen($name) < 2) {
            $err['name'] = 'Please enter your full name.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err['email'] = 'Enter a valid email address.';
        }
        if ($phone !== '' && !preg_match('/^1[3-9]\d{8}$/', $phone)) {
            $err['phone'] = 'Enter 10 digits starting 13-19, e.g. 17XXXXXXXX.';
        }
        if (mb_strlen($pw) < self::MIN_PASSWORD) {
            $err['pw'] = 'Password must be at least ' . self::MIN_PASSWORD . ' characters.';
        }
        if ($pw2 !== $pw) {
            $err['pw2'] = 'Passwords do not match.';
        }
        if (!$terms) {
            $err['terms'] = 'Please accept the Terms & Conditions.';
        }
        if ($err !== []) {
            return $this->fail($err);
        }

        if ($model->findByEmail($email) !== null) {
            return $this->fail(['email' => 'An account with that email already exists.']);
        }

        [$first, $last] = $this->splitName($name);

        $id = $model->insert([
            'first_name'     => $first,
            'last_name'      => $last,
            'email'          => $email,
            'phone'          => $phone !== '' ? '+880' . $phone : null,
            'whatsapp'       => $phone !== '' ? '+880' . $phone : null,
            'email_verified' => 0,
        ], true);

        if (!$id) {
            // Most likely the UNIQUE index caught a duplicate created between
            // the check above and this insert.
            $errors = $model->errors();

            return $this->fail($errors !== [] ? $errors : ['email' => 'Could not create that account.']);
        }

        $model->setPassword((int) $id, $pw);
        $this->startSession((int) $id, $first, $email, $last, $phone !== '' ? '+880' . $phone : null);

        return $this->ok([
            'name'  => $first,
            'email' => $email,
        ]);
    }

    /** POST /auth/login */
    public function login(): ResponseInterface
    {
        $in    = $this->input();
        $email = strtolower(trim((string) ($in['email'] ?? '')));
        $pw    = (string) ($in['pw'] ?? '');

        // Throttle by IP: 5 attempts per minute, so a stolen email list cannot
        // be sprayed against this endpoint.
        $throttler = Services::throttler();
        if ($throttler->check(md5('login-' . $this->request->getIPAddress()), 5, MINUTE) === false) {
            return $this->fail(
                ['pw' => 'Too many attempts. Please wait a moment and try again.'],
                429
            );
        }

        $model    = new CustomerModel();
        $customer = $model->findByEmail($email);

        // Deliberately the same message either way — this must not reveal
        // whether an email is registered.
        if (!$model->verifyPassword($customer, $pw)) {
            return $this->fail(['pw' => 'Email or password is incorrect.'], 401);
        }

        $model->markLogin((int) $customer['id']);
        $this->startSession(
            (int) $customer['id'],
            $customer['first_name'],
            $customer['email'],
            $customer['last_name'] ?? null,
            $customer['phone'] ?? null
        );

        return $this->ok([
            'name'  => $customer['first_name'],
            'email' => $customer['email'],
        ]);
    }

    /** GET|POST /auth/logout */
    public function logout()
    {
        session()->destroy();

        return redirect()->to(base_url());
    }

    /** GET /auth/me — who is signed in, for the client to render chrome. */
    public function me(): ResponseInterface
    {
        $s = session();

        return $this->response->setJSON([
            'ok'    => true,
            'authed' => (bool) $s->get('customerId'),
            'name'  => $s->get('customerName'),
            'email' => $s->get('customerEmail'),
            'token' => csrf_hash(),
        ]);
    }

    // ---------------------------------------------------------------------

    /** Accept JSON bodies, falling back to normal form posts. */
    private function input(): array
    {
        $json = $this->request->getJSON(true);

        return is_array($json) && $json !== [] ? $json : (array) $this->request->getPost();
    }

    private function splitName(string $full): array
    {
        $parts = preg_split('/\s+/', trim($full)) ?: [];
        $first = array_shift($parts) ?? '';

        return [$first, $parts !== [] ? implode(' ', $parts) : null];
    }

    private function startSession(int $id, string $name, string $email, ?string $last = null, ?string $phone = null): void
    {
        $s = session();
        // New session id on privilege change — blocks session fixation.
        $s->regenerate(true);
        $s->set([
            'customerId'       => $id,
            'customerName'     => $name,
            'customerLastName' => $last,
            'customerEmail'    => $email,
            'customerPhone'    => $phone,
        ]);
    }

    /** Always hand back a fresh CSRF token — CI4 rotates it per request. */
    private function ok(array $data = []): ResponseInterface
    {
        return $this->response->setJSON(['ok' => true, 'token' => csrf_hash()] + $data);
    }

    private function fail(array $errors, int $status = 422): ResponseInterface
    {
        return $this->response
            ->setStatusCode($status)
            ->setJSON(['ok' => false, 'errors' => $errors, 'token' => csrf_hash()]);
    }
}
