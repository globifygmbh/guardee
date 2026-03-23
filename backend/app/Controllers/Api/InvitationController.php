<?php

namespace App\Controllers\Api;

use App\Models\InvitationModel;
use App\Models\CampaignModel;
use App\Models\UserModel;
use App\Services\NotificationService;
use App\Services\ActivityLogService;

class InvitationController extends BaseApiController
{
    protected InvitationModel $invitationModel;
    protected CampaignModel $campaignModel;

    public function __construct()
    {
        $this->invitationModel = new InvitationModel();
        $this->campaignModel = new CampaignModel();
    }

    /**
     * GET /api/campaigns/$campaignId/invitations
     */
    public function byCampaign(int $campaignId)
    {
        $campaign = $this->campaignModel->find($campaignId);

        if (!$campaign) {
            return $this->jsonError('Campaign not found.', 404);
        }

        $invitations = $this->invitationModel->where('campaign_id', $campaignId)->findAll();

        return $this->jsonSuccess($invitations);
    }

    /**
     * POST /api/invitations
     * Admin invites an influencer to a campaign.
     */
    public function invite()
    {
        $rules = [
            'campaign_id'   => 'required|integer',
            'influencer_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->jsonError(implode(', ', $this->validator->getErrors()));
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        $campaign = $this->campaignModel->find($input['campaign_id']);
        if (!$campaign) {
            return $this->jsonError('Campaign not found.', 404);
        }

        // Check if influencer is already invited
        $existing = $this->invitationModel
            ->where('campaign_id', $input['campaign_id'])
            ->where('influencer_id', $input['influencer_id'])
            ->first();

        if ($existing) {
            return $this->jsonError('Influencer is already invited to this campaign.');
        }

        $invitationId = $this->invitationModel->insert([
            'campaign_id'   => $input['campaign_id'],
            'influencer_id' => $input['influencer_id'],
            'invited_by'    => $this->currentUserId(),
            'status'        => 'pending',
            'notes'         => $input['notes'] ?? null,
        ]);

        if (!$invitationId) {
            return $this->jsonError('Failed to create invitation.');
        }

        // Notify the influencer
        (new NotificationService())->createNotification(
            $input['influencer_id'],
            'invitation_received',
            'New Campaign Invitation',
            'You have been invited to the campaign "' . $campaign['title'] . '".',
            '/invitations'
        );

        (new ActivityLogService())->log(
            $this->currentUserId(),
            'invitation_sent',
            'invitation',
            $invitationId,
            ['campaign_id' => $input['campaign_id'], 'influencer_id' => $input['influencer_id']]
        );

        return $this->jsonSuccess($this->invitationModel->find($invitationId), 201);
    }

    /**
     * POST /api/invitations/$id/respond
     * Influencer responds to an invitation (interested/declined).
     */
    public function respond(int $id)
    {
        $invitation = $this->invitationModel->find($id);

        if (!$invitation) {
            return $this->jsonError('Invitation not found.', 404);
        }

        if ((int) $invitation['influencer_id'] !== $this->currentUserId()) {
            return $this->jsonError('You are not authorized to respond to this invitation.', 403);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $status = $input['status'] ?? null;

        if (!in_array($status, ['interested', 'declined'], true)) {
            return $this->jsonError('Status must be "interested" or "declined".');
        }

        $this->invitationModel->update($id, [
            'status'       => $status,
            'responded_at' => date('Y-m-d H:i:s'),
        ]);

        // Notify the inviter
        $campaign = $this->campaignModel->find($invitation['campaign_id']);
        if ($invitation['invited_by']) {
            (new NotificationService())->createNotification(
                $invitation['invited_by'],
                'invitation_response',
                'Invitation Response',
                'An influencer has ' . $status . ' the invitation for "' . ($campaign['title'] ?? 'a campaign') . '".',
                '/campaigns/' . $invitation['campaign_id'] . '/invitations'
            );
        }

        (new ActivityLogService())->log($this->currentUserId(), 'invitation_' . $status, 'invitation', $id);

        return $this->jsonSuccess($this->invitationModel->find($id));
    }

    /**
     * GET /api/invitations/mine
     * Influencer sees their invitations.
     */
    public function myInvitations()
    {
        $invitations = $this->invitationModel
            ->where('influencer_id', $this->currentUserId())
            ->findAll();

        return $this->jsonSuccess($invitations);
    }
}
