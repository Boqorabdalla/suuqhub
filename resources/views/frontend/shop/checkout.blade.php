@extends('layouts.frontend')
@push('title', get_phrase('Checkout'))
@push('meta')@endpush
@push('css')
<style>
    .checkout-section { display: none; }
    .checkout-section.active { display: block; }
</style>
@endpush
@push('js')
<script>
    function toggleShipping() {
        var pickup = document.getElementById('pickup_option');
        var deliveryFields = document.getElementById('delivery_fields');
        var shippingCost = document.getElementById('shipping_cost');
        var totalAmount = document.getElementById('total_amount');
        var deliveryPrice = {{ $deliveryPrice }};
        var cartTotal = {{ $cartTotal }};
        
        if (pickup.checked) {
            deliveryFields.style.display = 'none';
            shippingCost.textContent = '{{ currency(0) }}';
            totalAmount.textContent = '{{ currency($cartTotal) }}';
        } else {
            deliveryFields.style.display = 'block';
            shippingCost.textContent = '{{ currency($deliveryPrice) }}';
            totalAmount.textContent = '{{ currency($cartTotal + $deliveryPrice) }}';
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        toggleShipping();
    });
</script>
@endpush
@section('frontend_layout')

<section class="shop-header bg-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2 class="mb-2">{{ get_phrase('Checkout') }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ get_phrase('Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shop.cart') }}">{{ get_phrase('Cart') }}</a></li>
                        <li class="breadcrumb-item active">{{ get_phrase('Checkout') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

@php
$cartItems = \App\Models\ShopCartItem::with('product', 'product.user')
    ->where('user_id', auth()->user()->id)
    ->get();
$cartTotal = $cartItems->sum('subtotal');
@endphp

<section class="shop-content py-5">
    <div class="container">
        @if($cartItems->count() > 0)
        <form action="{{ route('shop.checkout.process') }}" method="post" id="checkoutForm">
            @csrf
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ get_phrase('Contact Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ get_phrase('Full Name') }} *</label>
                                    <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', auth()->user()->name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ get_phrase('Email') }} *</label>
                                    <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', auth()->user()->email) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ get_phrase('Phone') }} *</label>
                                    <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ get_phrase('Shipping Method') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check border p-3 rounded">
                                        <input class="form-check-input" type="radio" name="shipping_method" id="pickup_option" value="pickup" {{ old('shipping_method') == 'pickup' || !old('shipping_method') ? 'checked' : '' }} onchange="toggleShipping()">
                                        <label class="form-check-label" for="pickup_option">
                                            <strong>{{ get_phrase('Store Pickup') }}</strong>
                                            <br><small class="text-muted">{{ get_phrase('Free') }}</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check border p-3 rounded">
                                        <input class="form-check-input" type="radio" name="shipping_method" id="delivery_option" value="delivery" {{ old('shipping_method') == 'delivery' ? 'checked' : '' }} onchange="toggleShipping()">
                                        <label class="form-check-label" for="delivery_option">
                                            <strong>{{ get_phrase('Home Delivery') }}</strong>
                                            <br><small class="text-muted">{{ get_phrase('Delivery') }}: {{ currency($deliveryPrice) }}</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="delivery_fields" style="{{ old('shipping_method') == 'delivery' ? '' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">{{ get_phrase('Delivery Address') }} *</label>
                                        <textarea name="shipping_address" class="form-control" rows="2" placeholder="{{ get_phrase('Enter your full address') }}" required>{{ old('shipping_address') }}</textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ get_phrase('City') }} *</label>
                                        <input type="text" name="shipping_city" class="form-control" value="{{ old('shipping_city') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ get_phrase('Payment Method') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-check border p-3 rounded">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cod_option" value="cod" checked>
                                        <label class="form-check-label" for="cod_option">
                                            <strong>{{ get_phrase('Cash on Delivery') }}</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check border p-3 rounded">
                                        <input class="form-check-input" type="radio" name="payment_method" id="card_option" value="card">
                                        <label class="form-check-label" for="card_option">
                                            <strong>{{ get_phrase('Card Payment') }}</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check border p-3 rounded">
                                        <input class="form-check-input" type="radio" name="payment_method" id="momo_option" value="momo">
                                        <label class="form-check-label" for="momo_option">
                                            <strong>{{ get_phrase('Mobile Money') }}</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ get_phrase('Order Summary') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="checkout-items" style="max-height: 300px; overflow-y: auto;">
                                @foreach($cartItems as $item)
                                @php
                                    $itemProduct = $item->itemProduct;
                                    $imagePath = $item->item_type === 'inventory' ? 'uploads/shop/inventory/' : 'uploads/shop/products/';
                                @endphp
                                <div class="d-flex gap-3 p-3 border-bottom">
                                    @if($itemProduct && $itemProduct->featured_image)
                                        <img src="{{ asset($imagePath.$itemProduct->featured_image) }}" alt="" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fi-rr-picture text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-truncate" style="font-size: 14px;">{{ $itemProduct ? $itemProduct->name : 'Item not found' }}</h6>
                                        @if($item->variation_names)
                                            <small class="text-muted d-block">{{ $item->variation_names }}</small>
                                        @endif
                                        <div class="d-flex justify-content-between">
                                            <small>{{ $item->quantity }} × {{ currency($item->unit_price) }}</small>
                                            <strong>{{ currency($item->subtotal) }}</strong>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ get_phrase('Subtotal') }}</span>
                                    <span>{{ currency($cartTotal) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ get_phrase('Shipping') }}</span>
                                    <span id="shipping_cost">{{ currency(0) }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>{{ get_phrase('Total') }}</strong>
                                    <strong class="text-primary fs-5" id="total_amount">{{ currency($cartTotal) }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fi-rr-check-circle me-2"></i>{{ get_phrase('Place Order') }}
                            </button>
                            <p class="text-center text-muted small mt-2 mb-0">
                                {{ get_phrase('By placing this order, you agree to our terms and conditions') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @else
        <div class="text-center py-5">
            <i class="fi-rr-shopping-cart text-muted" style="font-size: 96px;"></i>
            <h3 class="mt-4 text-muted">{{ get_phrase('Your cart is empty') }}</h3>
            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">{{ get_phrase('Continue Shopping') }}</a>
        </div>
        @endif
    </div>
</section>

@endsection
