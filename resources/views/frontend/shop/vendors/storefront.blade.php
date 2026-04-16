@extends('layouts.frontend')
@push('title', $vendor->name . "'s Store")

@push('css')
<style>
    .vendor-header {
        background: linear-gradient(135deg, var(--themeColor) 0%, #764ba2 100%);
        color: white;
        padding: 40px 0;
        margin-bottom: 30px;
    }
    .vendor-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
    }
    .stat-card {
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
        padding: 15px;
        text-align: center;
    }
    .stat-card h4 {
        margin: 0;
        font-size: 24px;
    }
    .stat-card small {
        opacity: 0.8;
    }
    .filter-sidebar {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .product-card .product-image {
        height: 180px;
        object-fit: cover;
        width: 100%;
    }
    .rating-stars {
        color: #ffc107;
    }
</style>
@endpush

@section('content')
<!-- Vendor Header -->
<div class="vendor-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <img src="{{ asset($vendor->photo) }}" alt="{{ $vendor->name }}" class="vendor-avatar">
            </div>
            <div class="col-md-6">
                <h2 class="mb-1">{{ $vendor->name }}</h2>
                @if($vendor->product_reviews_avg_rating)
                    <div class="rating-stars mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($vendor->product_reviews_avg_rating))
                                <i class="bi bi-star-fill"></i>
                            @else
                                <i class="bi bi-star"></i>
                            @endif
                        @endfor
                        <span class="text-white">({{ round($vendor->product_reviews_avg_rating, 1) }} {{ get_phrase('rating') }})</span>
                    </div>
                @endif
                @if($vendor->address)
                    <p class="mb-0"><i class="bi bi-geo-alt"></i> {{ $vendor->address }}</p>
                @endif
                <div class="mt-2">
                    <a href="{{ route('shop.vendor.products', $vendor->id) }}" class="btn btn-light btn-sm me-2">
                        {{ get_phrase('All Products') }}
                    </a>
                    <a href="{{ route('shop.vendor.reviews', $vendor->id) }}" class="btn btn-outline-light btn-sm">
                        {{ get_phrase('Reviews') }} ({{ $stats['total_reviews'] }})
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-card">
                            <h4>{{ $stats['total_products'] }}</h4>
                            <small>{{ get_phrase('Products') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <h4>{{ $stats['total_sales'] }}</h4>
                            <small>{{ get_phrase('Sales') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <h4>{{ $stats['avg_rating'] }}</h4>
                            <small>{{ get_phrase('Rating') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <h4>{{ $stats['total_reviews'] }}</h4>
                            <small>{{ get_phrase('Reviews') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="filter-sidebar">
                <h5 class="mb-3">{{ get_phrase('Filters') }}</h5>
                
                <!-- Categories -->
                @if($categories->count() > 0)
                <div class="mb-4">
                    <h6 class="mb-2">{{ get_phrase('Categories') }}</h6>
                    @foreach($categories as $category)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                id="cat_{{ $category->id }}" 
                                onchange="filterProducts()"
                                {{ request('category') == $category->id ? 'checked' : '' }}>
                            <label class="form-check-label" for="cat_{{ $category->id }}">
                                {{ $category->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                @endif

                <!-- Price Range -->
                <div class="mb-4">
                    <h6 class="mb-2">{{ get_phrase('Price Range') }}</h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="number" class="form-control form-control-sm" 
                                id="min_price" placeholder="{{ get_phrase('Min') }}" 
                                value="{{ request('min_price') }}">
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control form-control-sm" 
                                id="max_price" placeholder="{{ get_phrase('Max') }}" 
                                value="{{ request('max_price') }}">
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary btn-sm w-100" onclick="filterProducts()">
                    {{ get_phrase('Apply Filters') }}
                </button>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted">{{ $products->total() }} {{ get_phrase('products found') }}</p>
                <select class="form-select form-select-sm w-auto" id="sort_select" onchange="filterProducts()">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ get_phrase('Newest First') }}</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ get_phrase('Price: Low to High') }}</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ get_phrase('Price: High to Low') }}</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ get_phrase('Most Popular') }}</option>
                </select>
            </div>

            @if($products->count() > 0)
            <div class="row">
                @foreach($products as $product)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="product-card card h-100">
                        @php $image = $product->images->first()->image ?? 'uploads/thumbnail.jpg'; @endphp
                        <img src="{{ asset($image) }}" class="product-image" alt="{{ $product->name }}">
                        <div class="card-body">
                            <h6 class="mb-1">
                                <a href="{{ route('shop.product', $product->id) }}" class="text-decoration-none text-dark">
                                    {{ Str::limit($product->name, 40) }}
                                </a>
                            </h6>
                            @if($product->category)
                                <small class="text-muted">{{ $product->category->name }}</small>
                            @endif
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <span class="price fw-bold" style="color: var(--themeColor);">
                                    {{ currency($product->current_price) }}
                                </span>
                                @if($product->original_price > $product->current_price)
                                    <small class="text-muted text-decoration-line-through">
                                        {{ currency($product->original_price) }}
                                    </small>
                                @endif
                            </div>
                            <a href="{{ route('shop.product', $product->id) }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
                                {{ get_phrase('View Details') }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center">
                {{ $products->withQueryString()->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-bag display-1 text-muted"></i>
                <h4 class="mt-3">{{ get_phrase('No products found') }}</h4>
                <p class="text-muted">{{ get_phrase('This vendor has no products matching your filters') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('js')
<script>
function filterProducts() {
    let url = new URL(window.location.href);
    let params = new URLSearchParams(url.search);
    
    // Update category
    let category = document.querySelector('input[name="category"]:checked');
    if (category) {
        params.set('category', category.value);
    } else {
        params.delete('category');
    }
    
    // Update price
    let minPrice = document.getElementById('min_price').value;
    let maxPrice = document.getElementById('max_price').value;
    
    if (minPrice) {
        params.set('min_price', minPrice);
    } else {
        params.delete('min_price');
    }
    
    if (maxPrice) {
        params.set('max_price', maxPrice);
    } else {
        params.delete('max_price');
    }
    
    // Update sort
    let sort = document.getElementById('sort_select').value;
    params.set('sort', sort);
    
    window.location.href = url.pathname + '?' + params.toString();
}
</script>
@endpush
@endsection
