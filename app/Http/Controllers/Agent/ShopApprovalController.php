<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\BeautyListing;
use App\Models\User;
use App\Notifications\Shop\NotificationSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ShopApprovalController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->user()->id;
        
        $myListingIds = BeautyListing::where('user_id', $userId)->pluck('id')->toArray();
        
        if (empty($myListingIds)) {
            $page_data['orders'] = collect([]);
            return view('agent.shop.approval.index', $page_data);
        }
        
        // Inventory orders only: listing_id is set (orders from Agent's listings)
        $query = ShopOrder::with(['user', 'items', 'listing'])
            ->whereIn('listing_id', $myListingIds)
            ->whereNotNull('listing_id')
            ->pendingApproval();
        
        if ($request->has('status') && $request->status) {
            if ($request->status == 'pending') {
                $query->where('approval_status', 'pending');
            } else {
                $query->where('approval_status', $request->status);
            }
        }
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
            });
        }
        
        $page_data['orders'] = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('agent.shop.approval.index', $page_data);
    }

    public function show($id)
    {
        $userId = auth()->user()->id;
        $myListingIds = BeautyListing::where('user_id', $userId)->pluck('id')->toArray();
        
        $page_data['order'] = ShopOrder::with(['user', 'items', 'listing'])
            ->whereIn('listing_id', $myListingIds)
            ->findOrFail($id);
            
        return view('agent.shop.approval.show', $page_data);
    }

    public function approve(Request $request, $id)
    {
        $userId = auth()->user()->id;
        $myListingIds = BeautyListing::where('user_id', $userId)->pluck('id')->toArray();
        
        $order = ShopOrder::whereIn('listing_id', $myListingIds)->findOrFail($id);
        
        $order->update([
            'approval_status' => 'approved',
            'delivery_status' => 'pending',
        ]);
        
        NotificationSender::orderApproved($order, auth()->user()->name);
        
        Session::flash('success', 'Order approved successfully! It will now be managed by the admin for delivery.');
        return redirect()->route('agent.shop.approval');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $userId = auth()->user()->id;
        $myListingIds = BeautyListing::where('user_id', $userId)->pluck('id')->toArray();
        
        $order = ShopOrder::whereIn('listing_id', $myListingIds)->findOrFail($id);
        
        $order->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'order_status' => 'cancelled',
        ]);
        
        NotificationSender::orderRejected($order, $request->rejection_reason, auth()->user()->name);
        
        Session::flash('success', 'Order rejected!');
        return redirect()->route('agent.shop.approval');
    }
}
