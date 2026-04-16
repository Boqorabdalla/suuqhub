@extends('layouts.admin')
@section('title', get_phrase('Delivery Orders'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-delivery-truck me-2"></i>
                {{ get_phrase('Delivery Orders') }}
            </h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.shop.delivery.settings') }}" class="btn ol-btn-outline-secondary">
                    <i class="fi-rr-settings me-2"></i>{{ get_phrase('Settings') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-20px">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="alert alert-info mb-0">
                    <strong>{{ get_phrase('Delivery Price') }}:</strong> {{ currency($delivery_price) }}
                </div>
            </div>
        </div>

        <form action="{{ route('admin.shop.delivery') }}" method="get" class="mb-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control ol-form-control" placeholder="{{ get_phrase('Search order number, name, phone...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select ol-form-control" onchange="this.form.submit()">
                        <option value="">{{ get_phrase('All Status') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                        <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>{{ get_phrase('Picked Up') }}</option>
                        <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>{{ get_phrase('In Transit') }}</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>{{ get_phrase('Delivered') }}</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ get_phrase('Failed') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">{{ get_phrase('Filter') }}</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ get_phrase('Order #') }}</th>
                        <th>{{ get_phrase('Customer') }}</th>
                        <th>{{ get_phrase('Items') }}</th>
                        <th>{{ get_phrase('Total') }}</th>
                        <th>{{ get_phrase('Delivery Status') }}</th>
                        <th>{{ get_phrase('Date') }}</th>
                        <th>{{ get_phrase('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>
                            {{ $order->customer_name }}<br>
                            <small class="text-muted">{{ $order->customer_phone }}</small>
                        </td>
                        <td>{{ $order->items->count() }} {{ get_phrase('items') }}</td>
                        <td><strong>{{ currency($order->total) }}</strong></td>
                        <td>
                            <span class="badge {{ $order->delivery_status_badge_class }}">{{ $order->delivery_status_label }}</span>
                        </td>
                        <td>{{ date_formatter($order->created_at) }}</td>
                        <td>
                            <a href="{{ route('admin.shop.delivery.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fi-rr-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fi-rr-box-open text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2">{{ get_phrase('No delivery orders found') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection
