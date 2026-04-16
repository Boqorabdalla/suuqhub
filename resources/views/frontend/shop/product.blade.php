@extends('layouts.frontend')
@push('title', $product->name)
@push('meta')@endpush
@push('css')
<style>
    .product-gallery-thumb {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border: 2px solid #eee;
        border-radius: 8px;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .product-gallery-thumb:hover, .product-gallery-thumb.active {
        border-color: var(--themeColor);
    }
    .variation-btn {
        border: 2px solid #eee;
        border-radius: 8px;
        padding: 8px 16px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
    }
    .variation-btn:hover, .variation-btn.selected {
        border-color: var(--themeColor);
        background: rgba(108, 28, 255, 0.05);
    }
    .quantity-input {
        width: 80px;
        text-align: center;
    }
</style>
@endpush
@section('frontend_layout')

<section class="shop-content py-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ get_phrase('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop') }}">{{ get_phrase('Shop') }}</a></li>
                @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('shop', ['category' => $product->category->id]) }}">{{ $product->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="product-gallery">
                    @php 
                        $allImages = [];
                        if ($product->images->count() > 0) {
                            foreach($product->images as $img) {
                                $allImages[] = ['image' => $img->image];
                            }
                        } else if ($product->featured_image) {
                            $allImages[] = ['image' => $product->featured_image];
                        }
                    @endphp
                    @if(count($allImages) > 0)
                        <div class="main-image mb-3">
                            <img src="{{ asset('uploads/shop/products/'.$allImages[0]['image']) }}" alt="{{ $product->name }}" class="img-fluid rounded" id="mainProductImage">
                        </div>
                        @if(count($allImages) > 1)
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($allImages as $index => $img)
                                    @php $isActive = ($index === 0) ? 'active' : ''; @endphp
                                    <img src="{{ asset('uploads/shop/products/'.$img['image']) }}" alt="" class="product-gallery-thumb {{ $isActive }}" onclick="changeMainImage(this, '{{ asset('uploads/shop/products/'.$img['image']) }}')">
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="main-image mb-3 bg-light rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                            <i class="bi bi-image text-muted" style="font-size: 96px;"></i>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-6">
                <div class="product-info">
                    @if($product->category)
                        <span class="badge bg-light text-dark mb-2">{{ $product->category->name }}</span>
                    @endif
                    <h2 class="mb-3">{{ $product->name }}</h2>

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <h3 class="mb-0 text-primary">{{ currency($product->price) }}</h3>
                        @if($product->original_price && $product->original_price > $product->price)
                            <span class="text-muted text-decoration-line-through">{{ currency($product->original_price) }}</span>
                            @php $discount = round((($product->original_price - $product->price) / $product->original_price) * 100); @endphp
                            <span class="badge bg-danger">{{ $discount }}% OFF</span>
                        @endif
                    </div>

                    @if($product->short_description)
                        <p class="text-muted mb-4">{{ $product->short_description }}</p>
                    @endif

                    @if($product->variations->count() > 0)
                        @php $groupedVariations = $product->variations->groupBy('name'); @endphp
                        @foreach($groupedVariations as $variationName => $variations)
                        <div class="mb-4">
                            <h6 class="mb-2">{{ ucfirst($variationName) }}</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach($variations as $variation)
                                    <button type="button" class="variation-btn variation-option" 
                                            data-variation-id="{{ $variation->id }}"
                                            data-price="{{ $variation->price_modifier }}"
                                            data-stock="{{ $variation->stock_quantity }}"
                                            data-value="{{ $variation->value }}"
                                            onclick="selectVariation(this)">
                                        {{ $variation->value }}
                                        @if($variation->price_modifier != 0)
                                            <small>({{ $variation->price_modifier > 0 ? '+' : '' }}{{ currency($variation->price_modifier) }})</small>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    @endif

                    <form action="{{ route('shop.cart.add') }}" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="input-group quantity-input">
                                <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="10">
                            </div>
                            <button type="submit" class="btn btn-primary flex-grow-1" {{ (!$product->is_published || ($product->track_stock && $product->stock_quantity <= 0)) ? 'disabled' : '' }}>
                                <i class="fi-rr-shopping-cart me-2"></i>{{ get_phrase('Add to Cart') }}
                            </button>
                        </div>
                    </form>

                    <div class="d-flex gap-3 mb-4">
                        @if($product->track_stock)
                            @if($product->stock_quantity > 0)
                                <span class="text-success"><i class="fi-rr-check-circle me-1"></i>{{ get_phrase('In Stock') }} ({{ $product->stock_quantity }})</span>
                            @else
                                <span class="text-danger"><i class="fi-rr-cross-circle me-1"></i>{{ get_phrase('Out of Stock') }}</span>
                            @endif
                        @else
                            <span class="text-muted"><i class="fi-rr-check-circle me-1"></i>{{ get_phrase('Available') }}</span>
                        @endif
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">{{ get_phrase('Shipping Options') }}</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ get_phrase('Pickup') }}</span>
                                <span>{{ $product->pickup_cost > 0 ? currency($product->pickup_cost) : get_phrase('Free') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>{{ get_phrase('Delivery') }}</span>
                                <span>{{ $product->delivery_cost > 0 ? currency($product->delivery_cost) : get_phrase('Free') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($product->description)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ get_phrase('Description') }}</h5>
                    </div>
                    <div class="card-body">
                        {!! $product->description !!}
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ get_phrase('Reviews') }} ({{ $product->reviews_count }} {{ get_phrase('reviews') }})</h5>
                        @php $averageRating = $product->average_rating; @endphp
                        <div class="d-flex align-items-center gap-2">
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fi-rr-star {{ $i <= $averageRating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            <span class="text-muted">{{ $averageRating > 0 ? $averageRating : '0' }} / 5</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(auth()->check())
                            <div class="review-form mb-4 border-bottom pb-4">
                                <h6 class="mb-3">{{ get_phrase('Write a Review') }}</h6>
                                <form id="reviewForm">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <div class="mb-3">
                                        <label class="form-label">{{ get_phrase('Your Rating') }}</label>
                                        <div class="rating-input">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" class="d-none">
                                                <label for="rating{{ $i }}" class="star-label" data-rating="{{ $i }}">
                                                    <i class="fi-rr-star"></i>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ get_phrase('Your Review') }}</label>
                                        <textarea name="comment" class="form-control" rows="3" placeholder="{{ get_phrase('Share your thoughts about this product...') }}"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        {{ get_phrase('Submit Review') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-info mb-4">
                                <a href="{{ route('login') }}">{{ get_phrase('Login') }}</a> {{ get_phrase('to write a review') }}
                            </div>
                        @endif

                        <div class="reviews-list">
                            @forelse($product->approvedReviews()->latest()->limit(10)->get() as $review)
                                <div class="review-item mb-4 pb-4 border-bottom">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $review->user->image ? asset('uploads/users/'.$review->user->image) : asset('image/user.jpg') }}" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <strong>{{ $review->user->name }}</strong>
                                                <div class="rating-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fi-rr-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" style="font-size: 12px;"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ date_formatter($review->created_at) }}</small>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-muted mb-0">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted text-center py-3">{{ get_phrase('No reviews yet. Be the first to review this product!') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($relatedProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="mb-4">{{ get_phrase('Related Products') }}</h4>
                <div class="row g-4">
                    @foreach($relatedProducts as $related)
                    <div class="col-md-3 col-6">
                        <div class="card h-100">
                            @if($related->featured_image)
                                <img src="{{ asset('uploads/shop/products/'.$related->featured_image) }}" alt="{{ $related->name }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                            @endif
                            <div class="card-body">
                                <h6 class="card-title text-truncate">{{ $related->name }}</h6>
                                <span class="text-primary fw-bold">{{ currency($related->price) }}</span>
                            </div>
                            <a href="{{ route('shop.product', $related->slug) }}" class="card-footer text-center text-primary text-decoration-none">
                                {{ get_phrase('View Details') }}
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

@endsection

@push('script')
<script>
    function changeMainImage(thumb, src) {
        document.getElementById('mainProductImage').src = src;
        document.querySelectorAll('.product-gallery-thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    }
</script>
@endpush
