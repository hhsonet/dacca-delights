<?php

namespace App\Controllers\Admin;

use App\Models\OrderItemModel;
use App\Models\OrderModel;

class Orders extends AdminController
{
    public function index(): string
    {
        $model  = new OrderModel();
        $q      = trim((string) $this->request->getGet('q'));
        $status = trim((string) $this->request->getGet('status'));

        $builder = $model->withCustomer();
        if ($q !== '') {
            $builder->groupStart()
                ->like('o.order_no', $q)
                ->orLike('c.first_name', $q)
                ->orLike('c.last_name', $q)
                ->orLike('c.email', $q)
                ->groupEnd();
        }
        if ($status !== '') {
            $builder->where('o.status', $status);
        }

        $rows = $builder->orderBy('o.placed_on', 'DESC')->orderBy('o.id', 'DESC')
            ->get()->getResultArray();

        return $this->render('orders/index', [
            'active'   => 'orders',
            'rows'     => $rows,
            'q'        => $q,
            'status'   => $status,
            'statuses' => OrderModel::STATUSES,
        ], 'Orders');
    }

    public function show(int $id): string
    {
        $order = (new OrderModel())->findFull($id);
        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('orders/show', [
            'active'   => 'orders',
            'order'    => $order,
            'items'    => (new OrderItemModel())->forOrder($id),
            'statuses' => OrderModel::STATUSES,
        ], 'Order ' . $order['order_no']);
    }

    /** Status is the one field the dashboard changes on an existing order. */
    public function updateStatus(int $id)
    {
        $status = (string) $this->request->getPost('status');

        if (!in_array($status, OrderModel::STATUSES, true)) {
            $this->flash('err', 'That is not a valid order status.');

            return redirect()->to(base_url('admin/orders/' . $id));
        }

        $model = new OrderModel();
        if (!$model->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Bypasses the model's order_no uniqueness rule, which would otherwise
        // fire on a partial update that does not include order_no.
        $model->protect(false)->update($id, ['status' => $status]);

        $this->flash('ok', 'Order status set to ' . $status . '.');

        return redirect()->to(base_url('admin/orders/' . $id));
    }
}
