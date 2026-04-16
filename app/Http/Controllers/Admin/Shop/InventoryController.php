<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with('images', 'variations');
        
        if ($request->has('listing_id') && $request->listing_id) {
            $query->where('listing_id', $request->listing_id);
        }
        
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $page_data['items'] = $query->orderBy('created_at', 'desc')->paginate(20);
        $page_data['categories'] = InventoryCategory::orderBy('name')->get();
        
        return view('admin.shop.inventory.index', $page_data);
    }

    public function create()
    {
        $page_data['categories'] = InventoryCategory::orderBy('name')->get();
        return view('admin.shop.inventory.create', $page_data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'listing_id' => 'nullable|integer',
            'type' => 'nullable|string|max:50',
        ]);
        
        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'sku' => $request->sku,
            'availability' => $request->availability ?? 1,
            'is_featured' => $request->is_featured ?? 0,
            'track_stock' => $request->track_stock ?? 1,
            'listing_id' => $request->listing_id,
            'type' => $request->type,
        ];
        
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = 'inv_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move('uploads/shop/inventory/', $filename);
            $data['featured_image'] = $filename;
        }
        
        $item = Inventory::create($data);
        
        if ($request->has('images')) {
            foreach ($request->images as $index => $image) {
                if ($request->hasFile('images.' . $index)) {
                    $file = $request->file('images.' . $index);
                    $filename = 'inv_img_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                    $file->move('uploads/shop/inventory/', $filename);
                    $item->images()->create([
                        'image' => $filename,
                        'sort_order' => $index,
                    ]);
                }
            }
        }
        
        if ($request->has('variation_names')) {
            foreach ($request->variation_names as $index => $name) {
                if ($name) {
                    $item->variations()->create([
                        'name' => $name,
                        'value' => $request->variation_values[$index] ?? $name,
                        'price_modifier' => $request->variation_prices[$index] ?? 0,
                        'stock_quantity' => $request->variation_stocks[$index] ?? 0,
                        'status' => 1,
                    ]);
                }
            }
        }
        
        Session::flash('success', get_phrase('Item created successfully!'));
        return redirect()->route('admin.shop.inventory');
    }

    public function edit($id)
    {
        $page_data['item'] = Inventory::with('images', 'variations')->findOrFail($id);
        $page_data['categories'] = InventoryCategory::orderBy('name')->get();
        return view('admin.shop.inventory.edit', $page_data);
    }

    public function update(Request $request, $id)
    {
        $item = Inventory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);
        
        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'sku' => $request->sku,
            'availability' => $request->availability ?? 1,
            'is_featured' => $request->is_featured ?? 0,
            'track_stock' => $request->track_stock ?? 1,
            'listing_id' => $request->listing_id,
            'type' => $request->type,
        ];
        
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = 'inv_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move('uploads/shop/inventory/', $filename);
            $data['featured_image'] = $filename;
        }
        
        $item->update($data);
        
        if ($request->has('variation_names')) {
            $item->variations()->delete();
            foreach ($request->variation_names as $index => $name) {
                if ($name) {
                    $item->variations()->create([
                        'name' => $name,
                        'value' => $request->variation_values[$index] ?? $name,
                        'price_modifier' => $request->variation_prices[$index] ?? 0,
                        'stock_quantity' => $request->variation_stocks[$index] ?? 0,
                        'status' => 1,
                    ]);
                }
            }
        }
        
        Session::flash('success', get_phrase('Item updated successfully!'));
        return redirect()->route('admin.shop.inventory');
    }

    public function destroy($id)
    {
        $item = Inventory::findOrFail($id);
        $item->images()->delete();
        $item->variations()->delete();
        $item->delete();
        
        Session::flash('success', get_phrase('Item deleted successfully!'));
        return redirect()->route('admin.shop.inventory');
    }
}
