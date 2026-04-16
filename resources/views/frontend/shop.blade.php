@if(isset($shopItems) && $shopItems->count() > 0)
<div class="at-shop-items mb-50px">
    <h4 class="title mb-20">{{ get_phrase('Shop Products') }}</h4>
    <div class="row g-4">
        @foreach($shopItems as $item)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="shop-item-card">
                @if($item->is_featured)
                    <span class="badge bg-warning position-absolute top-0 start-0 m-2">{{ get_phrase('Featured') }}</span>
                @endif
                @if($item->featured_image)
                    <div class="shop-item-image">
                        <img src="{{ asset('uploads/shop/inventory/'.$item->featured_image) }}" alt="{{ $item->name }}">
                    </div>
                @else
                    <div class="shop-item-image bg-light d-flex align-items-center justify-content-center">
                        <i class="fi-rr-picture text-muted" style="font-size: 48px;"></i>
                    </div>
                @endif
                <div class="shop-item-content p-3">
                    <h5 class="mb-1 text-truncate">{{ $item->name }}</h5>
                    <p class="text-muted small mb-2">{{ Str::limit($item->description, 50) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="price fw-bold">{{ currency($item->current_price) }}</span>
                        @if($item->track_stock)
                            @if($item->stock_quantity > 0)
                                <small class="text-success">{{ get_phrase('In Stock') }}</small>
                            @else
                                <small class="text-danger">{{ get_phrase('Out of Stock') }}</small>
                            @endif
                        @endif
                    </div>
                    @if(auth()->check())
                        <form action="{{ route('shop.cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <input type="hidden" name="item_type" value="inventory">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-sm btn-primary w-100 mt-2">
                                <i class="fi-rr-shopping-cart me-1"></i> {{ get_phrase('Add to Cart') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                            {{ get_phrase('Login to Buy') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
