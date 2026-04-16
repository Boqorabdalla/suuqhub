@extends('layouts.admin')
@section('title', get_phrase('Order Details'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-shopping-bag me-2"></i>
                {{ get_phrase('Order') }} #{{ $order->order_number }}
            </h4>
            <a href="{{ route('admin.shop.orders') }}" class="btn ol-btn-outline-secondary">
                <i class="fi-rr-arrow-left me-2"></i>{{ get_phrase('Back') }}
            </a>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-20px">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">{{ get_phrase('Order Items') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ get_phrase('Product') }}</th>
                                    <th>{{ get_phrase('Variation') }}</th>
                                    <th class="text-center">{{ get_phrase('Price') }}</th>
                                    <th class="text-center">{{ get_phrase('Qty') }}</th>
                                    <th class="text-end">{{ get_phrase('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                    </td>
                                    <td>
                                        @if($item->variation_name)
                                            <small class="text-muted">{{ $item->variation_name }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">{{ currency($item->price) }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end"><strong>{{ currency($item->subtotal) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">{{ get_phrase('Customer Information') }}</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>{{ get_phrase('Name') }}:</strong> {{ $order->customer_name }}</p>
                                <p><strong>{{ get_phrase('Email') }}:</strong> {{ $order->customer_email }}</p>
                                <p><strong>{{ get_phrase('Phone') }}:</strong> {{ $order->customer_phone }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">{{ get_phrase('Shipping Information') }}</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>{{ get_phrase('Method') }}:</strong> 
                                    @if($order->shipping_method == 'pickup')
                                        <span class="badge bg-info">{{ get_phrase('Store Pickup') }}</span>
                                    @else
                                        <span class="badge bg-primary">{{ get_phrase('Delivery') }}</span>
                                    @endif
                                </p>
                                @if($order->shipping_address)
                                    <p><strong>{{ get_phrase('Address') }}:</strong> {{ $order->shipping_address }}</p>
                                    <p><strong>{{ get_phrase('City') }}:</strong> {{ $order->shipping_city }}</p>
                                    @if($order->shipping_postal_code)
                                        <p><strong>{{ get_phrase('Postal Code') }}:</strong> {{ $order->shipping_postal_code }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">{{ get_phrase('Order Summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ get_phrase('Subtotal') }}</span>
                            <span>{{ currency($order->subtotal) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ get_phrase('Shipping') }}</span>
                            <span>{{ currency($order->shipping_cost) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>{{ get_phrase('Total') }}</strong>
                            <strong>{{ currency($order->total) }}</strong>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">{{ get_phrase('Order Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.shop.order.status', $order->id) }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Order Status') }}</label>
                                <select name="order_status" class="form-control ol-form-control">
                                    <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                                    <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>{{ get_phrase('Processing') }}</option>
                                    <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>{{ get_phrase('Shipped') }}</option>
                                    <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>{{ get_phrase('Delivered') }}</option>
                                    <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>{{ get_phrase('Cancelled') }}</option>
                                </select>
                            </div>
                            <button type="submit" class="btn ol-btn-primary btn-sm w-100">{{ get_phrase('Update Status') }}</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">{{ get_phrase('Payment Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.shop.order.payment', $order->id) }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <select name="payment_status" class="form-control ol-form-control">
                                    <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                                    <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>{{ get_phrase('Paid') }}</option>
                                    <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>{{ get_phrase('Failed') }}</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">{{ get_phrase('Method') }}: {{ ucfirst($order->payment_method) }}</small>
                            </div>
                            <button type="submit" class="btn ol-btn-outline-primary btn-sm w-100">{{ get_phrase('Update Payment') }}</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body text-center">
                        <small class="text-muted">{{ get_phrase('Order Date') }}</small>
                        <p class="mb-0">{{ date_formatter($order->created_at) }}</p>
                        <small class="text-muted">{{ get_phrase('Last Updated') }}</small>
                        <p class="mb-0">{{ date_formatter($order->updated_at) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
