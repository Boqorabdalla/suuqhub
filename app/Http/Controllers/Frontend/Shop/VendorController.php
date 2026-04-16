<?php

namespace App\Http\Controllers\Frontend\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 2)
            ->where('status', 1)
            ->whereHas('products', function($q) {
                $q->where('status', 'published');
            });

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }

        $vendors = $query->withCount('products')
            ->withAvg('productReviews', 'rating')
            ->orderByDesc('products_count')
            ->paginate(12);

        return view('frontend.shop.vendors.index', compact('vendors'));
    }

    public function show(Request $request, $id, $slug = null)
    {
        $vendor = User::where('id', $id)
            ->where('role', 2)
            ->where('status', 1)
            ->with(['productReviews' => function($q) {
                $q->with('user')->orderByDesc('created_at')->limit(5);
            }])
            ->withAvg('productReviews', 'rating')
            ->firstOrFail();

        $productsQuery = Product::where('user_id', $id)
            ->where('status', 'published')
            ->with(['variations', 'images']);

        if ($request->category) {
            $productsQuery->where('category_id', $request->category);
        }

        if ($request->min_price) {
            $productsQuery->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $productsQuery->where('price', '<=', $request->max_price);
        }

        $sortBy = $request->sort ?? 'newest';
        switch ($sortBy) {
            case 'price_low':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_high':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'popular':
                $productsQuery->withCount('orderItems')->orderByDesc('order_items_count');
                break;
            default:
                $productsQuery->orderByDesc('created_at');
        }

        $products = $productsQuery->paginate(12);

        $categories = Product::where('user_id', $id)
            ->where('status', 'published')
            ->whereNotNull('category_id')
            ->select('category_id')
            ->with('category')
            ->get()
            ->pluck('category')
            ->filter()
            ->unique('id')
            ->values();

        $stats = [
            'total_products' => Product::where('user_id', $id)->where('status', 'published')->count(),
            'total_sales' => DB::table('shop_order_items')
                ->join('products', 'shop_order_items.item_id', '=', 'products.id')
                ->where('products.user_id', $id)
                ->where('shop_order_items.item_type', 'product')
                ->sum('shop_order_items.quantity'),
            'avg_rating' => round($vendor->product_reviews_avg_rating ?? 0, 1),
            'total_reviews' => $vendor->productReviews->count(),
        ];

        return view('frontend.shop.vendors.storefront', compact(
            'vendor', 'products', 'categories', 'stats'
        ));
    }

    public function products($vendorId)
    {
        $vendor = User::where('id', $vendorId)
            ->where('role', 2)
            ->where('status', 1)
            ->firstOrFail();

        $products = Product::where('user_id', $vendorId)
            ->where('status', 'published')
            ->with(['variations', 'images', 'category'])
            ->orderByDesc('created_at')
            ->paginate(24);

        return view('frontend.shop.vendors.products', compact('vendor', 'products'));
    }

    public function reviews($vendorId)
    {
        $vendor = User::where('id', $vendorId)
            ->where('role', 2)
            ->where('status', 1)
            ->firstOrFail();

        $reviews = Review::where('seller_id', $vendorId)
            ->with(['user', 'product'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $stats = [
            'avg_rating' => round($vendor->product_reviews_avg_rating ?? 0, 1),
            'five_star' => Review::where('seller_id', $vendorId)->where('rating', 5)->count(),
            'four_star' => Review::where('seller_id', $vendorId)->where('rating', 4)->count(),
            'three_star' => Review::where('seller_id', $vendorId)->where('rating', 3)->count(),
            'two_star' => Review::where('seller_id', $vendorId)->where('rating', 2)->count(),
            'one_star' => Review::where('seller_id', $vendorId)->where('rating', 1)->count(),
        ];

        return view('frontend.shop.vendors.reviews', compact('vendor', 'reviews', 'stats'));
    }

    public function search(Request $request)
    {
        $query = $request->q ?? '';
        
        $vendors = User::where('role', 2)
            ->where('status', 1)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%')
                  ->orWhere('address', 'like', '%' . $query . '%');
            })
            ->withCount('products')
            ->withAvg('productReviews', 'rating')
            ->orderByDesc('products_count')
            ->limit(10)
            ->get();

        return response()->json([
            'vendors' => $vendors->map(function($vendor) {
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'email' => $vendor->email,
                    'photo' => $vendor->photo,
                    'products_count' => $vendor->products_count,
                    'rating' => round($vendor->product_reviews_avg_rating ?? 0, 1),
                ];
            })
        ]);
    }
}
