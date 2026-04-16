@extends('layouts.frontend')
@push('title', $vendor->name . "'s Reviews")

@push('css')
<style>
    .rating-chart {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }
    .rating-bar {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        flex: 1;
    }
    .rating-bar-fill {
        height: 100%;
        background: #ffc107;
    }
    .review-card {
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    .review-card:last-child {
        border-bottom: none;
    }
    .rating-stars {
        color: #ffc107;
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
            <li class="breadcrumb-item active">{{ get_phrase('Reviews') }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-8">
            <h3>{{ get_phrase('Reviews for') }} {{ $vendor->name }}</h3>
        </div>
        <div class="col-md-4">
            <a href="{{ route('shop.vendor', $vendor->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-arrow-left"></i> {{ get_phrase('Back to Store') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="display-4 mb-0">{{ $stats['avg_rating'] }}</h1>
                    <div class="rating-stars mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($stats['avg_rating']))
                                <i class="bi bi-star-fill fs-4"></i>
                            @else
                                <i class="bi bi-star fs-4"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="text-muted">{{ array_sum([$stats['five_star'], $stats['four_star'], $stats['three_star'], $stats['two_star'], $stats['one_star']]) }} {{ get_phrase('reviews') }}</p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    @php $starLabels = ['five_star', 'four_star', 'three_star', 'two_star', 'one_star']; @endphp
                    @php $ratingCounts = [
                        5 => $stats['five_star'] ?? 0,
                        4 => $stats['four_star'] ?? 0,
                        3 => $stats['three_star'] ?? 0,
                        2 => $stats['two_star'] ?? 0,
                        1 => $stats['one_star'] ?? 0
                    ]; 
                    $totalReviews = array_sum($ratingCounts);
                    @endphp
                    @foreach([5, 4, 3, 2, 1] as $star)
                        @php
                            $count = $ratingCounts[$star];
                            $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                        @endphp
                        <div class="rating-chart">
                            <span>{{ $star }} <i class="bi bi-star-fill text-warning"></i></span>
                            <div class="rating-bar">
                                <div class="rating-bar-fill" style="width: <?php echo $percent; ?>%"></div>
                            </div>
                            <small>{{ $count }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @if($reviews->count() > 0)
                @foreach($reviews as $review)
                <div class="review-card">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <strong>{{ $review->user->name ?? 'Anonymous' }}</strong>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                    @if($review->product)
                        <p class="mb-1">
                            <small class="text-muted">
                                {{ get_phrase('Product') }}: 
                                <a href="{{ route('shop.product', $review->product_id) }}">{{ $review->product->name }}</a>
                            </small>
                        </p>
                    @endif
                    <p class="mb-0">{{ $review->review }}</p>
                </div>
                @endforeach

                <div class="d-flex justify-content-center">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-chat-left-text display-1 text-muted"></i>
                    <h4 class="mt-3">{{ get_phrase('No reviews yet') }}</h4>
                    <p class="text-muted">{{ get_phrase('This vendor has not received any reviews') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
