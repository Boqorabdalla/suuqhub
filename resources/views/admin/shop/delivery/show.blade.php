@extends('layouts.admin')
@section('title', get_phrase('Order Details'))
@section('admin_layout')

<div class="mb-2">
    <a href="{{ route('admin.shop.delivery') }}" class="btn btn-outline-secondary">
        <i class="fi-rr-arrow-left me-2"></i>{{ get_phrase('Back to Deliveries') }}
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="ol-card">
            <div class="ol-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ get_phrase('Order') }} #{{ $order->order_number }}</h5>
                    <span class="badge {{ $order->delivery_status_badge_class }}">{{ $order->delivery_status_label }}</span>
                </div>
            </div>
            <div class="ol-card-body p-20px">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ get_phrase('Product') }}</th>
                            <th>{{ get_phrase('Price') }}</th>
                            <th>{{ get_phrase('Qty') }}</th>
                            <th>{{ get_phrase('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                                @if($item->variation_name)
                                    <br><small class="text-muted">{{ $item->variation_name }}</small>
                                @endif
                            </td>
                            <td>{{ currency($item->price) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ currency($item->subtotal) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end">{{ get_phrase('Subtotal') }}</td>
                            <td>{{ currency($order->subtotal) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">{{ get_phrase('Delivery') }}</td>
                            <td>{{ currency($order->delivery_price) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>{{ get_phrase('Total') }}</strong></td>
                            <td><strong>{{ currency($order->total) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ol-card mb-3">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Customer Info') }}</h5>
            </div>
            <div class="ol-card-body">
                <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                <p class="mb-1 text-muted">{{ $order->customer_email }}</p>
                <p class="mb-0 text-muted">{{ $order->customer_phone }}</p>
            </div>
        </div>

        <div class="ol-card mb-3">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Delivery Address') }}</h5>
            </div>
            <div class="ol-card-body">
                <p class="mb-1">{{ $order->shipping_address ?? '-' }}</p>
                <p class="mb-0 text-muted">{{ $order->shipping_city }} {{ $order->shipping_postal_code }}</p>
            </div>
        </div>

        <div class="ol-card mb-3">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Update Delivery Status') }}</h5>
            </div>
            <div class="ol-card-body">
                <form action="{{ route('admin.shop.delivery.update-status', $order->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <select name="delivery_status" class="form-select ol-form-control">
                            <option value="pending" {{ $order->delivery_status == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending Delivery') }}</option>
                            <option value="picked_up" {{ $order->delivery_status == 'picked_up' ? 'selected' : '' }}>{{ get_phrase('Picked Up') }}</option>
                            <option value="in_transit" {{ $order->delivery_status == 'in_transit' ? 'selected' : '' }}>{{ get_phrase('In Transit') }}</option>
                            <option value="delivered" {{ $order->delivery_status == 'delivered' ? 'selected' : '' }}>{{ get_phrase('Delivered') }}</option>
                            <option value="failed" {{ $order->delivery_status == 'failed' ? 'selected' : '' }}>{{ get_phrase('Delivery Failed') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">{{ get_phrase('Update Status') }}</button>
                </form>
            </div>
        </div>

        <div class="ol-card">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Order Info') }}</h5>
            </div>
            <div class="ol-card-body">
                <p class="mb-1"><strong>{{ get_phrase('Date') }}:</strong> {{ date_formatter($order->created_at) }}</p>
                <p class="mb-1"><strong>{{ get_phrase('Payment') }}:</strong> {{ strtoupper($order->payment_method) }}</p>
                <p class="mb-0"><strong>{{ get_phrase('Status') }}:</strong> <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">{{ $order->payment_status_label }}</span></p>
            </div>
        </div>
    </div>
</div>
@endsection
