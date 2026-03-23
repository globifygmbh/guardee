<?php

namespace App\Controllers\Api;

use App\Models\AssetModel;
use App\Models\CampaignModel;
use App\Models\OfferModel;
use App\Services\NotificationService;
use App\Services\ActivityLogService;

class AssetController extends BaseApiController
{
    protected AssetModel $assetModel;
    protected CampaignModel $campaignModel;

    public function __construct()
    {
        $this->assetModel = new AssetModel();
        $this->campaignModel = new CampaignModel();
    }

    /**
     * POST /api/assets/upload
     * Influencer uploads content.
     */
    public function upload()
    {
        $rules = [
            'campaign_id' => 'required|integer',
            'offer_id'    => 'required|integer',
            'asset_type'  => 'required|in_list[image,video,document,other]',
        ];

        if (!$this->validate($rules)) {
            return $this->jsonError(implode(', ', $this->validator->getErrors()));
        }

        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->jsonError('No valid file uploaded.');
        }

        if ($file->hasMoved()) {
            return $this->jsonError('File has already been moved.');
        }

        $input = $this->request->getPost();
        $userId = $this->currentUserId();

        // Verify campaign exists
        $campaign = $this->campaignModel->find($input['campaign_id']);
        if (!$campaign) {
            return $this->jsonError('Campaign not found.', 404);
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/assets', $newName);

        $assetId = $this->assetModel->insert([
            'campaign_id'  => $input['campaign_id'],
            'offer_id'     => $input['offer_id'],
            'uploaded_by'  => $userId,
            'file_name'    => $file->getClientName(),
            'file_path'    => 'assets/' . $newName,
            'file_type'    => $file->getClientMimeType(),
            'file_size'    => $file->getSize(),
            'asset_type'   => $input['asset_type'],
            'status'       => 'pending_review',
        ]);

        if (!$assetId) {
            return $this->jsonError('Failed to upload asset.');
        }

        // Notify campaign creator (brand) about new upload
        if ($campaign['created_by']) {
            (new NotificationService())->createNotification(
                $campaign['created_by'],
                'asset_uploaded',
                'New Content Uploaded',
                'New content has been uploaded for campaign "' . $campaign['title'] . '".',
                '/campaigns/' . $campaign['id'] . '/assets'
            );
        }

        (new ActivityLogService())->log($userId, 'asset_uploaded', 'asset', $assetId);

        return $this->jsonSuccess($this->assetModel->find($assetId), 201);
    }

    /**
     * GET /api/campaigns/$campaignId/assets
     * List assets for a campaign.
     */
    public function byCampaign(int $campaignId)
    {
        $campaign = $this->campaignModel->find($campaignId);

        if (!$campaign) {
            return $this->jsonError('Campaign not found.', 404);
        }

        $assets = $this->assetModel->where('campaign_id', $campaignId)->findAll();

        return $this->jsonSuccess($assets);
    }

    /**
     * POST /api/assets/$id/approve
     * Brand approves content.
     */
    public function approve(int $id)
    {
        $asset = $this->assetModel->find($id);

        if (!$asset) {
            return $this->jsonError('Asset not found.', 404);
        }

        $this->assetModel->update($id, [
            'status'      => 'approved',
            'reviewed_by' => $this->currentUserId(),
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);

        // Notify the uploader
        (new NotificationService())->createNotification(
            $asset['uploaded_by'],
            'asset_approved',
            'Content Approved',
            'Your uploaded content "' . $asset['file_name'] . '" has been approved.',
            '/campaigns/' . $asset['campaign_id'] . '/assets'
        );

        (new ActivityLogService())->log($this->currentUserId(), 'asset_approved', 'asset', $id);

        return $this->jsonSuccess($this->assetModel->find($id));
    }

    /**
     * POST /api/assets/$id/request-revision
     * Brand requests revision with notes.
     */
    public function requestRevision(int $id)
    {
        $asset = $this->assetModel->find($id);

        if (!$asset) {
            return $this->jsonError('Asset not found.', 404);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        $notes = $input['review_notes'] ?? '';

        if (empty($notes)) {
            return $this->jsonError('Review notes are required when requesting a revision.');
        }

        $this->assetModel->update($id, [
            'status'       => 'revision_requested',
            'reviewed_by'  => $this->currentUserId(),
            'reviewed_at'  => date('Y-m-d H:i:s'),
            'review_notes' => $notes,
        ]);

        // Notify the uploader
        (new NotificationService())->createNotification(
            $asset['uploaded_by'],
            'asset_revision_requested',
            'Revision Requested',
            'A revision has been requested for your content "' . $asset['file_name'] . '". Notes: ' . $notes,
            '/campaigns/' . $asset['campaign_id'] . '/assets'
        );

        (new ActivityLogService())->log($this->currentUserId(), 'asset_revision_requested', 'asset', $id);

        return $this->jsonSuccess($this->assetModel->find($id));
    }

    /**
     * GET /api/assets/$id/download
     * Download asset file.
     */
    public function download(int $id)
    {
        $asset = $this->assetModel->find($id);

        if (!$asset) {
            return $this->jsonError('Asset not found.', 404);
        }

        $filePath = WRITEPATH . 'uploads/' . $asset['file_path'];

        if (!file_exists($filePath)) {
            return $this->jsonError('File not found.', 404);
        }

        return $this->response->download($filePath, null)->setFileName($asset['file_name']);
    }
}
