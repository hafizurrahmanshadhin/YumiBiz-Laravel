<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller {
    public function getNotifications(Request $request) {
        $user          = auth()->user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status'        => true,
            'notifications' => $notifications,
            'code'          => 200,
        ]);
    }
}
