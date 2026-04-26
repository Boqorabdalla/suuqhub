@extends('layouts.admin')
@push('title')
{{ get_phrase('Order Details') }}
@endpush
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ get_phrase('Order') }} #{{ $order->order_number }}</h4>
                        <a href="{{ route('agent.shop.orders') }}" class="btn btn-secondary">
                            {{ get_phrase('Back to Orders') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ get_phrase('Order Items') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ get_phrase('Item') }}</th>
                                            <th>{{ get_phrase('Price') }}</th>
                                            <th>{{ get_phrase('Qty') }}</th>
                                            <th>{{ get_phrase('Subtotal') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($order->items as $item)
                                        <tr>
                                            <td>
                                                {{ $item->product_name }}
                                            </td>
                                            <td>{{ currency($item->unit_price) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ currency($item->subtotal) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4">{{ get_phrase('No items') }}</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>{{ get_phrase('Subtotal') }}</strong></td>
                                            <td>{{ currency($order->subtotal) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>{{ get_phrase('Delivery') }}</strong></td>
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
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ get_phrase('Customer Details') }}</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ get_phrase('Name') }}:</strong> {{ $order->customer_name }}</p>
                            <p><strong>{{ get_phrase('Email') }}:</strong> {{ $order->customer_email }}</p>
                            <p><strong>{{ get_phrase('Phone') }}:</strong> {{ $order->customer_phone }}</p>
                            @if($order->shipping_address)
                            <p><strong>{{ get_phrase('Address') }}:</strong> {{ $order->shipping_address }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ get_phrase('Order Status') }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('agent.shop.order.status', $order->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">{{ get_phrase('Status') }}</label>
                                    <select name="order_status" class="form-select">
                                        <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                                        <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>{{ get_phrase('Processing') }}</option>
                                        <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>{{ get_phrase('Shipped') }}</option>
                                        <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>{{ get_phrase('Delivered') }}</option>
                                        <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>{{ get_phrase('Cancelled') }}</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">{{ get_phrase('Update Status') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection