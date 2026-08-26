<?php

namespace App\Controllers\Admin;

use App\Models\AdminUserModel;

/**
 * Staff account management. Reachable only with role = admin (enforced by the
 * `admin:admin` filter on the routes).
 *
 * The guards here exist to stop an administrator locking everyone — including
 * themselves — out of the dashboard. They are checked server-side; the UI
 * merely hides the buttons.
 */
class Users extends AdminController
{
    private const MIN_PASSWORD = 10;
    private const ROLES        = ['admin', 'staff'];

    public function index(): string
    {
        $model = new AdminUserModel();

        return $this->render('users/index', [
            'active'     => 'users',
            'rows'       => $model->orderBy('role', 'ASC')->orderBy('name', 'ASC')->findAll(),
            'meId'       => (int) session()->get('adminId'),
            'adminCount' => $this->activeAdminCount(),
        ], 'Staff users');
    }

    public function create(): string
    {
        return $this->render('users/form', [
            'active' => 'users',
            'row'    => null,
            'roles'  => self::ROLES,
            'minPw'  => self::MIN_PASSWORD,
        ], 'New staff user');
    }

    public function edit(int $id): string
    {
        $row = (new AdminUserModel())->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->render('users/form', [
            'active'     => 'users',
            'row'        => $row,
            'roles'      => self::ROLES,
            'minPw'      => self::MIN_PASSWORD,
            'meId'       => (int) session()->get('adminId'),
            'adminCount' => $this->activeAdminCount(),
        ], 'Edit staff user');
    }

    public function store()
    {
        $post  = $this->request->getPost();
        $model = new AdminUserModel();

        $pw  = (string) ($post['password'] ?? '');
        $pw2 = (string) ($post['password_confirm'] ?? '');

        if ($err = $this->passwordProblem($pw, $pw2)) {
            return $this->fail($err, base_url('admin/users/create'));
        }

        $data = [
            'name'      => trim((string) ($post['name'] ?? '')),
            'email'     => strtolower(trim((string) ($post['email'] ?? ''))),
            'role'      => $this->cleanRole($post['role'] ?? 'staff'),
            'is_active' => empty($post['is_active']) ? 0 : 1,
        ];

        $id = $model->insert($data, true);
        if (!$id) {
            return $this->fail(implode(' ', $model->errors()), base_url('admin/users/create'));
        }

        $model->setPassword((int) $id, $pw);
        $this->flash('ok', 'Staff user created.');

        return redirect()->to(base_url('admin/users'));
    }

    public function update(int $id)
    {
        $model = new AdminUserModel();
        $row   = $model->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $post   = $this->request->getPost();
        $me     = (int) session()->get('adminId');
        $role   = $this->cleanRole($post['role'] ?? $row['role']);
        $active = empty($post['is_active']) ? 0 : 1;

        // You may not remove your own access — that is how people get locked out.
        if ($id === $me && $role !== 'admin') {
            return $this->fail('You cannot change your own role.', base_url('admin/users/' . $id . '/edit'));
        }
        if ($id === $me && !$active) {
            return $this->fail('You cannot deactivate your own account.', base_url('admin/users/' . $id . '/edit'));
        }

        // Never let the last active administrator be demoted or switched off.
        $losingAdmin = $row['role'] === 'admin' && $row['is_active'] && ($role !== 'admin' || !$active);
        if ($losingAdmin && $this->activeAdminCount() <= 1) {
            return $this->fail(
                'This is the only active administrator. Promote another account first.',
                base_url('admin/users/' . $id . '/edit')
            );
        }

        $data = [
            'id'        => $id, // resolves is_unique's {id} placeholder
            'name'      => trim((string) ($post['name'] ?? '')),
            'email'     => strtolower(trim((string) ($post['email'] ?? ''))),
            'role'      => $role,
            'is_active' => $active,
        ];

        if (!$model->update($id, $data)) {
            return $this->fail(implode(' ', $model->errors()), base_url('admin/users/' . $id . '/edit'));
        }

        $this->flash('ok', 'Staff user updated.');

        return redirect()->to(base_url('admin/users'));
    }

    /** Admin-initiated password reset — no old password required. */
    public function password(int $id)
    {
        $model = new AdminUserModel();
        $row   = $model->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $pw  = (string) $this->request->getPost('password');
        $pw2 = (string) $this->request->getPost('password_confirm');

        if ($err = $this->passwordProblem($pw, $pw2)) {
            return $this->fail($err, base_url('admin/users/' . $id . '/edit'));
        }

        $model->setPassword($id, $pw);

        $this->flash('ok', 'Password updated for ' . $row['name'] . '.');

        return redirect()->to(base_url('admin/users'));
    }

    public function delete(int $id)
    {
        $model = new AdminUserModel();
        $row   = $model->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($id === (int) session()->get('adminId')) {
            return $this->fail('You cannot delete your own account.', base_url('admin/users'));
        }

        if ($row['role'] === 'admin' && $row['is_active'] && $this->activeAdminCount() <= 1) {
            return $this->fail(
                'This is the only active administrator and cannot be deleted.',
                base_url('admin/users')
            );
        }

        $model->delete($id);
        $this->flash('ok', 'Staff user deleted.');

        return redirect()->to(base_url('admin/users'));
    }

    // ------------------------------------------------------------------

    private function activeAdminCount(): int
    {
        return (new AdminUserModel())->where('role', 'admin')->where('is_active', 1)->countAllResults();
    }

    private function cleanRole($role): string
    {
        $role = is_string($role) ? $role : 'staff';

        return in_array($role, self::ROLES, true) ? $role : 'staff';
    }

    private function passwordProblem(string $pw, string $pw2): ?string
    {
        if (strlen($pw) < self::MIN_PASSWORD) {
            return 'Password must be at least ' . self::MIN_PASSWORD . ' characters.';
        }
        if ($pw !== $pw2) {
            return 'Passwords do not match.';
        }

        return null;
    }

    private function fail(string $message, string $to)
    {
        return redirect()->to($to)->withInput()
            ->with('flash', ['type' => 'err', 'message' => $message]);
    }
}
