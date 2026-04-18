<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('frontend.notifications', ['notifications' => $notifications]);
    }

    public function markAsRead($id)
    {
        $notification = AppNotification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();
            
        if ($notification) {
            $notification->update(['read_at' => now()]);
        }
        
        return back();
    }

    public function markAllAsRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return back();
    }

    public function unreadCount()
    {
        $count = AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();
            
        return response()->json(['count' => $count]);
    }

    // Helper to create notifications (call this when needed)
    public static function create($userId, $type, $title, $message, $link = null)
    {
        AppNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }
}