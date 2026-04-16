@extends('layouts.admin')
@push('title', get_phrase('Order Manager Dashboard'))
@push('meta')@endpush
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body p-4">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="title fs-16px mb-0">
                <i class="fi-rr-truck me-2"></i>{{ get_phrase('Order Manager Dashboard') }}
            </h4>
            <span class="badge bg-primary fs-14px">{{ get_phrase('Order Management Only') }}</span>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="ol-card radius-8px h-100">
            <div class="ol-card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-muted small">{{ get_phrase('Pending Approval') }}</p>
                        <h3 class="mb-0 fw-bold">{{ $pendingOrders }}</h3>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-10">
                        <i class="fi-rr-clock text-warning fs-24px"></i>
                    </div>
                </div>
                <a href="{{ route('order.manager.approval') }}" class="btn btn-sm btn-outline-primary mt-3 w-100">
                    {{ get_phrase('View All') }}
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="ol-card radius-8px h-100">
            <div class="ol-card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-muted small">{{ get_phrase('Approved Orders') }}</p>
                        <h3 class="mb-0 fw-bold">{{ $approvedOrders }}</h3>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10">
                        <i class="fi-rr-check-circle text-success fs-24px"></i>
                    </div>
                </div>
                <a href="{{ route('order.manager.approval') }}" class="btn btn-sm btn-outline-success mt-3 w-100">
                    {{ get_phrase('View All') }}
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="ol-card radius-8px h-100">
            <div class="ol-card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-muted small">{{ get_phrase('Pending Delivery') }}</p>
                        <h3 class="mb-0 fw-bold">{{ $deliveryPending }}</h3>
                    </div>
                    <div class="icon-box bg-info bg-opacity-10">
                        <i class="fi-rr-boxes text-info fs-24px"></i>
                    </div>
                </div>
                <a href="{{ route('order.manager.delivery') }}" class="btn btn-sm btn-outline-info mt-3 w-100">
                    {{ get_phrase('View All') }}
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="ol-card radius-8px h-100">
            <div class="ol-card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-muted small">{{ get_phrase('Delivered') }}</p>
                        <h3 class="mb-0 fw-bold">{{ $deliveredOrders }}</h3>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10">
                        <i class="fi-rr-home Location text-primary fs-24px"></i>
                    </div>
                </div>
                <a href="{{ route('order.manager.delivery') }}" class="btn btn-sm btn-outline-primary mt-3 w-100">
                    {{ get_phrase('View All') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="ol-card radius-8px">
            <div class="ol-card-header d-flex justify-content-between align-items-center p-3">
                <h5 class="mb-0">{{ get_phrase('Quick Actions') }}</h5>
            </div>
            <div class="ol-card-body p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('order.manager.approval') }}" class="btn btn-outline-warning w-100 py-3">
                            <i class="fi-rr-clock me-2"></i>{{ get_phrase('Approve Orders') }}
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('order.manager.delivery') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="fi-rr-truck me-2"></i>{{ get_phrase('Manage Deliveries') }}
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('order.manager.delivery.settings') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="fi-rr-settings me-2"></i>{{ get_phrase('Delivery Settings') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="ol-card radius-8px">
            <div class="ol-card-header d-flex justify-content-between align-items-center p-3">
                <h5 class="mb-0">{{ get_phrase('Delivery Statistics') }}</h5>
            </div>
            <div class="ol-card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>{{ get_phrase('In Transit') }}</span>
                    <span class="badge bg-primary">{{ $deliveryInTransit }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>{{ get_phrase('Total Orders') }}</span>
                    <span class="badge bg-secondary">{{ $totalOrders }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>{{ get_phrase('Delivery Price') }}</span>
                    <span class="badge bg-success">{{ currency($delivery_price ?? 5) }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="ol-card radius-8px">
            <div class="ol-card-header d-flex justify-content-between align-items-center p-3">
                <h5 class="mb-0">{{ get_phrase('Order Summary') }}</h5>
            </div>
            <div class="ol-card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>{{ get_phrase('Pending Approval') }}</span>
                    <span class="badge bg-warning text-dark">{{ $pendingOrders }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>{{ get_phrase('Approved') }}</span>
                    <span class="badge bg-success">{{ $approvedOrders }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>{{ get_phrase('Delivered') }}</span>
                    <span class="badge bg-primary">{{ $deliveredOrders }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@endsection
