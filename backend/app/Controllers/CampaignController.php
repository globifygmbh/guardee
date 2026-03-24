<?php

namespace App\Controllers;

use App\Models\CampaignModel;
use App\Models\InvitationModel;
use App\Models\OfferModel;
use App\Models\AssetModel;
use App\Models\InfluencerProfileModel;
use App\Models\BrandProfileModel;

class CampaignController extends BaseController
{
    public function index()
    {
        $role      = session()->get('user_role');
        $profileId = session()->get('profile_id');
        $model     = new CampaignModel();

        if ($role === 'admin') {
            $campaigns = $model->orderBy('created_at', 'DESC')->findAll();
        } elseif ($role === 'brand') {
            $campaigns = $model->where('brand_id', $profileId)->orderBy('created_at', 'DESC')->findAll();
        } elseif ($role === 'influencer') {
            $campaigns = $model->select('campaigns.*')
                ->join('campaign_invitations', 'campaign_invitations.campaign_id = campaigns.id')
                ->where('campaign_invitations.influencer_id', $profileId)
                ->orderBy('campaigns.created_at', 'DESC')->findAll();
        } else {
            $campaigns = [];
        }

        return view('campaigns/index', [
            'campaigns' => $campaigns,
            'role'      => $role,
        ]);
    }

    public function create()
    {
        return view('campaigns/create');
    }

    public function store()
    {
        $profileId = session()->get('profile_id');
        $userId    = session()->get('user_id');

        $data = [
            'brand_id'        => $profileId,
            'created_by'      => $userId,
            'title'           => $this->request->getPost('title'),
            'description'     => $this->request->getPost('description'),
            'budget'          => $this->request->getPost('budget'),
            'target_audience' => $this->request->getPost('target_audience'),
            'countries'       => $this->request->getPost('countries'),
            'start_date'      => $this->request->getPost('start_date'),
            'end_date'        => $this->request->getPost('end_date'),
            'status'          => 'pending_approval',
        ];

        // Handle PDF upload
        $file = $this->request->getFile('briefing_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/briefings', $newName);
            $data['briefing_file'] = $newName;
        }

        $model = new CampaignModel();
        $model->insert($data);

        return redirect()->to('/campaigns')->with('success', 'Kampagne erstellt und zur Freigabe eingereicht.');
    }

    public function show($id)
    {
        $model    = new CampaignModel();
        $campaign = $model->find($id);
        if (!$campaign) {
            return redirect()->to('/campaigns')->with('error', 'Kampagne nicht gefunden.');
        }

        $invitationModel = new InvitationModel();
        $invitations = $invitationModel
            ->select('campaign_invitations.*, influencer_profiles.display_name, influencer_profiles.niche, influencer_profiles.instagram_handle, influencer_profiles.followers_count')
            ->join('influencer_profiles', 'influencer_profiles.id = campaign_invitations.influencer_id')
            ->where('campaign_id', $id)->findAll();

        $offers = (new OfferModel())->where('campaign_id', $id)->findAll();
        $assets = (new AssetModel())->where('campaign_id', $id)->orderBy('created_at', 'DESC')->findAll();

        // Brand info
        $brand = (new BrandProfileModel())->find($campaign['brand_id']);

        // For admin matching: all influencers
        $allInfluencers = [];
        if (session()->get('user_role') === 'admin') {
            $invitedIds = array_column($invitations, 'influencer_id');
            $infModel = new InfluencerProfileModel();
            if (!empty($invitedIds)) {
                $allInfluencers = $infModel->whereNotIn('id', $invitedIds)->findAll();
            } else {
                $allInfluencers = $infModel->findAll();
            }
        }

        return view('campaigns/show', [
            'campaign'        => $campaign,
            'invitations'     => $invitations,
            'offers'          => $offers,
            'assets'          => $assets,
            'brand'           => $brand,
            'allInfluencers'  => $allInfluencers,
            'role'            => session()->get('user_role'),
            'profileId'       => session()->get('profile_id'),
        ]);
    }

    public function update($id)
    {
        $model = new CampaignModel();
        $model->update($id, [
            'title'           => $this->request->getPost('title'),
            'description'     => $this->request->getPost('description'),
            'budget'          => $this->request->getPost('budget'),
            'target_audience' => $this->request->getPost('target_audience'),
            'countries'       => $this->request->getPost('countries'),
            'start_date'      => $this->request->getPost('start_date'),
            'end_date'        => $this->request->getPost('end_date'),
        ]);

        return redirect()->to("/campaigns/{$id}")->with('success', 'Kampagne aktualisiert.');
    }

    public function pending()
    {
        $campaigns = (new CampaignModel())
            ->where('status', 'pending_approval')
            ->orderBy('created_at', 'DESC')->findAll();

        return view('campaigns/pending', [
            'campaigns' => $campaigns,
        ]);
    }

    public function approve($id)
    {
        (new CampaignModel())->update($id, [
            'status'      => 'approved',
            'approved_by' => session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to("/campaigns/{$id}")->with('success', 'Kampagne freigegeben.');
    }

    public function reject($id)
    {
        (new CampaignModel())->update($id, [
            'status' => 'cancelled',
        ]);

        return redirect()->to('/campaigns/pending')->with('success', 'Kampagne abgelehnt.');
    }
}
