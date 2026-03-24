<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    public function index()
    {
        $notifications = (new NotificationModel())
            ->where('user_id', session()->get('user_id'))
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('notifications/index', ['notifications' => $notifications]);
    }

    public function markRead($id)
    {
        (new NotificationModel())->update($id, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Gelesen.');
    }
}
