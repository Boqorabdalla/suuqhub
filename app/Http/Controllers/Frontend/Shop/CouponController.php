<?php

namespace App\Http\Controllers\Frontend\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopCoupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = ShopCoupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Invalid coupon code.')
            ]);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('This coupon has expired or is no longer valid.')
            ]);
        }

        $discount = $coupon->calculateDiscount($request->subtotal);

        if ($discount <= 0) {
            return response()->json([
                'success' => false,
                'message' => get_phrase('Minimum order amount of :amount required', ['amount' => currency($coupon->min_order_amount)])
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => get_phrase('Coupon applied successfully!'),
            'discount' => $discount,
            'coupon_code' => $coupon->code,
            'coupon_type' => $coupon->type,
            'coupon_value' => $coupon->value,
        ]);
    }
}
