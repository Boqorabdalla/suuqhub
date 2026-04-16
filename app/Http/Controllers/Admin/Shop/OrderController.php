<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ShopOrder::with(['user', 'items']);
        
        if ($request->has('status') && $request->status) {
            $query->where('order_status', $request->status);
        }
        
        if (auth()->user()->role != 1) {
            $query->where('seller_id', auth()->user()->id);
        }
        
        $page_data['orders'] = $query->orderBy('created_at', 'desc')->paginate(20);
        $page_data['status'] = $request->status;
        
        return view('admin.shop.orders.index', $page_data);
    }

    public function show($id)
    {
        $order = ShopOrder::with(['user', 'items.product'])->findOrFail($id);
        
        if (auth()->user()->role != 1 && $order->seller_id != auth()->user()->id) {
            Session::flash('error', get_phrase('You do not have permission to view this order.'));
            return redirect()->route('admin.shop.orders');
        }
        
        $page_data['order'] = $order;
        
        return view('admin.shop.orders.show', $page_data);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = ShopOrder::findOrFail($id);
        
        if (auth()->user()->role != 1 && $order->seller_id != auth()->user()->id) {
            Session::flash('error', get_phrase('You do not have permission to update this order.'));
            return redirect()->route('admin.shop.orders');
        }
        
        $order->update([
            'order_status' => $request->order_status,
        ]);
        
        Session::flash('success', get_phrase('Order status updated successfully!'));
        return redirect()->back();
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $order = ShopOrder::findOrFail($id);
        
        if (auth()->user()->role != 1 && $order->seller_id != auth()->user()->id) {
            Session::flash('error', get_phrase('You do not have permission to update this order.'));
            return redirect()->route('admin.shop.orders');
        }
        
        $order->update([
            'payment_status' => $request->payment_status,
        ]);
        
        Session::flash('success', get_phrase('Payment status updated successfully!'));
        return redirect()->back();
    }

    public function destroy($id)
    {
        $order = ShopOrder::findOrFail($id);
        
        if (auth()->user()->role != 1 && $order->seller_id != auth()->user()->id) {
            Session::flash('error', get_phrase('You do not have permission to delete this order.'));
            return redirect()->route('admin.shop.orders');
        }
        
        $order->delete();
        
        Session::flash('success', get_phrase('Order deleted successfully!'));
        return redirect()->route('admin.shop.orders');
    }
}
