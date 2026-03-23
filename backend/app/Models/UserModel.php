<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'role_id',
        'email',
        'password',
        'first_name',
        'last_name',
        'avatar',
        'phone',
        'status',
        'email_verified_at',
        'last_login_at',
    ];

    protected $hiddenFields = ['password'];

    protected $beforeInsert = ['hashPassword'];

    /**
     * Hash password before inserting into the database.
     */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }

        return $data;
    }

    /**
     * Find a user by email address.
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Return user data without hidden fields.
     */
    public function toSafe(array $user): array
    {
        foreach ($this->hiddenFields as $field) {
            unset($user[$field]);
        }

        return $user;
    }
}
