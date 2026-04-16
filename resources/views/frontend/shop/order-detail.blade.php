@extends('layouts.frontend')
@push('title', get_phrase('Order Details'))
@push('meta')@endpush
@section('frontend_layout')

<section class="shop-header bg-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2 class="mb-2">{{ get_phrase('Order') }} #{{ $order->order_number }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ get_phrase('Home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shop.orders') }}">{{ get_phrase('My Orders') }}</a></li>
                        <li class="breadcrumb-item active">{{ $order->order_number }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="shop-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ get_phrase('Order Items') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ get_phrase('Product') }}</th>
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
                                        @if($item->variation_name)
                                            <br><small class="text-muted">{{ $item->variation_name }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ currency($item->price) }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ currency($item->subtotal) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ get_phrase('Customer Information') }}</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>{{ get_phrase('Name') }}:</strong> {{ $order->customer_name }}</p>
                                <p class="mb-1"><strong>{{ get_phrase('Email') }}:</strong> {{ $order->customer_email }}</p>
                                <p class="mb-0"><strong>{{ get_phrase('Phone') }}:</strong> {{ $order->customer_phone }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ get_phrase('Shipping Information') }}</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-1">
                                    <strong>{{ get_phrase('Method') }}:</strong>
                                    @if($order->shipping_method == 'pickup')
                                        <span class="badge bg-info">{{ get_phrase('Store Pickup') }}</span>
                                    @else
                                        <span class="badge bg-primary">{{ get_phrase('Delivery') }}</span>
                                    @endif
                                </p>
                                @if($order->shipping_address)
                                    <p class="mb-1"><strong>{{ get_phrase('Address') }}:</strong> {{ $order->shipping_address }}</p>
                                    <p class="mb-1"><strong>{{ get_phrase('City') }}:</strong> {{ $order->shipping_city }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
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
                        <div class="d-flex justify-content-between mb-0">
                            <strong>{{ get_phrase('Total') }}</strong>
                            <strong class="text-primary fs-5">{{ currency($order->total) }}</strong>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">{{ get_phrase('Order Status') }}</h5>
                    </div>
                    <div class="card-body text-center">
                        @php
                            $statusClass = match($order->order_status) {
                                'pending' => 'bg-warning text-dark',
                                'processing' => 'bg-info',
                                'shipped' => 'bg-primary',
                                'delivered' => 'bg-success',
                                'cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <div class="mb-3">
                            <small class="text-muted d-block">{{ get_phrase('Order Status') }}</small>
                            <span class="badge {{ $statusClass }} fs-6">{{ ucfirst($order->order_status) }}</span>
                        </div>
                        
                        @php
                            $paymentClass = match($order->payment_status) {
                                'paid' => 'bg-success',
                                'pending' => 'bg-warning text-dark',
                                'failed' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <div class="mb-3">
                            <small class="text-muted d-block">{{ get_phrase('Payment Status') }}</small>
                            <span class="badge {{ $paymentClass }}">{{ ucfirst($order->payment_status) }}</span>
                        </div>

                        @if($order->shipping_method == 'delivery')
                            <div class="mb-3">
                                <small class="text-muted d-block">{{ get_phrase('Approval Status') }}</small>
                                <span class="badge {{ $order->approval_badge_class }}">{{ $order->approval_status_label }}</span>
                                @if($order->approval_status == 'rejected' && $order->rejection_reason)
                                    <div class="mt-2 text-start">
                                        <small class="text-danger"><i class="fi-rr-info me-1"></i>{{ get_phrase('Reason') }}: {{ $order->rejection_reason }}</small>
                                    </div>
                                @endif
                            </div>

                            @if($order->approval_status == 'approved')
                                <div class="mb-3">
                                    <small class="text-muted d-block">{{ get_phrase('Delivery Status') }}</small>
                                    <span class="badge {{ $order->delivery_status_badge_class }}">{{ $order->delivery_status_label }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body text-center">
                        <small class="text-muted">{{ get_phrase('Order Date') }}</small>
                        <p class="mb-0 fw-bold">{{ date_formatter($order->created_at) }}</p>
                    </div>
                </div>

                <a href="{{ route('shop.orders') }}" class="btn btn-outline-primary w-100 mt-3">
                    <i class="fi-rr-arrow-left me-2"></i>{{ get_phrase('Back to Orders') }}
                </a>
                <a href="{{ route('shop') }}" class="btn btn-primary w-100 mt-2">
                    <i class="fi-rr-shop me-2"></i>{{ get_phrase('Continue Shopping') }}
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
