<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminUserModel;
use Config\Services;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('adminId')) {
            return redirect()->to(base_url('admin'));
        }

        return view('admin/login', ['title' => 'Sign in']);
    }

    public function attempt()
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $pw    = (string) $this->request->getPost('password');

        // 5 attempts per minute per IP.
        $throttler = Services::throttler();
        if ($throttler->check(md5('admin-login-' . $this->request->getIPAddress()), 5, MINUTE) === false) {
            return redirect()->to(base_url('admin/login'))->withInput()
                ->with('error', 'Too many attempts. Please wait a moment and try again.');
        }

        $model = new AdminUserModel();
        $user  = $model->findByEmail($email);

        // Same message whether the email is unknown, the password is wrong, or
        // the account is disabled — no probing which admin emails exist.
        if (!$model->verifyPassword($user, $pw)) {
            return redirect()->to(base_url('admin/login'))->withInput()
                ->with('error', 'Email or password is incorrect.');
        }

        $model->markLogin((int) $user['id']);

        $session = session();
        $session->regenerate(true); // block session fixation
        $session->set([
            'adminId'    => (int) $user['id'],
            'adminName'  => $user['name'],
            'adminEmail' => $user['email'],
            'adminRole'  => $user['role'],
        ]);

        $to = $session->get('adminRedirect') ?: base_url('admin');
        $session->remove('adminRedirect');

        return redirect()->to($to);
    }

    public function logout()
    {
        $s = session();
        // Clear only the admin keys, so a shopper session in the same browser
        // is not destroyed as a side effect.
        $s->remove(['adminId', 'adminName', 'adminEmail', 'adminRole', 'adminRedirect']);
        $s->regenerate(true);

        return redirect()->to(base_url('admin/login'));
    }
}
