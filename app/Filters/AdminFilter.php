<?php

namespace App\Filters;

use App\Models\AdminUserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Gate for everything under /admin.
 *
 * Checks `adminId` — the customer session key (`customerId`) is deliberately
 * not accepted here.
 *
 * The account is re-read from the database on every request rather than
 * trusting what was cached in the session at login. Without that, demoting or
 * deactivating someone would leave their existing session holding the old
 * privileges until they happened to log out.
 *
 * Usage: `['filter' => 'admin']` for any signed-in staff member,
 *        `['filter' => 'admin:admin']` to require role = admin.
 */
class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $id      = $session->get('adminId');

        if (!$id) {
            $session->set('adminRedirect', (string) current_url());

            return redirect()->to(base_url('admin/login'));
        }

        $user = (new AdminUserModel())->find((int) $id);

        // Deleted or deactivated mid-session: drop the session immediately.
        if (!$user || empty($user['is_active'])) {
            $session->remove(['adminId', 'adminName', 'adminEmail', 'adminRole', 'adminRedirect']);

            return redirect()->to(base_url('admin/login'))
                ->with('error', 'That account is no longer active.');
        }

        // Keep the session in step with the stored role.
        if (($session->get('adminRole') ?? null) !== $user['role']) {
            $session->set('adminRole', $user['role']);
        }
        if (($session->get('adminName') ?? null) !== $user['name']) {
            $session->set('adminName', $user['name']);
        }

        $required = is_array($arguments) ? ($arguments[0] ?? null) : $arguments;

        if ($required !== null && $user['role'] !== $required) {
            return redirect()->to(base_url('admin'))
                ->with('flash', ['type' => 'err', 'message' => 'That area is restricted to administrators.']);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
