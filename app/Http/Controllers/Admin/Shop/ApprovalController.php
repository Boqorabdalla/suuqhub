<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = ShopOrder::with(['user', 'items'])
            ->whereNull('listing_id')
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
        
        return view('admin.shop.approval.index', $page_data);
    }

    public function show($id)
    {
        $page_data['order'] = ShopOrder::with(['user', 'items'])->findOrFail($id);
        return view('admin.shop.approval.show', $page_data);
    }

    public function approve(Request $request, $id)
    {
        $order = ShopOrder::findOrFail($id);
        
        if ($order->listing_id) {
            Session::flash('error', 'This order should be approved by the agent!');
            return redirect()->back();
        }
        
        $order->update([
            'approval_status' => 'approved',
            'delivery_status' => 'pending',
        ]);
        
        Session::flash('success', 'Order approved successfully!');
        return redirect()->route('admin.shop.approval');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $order = ShopOrder::findOrFail($id);
        
        if ($order->listing_id) {
            Session::flash('error', 'This order should be rejected by the agent!');
            return redirect()->back();
        }
        
        $order->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'order_status' => 'cancelled',
        ]);
        
        Session::flash('success', 'Order rejected!');
        return redirect()->route('admin.shop.approval');
    }
}
