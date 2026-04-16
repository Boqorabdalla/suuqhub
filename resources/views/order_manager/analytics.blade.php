@extends('layouts.admin')
@push('title')
{{ get_phrase('Analytics Dashboard') }}
@endpush
@push('styles')
<style>
    .stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-icon { width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .growth-badge { font-size: 12px; padding: 4px 8px; }
</style>
@endpush
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ get_phrase('Analytics Dashboard') }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('order.manager.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>{{ get_phrase('Back to Dashboard') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('order.manager.analytics') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ get_phrase('From Date') }}</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ get_phrase('To Date') }}</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-filter me-2"></i>{{ get_phrase('Apply Filter') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card stat-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50">{{ get_phrase('Total Sales') }}</h6>
                                    <h3 class="mb-1">{{ currency($totalSales) }}</h3>
                                    @if($salesGrowth != 0)
                                        <span class="badge growth-badge {{ $salesGrowth >= 0 ? 'bg-success' : 'bg-danger' }}">
                                            <i class="bi bi-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i>
                                            {{ round(abs($salesGrowth), 1) }}%
                                        </span>
                                    @endif
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-currency-dollar text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50">{{ get_phrase('Total Orders') }}</h6>
                                    <h3 class="mb-1">{{ $totalOrders }}</h3>
                                    @if($ordersGrowth != 0)
                                        <span class="badge growth-badge {{ $ordersGrowth >= 0 ? 'bg-light text-success' : 'bg-danger' }}">
                                            <i class="bi bi-arrow-{{ $ordersGrowth >= 0 ? 'up' : 'down' }}"></i>
                                            {{ round(abs($ordersGrowth), 1) }}%
                                        </span>
                                    @endif
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-bag text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50">{{ get_phrase('Avg Order Value') }}</h6>
                                    <h3 class="mb-1">{{ currency($avgOrderValue) }}</h3>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-receipt text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-dark-50">{{ get_phrase('Delivered Orders') }}</h6>
                                    <h3 class="mb-1 text-dark">{{ $deliveredOrders }}</h3>
                                </div>
                                <div class="stat-icon bg-dark bg-opacity-25">
                                    <i class="bi bi-truck text-dark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Order Status Breakdown -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ get_phrase('Order Status Breakdown') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <span class="badge bg-warning text-dark">{{ get_phrase('Pending') }}</span>
                                    <strong>{{ $pendingOrders }}</strong>
                                </div>
                                <div>
                                    <span class="badge bg-success">{{ get_phrase('Approved') }}</span>
                                    <strong>{{ $approvedOrders }}</strong>
                                </div>
                                <div>
                                    <span class="badge bg-primary">{{ get_phrase('Delivered') }}</span>
                                    <strong>{{ $deliveredOrders }}</strong>
                                </div>
                                <div>
                                    <span class="badge bg-danger">{{ get_phrase('Cancelled') }}</span>
                                    <strong>{{ $cancelledOrders }}</strong>
                                </div>
                            </div>
                            <div class="progress" style="height: 20px;">
                                @php $total = $pendingOrders + $approvedOrders + $deliveredOrders + $cancelledOrders; @endphp
                                @if($total > 0)
                                    <div class="progress-bar bg-warning" style="width: {{ ($pendingOrders / $total) * 100 }}%"></div>
                                    <div class="progress-bar bg-success" style="width: {{ ($approvedOrders / $total) * 100 }}%"></div>
                                    <div class="progress-bar bg-primary" style="width: {{ ($deliveredOrders / $total) * 100 }}%"></div>
                                    <div class="progress-bar bg-danger" style="width: {{ ($cancelledOrders / $total) * 100 }}%"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping & Payment Methods -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ get_phrase('Shipping & Payment Methods') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <h6 class="text-muted mb-2">{{ get_phrase('Shipping') }}</h6>
                                    <div class="mb-2">
                                        <i class="bi bi-truck text-primary"></i> {{ get_phrase('Delivery') }}: <strong>{{ $deliveryOrders }}</strong>
                                    </div>
                                    <div>
                                        <i class="bi bi-shop text-info"></i> {{ get_phrase('Pickup') }}: <strong>{{ $pickupOrders }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-muted mb-2">{{ get_phrase('Payment') }}</h6>
                                    <div class="mb-2">
                                        <i class="bi bi-cash text-success"></i> COD: <strong>{{ $codOrders }}</strong>
                                    </div>
                                    <div>
                                        <i class="bi bi-credit-card text-warning"></i> {{ get_phrase('Online') }}: <strong>{{ $onlineOrders }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Selling Products -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ get_phrase('Top Selling Products') }}</h5>
                        </div>
                        <div class="card-body">
                            @if($topProducts->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ get_phrase('Product') }}</th>
                                                <th>{{ get_phrase('Quantity Sold') }}</th>
                                                <th>{{ get_phrase('Revenue') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topProducts as $index => $product)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $product->product_name }}</strong></td>
                                                <td><span class="badge bg-primary">{{ $product->total_sold }}</span></td>
                                                <td><strong>{{ currency($product->total_revenue) }}</strong></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-bar-chart" style="font-size: 48px;"></i>
                                    <p class="mt-2">{{ get_phrase('No sales data available') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
