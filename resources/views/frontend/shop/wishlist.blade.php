@extends('layouts.frontend')
@push('title', get_phrase('My Wishlist'))
@push('meta')@endpush
<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
</style>
@section('frontend_layout')

<section class="shop-header bg-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2 class="mb-2">{{ get_phrase('My Wishlist') }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ get_phrase('Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ get_phrase('Wishlist') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="shop-content py-5">
    <div class="container">
        @if($wishlists->count() > 0)
        <div class="row g-4">
            @foreach($wishlists as $item)
            @php 
                $itemProduct = $item->itemProduct;
                $isInventory = $item->item_type === 'inventory';
            @endphp
            @if($itemProduct)
            <div class="col-md-3 col-6">
                <div class="card h-100 product-card">
                    @if($itemProduct->is_featured)
                        <span class="badge bg-warning position-absolute top-0 start-0 m-2">{{ get_phrase('Featured') }}</span>
                    @endif
                    
                    @if($isInventory)
                        <span class="badge bg-success position-absolute top-0 end-0 m-2" style="z-index: 10;">
                            <i class="bi bi-shop"></i> {{ get_phrase('From Listing') }}
                        </span>
                    @else
                        <span class="badge bg-primary position-absolute top-0 end-0 m-2" style="z-index: 10;">
                            <i class="bi bi-building"></i> {{ get_phrase('Official Store') }}
                        </span>
                    @endif
                    
                    <div class="wishlist-remove position-absolute" style="top: 40px; right: 8px; z-index: 10;">
                        <a href="#" onclick="removeFromWishlist({{ $item->id }}, '{{ $item->item_type }}')" class="btn btn-sm btn-light rounded-circle">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                    
                    @php
                        $productImage = null;
                        if ($isInventory) {
                            if ($itemProduct->featured_image) {
                                $productImage = 'uploads/shop/inventory/'.$itemProduct->featured_image;
                            }
                        } else {
                            if ($itemProduct->featured_image) {
                                $productImage = 'uploads/shop/products/'.$itemProduct->featured_image;
                            }
                        }
                    @endphp
                    
                    @if($productImage)
                        <img src="{{ asset($productImage) }}" alt="{{ $itemProduct->name }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                            <i class="bi bi-image text-muted" style="font-size: 48px;"></i>
                        </div>
                    @endif
                    
                    <div class="card-body">
                        @if(!$isInventory && $itemProduct->category)
                            <small class="text-muted">{{ $itemProduct->category->name }}</small>
                        @endif
                        
                        @if($isInventory)
                            <small class="text-success d-block">
                                <i class="bi bi-geo-alt"></i> {{ Str::limit($itemProduct->name, 20) }}
                            </small>
                        @endif
                        
                        <a href="{{ $isInventory ? route('listing.details', ['type' => $itemProduct->type ?? 'beauty', 'id' => $itemProduct->listing_id ?? 0, 'slug' => slugify($itemProduct->name)]) : route('shop.product', $itemProduct->slug) }}" class="text-decoration-none">
                            <h6 class="card-title mt-1 text-truncate">{{ $itemProduct->name }}</h6>
                        </a>
                        
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="price text-primary fw-bold">{{ currency($itemProduct->current_price) }}</span>
                            @if(!$isInventory && $itemProduct->original_price && $itemProduct->original_price > $itemProduct->price)
                                <span class="original-price text-muted text-decoration-line-through">{{ currency($itemProduct->original_price) }}</span>
                            @endif
                        </div>
                        
                        <button class="btn btn-sm btn-primary w-100" onclick="addToCartFromWishlist({{ $itemProduct->id }}, '{{ $item->item_type }}')">
                            <i class="bi bi-cart me-1"></i>{{ get_phrase('Add to Cart') }}
                        </button>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <i class="fi-rr-heart text-muted" style="font-size: 96px;"></i>
            <h3 class="mt-4 text-muted">{{ get_phrase('Your wishlist is empty') }}</h3>
            <p class="text-muted">{{ get_phrase('Save products you like to your wishlist!') }}</p>
            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">
                <i class="fi-rr-shop me-2"></i>{{ get_phrase('Browse Products') }}
            </a>
        </div>
        @endif
    </div>
</section>

@endsection

@push('script')
<script>
    function removeFromWishlist(itemId, itemType) {
        $.ajax({
            url: "{{ route('shop.wishlist.toggle') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                item_id: itemId,
                item_type: itemType
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                }
            }
        });
    }

    function addToCartFromWishlist(itemId, itemType) {
        $.ajax({
            url: "{{ route('shop.cart.add') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                item_id: itemId,
                item_type: itemType,
                quantity: 1
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(function() {
                        window.location.href = "{{ route('shop.cart') }}";
                    }, 1000);
                } else {
                    toastr.error(response.message);
                }
            }
        });
    }
</script>
@endpush
