<?php

namespace App\Commands;

use App\Models\AdminUserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Creates or updates a staff login.
 *
 *   php spark make:admin "Name" email@example.com [role]
 *
 * The password is prompted for rather than passed as an argument, so it does
 * not end up in the shell history or the process list.
 */
class MakeAdmin extends BaseCommand
{
    protected $group       = 'Admin';
    protected $name        = 'make:admin';
    protected $description = 'Create (or update) an admin user for the dashboard.';
    protected $usage       = 'make:admin [name] [email] [role]';

    public function run(array $params): int
    {
        $name  = $params[0] ?? CLI::prompt('Full name', null, 'required');
        $email = strtolower(trim($params[1] ?? CLI::prompt('Email', null, 'required|valid_email')));
        $role  = $params[2] ?? CLI::prompt('Role', ['admin', 'staff']);

        $pw = CLI::prompt('Password (min 10 chars)', null, 'required');
        if (strlen($pw) < 10) {
            CLI::error('Password must be at least 10 characters.');

            return EXIT_ERROR;
        }
        if ($pw !== CLI::prompt('Confirm password', null, 'required')) {
            CLI::error('Passwords do not match.');

            return EXIT_ERROR;
        }

        $model    = new AdminUserModel();
        $existing = $model->findByEmail($email);

        if ($existing) {
            $model->update($existing['id'], ['name' => $name, 'role' => $role, 'is_active' => 1]);
            $model->setPassword((int) $existing['id'], $pw);
            CLI::write('Updated admin: ' . $email, 'green');

            return EXIT_SUCCESS;
        }

        $id = $model->insert(['name' => $name, 'email' => $email, 'role' => $role, 'is_active' => 1], true);

        if (!$id) {
            CLI::error(implode(' ', $model->errors()));

            return EXIT_ERROR;
        }

        $model->setPassword((int) $id, $pw);
        CLI::write('Created admin: ' . $email . ' (' . $role . ')', 'green');

        return EXIT_SUCCESS;
    }
}
