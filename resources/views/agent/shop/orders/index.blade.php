@extends('layouts.frontend')
@push('title', get_phrase('Shop Orders'))
@section('frontend_layout')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ get_phrase('Shop Orders') }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ get_phrase('Order #') }}</th>
                                <th>{{ get_phrase('Customer') }}</th>
                                <th>{{ get_phrase('Items') }}</th>
                                <th>{{ get_phrase('Total') }}</th>
                                <th>{{ get_phrase('Status') }}</th>
                                <th>{{ get_phrase('Date') }}</th>
                                <th>{{ get_phrase('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>
                                    <div>{{ $order->customer_name }}</div>
                                    <small class="text-muted">{{ $order->customer_email }}</small>
                                </td>
                                <td>{{ $order->items->sum('quantity') }}</td>
                                <td>{{ currency($order->total) }}</td>
                                <td>
                                    @if($order->order_status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($order->order_status == 'processing')
                                        <span class="badge bg-info">Processing</span>
                                    @elseif($order->order_status == 'delivered')
                                        <span class="badge bg-success">Delivered</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $order->order_status }}</span>
                                    @endif
                                </td>
                                <td>{{ date('d M Y', strtotime($order->created_at)) }}</td>
                                <td>
                                    <a href="{{ route('agent.shop.order.show', $order->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $orders->links() }}
                @else
                <div class="text-center py-5">
                    <p class="text-muted">{{ get_phrase('No orders found') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection