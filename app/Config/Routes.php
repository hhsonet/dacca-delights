<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Storefront::index');

// Storefront — one route per function.
$routes->get('menu',                    'Menu::index');
$routes->get('product/(:segment)',      'Menu::product/$1');
$routes->get('cart',                    'Cart::index');
$routes->get('checkout',                'Cart::checkout');
$routes->get('order/success',           'Cart::success');
$routes->get('account',                 'Account::index');
$routes->get('account/order/(:segment)', 'Account::order/$1');
$routes->get('login',                   'Account::login');
$routes->get('signup',                  'Auth::signupPage');

// Auth endpoints. CSRF is not enabled globally in Filters.php, so the state-
// changing routes opt into it explicitly here.
// Checkout submission. Totals are recomputed server-side in OrderPlacer.
$routes->post('order', 'Order::place', ['filter' => 'csrf']);

// Google sign-in. Both are GET redirects in the OAuth flow; the `state`
// parameter (not CSRF tokens) is what binds the callback to this session.
$routes->get('auth/google',          'GoogleAuth::redirectToGoogle');
$routes->get('auth/google/callback', 'GoogleAuth::callback');

$routes->get('auth/me',      'Auth::me');
$routes->get('auth/logout',  'Auth::logout');
$routes->post('auth/signup', 'Auth::signup', ['filter' => 'csrf']);
$routes->post('auth/login',  'Auth::login',  ['filter' => 'csrf']);
// ---------------------------------------------------------------------------
// Admin dashboard. Login is public; everything else sits behind the `admin`
// filter, and every mutating route additionally requires a CSRF token.
// ---------------------------------------------------------------------------
$routes->get('admin/login',   'Admin\Auth::login');
$routes->post('admin/login',  'Admin\Auth::attempt', ['filter' => 'csrf']);
$routes->get('admin/logout',  'Admin\Auth::logout');

$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
    $routes->get('', 'Admin\Dashboard::index');

    $crud = [
        'products'     => 'Products',
        'categories'   => 'Categories',
        'zones'        => 'Zones',
        'testimonials' => 'Testimonials',
        'gallery'      => 'Gallery',
    ];

    foreach ($crud as $seg => $ctrl) {
        $routes->get($seg,                       "Admin\\{$ctrl}::index");
        $routes->get("{$seg}/create",            "Admin\\{$ctrl}::create");
        $routes->get("{$seg}/(:num)/edit",       "Admin\\{$ctrl}::edit/$1");
        $routes->post($seg,                      "Admin\\{$ctrl}::store",        ['filter' => 'csrf']);
        $routes->post("{$seg}/(:num)",           "Admin\\{$ctrl}::update/$1",    ['filter' => 'csrf']);
        $routes->post("{$seg}/(:num)/delete",    "Admin\\{$ctrl}::delete/$1",    ['filter' => 'csrf']);
    }

    // Product photo uploads. Multipart, so CSRF is enforced here too.
    $routes->post('products/(:num)/photos',                  'Admin\Products::uploadPhoto/$1', ['filter' => 'csrf']);
    $routes->post('products/(:num)/photos/(:num)/delete',    'Admin\Products::deletePhoto/$1/$2', ['filter' => 'csrf']);
    $routes->post('products/(:num)/photos/(:num)/origin',    'Admin\Products::photoOrigin/$1/$2', ['filter' => 'csrf']);

    $routes->get('orders',                'Admin\Orders::index');
    $routes->get('orders/(:num)',         'Admin\Orders::show/$1');
    $routes->post('orders/(:num)/status', 'Admin\Orders::updateStatus/$1', ['filter' => 'csrf']);

    $routes->get('customers',        'Admin\Customers::index');
    $routes->get('customers/(:num)', 'Admin\Customers::show/$1');
});

// Staff account management — administrators only. Kept out of the group above
// so the stricter `admin:admin` filter applies to every route here.
$routes->group('admin/users', ['filter' => 'admin:admin'], static function ($routes) {
    $routes->get('',                   'Admin\Users::index');
    $routes->get('create',             'Admin\Users::create');
    $routes->get('(:num)/edit',        'Admin\Users::edit/$1');
    $routes->post('',                  'Admin\Users::store',    ['filter' => 'csrf']);
    $routes->post('(:num)',            'Admin\Users::update/$1', ['filter' => 'csrf']);
    $routes->post('(:num)/password',   'Admin\Users::password/$1', ['filter' => 'csrf']);
    $routes->post('(:num)/delete',     'Admin\Users::delete/$1', ['filter' => 'csrf']);
});

$routes->get('bulk',                    'Pages::bulk');
$routes->get('about',                   'Pages::about');
