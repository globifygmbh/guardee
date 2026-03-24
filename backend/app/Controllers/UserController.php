<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\InfluencerProfileModel;
use App\Models\BrandProfileModel;

class UserController extends BaseController
{
    public function index()
    {
        $users = (new UserModel())
            ->select('users.*, roles.name as role_name, roles.label as role_label')
            ->join('roles', 'roles.id = users.role_id')
            ->orderBy('users.created_at', 'DESC')->findAll();

        return view('users/index', ['users' => $users]);
    }

    public function influencers()
    {
        $influencers = (new InfluencerProfileModel())
            ->select('influencer_profiles.*, users.email, users.first_name, users.last_name, users.status')
            ->join('users', 'users.id = influencer_profiles.user_id')
            ->orderBy('influencer_profiles.display_name', 'ASC')->findAll();

        return view('users/influencers', ['influencers' => $influencers]);
    }
}
