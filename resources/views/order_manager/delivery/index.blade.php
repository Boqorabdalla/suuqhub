@extends('layouts.admin')
@push('title', get_phrase('Delivery Orders'))
@push('meta')@endpush
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-20px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-truck me-2"></i>{{ get_phrase('Delivery Orders') }}
            </h4>
            <div class="d-flex gap-2">
                <a href="{{ route('order.manager.delivery.settings') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fi-rr-settings me-1"></i>{{ get_phrase('Settings') }}
                </a>
                <a href="{{ route('order.manager.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fi-rr-arrow-left me-1"></i>{{ get_phrase('Back') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body">
        <div class="row mb-3">
            <div class="col-md-8">
                <form action="{{ route('order.manager.delivery') }}" method="get" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control ol-form-control" placeholder="{{ get_phrase('Search by order number, name, phone...') }}" value="{{ request('search') }}">
                    <select name="status" class="form-control ol-form-control" style="max-width: 150px;">
                        <option value="">{{ get_phrase('All Status') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                        <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>{{ get_phrase('Picked Up') }}</option>
                        <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>{{ get_phrase('In Transit') }}</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>{{ get_phrase('Delivered') }}</option>
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="fi-rr-search"></i></button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('order.manager.delivery') }}" class="btn btn-secondary"><i class="fi-rr-times"></i></a>
                    @endif
                </form>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-primary fs-14px px-3 py-2">
                    {{ get_phrase('Delivery Price') }}: {{ currency($delivery_price) }}
                </span>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ get_phrase('Order') }}</th>
                        <th>{{ get_phrase('Customer') }}</th>
                        <th>{{ get_phrase('Address') }}</th>
                        <th>{{ get_phrase('Total') }}</th>
                        <th>{{ get_phrase('Approval') }}</th>
                        <th>{{ get_phrase('Delivery Status') }}</th>
                        <th class="text-center">{{ get_phrase('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>
                            <div>{{ $order->customer_name }}</div>
                            <small class="text-muted">{{ $order->customer_phone }}</small>
                        </td>
                        <td>
                            <small>{{ $order->shipping_address ?? '-' }}</small>
                        </td>
                        <td><strong>{{ currency($order->total) }}</strong></td>
                        <td>
                            @php
                                $approvalClass = match($order->approval_status) {
                                    'pending' => 'bg-warning text-dark',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $approvalClass }}">{{ ucfirst($order->approval_status) }}</span>
                        </td>
                        <td>
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
                            <span class="badge {{ $statusClass }}">{{ $order->delivery_status_label }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('order.manager.delivery.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fi-rr-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fi-rr-inbox fs-48px d-block mb-2"></i>
                            {{ get_phrase('No delivery orders found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{ $orders->links() }}
    </div>
</div>

@endsection
