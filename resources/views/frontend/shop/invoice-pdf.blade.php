<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ get_phrase('Invoice') }} - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }
        .invoice-header {
            border-bottom: 3px solid #6c5ce7;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #6c5ce7;
            margin-bottom: 10px;
        }
        .row {
            display: table;
            width: 100%;
        }
        .col-6 {
            width: 48%;
            display: table-cell;
            vertical-align: top;
        }
        .col-6:last-child {
            text-align: right;
        }
        .invoice-info {
            background: #f5f5f5;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .invoice-info h4 {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .invoice-info p {
            margin-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #6c5ce7;
            color: white;
            padding: 12px 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .invoice-total {
            background: #6c5ce7;
            color: white;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
            width: 300px;
            margin-left: auto;
        }
        .invoice-total .row {
            margin-bottom: 8px;
        }
        .invoice-total .row:last-child {
            margin-bottom: 0;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.3);
        }
        .invoice-total .big {
            font-size: 20px;
            font-weight: bold;
        }
        .invoice-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-primary { background: #cfe2ff; color: #084298; }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div class="row">
            <div class="col-6">
                <div class="invoice-title">{{ strtoupper(get_phrase('INVOICE')) }}</div>
                <p><strong>{{ get_phrase('Order') }}:</strong> #{{ $order->order_number }}</p>
                <p><strong>{{ get_phrase('Date') }}:</strong> {{ date('F j, Y', strtotime($order->created_at)) }}</p>
            </div>
            <div class="col-6">
                <p>
                    <strong>{{ get_phrase('Status') }}:</strong>
                    @if($order->order_status == 'pending')
                        <span class="badge badge-warning">{{ get_phrase('Pending') }}</span>
                    @elseif($order->order_status == 'processing')
                        <span class="badge badge-info">{{ get_phrase('Processing') }}</span>
                    @elseif($order->order_status == 'shipped')
                        <span class="badge badge-primary">{{ get_phrase('Shipped') }}</span>
                    @elseif($order->order_status == 'delivered')
                        <span class="badge badge-success">{{ get_phrase('Delivered') }}</span>
                    @elseif($order->order_status == 'cancelled')
                        <span class="badge badge-danger">{{ get_phrase('Cancelled') }}</span>
                    @endif
                </p>
                <p>
                    <strong>{{ get_phrase('Payment') }}:</strong>
                    @if($order->payment_status == 'paid')
                        <span class="badge badge-success">{{ get_phrase('Paid') }}</span>
                    @elseif($order->payment_status == 'pending')
                        <span class="badge badge-warning">{{ get_phrase('Pending') }}</span>
                    @else
                        <span class="badge badge-danger">{{ ucfirst($order->payment_status) }}</span>
                    @endif
                    ({{ strtoupper($order->payment_method ?? 'COD') }})
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="invoice-info">
                <h4>{{ get_phrase('Customer Information') }}</h4>
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p>{{ $order->customer_email }}</p>
                <p>{{ $order->customer_phone }}</p>
            </div>
        </div>
        <div class="col-6">
            <div class="invoice-info">
                <h4>{{ get_phrase('Shipping Information') }}</h4>
                <p>
                    <strong>{{ get_phrase('Method') }}:</strong>
                    @if($order->shipping_method == 'pickup')
                        <span class="badge badge-info">{{ get_phrase('Store Pickup') }}</span>
                    @else
                        <span class="badge badge-primary">{{ get_phrase('Home Delivery') }}</span>
                    @endif
                </p>
                @if($order->shipping_address)
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_city }}@if($order->shipping_postal_code), {{ $order->shipping_postal_code }}@endif</p>
                @else
                    <p><em>{{ get_phrase('Pickup from store location') }}</em></p>
                @endif
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="55%">{{ get_phrase('Product') }}</th>
                <th class="text-center">{{ get_phrase('Price') }}</th>
                <th class="text-center">{{ get_phrase('Qty') }}</th>
                <th class="text-right">{{ get_phrase('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->product_name }}</strong>
                    @if($item->variation_name)
                        <br><small style="color:#666">{{ $item->variation_name }}</small>
                    @endif
                </td>
                <td class="text-center">{{ currency($item->price) }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ currency($item->subtotal) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="invoice-total">
        <div class="row">
            <span>{{ get_phrase('Subtotal') }}</span>
            <span>{{ currency($order->subtotal) }}</span>
        </div>
        <div class="row">
            <span>{{ get_phrase('Shipping') }}</span>
            <span>{{ currency($order->shipping_cost) }}</span>
        </div>
        @if(isset($order->discount_amount) && $order->discount_amount > 0)
        <div class="row">
            <span>{{ get_phrase('Discount') }}</span>
            <span>-{{ currency($order->discount_amount) }}</span>
        </div>
        @endif
        <div class="row big">
            <span>{{ get_phrase('Total') }}</span>
            <span>{{ currency($order->total) }}</span>
        </div>
    </div>

    <div class="invoice-footer">
        <p><strong>{{ get_phrase('Thank you for your purchase!') }}</strong></p>
        <p>{{ get_phrase('If you have any questions, please contact us.') }}</p>
        <p style="margin-top: 15px; font-size: 10px; color: #999;">{{ get_phrase('Generated on') }}: {{ date('F j, Y g:i A') }}</p>
    </div>
</body>
</html>
