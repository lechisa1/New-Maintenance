<?php

namespace App\Http\Controllers\notification;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    //
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        return redirect()->back();
    }
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }
    public function notificationIndex()
    {
        $notifications = auth()->user()->notifications()->paginate(10);
        return view('notifications.index', compact('notifications'));
    }
    public function redirectNotification($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        // Mark as read
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        // Redirect to URL from notification data
        return redirect($notification->data['url'] ?? '/');
    }
}
