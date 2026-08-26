<?php

namespace App\Controllers;

class Account extends BaseController
{
    public function index()
    {
        // The account area is only meaningful for a signed-in customer.
        if (!session()->get('customerId')) {
            return redirect()->to(base_url('login'));
        }

        return view('storefront/account', [
            'page'       => 'account',
            'accountTab' => $this->request->getGet('tab') ?: 'Dashboard',
        ]);
    }

    /**
     * $ref is the order number without its leading '#', e.g. BK-1024.
     * Resolved to an index against the ORDERS list in the shared logic.
     */
    public function order(string $ref): string
    {
        return view('storefront/order-detail', [
            'page'     => 'orderdetail',
            'orderRef' => $ref,
        ]);
    }

    public function login()
    {
        if (session()->get('customerId')) {
            return redirect()->to(base_url('account'));
        }

        return view('storefront/auth', ['page' => 'auth', 'authMode' => 'login']);
    }
}
