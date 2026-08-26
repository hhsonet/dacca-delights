<?php

namespace App\Controllers;

class Storefront extends BaseController
{
    public function index(): string
    {
        return view('storefront/home', ['page' => 'home']);
    }
}
