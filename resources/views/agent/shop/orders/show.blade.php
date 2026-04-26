@extends('layouts.admin')
@push('title')
{{ get_phrase('Order Details') }}
@endpush
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Order #{{ $order->order_number }}</h4>
            <a href="{{ route('agent.shop.orders') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ currency($item->unit_price) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ currency($item->subtotal) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                                <td>{{ currency($order->subtotal) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">Delivery</td>
                                <td>{{ currency($order->delivery_price) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total</strong></td>
                                <td><strong>{{ currency($order->total) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Customer</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                @if($order->shipping_address)
                <p><strong>Address:</strong> {{ $order->shipping_address }}</p>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Update Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('agent.shop.order.status', $order->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <select name="order_status" class="form-select">
                            <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection