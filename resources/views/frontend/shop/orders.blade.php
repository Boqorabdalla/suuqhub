@extends('layouts.frontend')
@push('title', get_phrase('My Orders'))
@push('meta')@endpush
@section('frontend_layout')

<section class="shop-header bg-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2 class="mb-2">{{ get_phrase('My Orders') }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ get_phrase('Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ get_phrase('My Orders') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('shop.track') }}" class="btn btn-outline-primary">
                    <i class="bi bi-search me-2"></i>{{ get_phrase('Track Order') }}
                </a>
            </div>
        </div>
    </div>
</section>

<section class="shop-content py-5">
    <div class="container">
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('shop.orders') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ get_phrase('Search Order') }}</label>
                        <input type="text" name="search" class="form-control" placeholder="{{ get_phrase('Order number...') }}" value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ get_phrase('Status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ get_phrase('All Status') }}</option>
                            <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                            <option value="approved" {{ ($filters['status'] ?? '') == 'approved' ? 'selected' : '' }}>{{ get_phrase('Approved') }}</option>
                            <option value="processing" {{ ($filters['status'] ?? '') == 'processing' ? 'selected' : '' }}>{{ get_phrase('Processing') }}</option>
                            <option value="shipped" {{ ($filters['status'] ?? '') == 'shipped' ? 'selected' : '' }}>{{ get_phrase('Shipped') }}</option>
                            <option value="delivered" {{ ($filters['status'] ?? '') == 'delivered' ? 'selected' : '' }}>{{ get_phrase('Delivered') }}</option>
                            <option value="rejected" {{ ($filters['status'] ?? '') == 'rejected' ? 'selected' : '' }}>{{ get_phrase('Rejected') }}</option>
                            <option value="cancelled" {{ ($filters['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>{{ get_phrase('Cancelled') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ get_phrase('From Date') }}</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ get_phrase('To Date') }}</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-funnel me-2"></i>{{ get_phrase('Filter') }}
                        </button>
                        <a href="{{ route('shop.orders') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>{{ get_phrase('Reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if($orders->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ get_phrase('Order History') }} ({{ $orders->total() }} {{ get_phrase('orders') }})</h5>
                        <a href="{{ route('shop') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-cart me-2"></i>{{ get_phrase('Continue Shopping') }}
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ get_phrase('Order #') }}</th>
                                        <th>{{ get_phrase('Items') }}</th>
                                        <th>{{ get_phrase('Total') }}</th>
                                        <th>{{ get_phrase('Shipping') }}</th>
                                        <th>{{ get_phrase('Payment') }}</th>
                                        <th>{{ get_phrase('Status') }}</th>
                                        <th>{{ get_phrase('Date') }}</th>
                                        <th>{{ get_phrase('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>
                                            @foreach($order->items->take(2) as $item)
                                                <small>{{ $item->product_name }} x{{ $item->quantity }}</small><br>
                                            @endforeach
                                            @if($order->items->count() > 2)
                                                <small class="text-muted">+{{ $order->items->count() - 2 }} more</small>
                                            @endif
                                        </td>
                                        <td><strong>{{ currency($order->total) }}</strong></td>
                                        <td>
                                            @if($order->shipping_method == 'pickup')
                                                <span class="badge bg-info">{{ get_phrase('Pickup') }}</span>
                                            @else
                                                <span class="badge bg-primary">{{ get_phrase('Delivery') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->payment_status == 'paid')
                                                <span class="badge bg-success">{{ get_phrase('Paid') }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ get_phrase('Pending') }}</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">{{ ucfirst($order->payment_method) }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $statusText = '';
                                                $statusClass = 'bg-secondary';
                                                
                                                if ($order->approval_status == 'pending') {
                                                    $statusText = get_phrase('Pending Approval');
                                                    $statusClass = 'bg-warning text-dark';
                                                } elseif ($order->approval_status == 'rejected') {
                                                    $statusText = get_phrase('Rejected');
                                                    $statusClass = 'bg-danger';
                                                } elseif ($order->order_status == 'cancelled') {
                                                    $statusText = get_phrase('Cancelled');
                                                    $statusClass = 'bg-danger';
                                                } elseif ($order->delivery_status == 'delivered') {
                                                    $statusText = get_phrase('Delivered');
                                                    $statusClass = 'bg-success';
                                                } elseif (in_array($order->delivery_status, ['picked_up', 'in_transit'])) {
                                                    $statusText = get_phrase('Shipped');
                                                    $statusClass = 'bg-primary';
                                                } elseif ($order->delivery_status == 'pending' && $order->approval_status == 'approved') {
                                                    $statusText = get_phrase('Processing');
                                                    $statusClass = 'bg-info';
                                                } else {
                                                    $statusText = get_phrase('Approved');
                                                    $statusClass = 'bg-success';
                                                }
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            <small>{{ date_formatter($order->created_at) }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('shop.order', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>{{ get_phrase('View') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-box-seam text-muted" style="font-size: 96px;"></i>
            <h3 class="mt-4 text-muted">{{ get_phrase('No orders found') }}</h3>
            <p class="text-muted">
                @if(request()->has('search') || request()->has('status'))
                    {{ get_phrase('Try adjusting your filters') }}
                @else
                    {{ get_phrase('Start shopping to see your orders here!') }}
                @endif
            </p>
            @if(request()->has('search') || request()->has('status'))
                <a href="{{ route('shop.orders') }}" class="btn btn-outline-primary mt-3">
                    <i class="bi bi-x-circle me-2"></i>{{ get_phrase('Clear Filters') }}
                </a>
            @else
                <a href="{{ route('shop') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-cart me-2"></i>{{ get_phrase('Browse Products') }}
                </a>
            @endif
        </div>
        @endif
    </div>
</section>

@endsection
