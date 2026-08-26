<?php

namespace App\Controllers\Admin;

use App\Models\TestimonialModel;

class Testimonials extends AdminController
{
    public function index(): string
    {
        return $this->render('testimonials/index', [
            'active' => 'testimonials',
            'rows'   => (new TestimonialModel())->orderBy('id', 'DESC')->findAll(),
        ], 'Testimonials');
    }

    public function create(): string
    {
        return $this->render('testimonials/form', ['active' => 'testimonials', 'row' => null], 'New testimonial');
    }

    public function edit(int $id): string
    {
        $row = (new TestimonialModel())->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('testimonials/form', ['active' => 'testimonials', 'row' => $row], 'Edit testimonial');
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
        $model = new TestimonialModel();
        $post  = $this->request->getPost();
        $data  = [
            'name'         => trim((string) ($post['name'] ?? '')),
            'quote'        => trim((string) ($post['quote'] ?? '')),
            'item'         => trim((string) ($post['item'] ?? '')) ?: null,
            'stars'        => max(1, min(5, (int) ($post['stars'] ?? 5))),
            'is_published' => empty($post['is_published']) ? 0 : 1,
        ];

        if ($id !== null) {
            $data['id'] = $id;
        }

        $ok = $id === null ? $model->insert($data) : $model->update($id, $data);
        if (!$ok) {
            // Explicit target: redirect()->back() throws when there is no Referer.
            $back = $id === null
                ? base_url('admin/testimonials/create')
                : base_url('admin/testimonials/' . $id . '/edit');

            return redirect()->to($back)->withInput()
                ->with('flash', ['type' => 'err', 'message' => implode(' ', $model->errors())]);
        }

        $this->flash('ok', $id === null ? 'Testimonial created.' : 'Testimonial updated.');

        return redirect()->to(base_url('admin/testimonials'));
    }

    public function delete(int $id)
    {
        if (!$this->requireAdminRole()) {
            $this->flash('err', 'Only an admin may delete testimonials.');
            return redirect()->to(base_url('admin/testimonials'));
        }
        (new TestimonialModel())->delete($id);
        $this->flash('ok', 'Testimonial deleted.');

        return redirect()->to(base_url('admin/testimonials'));
    }
}
