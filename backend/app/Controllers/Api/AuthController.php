<?php

namespace App\Controllers\Api;

use App\Libraries\JWTManager;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\InfluencerProfileModel;
use App\Models\BrandProfileModel;
use App\Services\ActivityLogService;

class AuthController extends BaseApiController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;
    protected JWTManager $jwt;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->jwt = new JWTManager();
    }

    /**
     * POST /api/auth/login
     */
    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return $this->jsonError(implode(', ', $this->validator->getErrors()));
        }

        $email = $this->request->getJsonVar('email') ?? $this->request->getPost('email');
        $password = $this->request->getJsonVar('password') ?? $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->jsonError('Invalid email or password.', 401);
        }

        if ($user['status'] !== 'active') {
            return $this->jsonError('Account is not active.', 403);
        }

        // Get role name
        $role = $this->roleModel->find($user['role_id']);
        $roleName = $role ? $role['name'] : null;

        // Update last login
        $this->userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        // Log activity
        (new ActivityLogService())->log($user['id'], 'login', 'user', $user['id']);

        $token = $this->jwt->encode([
            'id'    => $user['id'],
            'email' => $user['email'],
            'role'  => $roleName,
        ]);

        return $this->jsonSuccess([
            'token' => $token,
            'user'  => $this->userModel->toSafe($user),
            'role'  => $roleName,
        ]);
    }

    /**
     * POST /api/auth/register
     */
    public function register()
    {
        $rules = [
            'email'      => 'required|valid_email|is_unique[users.email]',
            'password'   => 'required|min_length[6]',
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'role'       => 'required|in_list[influencer,brand]',
        ];

        if (!$this->validate($rules)) {
            return $this->jsonError(implode(', ', $this->validator->getErrors()));
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $roleName = $input['role'];

        // Find the role ID
        $role = $this->roleModel->where('name', $roleName)->first();
        if (!$role) {
            return $this->jsonError('Invalid role.');
        }

        $userId = $this->userModel->insert([
            'role_id'    => $role['id'],
            'email'      => $input['email'],
            'password'   => $input['password'],
            'first_name' => $input['first_name'],
            'last_name'  => $input['last_name'],
            'status'     => 'active',
        ]);

        if (!$userId) {
            return $this->jsonError('Failed to create user.');
        }

        // Create profile based on role
        if ($roleName === 'influencer') {
            $profileModel = new InfluencerProfileModel();
            $profileModel->insert([
                'user_id'      => $userId,
                'display_name' => $input['first_name'] . ' ' . $input['last_name'],
            ]);
        } elseif ($roleName === 'brand') {
            $profileModel = new BrandProfileModel();
            $profileModel->insert([
                'user_id'      => $userId,
                'company_name' => $input['company_name'] ?? ($input['first_name'] . ' ' . $input['last_name']),
            ]);
        }

        $user = $this->userModel->find($userId);

        // Log activity
        (new ActivityLogService())->log($userId, 'register', 'user', $userId);

        $token = $this->jwt->encode([
            'id'    => $user['id'],
            'email' => $user['email'],
            'role'  => $roleName,
        ]);

        return $this->jsonSuccess([
            'token' => $token,
            'user'  => $this->userModel->toSafe($user),
            'role'  => $roleName,
        ], 201);
    }

    /**
     * GET /api/auth/me
     */
    public function me()
    {
        $currentUser = $this->currentUser();

        if (!$currentUser) {
            return $this->jsonError('Unauthorized.', 401);
        }

        $user = $this->userModel->find($currentUser->id);

        if (!$user) {
            return $this->jsonError('User not found.', 404);
        }

        $role = $this->roleModel->find($user['role_id']);
        $roleName = $role ? $role['name'] : null;
        $profile = null;

        if ($roleName === 'influencer') {
            $profile = (new InfluencerProfileModel())->where('user_id', $user['id'])->first();
        } elseif ($roleName === 'brand') {
            $profile = (new BrandProfileModel())->where('user_id', $user['id'])->first();
        }

        return $this->jsonSuccess([
            'user'    => $this->userModel->toSafe($user),
            'role'    => $roleName,
            'profile' => $profile,
        ]);
    }
}
