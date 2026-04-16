@extends('layouts.frontend')
@push('title', get_phrase('Shop'))
@push('meta')@endpush
@push('css')
<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
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
    .product-card .card-body {
        padding: 15px;
    }
    .product-card .price {
        font-size: 18px;
        font-weight: bold;
        color: var(--themeColor);
    }
    .product-card .original-price {
        text-decoration: line-through;
        color: #999;
        font-size: 14px;
    }
    .category-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        transition: transform 0.2s;
    }
    .category-card:hover {
        transform: scale(1.05);
        color: white;
    }
    .category-icon {
        font-size: 32px;
        margin-bottom: 10px;
    }
    .source-badge {
        font-size: 11px;
        padding: 3px 8px;
    }
    .source-admin {
        background-color: #e3f2fd;
        color: #1565c0;
    }
    .source-listing {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    .type-filter-btn {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
    }
    .type-filter-btn.active {
        background-color: var(--themeColor);
        color: white;
    }
    .category-separator {
        border-bottom: 1px solid #eee;
        padding-bottom: 8px;
        margin-bottom: 8px;
        font-weight: 600;
        color: #666;
        font-size: 12px;
        text-transform: uppercase;
    }
</style>
@endpush
@section('frontend_layout')

<section class="shop-header bg-light py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="mb-2">{{ get_phrase('Shop') }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ get_phrase('Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ get_phrase('Shop') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6">
                <div class="text-md-end mt-3 mt-md-0">
                    <a href="{{ route('shop.cart') }}" class="btn btn-outline-primary position-relative">
                        <i class="bi bi-cart3"></i> {{ get_phrase('Cart') }}
                        @php $cartCount = auth()->check() ? \App\Models\ShopCartItem::where('user_id', auth()->user()->id)->sum('quantity') : 0; @endphp
                        @if($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $cartCount }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="shop-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ get_phrase('Categories') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <a href="{{ route('shop', ['type' => 'all']) }}" class="text-decoration-none {{ (request('type') == 'all' || !request('type')) ? 'fw-bold text-primary' : 'text-muted' }}">
                                    {{ get_phrase('All Products') }}
                                </a>
                            </li>
                            @php
                                $adminCats = $categories->filter(function($cat) { return $cat['type'] === 'admin'; })->values();
                                $invCats = $categories->filter(function($cat) { return $cat['type'] === 'inventory'; })->values();
                            @endphp
                            
                            @if($adminCats->count() > 0)
                                <li class="list-group-item category-separator">{{ get_phrase('Admin Categories') }}</li>
                                @foreach($adminCats as $category)
                                <li class="list-group-item">
                                    <a href="{{ route('shop', ['category' => $category['id'], 'type' => request('type') ?? 'all']) }}" class="text-decoration-none {{ request('category') == $category['id'] ? 'fw-bold text-primary' : 'text-muted' }}">
                                        {{ $category['name'] }}
                                    </a>
                                </li>
                                @endforeach
                            @endif
                            
                            @if($invCats->count() > 0)
                                <li class="list-group-item category-separator">{{ get_phrase('Listing Categories') }}</li>
                                @foreach($invCats as $category)
                                <li class="list-group-item">
                                    <a href="{{ route('shop', ['category' => $category['id'], 'type' => request('type') ?? 'all']) }}" class="text-decoration-none {{ request('category') == $category['id'] ? 'fw-bold text-primary' : 'text-muted' }}">
                                        {{ $category['name'] }}
                                    </a>
                                </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

                @if($featuredProducts->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ get_phrase('Featured Products') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($featuredProducts as $featured)
                        <div class="d-flex gap-2 mb-3">
                            @if($featured['featured_image'])
                                <img src="{{ asset('uploads/shop/products/'.$featured['featured_image']) }}" alt="" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                            @elseif(is_object($featured['featured_image'] ?? null))
                                <img src="{{ asset('uploads/shop/inventory/'.$featured['featured_image']) }}" alt="" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fi-rr-picture text-muted"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-1 text-truncate" style="max-width: 150px;">{{ $featured['name'] }}</h6>
                                <span class="price">{{ currency($featured['current_price']) }}</span>
                                @if($featured['listing_title'])
                                    <br><small class="text-muted">{{ Str::limit($featured['listing_title'], 20) }}</small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-9">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('shop', array_merge(request()->except('type'), ['type' => 'all'])) }}" 
                           class="btn {{ ($currentType == 'all' || !$currentType) ? 'btn-primary' : 'btn-outline-secondary' }} type-filter-btn">
                            {{ get_phrase('All') }}
                        </a>
                        <a href="{{ route('shop', array_merge(request()->except('type'), ['type' => 'products'])) }}" 
                           class="btn {{ $currentType == 'products' ? 'btn-primary' : 'btn-outline-secondary' }} type-filter-btn">
                            {{ get_phrase('Official Store') }}
                        </a>
                        <a href="{{ route('shop', array_merge(request()->except('type'), ['type' => 'inventory'])) }}" 
                           class="btn {{ $currentType == 'inventory' ? 'btn-primary' : 'btn-outline-secondary' }} type-filter-btn">
                            {{ get_phrase('From Listings') }}
                        </a>
                    </div>
                    <form action="{{ route('shop') }}" method="get" class="d-flex gap-2">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('type'))
                            <input type="hidden" name="type" value="{{ request('type') }}">
                        @endif
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ get_phrase('Search products...') }}" value="{{ request('search') }}">
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ get_phrase('Newest') }}</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ get_phrase('Price: Low to High') }}</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ get_phrase('Price: High to Low') }}</option>
                        </select>
                    </form>
                </div>

                @php
                    $adminCount = $products->filter(function($p) { return $p['type'] === 'product'; })->count();
                    $listingCount = $products->filter(function($p) { return $p['type'] === 'inventory'; })->count();
                @endphp
                
                @if($currentType == 'all' || !$currentType)
                <div class="alert alert-light border mb-4" role="alert">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <span><i class="bi bi-box-seam"></i> {{ $adminCount }} {{ get_phrase('Official Store Products') }}</span>
                        <span><i class="bi bi-shop"></i> {{ $listingCount }} {{ get_phrase('Products from Listings') }}</span>
                    </div>
                </div>
                @endif

                @if($products->count() > 0)
                <div class="row g-4">
                    @foreach($products as $product)
                    <div class="col-md-4 col-6">
                        <div class="product-card h-100 position-relative">
                            @if($product['is_featured'])
                                <span class="badge bg-warning position-absolute top-0 start-0 m-2">{{ get_phrase('Featured') }}</span>
                            @endif
                            
                            @if($product['type'] === 'product')
                                <span class="badge source-badge source-admin position-absolute top-0 end-0 m-2">
                                    <i class="bi bi-building"></i> {{ get_phrase('Official Store') }}
                                </span>
                            @else
                                <span class="badge source-badge source-listing position-absolute top-0 end-0 m-2">
                                    <i class="bi bi-shop"></i> {{ get_phrase('From Listing') }}
                                </span>
                            @endif
                            
                            @php
                                $productImage = null;
                                if ($product['type'] === 'product') {
                                    $images = $product['images'];
                                    if (is_object($images) && $images->first()) {
                                        $productImage = 'uploads/shop/products/'.$images->first()->image;
                                    } elseif ($product['featured_image']) {
                                        $productImage = 'uploads/shop/products/'.$product['featured_image'];
                                    }
                                } else {
                                    $images = $product['images'];
                                    if (is_object($images) && $images->first()) {
                                        $productImage = 'uploads/shop/inventory/'.$images->first()->image;
                                    } elseif ($product['featured_image']) {
                                        $productImage = 'uploads/shop/inventory/'.$product['featured_image'];
                                    }
                                }
                            @endphp
                            
                            @if($productImage)
                                <img src="{{ asset($productImage) }}" alt="{{ $product['name'] }}" class="product-image">
                            @else
                                <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                    <i class="fi-rr-picture text-muted" style="font-size: 48px;"></i>
                                </div>
                            @endif
                            
                            <div class="card-body">
                                @if(is_object($product['category'] ?? null))
                                    <small class="text-muted">{{ $product['category']->name }}</small>
                                @endif
                                
                                @if($product['type'] === 'inventory' && $product['listing_title'])
                                    <small class="text-success d-block">
                                        <i class="bi bi-geo-alt"></i> {{ Str::limit($product['listing_title'], 30) }}
                                    </small>
                                @endif
                                
                                @if($product['type'] === 'product')
                                    <a href="{{ route('shop.product', $product['slug']) }}" class="text-decoration-none">
                                        <h5 class="card-title mt-1 text-truncate">{{ $product['name'] }}</h5>
                                    </a>
                                @else
                                    <a href="{{ route('listing.details', ['type' => $product['listing_type'] ?? 'beauty', 'id' => $product['listing_id'], 'slug' => slugify($product['name'])]) }}" class="text-decoration-none">
                                        <h5 class="card-title mt-1 text-truncate">{{ $product['name'] }}</h5>
                                    </a>
                                @endif
                                
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="price">{{ currency($product['current_price']) }}</span>
                                    @if($product['discount_price'] && $product['discount_price'] < $product['price'])
                                        <span class="original-price">{{ currency($product['price']) }}</span>
                                    @endif
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    @if($product['track_stock'])
                                        @if($product['stock_quantity'] > 0)
                                            <small class="text-success">{{ get_phrase('In Stock') }} ({{ $product['stock_quantity'] }})</small>
                                        @else
                                            <small class="text-danger">{{ get_phrase('Out of Stock') }}</small>
                                        @endif
                                    @else
                                        <small class="text-muted">{{ get_phrase('Available') }}</small>
                                    @endif
                                </div>
                                
                                @if(auth()->check())
                                <form action="{{ route('shop.cart.add') }}" method="POST" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $product['id'] }}">
                                    <input type="hidden" name="item_type" value="{{ $product['type'] }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                        <i class="bi bi-cart-plus"></i> {{ get_phrase('Add to Cart') }}
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary w-100 mt-2">
                                    {{ get_phrase('Login to Buy') }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-5">
                    {{ $products->withQueryString()->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fi-rr-box-open text-muted" style="font-size: 64px;"></i>
                    <h4 class="mt-3 text-muted">{{ get_phrase('No products found') }}</h4>
                    <a href="{{ route('shop') }}" class="btn btn-primary mt-3">{{ get_phrase('Browse All Products') }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection
