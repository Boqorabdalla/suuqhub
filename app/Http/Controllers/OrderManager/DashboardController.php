<?php

namespace App\Http\Controllers\OrderManager;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\User;
use App\Notifications\Shop\NotificationSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BeautyListing;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function __construct()
    {
        if (auth()->user()->role != 3) {
            abort(403, 'Access denied. Order Manager only.');
        }
    }

    public function dashboard()
    {
        // Products orders: Order Manager approves directly
        $page_data['pendingOrders'] = ShopOrder::where('approval_status', 'pending')
            ->where('shipping_method', 'delivery')
            ->whereNull('listing_id')
            ->count();
            
        // Inventory orders approved by Agent that need delivery management
        $page_data['approvedOrders'] = ShopOrder::where('approval_status', 'approved')
            ->whereNotNull('listing_id')
            ->where('shipping_method', 'delivery')
            ->count();
            
        $page_data['totalOrders'] = ShopOrder::count();
        
        // Products pending delivery (approved by Order Manager)
        $page_data['deliveryPending'] = ShopOrder::where('shipping_method', 'delivery')
            ->where('approval_status', 'approved')
            ->whereNull('listing_id')
            ->where('delivery_status', 'pending')
            ->count();
            
        // Inventory orders pending delivery (approved by Agent)
        $page_data['deliveryPending'] += ShopOrder::where('shipping_method', 'delivery')
            ->where('approval_status', 'approved')
            ->whereNotNull('listing_id')
            ->where('delivery_status', 'pending')
            ->count();
            
        $page_data['deliveryInTransit'] = ShopOrder::where('delivery_status', 'in_transit')
            ->count();
            
        $page_data['deliveredOrders'] = ShopOrder::where('delivery_status', 'delivered')
            ->count();
            
        return view('order_manager.dashboard', $page_data);
    }

    public function approvalOrders(Request $request)
    {
        // Products only: listing_id is null, Order Manager approves directly
        $query = ShopOrder::with(['user', 'items'])
            ->where('approval_status', 'pending')
            ->where('shipping_method', 'delivery')
            ->whereNull('listing_id');
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
            });
        }
        
        $page_data['orders'] = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('order_manager.approval.index', $page_data);
    }

    public function approvalShow($id)
    {
        $page_data['order'] = ShopOrder::with(['user', 'items'])->findOrFail($id);
        return view('order_manager.approval.show', $page_data);
    }

    public function approveOrder($id)
    {
        $order = ShopOrder::findOrFail($id);
        
        $order->update([
            'approval_status' => 'approved',
        ]);
        
        NotificationSender::orderApproved($order, 'Order Manager');
        
        Session::flash('success', 'Order approved successfully!');
        return redirect()->route('order.manager.approval');
    }

    public function rejectOrder(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $order = ShopOrder::findOrFail($id);
        
        $order->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        
        NotificationSender::orderRejected($order, $request->rejection_reason, 'Order Manager');
        
        Session::flash('success', 'Order rejected successfully!');
        return redirect()->route('order.manager.approval');
    }

    public function deliveryOrders(Request $request)
    {
        // Products: Order Manager approved them, now manage delivery
        // Inventory: Agent approved them, Order Manager manages delivery (only if home delivery)
        $query = ShopOrder::with(['user', 'items', 'listing'])
            ->where('shipping_method', 'delivery')
            ->where('approval_status', 'approved');
        
        if ($request->has('status') && $request->status) {
            $query->where('delivery_status', $request->status);
        }
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
            });
        }
        
        $page_data['orders'] = $query->orderBy('created_at', 'desc')->paginate(20);
        $page_data['delivery_price'] = get_settings('delivery_price') ?? 5;
        
        return view('order_manager.delivery.index', $page_data);
    }

    public function deliveryShow($id)
    {
        $page_data['order'] = ShopOrder::with(['user', 'items'])->findOrFail($id);
        return view('order_manager.delivery.show', $page_data);
    }

    public function updateDeliveryStatus(Request $request, $id)
    {
        $request->validate([
            'delivery_status' => 'required|in:pending,picked_up,in_transit,delivered,failed',
        ]);
        
        $order = ShopOrder::findOrFail($id);
        $oldStatus = $order->delivery_status;
        
        $order->update([
            'delivery_status' => $request->delivery_status,
        ]);
        
        if ($request->delivery_status == 'delivered') {
            $order->update(['order_status' => 'delivered']);
            NotificationSender::orderDelivered($order);
        } elseif (in_array($request->delivery_status, ['picked_up', 'in_transit'])) {
            NotificationSender::orderShipped($order, $request->delivery_status);
        }
        
        Session::flash('success', 'Delivery status updated successfully!');
        return redirect()->back();
    }

    public function deliverySettings()
    {
        $page_data['delivery_price'] = get_settings('delivery_price') ?? 5;
        return view('order_manager.delivery.settings', $page_data);
    }

    public function updateDeliverySettings(Request $request)
    {
        $request->validate([
            'delivery_price' => 'required|numeric|min:0',
        ]);
        
        \DB::table('system_settings')->updateOrInsert(
            ['key' => 'delivery_price'],
            ['key' => 'delivery_price', 'value' => $request->delivery_price]
        );
        
        Session::flash('success', 'Delivery settings updated successfully!');
        return redirect()->back();
    }

    public function allOrders(Request $request)
    {
        $query = ShopOrder::with(['user', 'items']);
        
        if ($request->has('status') && $request->status) {
            $query->where('order_status', $request->status);
        }
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%');
            });
        }
        
        $page_data['orders'] = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('order_manager.orders.index', $page_data);
    }

    public function orderShow($id)
    {
        $page_data['order'] = ShopOrder::with(['user', 'items'])->findOrFail($id);
        return view('order_manager.orders.show', $page_data);
    }
    
    public function analytics(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');
        
        // Sales Overview
        $page_data['totalSales'] = ShopOrder::whereBetween('created_at', [$dateFrom, $dateTo])->sum('total');
        $page_data['totalOrders'] = ShopOrder::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $page_data['avgOrderValue'] = $page_data['totalOrders'] > 0 ? $page_data['totalSales'] / $page_data['totalOrders'] : 0;
        
        // Order Status Breakdown
        $page_data['pendingOrders'] = ShopOrder::whereBetween('created_at', [$dateFrom, $dateTo])->where('approval_status', 'pending')->count();
        $page_data['approvedOrders'] = ShopOrder::whereBetween('created_at', [$dateFrom, $dateTo])->where('approval_status', 'approved')->count();
        $page_data['deliveredOrders'] = ShopOrder::whereBetween('created_at', [$dateFrom, $dateTo])->where('delivery_status', 'delivered')->count();
        $page_data['cancelledOrders'] = ShopOrder::whereBetween('created_at', [$dateFrom, $dateTo])->whereIn('order_status', ['cancelled', 'rejected'])->count();
        
        // Top Selling Products
        $page_data['topProducts'] = ShopOrderItem::select('product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('order', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->groupBy('product_name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();
        
        // Sales by Day (for chart)
        $page_data['dailySales'] = ShopOrder::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as orders'))
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Shipping Method Breakdown
        $page_data['deliveryOrders'] = ShopOrder::whereBetween('created_at', [$dateFrom, $dateTo])->where('shipping_method', 'delivery')->count();
        $page_data['pickupOrders'] = ShopOrder::whereBetween('created_at', [$dateFrom, $dateTo])->where('shipping_method', 'pickup')->count();
        
        // Payment Method Breakdown
        $page_data['codOrders'] = ShopOrder::whereBetween('created_at', [$dateFrom, $dateTo])->where('payment_method', 'cod')->count();
        $page_data['onlineOrders'] = ShopOrder::whereBetween('created_at', [$dateFrom, $dateTo])->where('payment_method', '!=', 'cod')->count();
        
        // Recent Trend (compared to previous period)
        $daysDiff = (strtotime($dateTo) - strtotime($dateFrom)) / (60 * 60 * 24) + 1;
        $prevDateFrom = date('Y-m-d', strtotime($dateFrom . ' -' . $daysDiff . ' days'));
        $prevDateTo = date('Y-m-d', strtotime($dateFrom . ' -1 day'));
        
        $page_data['prevTotalSales'] = ShopOrder::whereBetween('created_at', [$prevDateFrom, $prevDateTo])->sum('total');
        $page_data['prevTotalOrders'] = ShopOrder::whereBetween('created_at', [$prevDateFrom, $prevDateTo])->count();
        $page_data['salesGrowth'] = $page_data['prevTotalSales'] > 0 ? (($page_data['totalSales'] - $page_data['prevTotalSales']) / $page_data['prevTotalSales']) * 100 : 0;
        $page_data['ordersGrowth'] = $page_data['prevTotalOrders'] > 0 ? (($page_data['totalOrders'] - $page_data['prevTotalOrders']) / $page_data['prevTotalOrders']) * 100 : 0;
        
        $page_data['dateFrom'] = $dateFrom;
        $page_data['dateTo'] = $dateTo;
        
        return view('order_manager.analytics', $page_data);
    }
}
