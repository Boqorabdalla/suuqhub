@extends('layouts.admin')
@push('title', get_phrase('All Orders'))
@push('meta')@endpush
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-20px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-list me-2"></i>{{ get_phrase('All Orders') }}
            </h4>
            <a href="{{ route('order.manager.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fi-rr-arrow-left me-1"></i>{{ get_phrase('Back') }}
            </a>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body">
        <div class="row mb-3">
            <div class="col-md-8">
                <form action="{{ route('order.manager.orders') }}" method="get" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control ol-form-control" placeholder="{{ get_phrase('Search by order number, name...') }}" value="{{ request('search') }}">
                    <select name="status" class="form-control ol-form-control" style="max-width: 150px;">
                        <option value="">{{ get_phrase('All Status') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ get_phrase('Processing') }}</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>{{ get_phrase('Shipped') }}</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>{{ get_phrase('Delivered') }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ get_phrase('Cancelled') }}</option>
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="fi-rr-search"></i></button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('order.manager.orders') }}" class="btn btn-secondary"><i class="fi-rr-times"></i></a>
                    @endif
                </form>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ get_phrase('Order') }}</th>
                        <th>{{ get_phrase('Customer') }}</th>
                        <th>{{ get_phrase('Items') }}</th>
                        <th>{{ get_phrase('Total') }}</th>
                        <th>{{ get_phrase('Status') }}</th>
                        <th class="text-center">{{ get_phrase('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>
                            <div>{{ $order->customer_name }}</div>
                            <small class="text-muted">{{ $order->customer_email }}</small>
                        </td>
                        <td>{{ $order->items->count() }} {{ get_phrase('items') }}</td>
                        <td><strong>{{ currency($order->total) }}</strong></td>
                        <td>
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
                            <span class="badge {{ $statusClass }}">{{ ucfirst($order->order_status) }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('order.manager.order.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fi-rr-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fi-rr-inbox fs-48px d-block mb-2"></i>
                            {{ get_phrase('No orders found') }}
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
