<?php

namespace App\Http\Controllers\Frontend\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $notifications = ShopNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('frontend.shop.notifications', compact('notifications'));
    }
    
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = ShopNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        
        if ($notification) {
            $notification->markAsRead();
        }
        
        return redirect()->back();
    }
    
    public function markAllAsRead()
    {
        $user = Auth::user();
        ShopNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return redirect()->back()->with('success', 'All notifications marked as read');
    }
}
