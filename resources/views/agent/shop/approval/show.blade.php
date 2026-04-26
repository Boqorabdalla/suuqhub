@extends('layouts.admin')
@push('title', get_phrase('Order Details'))
@section('content')

<div class="mb-2">
    <a href="{{ route('agent.shop.approval') }}" class="btn btn-outline-secondary">
        <i class="fi-rr-arrow-left me-2"></i>{{ get_phrase('Back to Approvals') }}
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="ol-card">
            <div class="ol-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ get_phrase('Order') }} #{{ $order->order_number }}</h5>
                    <span class="badge {{ $order->approval_badge_class }}">{{ $order->approval_status_label }}</span>
                </div>
            </div>
            <div class="ol-card-body p-20px">
                @if($order->listing)
                <div class="alert alert-secondary mb-3">
                    <strong>{{ get_phrase('Listing') }}:</strong> {{ $order->listing->name }}
                </div>
                @endif

                @if($order->approval_status == 'rejected' && $order->rejection_reason)
                <div class="alert alert-danger mb-3">
                    <strong>{{ get_phrase('Rejection Reason') }}:</strong> {{ $order->rejection_reason }}
                </div>
                @endif

                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ get_phrase('Product') }}</th>
                            <th>{{ get_phrase('Price') }}</th>
                            <th>{{ get_phrase('Qty') }}</th>
                            <th>{{ get_phrase('Subtotal') }}</th>
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
                            <td>{{ currency($item->price) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ currency($item->subtotal) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end">{{ get_phrase('Subtotal') }}</td>
                            <td>{{ currency($order->subtotal) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">{{ get_phrase('Delivery') }}</td>
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

    <div class="col-lg-4">
        <div class="ol-card mb-3">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Customer Info') }}</h5>
            </div>
            <div class="ol-card-body">
                <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                <p class="mb-1 text-muted">{{ $order->customer_email }}</p>
                <p class="mb-0 text-muted">{{ $order->customer_phone }}</p>
            </div>
        </div>

        @if($order->shipping_method == 'delivery')
        <div class="ol-card mb-3">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Delivery Address') }}</h5>
            </div>
            <div class="ol-card-body">
                <p class="mb-1">{{ $order->shipping_address ?? '-' }}</p>
                <p class="mb-0 text-muted">{{ $order->shipping_city }} {{ $order->shipping_postal_code }}</p>
            </div>
        </div>
        @endif

        @if($order->approval_status == 'pending')
        <div class="ol-card mb-3">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Actions') }}</h5>
            </div>
            <div class="ol-card-body">
                <form action="{{ route('agent.shop.approval.approve', $order->id) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fi-rr-check me-2"></i>{{ get_phrase('Approve Order') }}
                    </button>
                </form>
                <button class="btn btn-danger w-100" onclick="showRejectModal()">
                    <i class="fi-rr-cross me-2"></i>{{ get_phrase('Reject Order') }}
                </button>
            </div>
        </div>
        @endif

        <div class="ol-card">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Order Info') }}</h5>
            </div>
            <div class="ol-card-body">
                <p class="mb-1"><strong>{{ get_phrase('Date') }}:</strong> {{ date_formatter($order->created_at) }}</p>
                <p class="mb-1"><strong>{{ get_phrase('Shipping') }}:</strong> {{ ucfirst($order->shipping_method) }}</p>
                <p class="mb-0"><strong>{{ get_phrase('Payment') }}:</strong> {{ strtoupper($order->payment_method) }}</p>
            </div>
        </div>
    </div>
</div>

@if($order->approval_status == 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('agent.shop.approval.reject', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ get_phrase('Reject Order') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Reason for rejection') }} *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
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

@push('script')
<script>
    function showRejectModal() {
        var modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }
</script>
@endpush
@endif
@endsection
