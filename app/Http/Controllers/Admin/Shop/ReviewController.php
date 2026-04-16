<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductReview::with('product', 'user');
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $page_data['reviews'] = $query->orderBy('created_at', 'desc')->paginate(20);
        $page_data['status'] = $request->status;
        
        return view('admin.shop.reviews.index', $page_data);
    }

    public function updateStatus(Request $request, $id)
    {
        $review = ProductReview::findOrFail($id);
        $review->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', get_phrase('Review status updated!'));
    }

    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->delete();
        
        return redirect()->back()->with('success', get_phrase('Review deleted!'));
    }
}
