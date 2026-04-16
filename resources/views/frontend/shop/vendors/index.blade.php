@extends('layouts.frontend')
@push('title', get_phrase('All Vendors'))

@push('css')
<style>
    .vendor-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
    }
    .vendor-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .vendor-card .vendor-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--themeColor);
    }
    .vendor-card .card-body {
        padding: 20px;
    }
    .vendor-stats {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 10px;
    }
    .vendor-stats span {
        font-size: 12px;
        color: #666;
    }
    .rating-stars {
        color: #ffc107;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-2">{{ get_phrase('Shop Vendors') }}</h2>
            <p class="text-muted">{{ get_phrase('Browse products from our verified sellers') }}</p>
        </div>
        <div class="col-md-4">
            <form action="{{ route('shop.vendors') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ get_phrase('Search vendors...') }}" value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($vendors->count() > 0)
    <div class="row">
        @foreach($vendors as $vendor)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="vendor-card card h-100">
                <div class="card-body text-center">
                    <img src="{{ asset($vendor->photo) }}" alt="{{ $vendor->name }}" class="vendor-avatar mb-3">
                    <h5 class="mb-1">{{ $vendor->name }}</h5>
                    @if($vendor->product_reviews_avg_rating)
                        <div class="rating-stars mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($vendor->product_reviews_avg_rating))
                                    <i class="bi bi-star-fill"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                            <span class="text-muted">({{ round($vendor->product_reviews_avg_rating, 1) }})</span>
                        </div>
                    @endif
                    <div class="vendor-stats">
                        <span><i class="bi bi-box-seam"></i> {{ $vendor->products_count }} {{ get_phrase('Products') }}</span>
                    </div>
                    @if($vendor->address)
                        <p class="text-muted small mb-3">
                            <i class="bi bi-geo-alt"></i> {{ Str::limit($vendor->address, 30) }}
                        </p>
                    @endif
                    <a href="{{ route('shop.vendor', $vendor->id) }}" class="btn btn-primary btn-sm w-100">
                        {{ get_phrase('Visit Store') }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="d-flex justify-content-center">
        {{ $vendors->links() }}
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-shop display-1 text-muted"></i>
        <h4 class="mt-3">{{ get_phrase('No vendors found') }}</h4>
        <p class="text-muted">{{ get_phrase('Try adjusting your search criteria') }}</p>
    </div>
    @endif
</div>
@endsection
