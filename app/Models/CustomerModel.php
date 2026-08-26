<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table         = 'customers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    /**
     * password_hash is deliberately NOT in here. It is written only through
     * setPassword(), so a stray mass-assignment can never set it directly.
     */
    protected $allowedFields = [
        'first_name', 'last_name', 'email', 'phone', 'whatsapp',
        'email_verified', 'last_login_at',
    ];

    protected $validationRules = [
        // Required so is_unique's {id} placeholder resolves on update.
        'id'         => 'permit_empty|is_natural_no_zero',
        'first_name' => 'required|min_length[2]|max_length[80]',
        'last_name'  => 'permit_empty|max_length[80]',
        // Guest checkout creates a customer with no email, so this cannot be
        // `required` here. Auth::signup validates the email itself before
        // registering, so registration is unaffected.
        'email'      => 'permit_empty|valid_email|max_length[160]|is_unique[customers.email,id,{id}]',
        'phone'      => 'permit_empty|max_length[32]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique'   => 'An account with that email already exists.',
            'valid_email' => 'Enter a valid email address.',
            'required'    => 'Enter a valid email address.',
        ],
        'first_name' => [
            'required'   => 'Please enter your full name.',
            'min_length' => 'Please enter your full name.',
        ],
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', strtolower(trim($email)))->first();
    }

    /**
     * Hash and store a password. Uses PASSWORD_DEFAULT so the algorithm
     * upgrades with PHP rather than being pinned here.
     */
    public function setPassword(int $id, string $plain): bool
    {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->update([
                'password_hash' => password_hash($plain, PASSWORD_DEFAULT),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * Verify a plaintext password against a stored hash.
     *
     * A row with no hash (seeded/guest customer) must never authenticate, so
     * we still burn a hash comparison to keep the timing comparable to a real
     * account and avoid leaking which emails are registered.
     */
    public function verifyPassword(?array $customer, string $plain): bool
    {
        $hash = $customer['password_hash'] ?? null;

        if ($hash === null || $hash === '') {
            password_verify($plain, '$2y$10$usesomesillystringforsalt0000000000000000000000000000000000');

            return false;
        }

        return password_verify($plain, $hash);
    }

    public function markLogin(int $id): void
    {
        $this->db->table($this->table)
            ->where('id', $id)
            ->update(['last_login_at' => date('Y-m-d H:i:s')]);
    }
}
