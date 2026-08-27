<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Google OAuth credentials.
 *
 * Values come from .env — never commit real credentials:
 *
 *   google.clientId     = 1234-abc.apps.googleusercontent.com
 *   google.clientSecret = GOCSPX-xxxxxxxx
 *   google.redirectUri  = https://yourdomain.com/auth/google/callback
 *
 * The redirect URI must match one registered in the Google Cloud console
 * exactly, including scheme, host, port and path.
 */
class Google extends BaseConfig
{
    public string $clientId = '';

    public string $clientSecret = '';

    /** Blank falls back to base_url('auth/google/callback'). */
    public string $redirectUri = '';

    public function isConfigured(): bool
    {
        return $this->clientId !== '' && $this->clientSecret !== '';
    }

    public function callbackUrl(): string
    {
        return $this->redirectUri !== '' ? $this->redirectUri : base_url('auth/google/callback');
    }
}
