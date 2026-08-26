<?php

namespace App\Controllers;

class Cart extends BaseController
{
    public function index(): string
    {
        return view('storefront/cart', ['page' => 'cart']);
    }

    public function checkout(): string
    {
        return view('storefront/checkout', ['page' => 'checkout']);
    }

    /**
     * Order confirmation. The invoice itself is handed over client-side via
     * sessionStorage (see bridge() in the shared logic) — there is no server
     * order record yet.
     */
    public function success(): string
    {
        return view('storefront/success', ['page' => 'success']);
    }
}
