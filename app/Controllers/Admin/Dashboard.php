<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\CustomerModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

class Dashboard extends AdminController
{
    public function index(): string
    {
        $orders = new OrderModel();
        $stats  = $orders->stats();

        $recent = $orders->withCustomer()
            ->orderBy('o.placed_on', 'DESC')
            ->orderBy('o.id', 'DESC')
            ->limit(8)->get()->getResultArray();

        $topSellers = array_slice($orders->unitsSold(), 0, 6, true);

        return $this->render('dashboard', [
            'active'     => '',
            'stats'      => $stats,
            'recent'     => $recent,
            'topSellers' => $topSellers,
            'counts'     => [
                'products'   => (new ProductModel())->countAllResults(),
                'categories' => (new CategoryModel())->countAllResults(),
                'customers'  => (new CustomerModel())->countAllResults(),
            ],
        ], 'Dashboard');
    }
}
