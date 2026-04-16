<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = ShopOrder::with(['user', 'items', 'listing'])
            ->forDelivery()
            ->approved();
        
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
        
        return view('admin.shop.delivery.index', $page_data);
    }

    public function show($id)
    {
        $page_data['order'] = ShopOrder::with(['user', 'items', 'listing'])->findOrFail($id);
        return view('admin.shop.delivery.show', $page_data);
    }

    public function updateDeliveryStatus(Request $request, $id)
    {
        $order = ShopOrder::findOrFail($id);
        
        $request->validate([
            'delivery_status' => 'required|in:pending,picked_up,in_transit,delivered,failed',
        ]);
        
        $order->update([
            'delivery_status' => $request->delivery_status,
        ]);
        
        if ($request->delivery_status == 'delivered') {
            $order->update(['order_status' => 'delivered']);
        }
        
        Session::flash('success', 'Delivery status updated successfully!');
        return redirect()->back();
    }

    public function settings()
    {
        $page_data['delivery_price'] = get_settings('delivery_price') ?? 5;
        return view('admin.shop.delivery.settings', $page_data);
    }

    public function updateSettings(Request $request)
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
}
