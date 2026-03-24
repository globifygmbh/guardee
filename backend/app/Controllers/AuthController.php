<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BrandProfileModel;
use App\Models\InfluencerProfileModel;

class AuthController extends BaseController
{
    public function loginForm()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Ungültige E-Mail oder Passwort.')->withInput();
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->with('error', 'Konto ist nicht aktiv.')->withInput();
        }

        // Get role name
        $db = \Config\Database::connect();
        $role = $db->table('roles')->where('id', $user['role_id'])->get()->getRowArray();

        // Get profile id
        $profileId = null;
        if ($role['name'] === 'brand') {
            $profile = (new BrandProfileModel())->where('user_id', $user['id'])->first();
            $profileId = $profile ? $profile['id'] : null;
        } elseif ($role['name'] === 'influencer') {
            $profile = (new InfluencerProfileModel())->where('user_id', $user['id'])->first();
            $profileId = $profile ? $profile['id'] : null;
        }

        session()->set([
            'user_id'    => $user['id'],
            'user_email' => $user['email'],
            'user_role'  => $role['name'],
            'user_name'  => trim($user['first_name'] . ' ' . $user['last_name']),
            'profile_id' => $profileId,
        ]);

        // Update last login
        $userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        return redirect()->to('/dashboard');
    }

    public function registerForm()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/register');
    }

    public function register()
    {
        $role     = $this->request->getPost('role');
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $firstName = $this->request->getPost('first_name');
        $lastName  = $this->request->getPost('last_name');

        $userModel = new UserModel();

        // Check if email exists
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->with('error', 'Diese E-Mail ist bereits registriert.')->withInput();
        }

        $db = \Config\Database::connect();
        $roleRow = $db->table('roles')->where('name', $role)->get()->getRowArray();
        if (!$roleRow) {
            return redirect()->back()->with('error', 'Ungültige Rolle.')->withInput();
        }

        $userId = $userModel->insert([
            'role_id'    => $roleRow['id'],
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_BCRYPT),
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'status'     => 'active',
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);

        $profileId = null;
        if ($role === 'brand') {
            $profileId = (new BrandProfileModel())->insert([
                'user_id'      => $userId,
                'company_name' => $this->request->getPost('company_name') ?: $firstName,
                'industry'     => $this->request->getPost('industry'),
            ]);
        } elseif ($role === 'influencer') {
            $profileId = (new InfluencerProfileModel())->insert([
                'user_id'          => $userId,
                'display_name'     => $this->request->getPost('display_name') ?: $firstName,
                'niche'            => $this->request->getPost('niche'),
                'instagram_handle' => $this->request->getPost('instagram_handle'),
                'country'          => $this->request->getPost('country'),
            ]);
        }

        session()->set([
            'user_id'    => $userId,
            'user_email' => $email,
            'user_role'  => $role,
            'user_name'  => trim($firstName . ' ' . $lastName),
            'profile_id' => $profileId,
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
