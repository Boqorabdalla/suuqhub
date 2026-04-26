@extends('layouts.frontend')
@push('title', get_phrase('Shopping Cart'))
@push('meta')@endpush
@section('frontend_layout')

<section class="shop-header bg-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2 class="mb-2">{{ get_phrase('Shopping Cart') }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ get_phrase('Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shop') }}">{{ get_phrase('Shop') }}</a></li>
                        <li class="breadcrumb-item active">{{ get_phrase('Cart') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="shop-content py-5">
    <div class="container">
        @if($cartItems->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ get_phrase('Cart Items') }} ({{ $cartItems->sum('quantity') }})</h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($cartItems as $item)
                        <div class="cart-item p-3 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-2 col-4">
                                    @php
                                        $itemProduct = $item->itemProduct;
                                        $imagePath = $item->item_type === 'inventory' 
                                            ? 'uploads/shop/inventory/' 
                                            : 'uploads/shop/products/';
                                        $featuredImg = $itemProduct ? $itemProduct->featured_image : null;
                                    @endphp
                                    @if($featuredImg)
                                        <img src="{{ asset($imagePath.$featuredImg) }}" alt="" class="img-fluid rounded">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4 col-8">
                                    @if($item->item_type === 'inventory' && $itemProduct)
                                        <h6 class="mb-1">{{ $itemProduct->name }}</h6>
                                        <small class="badge bg-secondary">Inventory Item</small>
                                    @elseif($itemProduct)
                                        <a href="{{ route('shop.product', $itemProduct->slug) }}" class="text-decoration-none">
                                            <h6 class="mb-1">{{ $itemProduct->name }}</h6>
                                        </a>
                                    @else
                                        <h6 class="mb-1 text-muted">Item no longer available</h6>
                                    @endif
                                    @if($item->variation_names)
                                        <br><small class="text-muted">{{ $item->variation_names }}</small>
                                    @endif
                                </div>
                                <div class="col-md-2 col-6 mt-3 mt-md-0">
                                    <span class="fw-bold">{{ currency($item->unit_price) }}</span>
                                </div>
                                <div class="col-md-2 col-6 mt-3 mt-md-0">
                                    <div class="input-group input-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" @if($item->quantity <= 1) disabled @endif onclick="updateQuantity({{ $item->id }}, 1)">-</button>
                                        <input type="text" class="form-control text-center" value="{{ $item->quantity }}" readonly>
                                        <button type="button" class="btn btn-outline-secondary" onclick="updateQuantity({{ $item->id }}, {{ intval($item->quantity) + 1 }})">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6 mt-3 mt-md-0 text-end">
                                    <span class="fw-bold text-primary">{{ currency($item->subtotal) }}</span>
                                    <a href="{{ route('shop.cart.remove', $item->id) }}" class="btn btn-sm text-danger d-block mt-2">
                                        <i class="fi-rr-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('shop') }}" class="btn btn-outline-secondary">
                            <i class="fi-rr-arrow-left me-2"></i>{{ get_phrase('Continue Shopping') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ get_phrase('Order Summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ get_phrase('Subtotal') }}</span>
                            <span>{{ currency($cartTotal) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ get_phrase('Shipping') }}</span>
                            <span>{{ get_phrase('Calculated at checkout') }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-0">
                            <strong>{{ get_phrase('Estimated Total') }}</strong>
                            <strong class="text-primary">{{ currency($cartTotal) }}</strong>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('shop.checkout') }}" class="btn btn-primary w-100">
                            {{ get_phrase('Proceed to Checkout') }} <i class="fi-rr-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="mb-3">{{ get_phrase('We Accept') }}</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-light text-dark border">{{ get_phrase('Cash on Delivery') }}</span>
                            <span class="badge bg-light text-dark border">{{ get_phrase('Card Payment') }}</span>
                            <span class="badge bg-light text-dark border">{{ get_phrase('Mobile Money') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fi-rr-shopping-cart text-muted" style="font-size: 96px;"></i>
            <h3 class="mt-4 text-muted">{{ get_phrase('Your cart is empty') }}</h3>
            <p class="text-muted">{{ get_phrase('Add some products to get started!') }}</p>
            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">
                <i class="fi-rr-shop me-2"></i>{{ get_phrase('Browse Products') }}
            </a>
        </div>
        @endif
    </div>
</section>

<form id="updateCartForm" action="{{ route('shop.cart.update') }}" method="post" style="display: none;">
    @csrf
    <input type="hidden" name="item_id" id="updateItemId">
    <input type="hidden" name="quantity" id="updateQuantity">
</form>

@endsection

@push('script')
<script>
    function updateQuantity(itemId, quantity) {
        document.getElementById('updateItemId').value = itemId;
        document.getElementById('updateQuantity').value = quantity;
        document.getElementById('updateCartForm').submit();
    }
</script>
@endpush
