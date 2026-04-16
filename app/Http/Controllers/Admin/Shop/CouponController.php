<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class CouponController extends Controller
{
    public function index()
    {
        $page_data['coupons'] = ShopCoupon::orderBy('created_at', 'desc')->get();
        return view('admin.shop.coupons.index', $page_data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:shop_coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ]);

        ShopCoupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount' => $request->max_discount,
            'usage_limit' => $request->usage_limit,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'status' => $request->status ?? 1,
        ]);

        Session::flash('success', get_phrase('Coupon created successfully!'));
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $coupon = ShopCoupon::findOrFail($id);
        
        $request->validate([
            'code' => 'required|string|max:50|unique:shop_coupons,code,'.$id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ]);

        $coupon->update([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount' => $request->max_discount,
            'usage_limit' => $request->usage_limit,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'status' => $request->status ?? 1,
        ]);

        Session::flash('success', get_phrase('Coupon updated successfully!'));
        return redirect()->back();
    }

    public function destroy($id)
    {
        $coupon = ShopCoupon::findOrFail($id);
        $coupon->delete();

        Session::flash('success', get_phrase('Coupon deleted successfully!'));
        return redirect()->back();
    }

    public function generateCode()
    {
        return response()->json(['code' => strtoupper(Str::random(8))]);
    }
}
