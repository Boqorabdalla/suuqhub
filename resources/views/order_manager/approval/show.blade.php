@extends('layouts.admin')
@push('title', get_phrase('Order Details'))
@push('meta')@endpush
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-20px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-truck me-2"></i>{{ get_phrase('Order') }}: {{ $order->order_number }}
            </h4>
            <a href="{{ route('order.manager.approval') }}" class="btn btn-outline-secondary btn-sm">
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
                        <h5 class="mb-0">{{ get_phrase('Shipping Information') }}</h5>
                    </div>
                    <div class="ol-card-body">
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
                            <p class="mb-0"><strong>{{ get_phrase('City') }}:</strong> {{ $order->shipping_city }}</p>
                        @endif
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
                <h5 class="mb-0">{{ get_phrase('Approval Action') }}</h5>
            </div>
            <div class="ol-card-body">
                <div class="d-flex gap-2">
                    <form action="{{ route('order.manager.approval.approve', $order->id) }}" method="post" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fi-rr-check-circle me-1"></i>{{ get_phrase('Approve') }}
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fi-rr-times-circle me-1"></i>{{ get_phrase('Reject') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('order.manager.approval.reject', $order->id) }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">{{ get_phrase('Reject Order') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Rejection Reason') }} *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="{{ get_phrase('Please provide a reason for rejection...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ get_phrase('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ get_phrase('Reject Order') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
