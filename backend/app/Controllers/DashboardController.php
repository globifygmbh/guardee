<?php

namespace App\Controllers;

use App\Models\CampaignModel;
use App\Models\InvitationModel;
use App\Models\OfferModel;
use App\Models\UserModel;
use App\Models\NotificationModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $role      = session()->get('user_role');
        $userId    = session()->get('user_id');
        $profileId = session()->get('profile_id');

        $campaignModel    = new CampaignModel();
        $invitationModel  = new InvitationModel();
        $offerModel       = new OfferModel();
        $notificationModel = new NotificationModel();

        $data = [
            'role'          => $role,
            'user_name'     => session()->get('user_name'),
            'notifications' => $notificationModel->where('user_id', $userId)
                ->where('is_read', 0)->orderBy('created_at', 'DESC')->limit(5)->findAll(),
        ];

        if ($role === 'admin') {
            $data['pending_count']   = $campaignModel->where('status', 'pending_approval')->countAllResults(false);
            $data['active_count']    = $campaignModel->where('status', 'active')->countAllResults(false);
            $data['total_campaigns'] = $campaignModel->countAllResults(false);
            $data['total_users']     = (new UserModel())->countAllResults();
            $data['recent_campaigns'] = $campaignModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
        } elseif ($role === 'brand') {
            $data['my_campaigns']     = $campaignModel->where('brand_id', $profileId)->orderBy('created_at', 'DESC')->limit(5)->findAll();
            $data['total_campaigns']  = $campaignModel->where('brand_id', $profileId)->countAllResults(false);
            $data['active_count']     = $campaignModel->where('brand_id', $profileId)->where('status', 'active')->countAllResults(false);
            $data['pending_count']    = $campaignModel->where('brand_id', $profileId)->where('status', 'pending_approval')->countAllResults(false);
        } elseif ($role === 'influencer') {
            $data['invitations']      = $invitationModel->select('campaign_invitations.*, campaigns.title as campaign_title, campaigns.budget, campaigns.start_date, campaigns.end_date')
                ->join('campaigns', 'campaigns.id = campaign_invitations.campaign_id')
                ->where('campaign_invitations.influencer_id', $profileId)
                ->orderBy('campaign_invitations.created_at', 'DESC')->limit(5)->findAll();
            $data['pending_invites']  = $invitationModel->where('influencer_id', $profileId)->where('status', 'pending')->countAllResults(false);
            $data['active_collabs']   = $offerModel->where('influencer_id', $profileId)->where('status', 'accepted')->countAllResults();
        }

        return view('dashboard/index', $data);
    }
}
