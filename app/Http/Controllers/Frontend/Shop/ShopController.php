<?php

namespace App\Http\Controllers\Frontend\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\ShopCartItem;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\BeautyListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->type ?? 'all';
        $search = $request->search ?? '';
        $category = $request->category ?? '';
        $sort = $request->sort ?? 'newest';
        
        $allProducts = [];
        $featuredProducts = [];
        $allCategories = [];
        
        // Fetch Admin Products
        $adminProductsQuery = Product::with('category', 'images')
            ->published()
            ->where('is_published', 1);
        
        // Fetch Agent Inventories (available ones)
        $inventoryQuery = Inventory::with('images')
            ->where('availability', 1);
        
        // Search filter
        if ($search) {
            $adminProductsQuery->where('name', 'like', '%' . $search . '%');
            $inventoryQuery->where('name', 'like', '%' . $search . '%');
        }
        
        // Category filter for admin products
        if ($category && str_starts_with($category, 'admin_')) {
            $catId = substr($category, 6);
            $adminProductsQuery->where('category_id', $catId);
        }
        
        // Category filter for inventory products
        if ($category && str_starts_with($category, 'inv_')) {
            $catId = substr($category, 4);
            $inventoryQuery->whereHas('variations', function($q) use ($catId) {
                $q->where('category_id', $catId);
            });
        }
        
        // Sorting
        switch ($sort) {
            case 'price_low':
                $adminProductsQuery->orderBy('price', 'asc');
                $inventoryQuery->orderBy('price', 'asc');
                break;
            case 'price_high':
                $adminProductsQuery->orderBy('price', 'desc');
                $inventoryQuery->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $adminProductsQuery->orderBy('created_at', 'desc');
                $inventoryQuery->orderBy('created_at', 'desc');
                break;
        }
        
        // Get admin products
        $adminProducts = $adminProductsQuery->get();
        
        // Get inventory products with listing info
        $inventories = $inventoryQuery->get()->map(function($item) {
            $listing = null;
            if ($item->listing_id) {
                $listing = BeautyListing::find($item->listing_id);
            }
            return [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'price' => $item->price,
                'discount_price' => $item->discount_price,
                'current_price' => $item->current_price,
                'featured_image' => $item->featured_image,
                'images' => $item->images,
                'is_featured' => $item->is_featured,
                'track_stock' => $item->track_stock,
                'stock_quantity' => $item->stock_quantity,
                'is_in_stock' => $item->isInStock,
                'type' => 'inventory',
                'listing_id' => $item->listing_id,
                'listing_title' => $listing ? $listing->title : null,
                'listing_type' => $item->type,
                'created_at' => $item->created_at,
            ];
        });
        
        // Transform admin products to unified format
        $transformedAdminProducts = $adminProducts->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'current_price' => $product->current_price,
                'featured_image' => $product->featured_image,
                'images' => $product->images,
                'is_featured' => $product->is_featured,
                'track_stock' => $product->track_stock,
                'stock_quantity' => $product->stock_quantity,
                'is_in_stock' => $product->isInStock,
                'type' => 'product',
                'category' => $product->category,
                'listing_id' => null,
                'listing_title' => null,
                'listing_type' => null,
                'created_at' => $product->created_at,
            ];
        });
        
        // Merge both
        $allProducts = $transformedAdminProducts->merge($inventories);
        
        // Sort the merged collection
        if ($sort === 'price_low') {
            $allProducts = $allProducts->sortBy('current_price');
        } elseif ($sort === 'price_high') {
            $allProducts = $allProducts->sortByDesc('current_price');
        } else {
            $allProducts = $allProducts->sortByDesc('created_at');
        }
        
        // Get featured products (both types)
        $featuredAdminProducts = Product::with('category', 'images')
            ->published()
            ->featured()
            ->limit(4)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'current_price' => $product->current_price,
                    'featured_image' => $product->featured_image,
                    'images' => $product->images,
                    'type' => 'product',
                    'category' => $product->category,
                    'listing_id' => null,
                    'listing_title' => null,
                ];
            });
        
        $featuredInventories = Inventory::with('images')
            ->where('availability', 1)
            ->where('is_featured', 1)
            ->limit(2)
            ->get()
            ->map(function($item) {
                $listing = null;
                if ($item->listing_id) {
                    $listing = BeautyListing::find($item->listing_id);
                }
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'price' => $item->price,
                    'current_price' => $item->current_price,
                    'featured_image' => $item->featured_image,
                    'images' => $item->images,
                    'type' => 'inventory',
                    'category' => null,
                    'listing_id' => $item->listing_id,
                    'listing_title' => $listing ? $listing->title : null,
                ];
            });
        
        $featuredProducts = $featuredAdminProducts->merge($featuredInventories)->take(6);
        
        // Get all categories (both types)
        $adminCategories = ProductCategory::where('status', 1)
            ->get()
            ->map(function($cat) {
                return [
                    'id' => 'admin_' . $cat->id,
                    'name' => $cat->name,
                    'type' => 'admin',
                ];
            });
        
        $inventoryCategories = InventoryCategory::active()
            ->get()
            ->map(function($cat) {
                return [
                    'id' => 'inv_' . $cat->id,
                    'name' => $cat->name,
                    'type' => 'inventory',
                ];
            });
        
        $allCategories = $adminCategories->merge($inventoryCategories);
        
        // Filter by type if specified
        if ($type === 'products') {
            $allProducts = $allProducts->filter(function($item) {
                return $item['type'] === 'product';
            });
        } elseif ($type === 'inventory') {
            $allProducts = $allProducts->filter(function($item) {
                return $item['type'] === 'inventory';
            });
        }
        
        // Paginate
        $perPage = 12;
        $currentPage = $request->page ?? 1;
        $totalProducts = $allProducts->count();
        $products = $allProducts->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $page_data['products'] = new \Illuminate\Pagination\LengthAwarePaginator(
            $products,
            $totalProducts,
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );
        
        $page_data['categories'] = $allCategories;
        $page_data['featuredProducts'] = $featuredProducts;
        $page_data['currentType'] = $type;
        
        return view('frontend.shop.index', $page_data);
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'images', 'variations'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();
            
        $page_data['product'] = $product;
        $page_data['relatedProducts'] = Product::with('category', 'images')
            ->published()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();
            
        return view('frontend.shop.product', $page_data);
    }

    public function addToCart(Request $request)
    {
        $itemType = $request->item_type ?? 'product';
        
        if ($itemType === 'inventory') {
            $request->validate([
                'item_id' => 'required|exists:inventories,id',
                'quantity' => 'required|numeric|min:1',
            ]);
            
            $item = Inventory::findOrFail($request->item_id);
            
            if (!$item->availability) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Item not available']);
                }
                return redirect()->back()->with('error', 'Item not available');
            }
            
            if ($item->track_stock && $item->stock_quantity < $request->quantity) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Not enough stock available']);
                }
                return redirect()->back()->with('error', 'Not enough stock available');
            }
            
            $existingItem = ShopCartItem::where('user_id', auth()->user()->id)
                ->where('item_type', 'inventory')
                ->where('item_id', $request->item_id)
                ->first();
                
            if ($existingItem) {
                $existingItem->quantity += $request->quantity;
                $existingItem->save();
            } else {
                ShopCartItem::create([
                    'user_id' => auth()->user()->id,
                    'item_type' => 'inventory',
                    'item_id' => $request->item_id,
                    'quantity' => $request->quantity,
                ]);
            }
        } else {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|numeric|min:1',
            ]);
            
            $product = Product::findOrFail($request->product_id);
            
            if (!$product->is_published) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Product not available']);
                }
                return redirect()->back()->with('error', 'Product not available');
            }
            
            if (!$product->is_in_stock) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Product out of stock']);
                }
                return redirect()->back()->with('error', 'Product out of stock');
            }
            
            $variationIds = null;
            if ($request->has('variation_ids') && $request->variation_ids) {
                $variationIds = json_encode($request->variation_ids);
            }
            
            $existingItem = ShopCartItem::where('user_id', auth()->user()->id)
                ->where('product_id', $request->product_id)
                ->where('variation_ids', $variationIds)
                ->first();
                
            if ($existingItem) {
                $existingItem->quantity += $request->quantity;
                $existingItem->save();
            } else {
                ShopCartItem::create([
                    'user_id' => auth()->user()->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'variation_ids' => $variationIds,
                ]);
            }
        }
        
        if ($request->ajax()) {
            $cartCount = ShopCartItem::where('user_id', auth()->user()->id)->sum('quantity');
            return response()->json([
                'success' => true, 
                'message' => 'Item added to cart!',
                'cart_count' => $cartCount
            ]);
        }
        
        return redirect()->route('shop.cart')->with('success', 'Item added to cart!');
    }

    public function cart()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $page_data['cartItems'] = ShopCartItem::where('user_id', auth()->user()->id)->get();
            
        $page_data['cartTotal'] = $page_data['cartItems']->sum('subtotal');
        
        return view('frontend.shop.cart', $page_data);
    }

    public function updateCart(Request $request)
    {
        $cartItem = ShopCartItem::where('id', $request->item_id)
            ->where('user_id', auth()->user()->id)
            ->first();
            
        if ($cartItem) {
            if ($request->quantity <= 0) {
                $cartItem->delete();
            } else {
                $cartItem->quantity = $request->quantity;
                $cartItem->save();
            }
        }
        
        return redirect()->back();
    }

    public function removeFromCart($id)
    {
        $cartItem = ShopCartItem::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->first();
            
        if ($cartItem) {
            $cartItem->delete();
        }
        
        return redirect()->back();
    }

    public function showCheckout()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $page_data['cartItems'] = ShopCartItem::where('user_id', auth()->user()->id)->get();
        
        if ($page_data['cartItems']->isEmpty()) {
            return redirect()->route('shop.cart')->with('error', 'Your cart is empty!');
        }
        
        $page_data['cartTotal'] = $page_data['cartItems']->sum('subtotal');
        $page_data['deliveryPrice'] = get_settings('delivery_price') ?? 5;
        
        return view('frontend.shop.checkout', $page_data);
    }

    public function checkout(Request $request)
    {
        $rules = [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:50',
            'shipping_method' => 'required|in:pickup,delivery',
        ];
        
        if ($request->shipping_method == 'delivery') {
            $rules['shipping_address'] = 'required|string';
            $rules['shipping_city'] = 'required|string';
        }
        
        $request->validate($rules);
        
        $cartItems = ShopCartItem::where('user_id', auth()->user()->id)->get();
            
        if ($cartItems->isEmpty()) {
            Session::flash('error', 'Your cart is empty!');
            return redirect()->route('shop.cart');
        }
        
        $subtotal = $cartItems->sum('subtotal');
        $deliveryPrice = get_settings('delivery_price') ?? 5;
        $shippingCost = $request->shipping_method == 'delivery' ? $deliveryPrice : 0;
        $total = $subtotal + $shippingCost;
        
        $firstItem = $cartItems->first();
        $listingId = null;
        
        if ($firstItem->item_type === 'inventory') {
            $inventory = \App\Models\Inventory::find($firstItem->item_id);
            if ($inventory) {
                $listingId = $inventory->listing_id;
            }
        }
        
        $sellerId = auth()->user()->id;
        if ($firstItem->item_type === 'inventory' && $firstItem->item_id) {
            $inventory = \App\Models\Inventory::find($firstItem->item_id);
            if ($inventory && $inventory->listing_id) {
                $listing = \App\Models\BeautyListing::find($inventory->listing_id);
                if ($listing) {
                    $sellerId = $listing->user_id;
                }
            }
        }
        
        $order = ShopOrder::create([
            'order_number' => ShopOrder::generateOrderNumber(),
            'user_id' => auth()->user()->id,
            'seller_id' => $sellerId,
            'listing_id' => $listingId,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'shipping_address' => $request->shipping_address ?? null,
            'shipping_city' => $request->shipping_city ?? null,
            'shipping_postal_code' => $request->shipping_postal_code ?? null,
            'shipping_method' => $request->shipping_method,
            'shipping_cost' => $shippingCost,
            'delivery_price' => $shippingCost,
            'delivery_status' => 'pending',
            'approval_status' => 'pending',
            'subtotal' => $subtotal,
            'total' => $total,
            'payment_method' => $request->payment_method ?? 'cod',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);
        
        foreach ($cartItems as $item) {
            $itemProduct = $item->itemProduct;
            ShopOrderItem::create([
                'order_id' => $order->id,
                'item_type' => $item->item_type,
                'item_id' => $item->item_id,
                'product_id' => $item->item_type === 'inventory' ? null : $item->product_id,
                'product_name' => $itemProduct ? $itemProduct->name : 'Unknown Item',
                'price' => $item->unit_price,
                'quantity' => $item->quantity,
                'variation_name' => $item->variation_names,
                'subtotal' => $item->subtotal,
            ]);
            
            $item->delete();
        }
        
        Session::flash('success', 'Your order has been placed successfully!');
        return redirect()->route('shop.orders');
    }

    public function orders(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $query = ShopOrder::with('items')
            ->where('user_id', auth()->user()->id);
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            switch ($request->status) {
                case 'pending':
                    $query->where('approval_status', 'pending');
                    break;
                case 'approved':
                    $query->where('approval_status', 'approved');
                    break;
                case 'rejected':
                    $query->where('approval_status', 'rejected');
                    break;
                case 'processing':
                    $query->where('approval_status', 'approved')
                          ->where('delivery_status', 'pending');
                    break;
                case 'shipped':
                    $query->whereIn('delivery_status', ['picked_up', 'in_transit']);
                    break;
                case 'delivered':
                    $query->where('delivery_status', 'delivered');
                    break;
                case 'cancelled':
                    $query->where('order_status', 'cancelled');
                    break;
            }
        }
        
        // Search by order number
        if ($request->has('search') && $request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $page_data['orders'] = $query->orderBy('created_at', 'desc')->paginate(10);
        $page_data['filters'] = [
            'status' => $request->status ?? '',
            'search' => $request->search ?? '',
            'date_from' => $request->date_from ?? '',
            'date_to' => $request->date_to ?? '',
        ];
            
        return view('frontend.shop.orders', $page_data);
    }

    public function orderDetail($id)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $order = ShopOrder::with('items.product', 'seller')
            ->where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();
            
        $page_data['order'] = $order;
        
        return view('frontend.shop.order-detail', $page_data);
    }

    public function trackOrder(Request $request)
    {
        $page_data['order'] = null;
        
        if ($request->has('order_number') && $request->order_number) {
            $request->validate([
                'order_number' => 'required|string',
            ]);
            
            $order = ShopOrder::where('order_number', $request->order_number)->first();
            
            if (!$order) {
                return redirect()->back()->with('error', get_phrase('Order not found.'));
            }
            
            $page_data['order'] = $order;
        }
        
        return view('frontend.shop.track-order', $page_data);
    }

    public function invoice($id)
    {
        $order = ShopOrder::with(['items.product', 'items.inventoryItem', 'seller', 'user'])
            ->where('id', $id)
            ->where(function($query) {
                $query->where('user_id', auth()->user()->id)
                      ->orWhere('seller_id', auth()->user()->id);
            })
            ->firstOrFail();

        $page_data['order'] = $order;
        $page_data['is_download'] = false;

        return view('frontend.shop.invoice', $page_data);
    }

    public function downloadInvoice($id)
    {
        $order = ShopOrder::with(['items.product', 'items.inventoryItem', 'seller', 'user'])
            ->where('id', $id)
            ->where(function($query) {
                $query->where('user_id', auth()->user()->id)
                      ->orWhere('seller_id', auth()->user()->id);
            })
            ->firstOrFail();

        $page_data['order'] = $order;

        $html = view('frontend.shop.invoice-pdf', $page_data)->render();

        $pdf = \PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'invoice-' . $order->order_number . '.pdf';
        return $pdf->download($filename);
    }
}
