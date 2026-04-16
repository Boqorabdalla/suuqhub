@extends('layouts.frontend')
@push('title', $vendor->name . "'s Products")

@push('css')
<style>
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
        height: 200px;
        object-fit: cover;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ get_phrase('Home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.vendors') }}">{{ get_phrase('Vendors') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.vendor', $vendor->id) }}">{{ $vendor->name }}</a></li>
            <li class="breadcrumb-item active">{{ get_phrase('Products') }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-8">
            <h3>{{ $vendor->name }} - {{ get_phrase('Products') }}</h3>
        </div>
        <div class="col-md-4">
            <a href="{{ route('shop.vendor', $vendor->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left"></i> {{ get_phrase('Back to Store') }}
            </a>
        </div>
    </div>

    @if($products->count() > 0)
    <div class="row">
        @foreach($products as $product)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
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
                    @if($product->is_in_stock)
                        <span class="badge bg-success mb-2">{{ get_phrase('In Stock') }}</span>
                    @else
                        <span class="badge bg-danger mb-2">{{ get_phrase('Out of Stock') }}</span>
                    @endif
                    <a href="{{ route('shop.product', $product->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        {{ get_phrase('View Details') }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-bag display-1 text-muted"></i>
        <h4 class="mt-3">{{ get_phrase('No products yet') }}</h4>
        <p class="text-muted">{{ get_phrase('This vendor has not added any products') }}</p>
    </div>
    @endif
</div>
@endsection
