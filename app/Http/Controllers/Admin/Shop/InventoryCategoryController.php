<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class InventoryCategoryController extends Controller
{
    public function index()
    {
        $page_data['categories'] = InventoryCategory::orderBy('name')->get();
        return view('admin.shop.inventory.categories', $page_data);
    }

    public function create(Request $request)
    {
        $page_data['type'] = $request->type ?? 'beauty';
        $page_data['listing_id'] = $request->listing_id ?? null;
        $page_data['prefix'] = $request->prefix ?? 'admin';
        
        return view('admin.shop.inventory.category_form', $page_data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        InventoryCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'listing_id' => $request->listing_id,
            'type' => $request->type,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status ?? 1,
        ]);
        
        Session::flash('success', get_phrase('Category created successfully!'));
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $category = InventoryCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status ?? 1,
        ]);
        
        Session::flash('success', get_phrase('Category updated successfully!'));
        return redirect()->back();
    }

    public function destroy($id)
    {
        $category = InventoryCategory::findOrFail($id);
        $category->delete();
        
        Session::flash('success', get_phrase('Category deleted successfully!'));
        return redirect()->back();
    }
}
