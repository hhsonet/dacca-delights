<?php

namespace App\Controllers\Admin;

use App\Models\GalleryModel;

class Gallery extends AdminController
{
    public function index(): string
    {
        return $this->render('gallery/index', [
            'active' => 'gallery', 'rows' => (new GalleryModel())->ordered(),
        ], 'Gallery');
    }

    public function create(): string
    {
        return $this->render('gallery/form', ['active' => 'gallery', 'row' => null], 'New image');
    }

    public function edit(int $id): string
    {
        $row = (new GalleryModel())->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('gallery/form', ['active' => 'gallery', 'row' => $row], 'Edit image');
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
        $model = new GalleryModel();
        $post  = $this->request->getPost();
        $data  = [
            'src'        => trim((string) ($post['src'] ?? '')),
            'alt'        => trim((string) ($post['alt'] ?? '')) ?: null,
            'span'       => max(1, min(3, (int) ($post['span'] ?? 1))),
            'sort_order' => (int) ($post['sort_order'] ?? 0),
        ];

        if ($id !== null) {
            $data['id'] = $id;
        }

        $ok = $id === null ? $model->insert($data) : $model->update($id, $data);
        if (!$ok) {
            // Explicit target: redirect()->back() throws when there is no Referer.
            $back = $id === null
                ? base_url('admin/gallery/create')
                : base_url('admin/gallery/' . $id . '/edit');

            return redirect()->to($back)->withInput()
                ->with('flash', ['type' => 'err', 'message' => implode(' ', $model->errors())]);
        }

        $this->flash('ok', $id === null ? 'Image added.' : 'Image updated.');

        return redirect()->to(base_url('admin/gallery'));
    }

    public function delete(int $id)
    {
        if (!$this->requireAdminRole()) {
            $this->flash('err', 'Only an admin may delete images.');
            return redirect()->to(base_url('admin/gallery'));
        }
        (new GalleryModel())->delete($id);
        $this->flash('ok', 'Image deleted.');

        return redirect()->to(base_url('admin/gallery'));
    }
}
