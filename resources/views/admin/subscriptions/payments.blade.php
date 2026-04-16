@extends('layouts.admin')
@section('title', get_phrase('Subscription Payments'))

@section('admin_layout')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Subscription Payments</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.shop.subscriptions.subscriptions') }}" class="btn btn-sm btn-outline-secondary">View Subscriptions</a>
            <a href="{{ route('admin.shop.subscriptions.stats') }}" class="btn btn-sm btn-outline-info">Statistics</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5>Total Revenue</h5>
                        <h3>{{ currency($stats['total']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h5>Pending</h5>
                        <h3>{{ currency($stats['pending']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5>Completed</h5>
                        <h3>{{ currency($stats['completed']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search user..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.shop.subscriptions.payments') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>#{{ $payment->id }}</td>
                        <td>
                            <strong>{{ $payment->user->name ?? 'N/A' }}</strong><br>
                            <small>{{ $payment->user->email ?? '' }}</small>
                        </td>
                        <td>{{ $payment->plan->name ?? 'N/A' }}</td>
                        <td>{{ currency($payment->amount) }}</td>
                        <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $payment->status_badge_class }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td>{{ $payment->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($payment->status === 'pending')
                                <form action="{{ route('admin.shop.subscriptions.approve_payment', $payment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this payment?')">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" onclick="rejectPayment('{{ $payment->id }}')">
                                    <i class="bi bi-x-circle"></i> Reject
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No payments found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $payments->links() }}
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="rejectForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason (optional)</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function rejectPayment(paymentId) {
    document.getElementById('rejectForm').action = '/admin/shop-subscriptions/reject-payment/' + paymentId;
    $('#rejectModal').modal('show');
}
</script>
@endpush
@endsection
