<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function about(): string
    {
        return view('storefront/about', ['page' => 'about']);
    }

    public function bulk(): string
    {
        return view('storefront/bulk', ['page' => 'bulk']);
    }
}
