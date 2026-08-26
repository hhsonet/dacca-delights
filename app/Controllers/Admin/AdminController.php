<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Shared behaviour for every dashboard screen: consistent view rendering and
 * one-shot flash messages.
 */
abstract class AdminController extends BaseController
{
    protected function render(string $view, array $data = [], string $title = ''): string
    {
        $data['title']     = $title;
        $data['adminName'] = session()->get('adminName');
        $data['adminRole'] = session()->get('adminRole');
        $data['active']    = $data['active'] ?? '';

        return view('admin/' . $view, $data);
    }

    protected function flash(string $type, string $message): void
    {
        session()->setFlashdata('flash', ['type' => $type, 'message' => $message]);
    }

    /** Only 'admin' may destroy records; 'staff' can edit but not delete. */
    protected function requireAdminRole(): bool
    {
        return session()->get('adminRole') === 'admin';
    }
}
