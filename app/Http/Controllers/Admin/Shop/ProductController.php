<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariation;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'user');
        
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        if (auth()->user()->role != 1) {
            $query->where('user_id', auth()->user()->id);
        }
        
        $page_data['products'] = $query->orderBy('created_at', 'desc')->paginate(20);
        $page_data['categories'] = ProductCategory::where('status', 1)->get();
        
        return view('admin.shop.products.index', $page_data);
    }

    public function create()
    {
        $page_data['categories'] = ProductCategory::where('status', 1)->get();
        return view('admin.shop.products.create', $page_data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->user()->id;
        $data['slug'] = Str::slug($request->name);
        
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/shop/products'), $imageName);
            $data['featured_image'] = $imageName;
        }
        
        $product = Product::create($data);
        
        if ($request->has('images')) {
            foreach ($request->images as $index => $image) {
                if ($image) {
                    $imageName = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads/shop/products'), $imageName);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $imageName,
                        'sort_order' => $index,
                    ]);
                }
            }
        }
        
        if ($request->has_variation && $request->variations) {
            foreach ($request->variations as $variation) {
                if (!empty($variation['name']) && !empty($variation['value'])) {
                    ProductVariation::create([
                        'product_id' => $product->id,
                        'name' => $variation['name'],
                        'value' => $variation['value'],
                        'price_modifier' => $variation['price_modifier'] ?? 0,
                        'stock_quantity' => $variation['stock_quantity'] ?? 0,
                        'is_default' => isset($variation['is_default']) ? 1 : 0,
                    ]);
                }
            }
        }
        
        Session::flash('success', get_phrase('Product created successfully!'));
        return redirect()->route('admin.shop.products');
    }

    public function edit($id)
    {
        $product = Product::with('images', 'variations')->findOrFail($id);
        
        if (auth()->user()->role != 1 && $product->user_id != auth()->user()->id) {
            Session::flash('error', get_phrase('You do not have permission to edit this product.'));
            return redirect()->route('admin.shop.products');
        }
        
        $page_data['product'] = $product;
        $page_data['categories'] = ProductCategory::where('status', 1)->get();
        
        return view('admin.shop.products.edit', $page_data);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        if (auth()->user()->role != 1 && $product->user_id != auth()->user()->id) {
            Session::flash('error', get_phrase('You do not have permission to update this product.'));
            return redirect()->route('admin.shop.products');
        }
        
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/shop/products'), $imageName);
            $data['featured_image'] = $imageName;
        }
        
        $product->update($data);
        
        if ($request->has('new_images')) {
            $currentCount = $product->images()->count();
            foreach ($request->new_images as $index => $image) {
                if ($image) {
                    $imageName = time() . '_new_' . $index . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads/shop/products'), $imageName);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $imageName,
                        'sort_order' => $currentCount + $index,
                    ]);
                }
            }
        }
        
        if ($request->has('variations')) {
            $product->variations()->delete();
            foreach ($request->variations as $variation) {
                if (!empty($variation['name']) && !empty($variation['value'])) {
                    ProductVariation::create([
                        'product_id' => $product->id,
                        'name' => $variation['name'],
                        'value' => $variation['value'],
                        'price_modifier' => $variation['price_modifier'] ?? 0,
                        'stock_quantity' => $variation['stock_quantity'] ?? 0,
                        'is_default' => isset($variation['is_default']) ? 1 : 0,
                    ]);
                }
            }
        }
        
        Session::flash('success', get_phrase('Product updated successfully!'));
        return redirect()->route('admin.shop.products');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        if (auth()->user()->role != 1 && $product->user_id != auth()->user()->id) {
            Session::flash('error', get_phrase('You do not have permission to delete this product.'));
            return redirect()->route('admin.shop.products');
        }
        
        $product->delete();
        
        Session::flash('success', get_phrase('Product deleted successfully!'));
        return redirect()->route('admin.shop.products');
    }

    public function deleteImage($id)
    {
        $image = ProductImage::findOrFail($id);
        $image->delete();
        return response()->json(['success' => true]);
    }
}
