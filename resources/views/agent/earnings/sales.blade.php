@extends('layouts.admin')
@push('title')
{{ get_phrase('Sales Report') }}
@endpush
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ get_phrase('Sales Report') }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('agent.earnings') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>{{ get_phrase('Back to Earnings') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="text-white-50">{{ get_phrase('Total Revenue') }}</h6>
                            <h3 class="mb-0">{{ currency($stats['total_revenue']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <h6 class="text-white-50">{{ get_phrase('Total Orders') }}</h6>
                            <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <h6 class="text-white-50">{{ get_phrase('Total Items Sold') }}</h6>
                            <h3 class="mb-0">{{ $stats['total_items'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <!-- Date Filter -->
                    <form action="{{ route('agent.earnings.sales') }}" method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">{{ get_phrase('From Date') }}</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ get_phrase('To Date') }}</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-funnel me-2"></i>{{ get_phrase('Filter') }}
                            </button>
                            <a href="{{ route('agent.earnings.sales') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </form>

                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ get_phrase('Order #') }}</th>
                                        <th>{{ get_phrase('Items') }}</th>
                                        <th>{{ get_phrase('Total') }}</th>
                                        <th>{{ get_phrase('Status') }}</th>
                                        <th>{{ get_phrase('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td><code>{{ $order->order_number }}</code></td>
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
                                            @if($order->delivery_status == 'delivered')
                                                <span class="badge bg-success">{{ get_phrase('Delivered') }}</span>
                                            @elseif(in_array($order->delivery_status, ['picked_up', 'in_transit']))
                                                <span class="badge bg-primary">{{ get_phrase('Shipped') }}</span>
                                            @elseif($order->approval_status == 'approved')
                                                <span class="badge bg-info">{{ get_phrase('Processing') }}</span>
                                            @elseif($order->approval_status == 'pending')
                                                <span class="badge bg-warning text-dark">{{ get_phrase('Pending') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($order->approval_status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ date_formatter($order->created_at) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bar-chart text-muted" style="font-size: 64px;"></i>
                            <h5 class="mt-3 text-muted">{{ get_phrase('No sales found') }}</h5>
                            <p class="text-muted">{{ get_phrase('Try adjusting your date filters') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
