<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminUserModel extends Model
{
    protected $table         = 'admin_users';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    /** password_hash is written only via setPassword(), never mass-assigned. */
    protected $allowedFields = ['name', 'email', 'role', 'is_active', 'last_login_at'];

    protected $validationRules = [
        // Required so is_unique's {id} placeholder resolves on update.
        'id'    => 'permit_empty|is_natural_no_zero',
        'name'  => 'required|min_length[2]|max_length[120]',
        'email' => 'required|valid_email|max_length[160]|is_unique[admin_users.email,id,{id}]',
        'role'  => 'permit_empty|in_list[admin,staff]',
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', strtolower(trim($email)))->first();
    }

    public function setPassword(int $id, string $plain): bool
    {
        return $this->db->table($this->table)->where('id', $id)->update([
            'password_hash' => password_hash($plain, PASSWORD_DEFAULT),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * An inactive account or a missing hash must never authenticate. The dummy
     * verify keeps timing comparable so this cannot be used to probe which
     * admin emails exist.
     */
    public function verifyPassword(?array $user, string $plain): bool
    {
        $hash = $user['password_hash'] ?? null;

        if ($hash === null || $hash === '' || empty($user['is_active'])) {
            password_verify($plain, '$2y$10$usesomesillystringforsalt0000000000000000000000000000000000');

            return false;
        }

        return password_verify($plain, $hash);
    }

    public function markLogin(int $id): void
    {
        $this->db->table($this->table)->where('id', $id)
            ->update(['last_login_at' => date('Y-m-d H:i:s')]);
    }
}
