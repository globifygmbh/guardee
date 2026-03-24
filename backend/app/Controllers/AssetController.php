<?php

namespace App\Controllers;

use App\Models\AssetModel;

class AssetController extends BaseController
{
    public function upload()
    {
        $campaignId = $this->request->getPost('campaign_id');
        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Datei-Upload fehlgeschlagen.');
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/content', $newName);

        (new AssetModel())->insert([
            'campaign_id' => $campaignId,
            'offer_id'    => $this->request->getPost('offer_id'),
            'uploaded_by'  => session()->get('user_id'),
            'file_name'    => $file->getClientName(),
            'file_path'    => 'uploads/content/' . $newName,
            'file_type'    => $file->getClientMimeType(),
            'file_size'    => $file->getSize(),
            'asset_type'   => $this->request->getPost('asset_type') ?: 'content',
            'status'       => 'pending_review',
        ]);

        return redirect()->to("/campaigns/{$campaignId}")->with('success', 'Datei hochgeladen.');
    }

    public function approve($id)
    {
        $asset = (new AssetModel())->find($id);
        (new AssetModel())->update($id, [
            'status'      => 'approved',
            'reviewed_by' => session()->get('user_id'),
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to("/campaigns/{$asset['campaign_id']}")->with('success', 'Content freigegeben.');
    }

    public function requestRevision($id)
    {
        $asset = (new AssetModel())->find($id);
        (new AssetModel())->update($id, [
            'status'       => 'revision_requested',
            'reviewed_by'  => session()->get('user_id'),
            'reviewed_at'  => date('Y-m-d H:i:s'),
            'review_notes' => $this->request->getPost('review_notes'),
        ]);

        return redirect()->to("/campaigns/{$asset['campaign_id']}")->with('success', 'Änderung angefragt.');
    }

    public function download($id)
    {
        $asset = (new AssetModel())->find($id);
        if (!$asset) {
            return redirect()->back()->with('error', 'Datei nicht gefunden.');
        }

        $path = WRITEPATH . $asset['file_path'];
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Datei nicht auf dem Server gefunden.');
        }

        return $this->response->download($path, null)->setFileName($asset['file_name']);
    }
}
