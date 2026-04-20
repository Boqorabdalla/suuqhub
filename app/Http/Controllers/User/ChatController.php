<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\BeautyListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ChatController extends Controller
{
    public function conversations()
    {
        try {
            $userId = Auth::id();
            
            // Get unique conversations (users we've chatted with)
            $sentMessages = collect([]);
            $receivedMessages = collect([]);
            
            try {
                $sentMessages = ChatMessage::where('sender_id', $userId)->pluck('receiver_id')->toArray();
                $receivedMessages = ChatMessage::where('receiver_id', $userId)->pluck('sender_id')->toArray();
            } catch (\Exception $e) {
                // Table might not exist
            }
            
            $userIds = array_unique(array_merge($sentMessages, $receivedMessages));
            
            $conversations = [];
            foreach ($userIds as $otherUserId) {
                $otherUser = User::find($otherUserId);
                if ($otherUser) {
                    $lastMessage = null;
                    try {
                        $lastMessage = ChatMessage::where(function($q) use ($userId, $otherUserId) {
                            $q->where(function($q2) use ($userId, $otherUserId) {
                                $q2->where('sender_id', $userId)->where('receiver_id', $otherUserId);
                            })->orWhere(function($q2) use ($userId, $otherUserId) {
                                $q2->where('sender_id', $otherUserId)->where('receiver_id', $userId);
                            });
                        })->latest()->first();
                    } catch (\Exception $e) {
                        // Table might not exist
                    }
                    
                    $unreadCount = 0;
                    try {
                        $unreadCount = ChatMessage::where('sender_id', $otherUserId)
                            ->where('receiver_id', $userId)
                            ->whereNull('read_at')
                            ->count();
                    } catch (\Exception $e) {
                        // Table might not exist
                    }
                    
                    $conversations[] = [
                        'user' => $otherUser,
                        'last_message' => $lastMessage,
                        'unread_count' => $unreadCount,
                    ];
                }
            }
            
            // Sort by last message time
            usort($conversations, function($a, $b) {
                if (!$a['last_message'] || !$b['last_message']) return 0;
                return $b['last_message']->created_at <=> $a['last_message']->created_at;
            });
            
            return view('user.chat.conversations', ['conversations' => $conversations]);
        } catch (\Exception $e) {
            return view('user.chat.conversations', ['conversations' => []]);
        }
    }
    
    public function chat($userId)
    {
        try {
            $currentUserId = Auth::id();
            
            // Get or create conversation
            try {
                $messages = ChatMessage::where(function($q) use ($currentUserId, $userId) {
                    $q->where(function($q2) use ($currentUserId, $userId) {
                        $q2->where('sender_id', $currentUserId)->where('receiver_id', $userId);
                    })->orWhere(function($q2) use ($currentUserId, $userId) {
                        $q2->where('sender_id', $userId)->where('receiver_id', $currentUserId);
                    });
                })->orderBy('created_at', 'asc')->paginate(50);
                
                // Mark as read
                ChatMessage::where('sender_id', $userId)
                    ->where('receiver_id', $currentUserId)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            } catch (\Exception $e) {
                $messages = collect([]);
            }
            
            $otherUser = User::find($userId);
            
            return view('user.chat.chat', [
                'messages' => $messages, 
                'otherUser' => $otherUser,
                'otherUserId' => $userId
            ]);
        } catch (\Exception $e) {
            return redirect()->route('user.conversations', ['prefix' => 'customer'])->with('error', 'Chat not found');
        }
    }
    
    public function sendMessage(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        
        try {
            $message = ChatMessage::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $userId,
                'message' => $request->message,
            ]);
            
            // Create notification for receiver
            try {
                app(\App\Http\Controllers\Frontend\NotificationController::class)::create(
                    $userId,
                    'chat',
                    'New Message',
                    Auth::user()->name . ' sent you a message',
                    route('user.chat', ['prefix' => 'customer', 'userId' => Auth::id()])
                );
            } catch (\Exception $e) {
                // Notification might fail
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send message');
        }
        
        return back()->with('success', 'Message sent!');
    }
    
    public function startChatFromListing(Request $request)
    {
        $request->validate([
            'listing_id' => 'required',
            'message' => 'required|string',
        ]);
        
        $listing = BeautyListing::find($request->listing_id);
        if (!$listing) {
            return back()->with('error', 'Listing not found');
        }
        
        $receiverId = $listing->user_id;
        
        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'listing_id' => $request->listing_id,
            'message' => $request->message,
        ]);
        
        // Create notification
        app(\App\Http\Controllers\Frontend\NotificationController::class)::create(
            $receiverId,
            'chat',
            'New Message',
            Auth::user()->name . ' sent you a message about ' . $listing->title,
            route('user.chat', Auth::id())
        );
        
        return back()->with('success', 'Message sent!');
    }
}