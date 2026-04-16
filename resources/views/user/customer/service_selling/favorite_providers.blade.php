@extends('layouts.frontend')
@push('title', get_phrase('Favorite Service Providers'))
@push('meta')@endpush
@section('frontend_layout')

<style>
    .provider-card {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
        background: white;
    }
    .provider-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .favorite-btn {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .provider-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #6c757d;
    }
</style>

<section class="ca-wraper-main mb-90px mt-4">
    <div class="container">
        <div class="row gx-20px">
            <div class="col-lg-4 col-xl-3">
                @include('user.navigation')
            </div>
            <div class="col-lg-8 col-xl-9">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-20px">
                    <div class="d-flex justify-content-between align-items-start gap-12px flex-column flex-lg-row w-100">
                        <h1 class="ca-title-18px">{{get_phrase('Favorite Service Providers')}}</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb cap-breadcrumb">
                                <li class="breadcrumb-item cap-breadcrumb-item"><a href="{{route('home')}}">{{get_phrase('Home')}}</a></li>
                                <li class="breadcrumb-item cap-breadcrumb-item active" aria-current="page">{{get_phrase('Favorites')}}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <div class="ca-content-card">
                    @if($favorites->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-heart fs-1 text-muted"></i>
                            <p class="text-muted mt-3">{{ get_phrase('You have no favorite providers yet') }}</p>
                            <a href="{{route('home')}}" class="btn btn-primary">{{ get_phrase('Browse Services') }}</a>
                        </div>
                    @else
                        <div class="row">
                            @foreach($favorites as $favorite)
                                @php
                                    $employee = $favorite->employee;
                                    if (!$employee) continue;
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="provider-card position-relative">
                                        <div class="d-flex align-items-center">
                                            <div class="provider-avatar me-3">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">{{ $employee->name }}</h5>
                                                <p class="text-muted mb-1">
                                                    <i class="bi bi-envelope"></i> {{ $employee->email ?? 'N/A' }}
                                                </p>
                                                <p class="text-muted mb-0">
                                                    <i class="bi bi-telephone"></i> {{ $employee->phone ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-3 d-flex gap-2">
                                            @if($favorite->listing_id)
                                                <a href="{{ route('service.slot', ['type' => 'beauty', 'listing_id' => $favorite->listing_id, 'id' => $employee->id]) }}" class="btn btn-sm btn-primary">
                                                    {{ get_phrase('Book Now') }}
                                                </a>
                                            @endif
                                            <a href="{{ route('customer.remove_favorite', ['id' => $favorite->id]) }}" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-heart-fill text-danger"></i> {{ get_phrase('Remove') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
