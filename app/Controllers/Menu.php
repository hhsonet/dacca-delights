<?php

namespace App\Controllers;

class Menu extends BaseController
{
    public function index(): string
    {
        return view('storefront/menu', [
            'page'     => 'menu',
            'category' => $this->request->getGet('category') ?: 'Best Sellers',
            'query'    => $this->request->getGet('q') ?: '',
        ]);
    }

    public function product(string $slug): string
    {
        return view('storefront/product', [
            'page' => 'product',
            'slug' => $slug,
        ]);
    }
}
