<?php

namespace App\Controllers\Api;

use App\Models\CampaignModel;
use App\Models\InvitationModel;
use App\Models\BrandProfileModel;
use App\Services\NotificationService;
use App\Services\ActivityLogService;

class CampaignController extends BaseApiController
{
    protected CampaignModel $campaignModel;
    protected InvitationModel $invitationModel;

    public function __construct()
    {
        $this->campaignModel = new CampaignModel();
        $this->invitationModel = new InvitationModel();
    }

    /**
     * GET /api/campaigns
     * List campaigns filtered by role.
     */
    public function index()
    {
        $role = $this->currentUserRole();
        $userId = $this->currentUserId();

        if ($role === 'admin') {
            $campaigns = $this->campaignModel->findAll();
        } elseif ($role === 'brand') {
            // Brand sees campaigns linked to their brand profile
            $brandProfile = (new BrandProfileModel())->where('user_id', $userId)->first();
            if ($brandProfile) {
                $campaigns = $this->campaignModel->where('brand_id', $brandProfile['id'])->findAll();
            } else {
                $campaigns = $this->campaignModel->where('created_by', $userId)->findAll();
            }
        } elseif ($role === 'influencer') {
            // Influencer sees campaigns they've been invited to
            $invitations = $this->invitationModel->where('influencer_id', $userId)->findAll();
            $campaignIds = array_column($invitations, 'campaign_id');
            if (!empty($campaignIds)) {
                $campaigns = $this->campaignModel->whereIn('id', $campaignIds)->findAll();
            } else {
                $campaigns = [];
            }
        } else {
            $campaigns = [];
        }

        return $this->jsonSuccess($campaigns);
    }

    /**
     * GET /api/campaigns/$id
     */
    public function show(int $id)
    {
        $campaign = $this->campaignModel->find($id);

        if (!$campaign) {
            return $this->jsonError('Campaign not found.', 404);
        }

        $invitationsCount = $this->invitationModel->where('campaign_id', $id)->countAllResults();

        $campaign['invitations_count'] = $invitationsCount;

        return $this->jsonSuccess($campaign);
    }

    /**
     * POST /api/campaigns
     * Brand creates a campaign.
     */
    public function create()
    {
        $rules = [
            'title'       => 'required|min_length[3]',
            'description' => 'required',
            'budget'      => 'required|numeric',
            'start_date'  => 'required|valid_date',
            'end_date'    => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return $this->jsonError(implode(', ', $this->validator->getErrors()));
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $userId = $this->currentUserId();

        // Get brand profile
        $brandProfile = (new BrandProfileModel())->where('user_id', $userId)->first();

        $data = [
            'brand_id'        => $brandProfile ? $brandProfile['id'] : null,
            'created_by'      => $userId,
            'title'           => $input['title'],
            'description'     => $input['description'],
            'budget'          => $input['budget'],
            'target_audience' => $input['target_audience'] ?? null,
            'countries'       => $input['countries'] ?? null,
            'start_date'      => $input['start_date'],
            'end_date'        => $input['end_date'],
            'status'          => 'draft',
        ];

        // Handle briefing file upload
        $file = $this->request->getFile('briefing_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/briefings', $newName);
            $data['briefing_file'] = 'briefings/' . $newName;
        }

        $campaignId = $this->campaignModel->insert($data);

        if (!$campaignId) {
            return $this->jsonError('Failed to create campaign.');
        }

        (new ActivityLogService())->log($userId, 'campaign_created', 'campaign', $campaignId);

        $campaign = $this->campaignModel->find($campaignId);

        return $this->jsonSuccess($campaign, 201);
    }

    /**
     * PUT /api/campaigns/$id
     */
    public function update(int $id)
    {
        $campaign = $this->campaignModel->find($id);

        if (!$campaign) {
            return $this->jsonError('Campaign not found.', 404);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        $allowedUpdates = ['title', 'description', 'budget', 'target_audience', 'countries', 'start_date', 'end_date', 'status'];
        $data = array_intersect_key($input, array_flip($allowedUpdates));

        if (empty($data)) {
            return $this->jsonError('No valid fields to update.');
        }

        $this->campaignModel->update($id, $data);

        (new ActivityLogService())->log($this->currentUserId(), 'campaign_updated', 'campaign', $id);

        return $this->jsonSuccess($this->campaignModel->find($id));
    }

    /**
     * POST /api/campaigns/$id/approve
     * Admin approves a campaign.
     */
    public function approve(int $id)
    {
        $campaign = $this->campaignModel->find($id);

        if (!$campaign) {
            return $this->jsonError('Campaign not found.', 404);
        }

        $this->campaignModel->update($id, [
            'status'      => 'approved',
            'approved_by' => $this->currentUserId(),
            'approved_at' => date('Y-m-d H:i:s'),
        ]);

        // Notify the campaign creator
        (new NotificationService())->createNotification(
            $campaign['created_by'],
            'campaign_approved',
            'Campaign Approved',
            'Your campaign "' . $campaign['title'] . '" has been approved.',
            '/campaigns/' . $id
        );

        (new ActivityLogService())->log($this->currentUserId(), 'campaign_approved', 'campaign', $id);

        return $this->jsonSuccess($this->campaignModel->find($id));
    }

    /**
     * POST /api/campaigns/$id/reject
     * Admin rejects a campaign.
     */
    public function reject(int $id)
    {
        $campaign = $this->campaignModel->find($id);

        if (!$campaign) {
            return $this->jsonError('Campaign not found.', 404);
        }

        $this->campaignModel->update($id, [
            'status' => 'rejected',
        ]);

        // Notify the campaign creator
        (new NotificationService())->createNotification(
            $campaign['created_by'],
            'campaign_rejected',
            'Campaign Rejected',
            'Your campaign "' . $campaign['title'] . '" has been rejected.',
            '/campaigns/' . $id
        );

        (new ActivityLogService())->log($this->currentUserId(), 'campaign_rejected', 'campaign', $id);

        return $this->jsonSuccess($this->campaignModel->find($id));
    }

    /**
     * GET /api/campaigns/pending
     * Admin gets pending campaigns.
     */
    public function pending()
    {
        $campaigns = $this->campaignModel->where('status', 'draft')->findAll();

        return $this->jsonSuccess($campaigns);
    }
}
