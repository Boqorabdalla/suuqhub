<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentEarning;
use App\Models\AgentPayout;
use App\Models\ShopOrder;
use App\Models\BeautyListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AgentEarningsController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->user()->id;
        
        // Get statistics
        $stats = [
            'total_earnings' => AgentEarning::forAgent($userId)->sum('commission_amount'),
            'pending_earnings' => AgentEarning::forAgent($userId)->where('status', 'approved')->sum('commission_amount'),
            'paid_earnings' => AgentEarning::forAgent($userId)->where('status', 'paid')->sum('commission_amount'),
            'total_sales' => AgentEarning::forAgent($userId)->where('type', 'sale')->count(),
            'pending_payouts' => AgentPayout::forAgent($userId)->where('status', 'pending')->sum('amount'),
        ];
        
        // Get recent earnings
        $query = AgentEarning::forAgent($userId)->with('order');
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $earnings = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $page_data['stats'] = $stats;
        $page_data['earnings'] = $earnings;
        $page_data['filters'] = [
            'type' => $request->type ?? '',
            'status' => $request->status ?? '',
            'date_from' => $request->date_from ?? '',
            'date_to' => $request->date_to ?? '',
        ];
        
        return view('agent.earnings.index', $page_data);
    }
    
    public function requestPayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
        ]);
        
        $userId = auth()->user()->id;
        $availableBalance = AgentEarning::forAgent($userId)->where('status', 'approved')->sum('commission_amount');
        $pendingPayouts = AgentPayout::forAgent($userId)->whereIn('status', ['pending', 'processing'])->sum('amount');
        $availableForPayout = $availableBalance - $pendingPayouts;
        
        if ($request->amount > $availableForPayout) {
            return redirect()->back()->with('error', 'Amount exceeds available balance');
        }
        
        AgentPayout::create([
            'agent_id' => $userId,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);
        
        Session::flash('success', 'Payout request submitted successfully!');
        return redirect()->back();
    }
    
    public function payoutHistory()
    {
        $userId = auth()->user()->id;
        $payouts = AgentPayout::forAgent($userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('agent.earnings.payouts', compact('payouts'));
    }
    
    public function salesReport(Request $request)
    {
        $userId = auth()->user()->id;
        $myListingIds = BeautyListing::where('user_id', $userId)->pluck('id')->toArray();
        
        $query = ShopOrder::with('items')
            ->whereIn('listing_id', $myListingIds)
            ->where('approval_status', 'approved');
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $stats = [
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('total'),
            'total_items' => $query->withCount('items')->get()->sum('items_count'),
        ];
        
        $page_data['orders'] = $orders;
        $page_data['stats'] = $stats;
        $page_data['filters'] = [
            'date_from' => $request->date_from ?? '',
            'date_to' => $request->date_to ?? '',
        ];
        
        return view('agent.earnings.sales', $page_data);
    }
}
