<?php

namespace App\Controllers;

use App\Models\InvitationModel;
use App\Models\OfferModel;
use App\Services\NotificationService;

class InvitationController extends BaseController
{
    public function index()
    {
        $profileId = session()->get('profile_id');
        $model = new InvitationModel();

        $invitations = $model
            ->select('campaign_invitations.*, campaigns.title as campaign_title, campaigns.budget, campaigns.start_date, campaigns.end_date, campaigns.description as campaign_description')
            ->join('campaigns', 'campaigns.id = campaign_invitations.campaign_id')
            ->where('campaign_invitations.influencer_id', $profileId)
            ->orderBy('campaign_invitations.created_at', 'DESC')
            ->findAll();

        // Attach offer info
        $offerModel = new OfferModel();
        foreach ($invitations as &$inv) {
            $inv['offer'] = $offerModel->where('invitation_id', $inv['id'])->first();
        }

        return view('invitations/index', [
            'invitations' => $invitations,
        ]);
    }

    public function invite()
    {
        $campaignId  = $this->request->getPost('campaign_id');
        $influencerId = $this->request->getPost('influencer_id');

        $model = new InvitationModel();

        // Check duplicate
        $exists = $model->where('campaign_id', $campaignId)
            ->where('influencer_id', $influencerId)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Influencer bereits eingeladen.');
        }

        $model->insert([
            'campaign_id'   => $campaignId,
            'influencer_id' => $influencerId,
            'invited_by'    => session()->get('user_id'),
            'status'        => 'pending',
        ]);

        return redirect()->to("/campaigns/{$campaignId}")->with('success', 'Influencer eingeladen.');
    }

    public function respond($id)
    {
        $status = $this->request->getPost('status');
        if (!in_array($status, ['interested', 'declined'])) {
            return redirect()->back()->with('error', 'Ungültige Antwort.');
        }

        (new InvitationModel())->update($id, [
            'status'       => $status,
            'responded_at' => date('Y-m-d H:i:s'),
        ]);

        $label = $status === 'interested' ? 'Interesse bekundet' : 'Abgelehnt';
        return redirect()->to('/invitations')->with('success', "Einladung: {$label}.");
    }
}
