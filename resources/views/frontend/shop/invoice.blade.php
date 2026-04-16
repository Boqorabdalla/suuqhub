@extends('layouts.frontend')
@push('title', get_phrase('Invoice') . ' - ' . $order->order_number)
@push('css')
<style>
    .invoice-header {
        border-bottom: 2px solid #6c5ce7;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }
    .invoice-title {
        font-size: 28px;
        font-weight: bold;
        color: #6c5ce7;
    }
    .invoice-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }
    .invoice-table th {
        background: #6c5ce7;
        color: white;
        padding: 12px;
        text-align: left;
    }
    .invoice-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }
    .invoice-table tr:nth-child(even) {
        background: #f9f9f9;
    }
    .invoice-total {
        background: #6c5ce7;
        color: white;
        padding: 15px;
        border-radius: 8px;
        text-align: right;
    }
    .invoice-total-amount {
        font-size: 24px;
        font-weight: bold;
    }
    .invoice-footer {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        text-align: center;
        color: #666;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-pending { background: #ffeaa7; color: #d63031; }
    .status-approved { background: #55efc4; color: #00b894; }
    .status-rejected { background: #fab1a0; color: #d63031; }
    .status-delivered { background: #74b9ff; color: #0984e3; }
    @media print {
        .no-print { display: none !important; }
        .invoice-header { border-bottom-color: #333; }
        .invoice-table th { background: #333 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .invoice-total { background: #333 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endpush
@section('frontend_layout')

<div class="container py-4">
    <div class="no-print text-end mb-3">
        <button onclick="window.print()" class="btn btn-secondary me-2">
            <i class="fi-rr-print me-2"></i>{{ get_phrase('Print Invoice') }}
        </button>
        <a href="{{ route('shop.order.invoice.download', $order->id) }}" class="btn btn-primary">
            <i class="fi-rr-download me-2"></i>{{ get_phrase('Download PDF') }}
        </a>
    </div>

    <div class="invoice-container bg-white p-4" style="border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div class="invoice-header">
            <div class="row">
                <div class="col-md-6">
                    <div class="invoice-title">{{ get_phrase('INVOICE') }}</div>
                    <p class="mb-1"><strong>{{ get_phrase('Order') }}:</strong> #{{ $order->order_number }}</p>
                    <p class="mb-0"><strong>{{ get_phrase('Date') }}:</strong> {{ date('F j, Y', strtotime($order->created_at)) }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1"><strong>{{ get_phrase('Status') }}:</strong> 
                        <span class="status-badge status-{{ $order->order_status }}">
                            @if($order->order_status == 'pending')
                                {{ get_phrase('Pending') }}
                            @elseif($order->order_status == 'processing')
                                {{ get_phrase('Processing') }}
                            @elseif($order->order_status == 'shipped')
                                {{ get_phrase('Shipped') }}
                            @elseif($order->order_status == 'delivered')
                                {{ get_phrase('Delivered') }}
                            @elseif($order->order_status == 'cancelled')
                                {{ get_phrase('Cancelled') }}
                            @else
                                {{ ucfirst($order->order_status) }}
                            @endif
                        </span>
                    </p>
                    <p class="mb-0"><strong>{{ get_phrase('Payment') }}:</strong> 
                        @if($order->payment_status == 'paid')
                            <span class="badge bg-success">{{ get_phrase('Paid') }}</span>
                        @elseif($order->payment_status == 'pending')
                            <span class="badge bg-warning text-dark">{{ get_phrase('Pending') }}</span>
                        @else
                            <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                        @endif
                        <span class="text-muted">({{ strtoupper($order->payment_method ?? 'COD') }})</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="invoice-info">
                    <h6 class="text-muted mb-2">{{ get_phrase('Customer Information') }}</h6>
                    <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                    <p class="mb-1">{{ $order->customer_email }}</p>
                    <p class="mb-0">{{ $order->customer_phone }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="invoice-info">
                    <h6 class="text-muted mb-2">{{ get_phrase('Shipping Information') }}</h6>
                    <p class="mb-1">
                        <strong>{{ get_phrase('Method') }}:</strong>
                        @if($order->shipping_method == 'pickup')
                            <span class="badge bg-info">{{ get_phrase('Store Pickup') }}</span>
                        @else
                            <span class="badge bg-primary">{{ get_phrase('Home Delivery') }}</span>
                        @endif
                    </p>
                    @if($order->shipping_address)
                        <p class="mb-1">{{ $order->shipping_address }}</p>
                        <p class="mb-0">{{ $order->shipping_city }}@if($order->shipping_postal_code), {{ $order->shipping_postal_code }}@endif</p>
                    @else
                        <p class="mb-0 text-muted">{{ get_phrase('Pickup from store location') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th width="50%">{{ get_phrase('Product') }}</th>
                    <th class="text-center">{{ get_phrase('Price') }}</th>
                    <th class="text-center">{{ get_phrase('Qty') }}</th>
                    <th class="text-end">{{ get_phrase('Total') }}</th>
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
                        @if($item->item_type === 'inventory' && $item->itemProduct)
                            <br><small class="text-success">{{ get_phrase('From Listing Product') }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ currency($item->price) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">{{ currency($item->subtotal) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row mt-4">
            <div class="col-md-6">
                @if($order->notes)
                <div class="invoice-info">
                    <h6 class="text-muted mb-2">{{ get_phrase('Order Notes') }}</h6>
                    <p class="mb-0">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
            <div class="col-md-6">
                <div class="invoice-total">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ get_phrase('Subtotal') }}</span>
                        <span>{{ currency($order->subtotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ get_phrase('Shipping') }}</span>
                        <span>{{ currency($order->shipping_cost) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ get_phrase('Discount') }}</span>
                        <span>-{{ currency($order->discount_amount) }}</span>
                    </div>
                    @endif
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <div class="d-flex justify-content-between">
                        <span class="fs-5">{{ get_phrase('Total') }}</span>
                        <span class="invoice-total-amount">{{ currency($order->total) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-footer">
            <p class="mb-1"><strong>{{ get_phrase('Thank you for your purchase!') }}</strong></p>
            <p class="mb-0">{{ get_phrase('If you have any questions, please contact us.') }}</p>
            <p class="mt-2"><small class="text-muted">{{ get_phrase('Generated on') }}: {{ date('F j, Y g:i A') }}</small></p>
        </div>
    </div>

    <div class="no-print text-center mt-4">
        <a href="{{ route('shop.order', $order->id) }}" class="btn btn-outline-secondary">
            <i class="fi-rr-arrow-left me-2"></i>{{ get_phrase('Back to Order Details') }}
        </a>
    </div>
</div>

@endsection
