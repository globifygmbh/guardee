<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\InfluencerProfileModel;
use App\Models\BrandProfileModel;

class UserController extends BaseApiController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * GET /api/users/influencers
     * List all influencer profiles (for admin matching).
     */
    public function influencers()
    {
        $influencerRole = $this->roleModel->where('name', 'influencer')->first();

        if (!$influencerRole) {
            return $this->jsonSuccess([]);
        }

        $users = $this->userModel
            ->where('role_id', $influencerRole['id'])
            ->where('status', 'active')
            ->findAll();

        $profileModel = new InfluencerProfileModel();
        $result = [];

        foreach ($users as $user) {
            $profile = $profileModel->where('user_id', $user['id'])->first();
            $result[] = [
                'user'    => $this->userModel->toSafe($user),
                'profile' => $profile,
            ];
        }

        return $this->jsonSuccess($result);
    }

    /**
     * GET /api/users/brands
     * List all brand profiles.
     */
    public function brands()
    {
        $brandRole = $this->roleModel->where('name', 'brand')->first();

        if (!$brandRole) {
            return $this->jsonSuccess([]);
        }

        $users = $this->userModel
            ->where('role_id', $brandRole['id'])
            ->where('status', 'active')
            ->findAll();

        $profileModel = new BrandProfileModel();
        $result = [];

        foreach ($users as $user) {
            $profile = $profileModel->where('user_id', $user['id'])->first();
            $result[] = [
                'user'    => $this->userModel->toSafe($user),
                'profile' => $profile,
            ];
        }

        return $this->jsonSuccess($result);
    }

    /**
     * GET /api/users/$id
     * Get user detail with profile.
     */
    public function show(int $id)
    {
        $user = $this->userModel->find($id);

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
