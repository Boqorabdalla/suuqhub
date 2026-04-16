@if(auth()->check())
@php
$cartItems = \App\Models\ShopCartItem::with('product')
    ->where('user_id', auth()->id())
    ->get();
$cartTotal = $cartItems->sum('subtotal');
$cartCount = $cartItems->sum('quantity');
@endphp

<div class="mini-cart dropdown">
    <a href="#" class="mini-cart-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fi-rr-shopping-cart"></i>
        @if($cartCount > 0)
            <span class="badge bg-danger rounded-circle position-absolute cart-badge" style="top: -5px; right: -5px; font-size: 10px;">{{ $cartCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end mini-cart-dropdown" style="width: 350px;">
        <div class="mini-cart-header p-3 border-bottom">
            <h6 class="mb-0">{{ get_phrase('Shopping Cart') }}</h6>
        </div>
        @if($cartCount > 0)
            <div class="mini-cart-items" style="max-height: 300px; overflow-y: auto;">
                @foreach($cartItems as $item)
                    @if($item->product)
                    <div class="mini-cart-item d-flex gap-3 p-3 border-bottom">
                        @if($item->product->featured_image)
                            <img src="{{ asset('uploads/shop/products/'.$item->product->featured_image) }}" alt="" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fi-rr-picture text-muted"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-truncate" style="font-size: 14px;">{{ $item->product->name }}</h6>
                            <small class="text-muted">{{ $item->quantity }} × {{ currency($item->unit_price) }}</small>
                        </div>
                        <div class="text-end">
                            <strong>{{ currency($item->subtotal) }}</strong>
                            <a href="{{ route('shop.cart.remove', $item->id) }}" class="btn btn-sm text-danger p-0 d-block">
                                <i class="fi-rr-trash"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            <div class="mini-cart-footer p-3">
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ get_phrase('Subtotal') }}</span>
                    <strong>{{ currency($cartTotal) }}</strong>
                </div>
                <a href="{{ route('shop.cart') }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                    {{ get_phrase('View Cart') }}
                </a>
                <a href="{{ route('shop.checkout') }}" class="btn btn-primary btn-sm w-100">
                    {{ get_phrase('Checkout') }}
                </a>
            </div>
        @else
            <div class="p-4 text-center">
                <i class="fi-rr-shopping-cart text-muted" style="font-size: 48px;"></i>
                <p class="text-muted mt-2 mb-0">{{ get_phrase('Your cart is empty') }}</p>
                <a href="{{ route('shop') }}" class="btn btn-sm btn-primary mt-3">{{ get_phrase('Shop Now') }}</a>
            </div>
        @endif
    </div>
</div>
@else
<div class="mini-cart">
    <a href="{{ route('shop') }}" class="mini-cart-toggle">
        <i class="fi-rr-shopping-cart"></i>
    </a>
</div>
@endif
