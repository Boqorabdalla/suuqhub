<?php

namespace App\Http\Controllers\Frontend\Shop;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'item_type' => 'required|in:product,inventory',
        ]);

        $userId = auth()->id();
        $itemId = $request->item_id;
        $itemType = $request->item_type;

        // Validate the item exists
        if ($itemType === 'product') {
            $item = Product::find($itemId);
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => get_phrase('Product not found')
                ]);
            }
        } else {
            $item = Inventory::find($itemId);
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => get_phrase('Item not found')
                ]);
            }
        }

        // Check if already in wishlist
        $query = Wishlist::where('user_id', $userId)->where('item_type', $itemType);
        if ($itemType === 'product') {
            $query->where('product_id', $itemId);
        } else {
            $query->where('inventory_id', $itemId);
        }
        $existing = $query->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success' => true,
                'message' => get_phrase('Removed from wishlist'),
                'in_wishlist' => false
            ]);
        }

        // Add to wishlist
        $wishlistData = [
            'user_id' => $userId,
            'item_type' => $itemType,
        ];
        
        if ($itemType === 'product') {
            $wishlistData['product_id'] = $itemId;
        } else {
            $wishlistData['inventory_id'] = $itemId;
        }

        Wishlist::create($wishlistData);

        return response()->json([
            'success' => true,
            'message' => get_phrase('Added to wishlist'),
            'in_wishlist' => true
        ]);
    }

    public function index()
    {
        $userId = auth()->id();
        
        $productWishlists = Wishlist::with(['product.images', 'product.category'])
            ->where('user_id', $userId)
            ->where('item_type', 'product')
            ->get();
            
        $inventoryWishlists = Wishlist::with(['inventory.images'])
            ->where('user_id', $userId)
            ->where('item_type', 'inventory')
            ->get();
        
        $page_data['wishlists'] = $productWishlists->merge($inventoryWishlists)->sortByDesc('created_at');
        $page_data['wishlists'] = $page_data['wishlists']->paginate ? $page_data['wishlists'] : new \Illuminate\Pagination\LengthAwarePaginator($page_data['wishlists']->all(), count($page_data['wishlists']), 12);
            
        return view('frontend.shop.wishlist', $page_data);
    }

    public function count()
    {
        $count = Wishlist::where('user_id', auth()->id())->count();
        return response()->json(['count' => $count]);
    }
}
