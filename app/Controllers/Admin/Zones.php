<?php

namespace App\Controllers\Admin;

use App\Models\DeliveryZoneModel;

class Zones extends AdminController
{
    public function index(): string
    {
        return $this->render('zones/index', [
            'active' => 'zones', 'rows' => (new DeliveryZoneModel())->ordered(),
        ], 'Delivery zones');
    }

    public function create(): string
    {
        return $this->render('zones/form', ['active' => 'zones', 'row' => null], 'New zone');
    }

    public function edit(int $id): string
    {
        $row = (new DeliveryZoneModel())->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('zones/form', ['active' => 'zones', 'row' => $row], 'Edit zone');
    }

    public function store()
    {
        return $this->save(null);
    }

    public function update(int $id)
    {
        return $this->save($id);
    }

    private function save(?int $id)
    {
        $model = new DeliveryZoneModel();
        $post  = $this->request->getPost();
        $data  = [
            'name'        => trim((string) ($post['name'] ?? '')),
            // Blank fee means "we do not deliver here", which is not the same as free.
            'fee'         => ($post['fee'] ?? '') === '' ? null : (int) $post['fee'],
            'is_limited'  => empty($post['is_limited']) ? 0 : 1,
            'cod_allowed' => empty($post['cod_allowed']) ? 0 : 1,
        ];

        if ($id !== null) {
            // is_unique resolves its {id} placeholder from the data array, not
            // the update key — without this the row collides with itself.
            $data['id'] = $id;
        }

        $ok = $id === null ? $model->insert($data) : $model->update($id, $data);
        if (!$ok) {
            // Explicit target: redirect()->back() throws when there is no Referer.
            $back = $id === null
                ? base_url('admin/zones/create')
                : base_url('admin/zones/' . $id . '/edit');

            return redirect()->to($back)->withInput()
                ->with('flash', ['type' => 'err', 'message' => implode(' ', $model->errors())]);
        }

        $this->flash('ok', $id === null ? 'Zone created.' : 'Zone updated.');

        return redirect()->to(base_url('admin/zones'));
    }

    public function delete(int $id)
    {
        if (!$this->requireAdminRole()) {
            $this->flash('err', 'Only an admin may delete zones.');
            return redirect()->to(base_url('admin/zones'));
        }
        (new DeliveryZoneModel())->delete($id);
        $this->flash('ok', 'Zone deleted.');

        return redirect()->to(base_url('admin/zones'));
    }
}
