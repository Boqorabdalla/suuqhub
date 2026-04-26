<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\BeautyListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AgentInventoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->user()->id;
        
        $myListingIds = BeautyListing::where('user_id', $userId)->pluck('id')->toArray();
        
        $listings = BeautyListing::where('user_id', $userId)->get();
        
        if (empty($myListingIds)) {
            $page_data['inventories'] = collect([]);
            $page_data['listings'] = $listings;
            return view('agent.inventory.index', $page_data);
        }
        
        $query = Inventory::whereIn('listing_id', $myListingIds);
        
        // Filter by listing
        if ($request->has('listing_id') && $request->listing_id) {
            $query->where('listing_id', $request->listing_id);
        }
        
        // Filter by availability
        if ($request->has('availability') && $request->availability !== '') {
            $query->where('availability', $request->availability);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by stock status
        if ($request->has('stock_status') && $request->stock_status) {
            if ($request->stock_status == 'in_stock') {
                $query->where('track_stock', 1)->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status == 'low_stock') {
                $query->where('track_stock', 1)->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5);
            } elseif ($request->stock_status == 'out_of_stock') {
                $query->where(function($q) {
                    $q->where('track_stock', 0)
                      ->orWhere('stock_quantity', '<=', 0);
                });
            }
        }
        
        $page_data['inventories'] = $query->orderBy('created_at', 'desc')->paginate(20);
        $page_data['listings'] = BeautyListing::where('user_id', $userId)->get();
        $page_data['filters'] = [
            'listing_id' => $request->listing_id ?? '',
            'availability' => $request->availability ?? '',
            'search' => $request->search ?? '',
            'stock_status' => $request->stock_status ?? '',
        ];
        
        return view('agent.inventory.index', $page_data);
    }
    
    public function create()
    {
        $userId = auth()->user()->id;
        $page_data['listings'] = BeautyListing::where('user_id', $userId)->where('status', 'active')->get();
        
        return view('agent.inventory.create', $page_data);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:beauty_listings,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'track_stock' => 'nullable|boolean',
            'availability' => 'nullable|boolean',
        ]);
        
        // Verify listing belongs to user
        $listing = BeautyListing::where('id', $request->listing_id)
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();
        
        Inventory::create([
            'listing_id' => $request->listing_id,
            'type' => 'product',
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'sku' => $request->sku,
            'featured_image' => $request->featured_image,
            'track_stock' => $request->track_stock ?? 0,
            'availability' => $request->availability ?? 1,
        ]);
        
        Session::flash('success', get_phrase('Inventory item created successfully!'));
        return redirect()->route('agent.inventory');
    }
    
    public function edit($id)
    {
        $userId = auth()->user()->id;
        $myListingIds = BeautyListing::where('user_id', $userId)->pluck('id')->toArray();
        
        $page_data['inventory'] = Inventory::whereIn('listing_id', $myListingIds)->findOrFail($id);
        $page_data['listings'] = BeautyListing::where('user_id', $userId)->where('status', 'active')->get();
        
        return view('agent.inventory.edit', $page_data);
    }
    
    public function update(Request $request, $id)
    {
        $userId = auth()->user()->id;
        $myListingIds = BeautyListing::where('user_id', $userId)->pluck('id')->toArray();
        
        $inventory = Inventory::whereIn('listing_id', $myListingIds)->findOrFail($id);
        
        $request->validate([
            'listing_id' => 'required|exists:beauty_listings,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'track_stock' => 'nullable|boolean',
            'availability' => 'nullable|boolean',
        ]);
        
        // Verify listing belongs to user
        $listing = BeautyListing::where('id', $request->listing_id)
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();
        
        $inventory->update([
            'listing_id' => $request->listing_id,
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'sku' => $request->sku,
            'featured_image' => $request->featured_image,
            'track_stock' => $request->track_stock ?? 0,
            'availability' => $request->availability ?? 1,
        ]);
        
        Session::flash('success', get_phrase('Inventory item updated successfully!'));
        return redirect()->route('agent.inventory');
    }
    
    public function destroy($id)
    {
        $userId = auth()->user()->id;
        $myListingIds = BeautyListing::where('user_id', $userId)->pluck('id')->toArray();
        
        $inventory = Inventory::whereIn('listing_id', $myListingIds)->findOrFail($id);
        $inventory->delete();
        
        Session::flash('success', get_phrase('Inventory item deleted successfully!'));
        return redirect()->route('agent.inventory');
    }
    
    public function updateStock(Request $request, $id)
    {
        $userId = auth()->user()->id;
        $myListingIds = BeautyListing::where('user_id', $userId)->pluck('id')->toArray();
        
        $inventory = Inventory::whereIn('listing_id', $myListingIds)->findOrFail($id);
        
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);
        
        $inventory->update([
            'stock_quantity' => $request->stock_quantity,
            'track_stock' => 1,
        ]);
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Stock updated']);
        }
        
        Session::flash('success', get_phrase('Stock updated successfully!'));
        return redirect()->back();
    }
    
    public function bulkUpdateStock(Request $request)
    {
        $userId = auth()->user()->id;
        $myListingIds = BeautyListing::where('user_id', $userId)->pluck('id')->toArray();
        
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inventories,id',
            'items.*.stock_quantity' => 'required|integer|min:0',
        ]);
        
        foreach ($request->items as $item) {
            $inventory = Inventory::whereIn('listing_id', $myListingIds)
                ->where('id', $item['id'])
                ->first();
            
            if ($inventory) {
                $inventory->update([
                    'stock_quantity' => $item['stock_quantity'],
                    'track_stock' => 1,
                ]);
            }
        }
        
        Session::flash('success', get_phrase('Stock updated successfully!'));
        return redirect()->route('agent.inventory');
    }
}
