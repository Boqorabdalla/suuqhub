@extends('layouts.admin')
@section('title', get_phrase('Shop Orders'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-shopping-bag me-2"></i>
                {{ get_phrase('Shop Orders') }}
            </h4>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-3">
        <div class="row mb-3">
            <div class="col-md-4">
                <form action="{{ route('admin.shop.orders') }}" method="get">
                    <select name="status" class="form-control ol-form-control" onchange="this.form.submit()">
                        <option value="">{{ get_phrase('All Orders') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ get_phrase('Processing') }}</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>{{ get_phrase('Shipped') }}</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>{{ get_phrase('Delivered') }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ get_phrase('Cancelled') }}</option>
                    </select>
                </form>
            </div>
        </div>

        @if(count($orders))
        <table id="datatable" class="table nowrap w-100">
            <thead>
                <tr>
                    <th>{{ get_phrase('ID') }}</th>
                    <th>{{ get_phrase('Order #') }}</th>
                    <th>{{ get_phrase('Customer') }}</th>
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
                @php $num = 1 @endphp
                @foreach($orders as $order)
                <tr>
                    <td>{{ $num++ }}</td>
                    <td><strong>{{ $order->order_number }}</strong></td>
                    <td>
                        <p class="sub-title2 text-13px">{{ $order->customer_name }}</p>
                        <p class="text-muted text-11px">{{ $order->customer_phone }}</p>
                    </td>
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
                            @if($order->shipping_address)
                                <br><small class="text-muted">{{ $order->shipping_city ?? '' }}</small>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($order->payment_status == 'paid')
                            <span class="badge bg-success">{{ get_phrase('Paid') }}</span>
                        @elseif($order->payment_status == 'pending')
                            <span class="badge bg-warning">{{ get_phrase('Pending') }}</span>
                        @else
                            <span class="badge bg-danger">{{ get_phrase('Failed') }}</span>
                        @endif
                        <br>
                        <small class="text-muted">{{ ucfirst($order->payment_method) }}</small>
                    </td>
                    <td>
                        @php
                            $statusClass = match($order->order_status) {
                                'pending' => 'bg-warning',
                                'processing' => 'bg-info',
                                'shipped' => 'bg-primary',
                                'delivered' => 'bg-success',
                                'cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($order->order_status) }}</span>
                    </td>
                    <td>{{ date_formatter($order->created_at) }}</td>
                    <td>
                        <div class="dropdown ol-icon-dropdown">
                            <button class="px-2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="fi-rr-menu-dots-vertical"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item fs-14px" href="{{ route('admin.shop.order.show', $order->id) }}"><i class="fi-rr-eye me-2"></i>{{ get_phrase('View') }}</a></li>
                                <li><a class="dropdown-item fs-14px" onclick="delete_modal('{{ route('admin.shop.order.delete', $order->id) }}')"><i class="fi-rr-trash me-2"></i>{{ get_phrase('Delete') }}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
        @else
            @include('layouts.no_data_found')
        @endif
    </div>
</div>

@endsection
