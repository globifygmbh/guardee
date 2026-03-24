<?php

namespace App\Controllers;

use App\Models\OfferModel;
use App\Models\InvitationModel;
use App\Models\CampaignModel;

class OfferController extends BaseController
{
    public function show($id)
    {
        $offer = (new OfferModel())->find($id);
        if (!$offer) {
            return redirect()->to('/dashboard')->with('error', 'Angebot nicht gefunden.');
        }

        $campaign   = (new CampaignModel())->find($offer['campaign_id']);
        $invitation = (new InvitationModel())->find($offer['invitation_id']);

        return view('offers/show', [
            'offer'      => $offer,
            'campaign'   => $campaign,
            'invitation' => $invitation,
            'role'       => session()->get('user_role'),
        ]);
    }

    public function create()
    {
        $profileId = session()->get('profile_id');

        $invitationId = $this->request->getPost('invitation_id');
        $invitation   = (new InvitationModel())->find($invitationId);
        if (!$invitation) {
            return redirect()->back()->with('error', 'Einladung nicht gefunden.');
        }

        (new OfferModel())->insert([
            'campaign_id'   => $invitation['campaign_id'],
            'invitation_id' => $invitationId,
            'brand_id'      => $profileId,
            'influencer_id' => $invitation['influencer_id'],
            'amount'        => $this->request->getPost('amount'),
            'currency'      => 'EUR',
            'message'       => $this->request->getPost('message'),
            'status'        => 'pending',
        ]);

        return redirect()->to("/campaigns/{$invitation['campaign_id']}")->with('success', 'Angebot gesendet.');
    }

    public function update($id)
    {
        (new OfferModel())->update($id, [
            'amount'  => $this->request->getPost('amount'),
            'message' => $this->request->getPost('message'),
            'status'  => 'pending',
        ]);

        return redirect()->to("/offers/{$id}")->with('success', 'Angebot aktualisiert.');
    }

    public function accept($id)
    {
        (new OfferModel())->update($id, ['status' => 'accepted']);
        return redirect()->to("/offers/{$id}")->with('success', 'Angebot angenommen.');
    }

    public function decline($id)
    {
        (new OfferModel())->update($id, ['status' => 'declined']);
        return redirect()->to("/offers/{$id}")->with('success', 'Angebot abgelehnt.');
    }

    public function acceptTerms($id)
    {
        (new OfferModel())->update($id, [
            'terms_accepted'    => 1,
            'terms_accepted_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to("/offers/{$id}")->with('success', 'AGB akzeptiert. Du kannst jetzt Content hochladen.');
    }
}
