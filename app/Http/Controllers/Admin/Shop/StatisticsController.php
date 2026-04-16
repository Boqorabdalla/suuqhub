<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\ProductReview;
use App\Models\ShopCartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index()
    {
        $page_data = $this->getStatistics();
        return view('admin.shop.statistics.index', $page_data);
    }

    private function getStatistics()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        
        $data = [];
        
        // Orders Statistics
        $data['totalOrders'] = ShopOrder::count();
        $data['todayOrders'] = ShopOrder::whereDate('created_at', $today)->count();
        $data['weekOrders'] = ShopOrder::where('created_at', '>=', $thisWeek)->count();
        $data['monthOrders'] = ShopOrder::where('created_at', '>=', $thisMonth)->count();
        
        // Order Status
        $data['pendingOrders'] = ShopOrder::where('order_status', 'pending')->count();
        $data['processingOrders'] = ShopOrder::where('order_status', 'processing')->count();
        $data['shippedOrders'] = ShopOrder::where('order_status', 'shipped')->count();
        $data['deliveredOrders'] = ShopOrder::where('order_status', 'delivered')->count();
        $data['cancelledOrders'] = ShopOrder::where('order_status', 'cancelled')->count();
        
        // Revenue Statistics
        $data['totalRevenue'] = ShopOrder::where('payment_status', 'paid')->sum('total');
        $data['todayRevenue'] = ShopOrder::where('payment_status', 'paid')->whereDate('created_at', $today)->sum('total');
        $data['weekRevenue'] = ShopOrder::where('payment_status', 'paid')->where('created_at', '>=', $thisWeek)->sum('total');
        $data['monthRevenue'] = ShopOrder::where('payment_status', 'paid')->where('created_at', '>=', $thisMonth)->sum('total');
        
        // Pending Approval
        $data['pendingApproval'] = ShopOrder::where('approval_status', 'pending')->count();
        $data['pendingDelivery'] = ShopOrder::where('delivery_status', 'pending')->where('approval_status', 'approved')->count();
        
        // Products Statistics
        $data['totalProducts'] = Product::where('is_published', 1)->count();
        $data['totalInventory'] = Inventory::where('availability', 1)->count();
        $data['outOfStockProducts'] = Product::where('is_published', 1)->where('track_stock', 1)->where('stock_quantity', '<=', 0)->count();
        $data['outOfStockInventory'] = Inventory::where('availability', 1)->where('track_stock', 1)->where('stock_quantity', '<=', 0)->count();
        
        // Reviews
        $data['totalReviews'] = ProductReview::count();
        $data['pendingReviews'] = ProductReview::where('status', 'pending')->count();
        $data['approvedReviews'] = ProductReview::where('status', 'approved')->count();
        
        // Cart
        $data['activeCarts'] = ShopCartItem::distinct('user_id')->count('user_id');
        $data['cartItems'] = ShopCartItem::count();
        
        // Orders by Shipping Method
        $data['pickupOrders'] = ShopOrder::where('shipping_method', 'pickup')->count();
        $data['deliveryOrders'] = ShopOrder::where('shipping_method', 'delivery')->count();
        
        // Recent Orders
        $data['recentOrders'] = ShopOrder::with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Top Selling Products (from order items)
        $data['topProducts'] = \DB::table('shop_order_items')
            ->select('product_name', \DB::raw('SUM(quantity) as total_sold'), \DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
        
        // Monthly Orders (last 6 months)
        $data['monthlyOrders'] = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonth($i);
            $count = ShopOrder::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $revenue = ShopOrder::where('payment_status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total');
            $data['monthlyOrders'][] = [
                'month' => $month->format('M Y'),
                'orders' => $count,
                'revenue' => $revenue
            ];
        }
        
        // Payment Method Distribution
        $data['codOrders'] = ShopOrder::where('payment_method', 'cod')->count();
        $data['onlineOrders'] = ShopOrder::where('payment_method', '!=', 'cod')->count();
        
        return $data;
    }
}
