@extends('layouts.frontend')
@push('title', get_phrase('My Reviews'))
@push('meta')@endpush
@section('frontend_layout')

<section class="shop-header bg-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2 class="mb-2">{{ get_phrase('My Reviews') }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ get_phrase('Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ get_phrase('My Reviews') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="shop-content py-5">
    <div class="container">
        @if($reviews->count() > 0)
        <div class="row">
            @foreach($reviews as $review)
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <a href="{{ route('shop.product', $review->product->slug ?? '#') }}" class="text-decoration-none">
                                    <h5 class="mb-1">{{ $review->product->name ?? 'Product Deleted' }}</h5>
                                </a>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fi-rr-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            @php
                                $statusClass = match($review->status) {
                                    'pending' => 'bg-warning text-dark',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($review->status) }}</span>
                        </div>
                        @if($review->comment)
                            <p class="text-muted mb-0">{{ $review->comment }}</p>
                        @endif
                        <small class="text-muted mt-2 d-block">{{ date_formatter($review->created_at) }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center">
            {{ $reviews->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fi-rr-star text-muted" style="font-size: 96px;"></i>
            <h3 class="mt-4 text-muted">{{ get_phrase('No reviews yet') }}</h3>
            <p class="text-muted">{{ get_phrase('Products you purchase will appear here for review.') }}</p>
            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">
                <i class="fi-rr-shop me-2"></i>{{ get_phrase('Browse Products') }}
            </a>
        </div>
        @endif
    </div>
</section>

@endsection
