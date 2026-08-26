<?php

namespace App\Controllers\Admin;

use App\Models\CustomerModel;
use App\Models\OrderModel;

class Customers extends AdminController
{
    public function index(): string
    {
        $model = new CustomerModel();
        $q     = trim((string) $this->request->getGet('q'));

        if ($q !== '') {
            $model->groupStart()
                ->like('first_name', $q)->orLike('last_name', $q)->orLike('email', $q)->orLike('phone', $q)
                ->groupEnd();
        }

        return $this->render('customers/index', [
            'active' => 'customers',
            'rows'   => $model->orderBy('id', 'DESC')->findAll(),
            'q'      => $q,
        ], 'Customers');
    }

    public function show(int $id): string
    {
        $row = (new CustomerModel())->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $orders = (new OrderModel())->where('customer_id', $id)
            ->orderBy('placed_on', 'DESC')->findAll();

        return $this->render('customers/show', [
            'active' => 'customers', 'row' => $row, 'orders' => $orders,
        ], 'Customer');
    }
}
