<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Products extends AdminController
{
    public function index(): string
    {
        $model = new ProductModel();
        $q     = trim((string) $this->request->getGet('q'));
        $cat   = (int) $this->request->getGet('category');

        $builder = $model->withCategory();
        if ($q !== '') {
            $builder->groupStart()
                ->like('p.name', $q)->orLike('p.slug', $q)->orLike('p.ingredients', $q)
                ->groupEnd();
        }
        if ($cat > 0) {
            $builder->where('p.category_id', $cat);
        }

        $rows = $builder->orderBy('p.name', 'ASC')->get()->getResultArray();

        return $this->render('products/index', [
            'active'     => 'products',
            'rows'       => $rows,
            'categories' => (new CategoryModel())->ordered(),
            'q'          => $q,
            'cat'        => $cat,
            // Primary photo per product, so the list can show it with its mark.
            'primary'    => (new \App\Models\ProductPhotoModel())->primaryByProduct(),
        ], 'Products');
    }

    public function create(): string
    {
        return $this->render('products/form', [
            'active'     => 'products',
            'row'        => null,
            'categories' => (new CategoryModel())->ordered(),
        ], 'New product');
    }

    public function edit(int $id): string
    {
        $row = (new ProductModel())->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('products/form', [
            'active'     => 'products',
            'row'        => $row,
            'categories' => (new CategoryModel())->ordered(),
            'photos'     => (new \App\Models\ProductPhotoModel())->forProduct($id),
            'maxPhotos'  => \App\Libraries\ProductPhotoStore::MAX_PHOTOS,
        ], 'Edit product');
    }

    /** POST /admin/products/(id)/photos — add one photo. */
    public function uploadPhoto(int $id)
    {
        $product = (new ProductModel())->find($id);
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $file  = $this->request->getFile('photo');
        $store = new \App\Libraries\ProductPhotoStore();

        if ($file === null) {
            $this->flash('err', 'Choose an image to upload.');
        } elseif (!$store->add($product, $file)) {
            $this->flash('err', $store->error());
        } else {
            $this->flash('ok', $store->replacedLast()
                ? 'Photo saved — the last slot was replaced.'
                : 'Photo added.');
        }

        return redirect()->to(base_url('admin/products/' . $id . '/edit'));
    }

    /** POST /admin/products/(id)/photos/(photoId)/origin — mark real or AI. */
    public function photoOrigin(int $id, int $photoId)
    {
        $model = new \App\Models\ProductPhotoModel();
        $photo = $model->where('id', $photoId)->where('product_id', $id)->first();

        if (!$photo) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $isAi = $this->request->getPost('is_ai') === '1';
        $model->update($photoId, ['is_ai' => $isAi ? 1 : 0]);

        $this->flash('ok', 'Photo marked as ' . ($isAi ? 'AI-generated' : 'a real photo') . '.');

        return redirect()->to(base_url('admin/products/' . $id . '/edit'));
    }

    /** POST /admin/products/(id)/photos/(photoId)/delete */
    public function deletePhoto(int $id, int $photoId)
    {
        $product = (new ProductModel())->find($id);
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $store = new \App\Libraries\ProductPhotoStore();
        if ($store->delete($product, $photoId)) {
            $this->flash('ok', 'Photo removed.');
        } else {
            $this->flash('err', $store->error());
        }

        return redirect()->to(base_url('admin/products/' . $id . '/edit'));
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
        $model = new ProductModel();
        $post  = $this->request->getPost();

        $data = [
            'category_id'   => (int) ($post['category_id'] ?? 0),
            'name'          => trim((string) ($post['name'] ?? '')),
            'slug'          => trim((string) ($post['slug'] ?? '')),
            'note'          => trim((string) ($post['note'] ?? '')) ?: null,
            'price'         => (int) ($post['price'] ?? 0),
            'kcal'          => ($post['kcal'] ?? '') === '' ? null : (int) $post['kcal'],
            'ingredients'   => trim((string) ($post['ingredients'] ?? '')) ?: null,
            'image'         => trim((string) ($post['image'] ?? '')) ?: null,
            'is_new'        => empty($post['is_new']) ? 0 : 1,
            'is_featured'   => empty($post['is_featured']) ? 0 : 1,
            'in_bagel_pool' => empty($post['in_bagel_pool']) ? 0 : 1,
            'is_active'     => empty($post['is_active']) ? 0 : 1,
            'min_qty'       => max(1, (int) ($post['min_qty'] ?? 1)),
        ];

        if ($data['slug'] === '' && $data['name'] !== '') {
            $data['slug'] = url_title($data['name'], '-', true);
        }

        // Codes are assigned by the system, never accepted from the form.
        if ($id === null) {
            $data['code'] = $model->generateCode();
        }

        if ($id !== null) {
            // is_unique[...,id,{id}] resolves {id} from the data array, not from
            // the update key — without this the row collides with itself.
            $data['id'] = $id;
        }

        $ok = $id === null ? $model->insert($data) : $model->update($id, $data);

        if (!$ok) {
            // Explicit target: redirect()->back() throws when there is no Referer.
            $back = $id === null
                ? base_url('admin/products/create')
                : base_url('admin/products/' . $id . '/edit');

            return redirect()->to($back)->withInput()
                ->with('flash', ['type' => 'err', 'message' => implode(' ', $model->errors())]);
        }

        $this->flash('ok', $id === null ? 'Product created.' : 'Product updated.');

        return redirect()->to(base_url('admin/products'));
    }

    public function delete(int $id)
    {
        if (!$this->requireAdminRole()) {
            $this->flash('err', 'Only an admin may delete products.');

            return redirect()->to(base_url('admin/products'));
        }

        (new ProductModel())->delete($id);
        $this->flash('ok', 'Product deleted.');

        return redirect()->to(base_url('admin/products'));
    }
}
