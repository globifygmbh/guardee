<?php

namespace App\Controllers\Api;

use App\Models\OfferModel;
use App\Models\InvitationModel;
use App\Models\CampaignModel;
use App\Models\BrandProfileModel;
use App\Services\NotificationService;
use App\Services\ActivityLogService;

class OfferController extends BaseApiController
{
    protected OfferModel $offerModel;
    protected InvitationModel $invitationModel;
    protected CampaignModel $campaignModel;

    public function __construct()
    {
        $this->offerModel = new OfferModel();
        $this->invitationModel = new InvitationModel();
        $this->campaignModel = new CampaignModel();
    }

    /**
     * POST /api/offers
     * Brand creates an offer for an interested influencer.
     */
    public function create()
    {
        $rules = [
            'campaign_id'   => 'required|integer',
            'invitation_id' => 'required|integer',
            'influencer_id' => 'required|integer',
            'amount'        => 'required|numeric',
            'currency'      => 'required|min_length[3]|max_length[3]',
        ];

        if (!$this->validate($rules)) {
            return $this->jsonError(implode(', ', $this->validator->getErrors()));
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $userId = $this->currentUserId();

        // Verify invitation exists and is "interested"
        $invitation = $this->invitationModel->find($input['invitation_id']);
        if (!$invitation) {
            return $this->jsonError('Invitation not found.', 404);
        }

        if ($invitation['status'] !== 'interested') {
            return $this->jsonError('Influencer has not expressed interest in this invitation.');
        }

        // Get brand profile
        $brandProfile = (new BrandProfileModel())->where('user_id', $userId)->first();

        $offerId = $this->offerModel->insert([
            'campaign_id'   => $input['campaign_id'],
            'invitation_id' => $input['invitation_id'],
            'brand_id'      => $brandProfile ? $brandProfile['id'] : null,
            'influencer_id' => $input['influencer_id'],
            'amount'        => $input['amount'],
            'currency'      => $input['currency'],
            'message'       => $input['message'] ?? null,
            'status'        => 'pending',
        ]);

        if (!$offerId) {
            return $this->jsonError('Failed to create offer.');
        }

        // Notify the influencer
        $campaign = $this->campaignModel->find($input['campaign_id']);
        (new NotificationService())->createNotification(
            $input['influencer_id'],
            'offer_received',
            'New Offer Received',
            'You have received an offer of ' . $input['amount'] . ' ' . $input['currency'] . ' for campaign "' . ($campaign['title'] ?? '') . '".',
            '/offers'
        );

        (new ActivityLogService())->log($userId, 'offer_created', 'offer', $offerId);

        return $this->jsonSuccess($this->offerModel->find($offerId), 201);
    }

    /**
     * PUT /api/offers/$id
     * Brand updates offer amount.
     */
    public function update(int $id)
    {
        $offer = $this->offerModel->find($id);

        if (!$offer) {
            return $this->jsonError('Offer not found.', 404);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        $allowedUpdates = ['amount', 'currency', 'message'];
        $data = array_intersect_key($input, array_flip($allowedUpdates));

        if (empty($data)) {
            return $this->jsonError('No valid fields to update.');
        }

        $this->offerModel->update($id, $data);

        // Notify influencer about updated offer
        (new NotificationService())->createNotification(
            $offer['influencer_id'],
            'offer_updated',
            'Offer Updated',
            'An offer has been updated. New amount: ' . ($data['amount'] ?? $offer['amount']) . ' ' . ($data['currency'] ?? $offer['currency']) . '.',
            '/offers'
        );

        (new ActivityLogService())->log($this->currentUserId(), 'offer_updated', 'offer', $id);

        return $this->jsonSuccess($this->offerModel->find($id));
    }

    /**
     * POST /api/offers/$id/accept
     * Influencer accepts an offer.
     */
    public function accept(int $id)
    {
        $offer = $this->offerModel->find($id);

        if (!$offer) {
            return $this->jsonError('Offer not found.', 404);
        }

        if ((int) $offer['influencer_id'] !== $this->currentUserId()) {
            return $this->jsonError('You are not authorized to accept this offer.', 403);
        }

        $this->offerModel->update($id, [
            'status' => 'accepted',
        ]);

        // Update invitation status
        if ($offer['invitation_id']) {
            $this->invitationModel->update($offer['invitation_id'], [
                'status' => 'accepted',
            ]);
        }

        // Notify brand
        $campaign = $this->campaignModel->find($offer['campaign_id']);
        if ($campaign) {
            (new NotificationService())->createNotification(
                $campaign['created_by'],
                'offer_accepted',
                'Offer Accepted',
                'An influencer has accepted the offer for campaign "' . $campaign['title'] . '".',
                '/campaigns/' . $campaign['id']
            );
        }

        (new ActivityLogService())->log($this->currentUserId(), 'offer_accepted', 'offer', $id);

        return $this->jsonSuccess($this->offerModel->find($id));
    }

    /**
     * POST /api/offers/$id/decline
     * Influencer declines an offer.
     */
    public function decline(int $id)
    {
        $offer = $this->offerModel->find($id);

        if (!$offer) {
            return $this->jsonError('Offer not found.', 404);
        }

        if ((int) $offer['influencer_id'] !== $this->currentUserId()) {
            return $this->jsonError('You are not authorized to decline this offer.', 403);
        }

        $this->offerModel->update($id, [
            'status' => 'declined',
        ]);

        // Notify brand
        $campaign = $this->campaignModel->find($offer['campaign_id']);
        if ($campaign) {
            (new NotificationService())->createNotification(
                $campaign['created_by'],
                'offer_declined',
                'Offer Declined',
                'An influencer has declined the offer for campaign "' . $campaign['title'] . '".',
                '/campaigns/' . $campaign['id']
            );
        }

        (new ActivityLogService())->log($this->currentUserId(), 'offer_declined', 'offer', $id);

        return $this->jsonSuccess($this->offerModel->find($id));
    }

    /**
     * POST /api/offers/$id/accept-terms
     * Influencer accepts terms (AGB).
     */
    public function acceptTerms(int $id)
    {
        $offer = $this->offerModel->find($id);

        if (!$offer) {
            return $this->jsonError('Offer not found.', 404);
        }

        if ((int) $offer['influencer_id'] !== $this->currentUserId()) {
            return $this->jsonError('You are not authorized to accept terms for this offer.', 403);
        }

        if ($offer['status'] !== 'accepted') {
            return $this->jsonError('Offer must be accepted before accepting terms.');
        }

        $this->offerModel->update($id, [
            'terms_accepted'    => 1,
            'terms_accepted_at' => date('Y-m-d H:i:s'),
        ]);

        (new ActivityLogService())->log($this->currentUserId(), 'terms_accepted', 'offer', $id);

        return $this->jsonSuccess($this->offerModel->find($id));
    }

    /**
     * GET /api/invitations/$invitationId/offer
     * Get offer for an invitation.
     */
    public function byInvitation(int $invitationId)
    {
        $invitation = $this->invitationModel->find($invitationId);

        if (!$invitation) {
            return $this->jsonError('Invitation not found.', 404);
        }

        $offer = $this->offerModel->where('invitation_id', $invitationId)->first();

        if (!$offer) {
            return $this->jsonError('No offer found for this invitation.', 404);
        }

        return $this->jsonSuccess($offer);
    }
}
