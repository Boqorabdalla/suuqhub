@extends('layouts.admin')
@push('title', get_phrase('Delivery Order Details'))
@push('meta')@endpush
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-20px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-truck me-2"></i>{{ get_phrase('Order') }}: {{ $order->order_number }}
            </h4>
            <a href="{{ route('order.manager.delivery') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fi-rr-arrow-left me-1"></i>{{ get_phrase('Back') }}
            </a>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-lg-8">
        <div class="ol-card mb-3">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Order Items') }}</h5>
            </div>
            <div class="ol-card-body p-0">
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
                <div class="ol-card">
                    <div class="ol-card-header">
                        <h5 class="mb-0">{{ get_phrase('Customer Information') }}</h5>
                    </div>
                    <div class="ol-card-body">
                        <p class="mb-1"><strong>{{ get_phrase('Name') }}:</strong> {{ $order->customer_name }}</p>
                        <p class="mb-1"><strong>{{ get_phrase('Email') }}:</strong> {{ $order->customer_email }}</p>
                        <p class="mb-0"><strong>{{ get_phrase('Phone') }}:</strong> {{ $order->customer_phone }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="ol-card">
                    <div class="ol-card-header">
                        <h5 class="mb-0">{{ get_phrase('Delivery Address') }}</h5>
                    </div>
                    <div class="ol-card-body">
                        <p class="mb-1"><strong>{{ get_phrase('Address') }}:</strong> {{ $order->shipping_address ?? '-' }}</p>
                        <p class="mb-0"><strong>{{ get_phrase('City') }}:</strong> {{ $order->shipping_city ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="ol-card">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Order Summary') }}</h5>
            </div>
            <div class="ol-card-body">
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
                    <strong class="text-primary">{{ currency($order->total) }}</strong>
                </div>
            </div>
        </div>
        
        <div class="ol-card mt-3">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Delivery Status') }}</h5>
            </div>
            <div class="ol-card-body">
                <div class="mb-3 text-center">
                    @php
                        $statusClass = match($order->delivery_status) {
                            'pending' => 'bg-warning text-dark',
                            'picked_up' => 'bg-info',
                            'in_transit' => 'bg-primary',
                            'delivered' => 'bg-success',
                            'failed' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }} fs-5 px-3 py-2">{{ $order->delivery_status_label }}</span>
                </div>
                
                <form action="{{ route('order.manager.delivery.update-status', $order->id) }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Update Status') }}</label>
                        <select name="delivery_status" class="form-control ol-form-control" required>
                            <option value="pending" {{ $order->delivery_status == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                            <option value="picked_up" {{ $order->delivery_status == 'picked_up' ? 'selected' : '' }}>{{ get_phrase('Picked Up') }}</option>
                            <option value="in_transit" {{ $order->delivery_status == 'in_transit' ? 'selected' : '' }}>{{ get_phrase('In Transit') }}</option>
                            <option value="delivered" {{ $order->delivery_status == 'delivered' ? 'selected' : '' }}>{{ get_phrase('Delivered') }}</option>
                            <option value="failed" {{ $order->delivery_status == 'failed' ? 'selected' : '' }}>{{ get_phrase('Failed') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fi-rr-check me-1"></i>{{ get_phrase('Update Status') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
