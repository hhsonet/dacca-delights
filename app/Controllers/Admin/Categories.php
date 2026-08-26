<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Categories extends AdminController
{
    public function index(): string
    {
        $rows = (new CategoryModel())->ordered();
        $counts = [];
        foreach ((new ProductModel())->select('category_id, COUNT(*) n')->groupBy('category_id')->findAll() as $r) {
            $counts[(int) $r['category_id']] = (int) $r['n'];
        }

        return $this->render('categories/index', [
            'active' => 'categories', 'rows' => $rows, 'counts' => $counts,
        ], 'Categories');
    }

    public function create(): string
    {
        return $this->render('categories/form', ['active' => 'categories', 'row' => null], 'New category');
    }

    public function edit(int $id): string
    {
        $row = (new CategoryModel())->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('categories/form', ['active' => 'categories', 'row' => $row], 'Edit category');
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
        $model = new CategoryModel();
        $post  = $this->request->getPost();
        $data  = [
            'name'       => trim((string) ($post['name'] ?? '')),
            'slug'       => trim((string) ($post['slug'] ?? '')),
            'blurb'      => trim((string) ($post['blurb'] ?? '')) ?: null,
            'image'      => trim((string) ($post['image'] ?? '')) ?: null,
            'sort_order' => (int) ($post['sort_order'] ?? 0),
        ];
        if ($data['slug'] === '' && $data['name'] !== '') {
            $data['slug'] = url_title($data['name'], '-', true);
        }

        if ($id !== null) {
            // is_unique resolves its {id} placeholder from the data array, not
            // the update key — without this the row collides with itself.
            $data['id'] = $id;
        }

        $ok = $id === null ? $model->insert($data) : $model->update($id, $data);
        if (!$ok) {
            // Explicit target: redirect()->back() throws when there is no Referer.
            $back = $id === null
                ? base_url('admin/categories/create')
                : base_url('admin/categories/' . $id . '/edit');

            return redirect()->to($back)->withInput()
                ->with('flash', ['type' => 'err', 'message' => implode(' ', $model->errors())]);
        }

        $this->flash('ok', $id === null ? 'Category created.' : 'Category updated.');

        return redirect()->to(base_url('admin/categories'));
    }

    public function delete(int $id)
    {
        if (!$this->requireAdminRole()) {
            $this->flash('err', 'Only an admin may delete categories.');
            return redirect()->to(base_url('admin/categories'));
        }

        // Deleting a category cascades to its products, so refuse while any remain.
        $inUse = (new ProductModel())->where('category_id', $id)->countAllResults();
        if ($inUse > 0) {
            $this->flash('err', "Cannot delete: {$inUse} product(s) still use this category.");
            return redirect()->to(base_url('admin/categories'));
        }

        (new CategoryModel())->delete($id);
        $this->flash('ok', 'Category deleted.');

        return redirect()->to(base_url('admin/categories'));
    }
}
