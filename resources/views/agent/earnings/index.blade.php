@extends('layouts.admin')
@push('title')
{{ get_phrase('Earnings & Commissions') }}
@endpush
@push('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 10px;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
</style>
@endpush
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ get_phrase('Earnings & Commissions') }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('agent.earnings.sales') }}" class="btn btn-outline-primary me-2">
                                <i class="bi bi-bar-chart me-2"></i>{{ get_phrase('Sales Report') }}
                            </a>
                            <a href="{{ route('agent.earnings.payouts') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-wallet2 me-2"></i>{{ get_phrase('Payout History') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card stat-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-2">{{ get_phrase('Total Earnings') }}</h6>
                                    <h3 class="mb-0">{{ currency($stats['total_earnings']) }}</h3>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-currency-dollar text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-dark-50 mb-2">{{ get_phrase('Available Balance') }}</h6>
                                    <h3 class="mb-0 text-dark">{{ currency($stats['pending_earnings']) }}</h3>
                                </div>
                                <div class="stat-icon bg-dark bg-opacity-25">
                                    <i class="bi bi-wallet2 text-dark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-2">{{ get_phrase('Total Paid') }}</h6>
                                    <h3 class="mb-0">{{ currency($stats['paid_earnings']) }}</h3>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-check-circle text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-2">{{ get_phrase('Total Sales') }}</h6>
                                    <h3 class="mb-0">{{ $stats['total_sales'] }}</h3>
                                </div>
                                <div class="stat-icon bg-white bg-opacity-25">
                                    <i class="bi bi-bag text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Request Payout Card -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ get_phrase('Request Payout') }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('agent.earnings.payout') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">{{ get_phrase('Amount') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ get_currency_symbol() }}</span>
                                        <input type="number" name="amount" class="form-control" min="1" max="{{ $stats['pending_earnings'] - $stats['pending_payouts'] }}" required>
                                        <small class="text-muted d-block mt-1">
                                            {{ get_phrase('Available:') }} {{ currency($stats['pending_earnings'] - $stats['pending_payouts']) }}
                                        </small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ get_phrase('Payment Method') }}</label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="bank_transfer">{{ get_phrase('Bank Transfer') }}</option>
                                        <option value="paypal">{{ get_phrase('PayPal') }}</option>
                                        <option value="mobile_money">{{ get_phrase('Mobile Money') }}</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ get_phrase('Notes (Optional)') }}</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="{{ get_phrase('Additional payment details...') }}"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" {{ $stats['pending_earnings'] - $stats['pending_payouts'] <= 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-send me-2"></i>{{ get_phrase('Request Payout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ get_phrase('Earnings History') }}</h5>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form action="{{ route('agent.earnings') }}" method="GET" class="row g-3 mb-4">
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="">{{ get_phrase('All Types') }}</option>
                                <option value="sale" {{ ($filters['type'] ?? '') == 'sale' ? 'selected' : '' }}>{{ get_phrase('Sales') }}</option>
                                <option value="commission" {{ ($filters['type'] ?? '') == 'commission' ? 'selected' : '' }}>{{ get_phrase('Commission') }}</option>
                                <option value="refund" {{ ($filters['type'] ?? '') == 'refund' ? 'selected' : '' }}>{{ get_phrase('Refund') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">{{ get_phrase('All Status') }}</option>
                                <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                                <option value="approved" {{ ($filters['status'] ?? '') == 'approved' ? 'selected' : '' }}>{{ get_phrase('Approved') }}</option>
                                <option value="paid" {{ ($filters['status'] ?? '') == 'paid' ? 'selected' : '' }}>{{ get_phrase('Paid') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel me-2"></i>{{ get_phrase('Filter') }}
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('agent.earnings') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-x-circle me-2"></i>{{ get_phrase('Reset') }}
                            </a>
                        </div>
                    </form>

                    @if($earnings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ get_phrase('Date') }}</th>
                                        <th>{{ get_phrase('Order') }}</th>
                                        <th>{{ get_phrase('Type') }}</th>
                                        <th>{{ get_phrase('Order Amount') }}</th>
                                        <th>{{ get_phrase('Commission') }}</th>
                                        <th>{{ get_phrase('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($earnings as $earning)
                                    <tr>
                                        <td>{{ date_formatter($earning->created_at) }}</td>
                                        <td>
                                            @if($earning->order)
                                                <code>{{ $earning->order->order_number }}</code>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($earning->type == 'sale')
                                                <span class="badge bg-primary">{{ get_phrase('Sale') }}</span>
                                            @elseif($earning->type == 'commission')
                                                <span class="badge bg-info">{{ get_phrase('Commission') }}</span>
                                            @elseif($earning->type == 'payout')
                                                <span class="badge bg-warning text-dark">{{ get_phrase('Payout') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($earning->type) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ currency($earning->amount) }}</td>
                                        <td>
                                            <strong class="text-success">{{ currency($earning->commission_amount) }}</strong>
                                            <small class="text-muted d-block">({{ $earning->commission_rate }}%)</small>
                                        </td>
                                        <td>
                                            @if($earning->status == 'pending')
                                                <span class="badge bg-warning text-dark">{{ get_phrase('Pending') }}</span>
                                            @elseif($earning->status == 'approved')
                                                <span class="badge bg-success">{{ get_phrase('Approved') }}</span>
                                            @else
                                                <span class="badge bg-info">{{ get_phrase('Paid') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $earnings->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-cash-stack text-muted" style="font-size: 64px;"></i>
                            <h5 class="mt-3 text-muted">{{ get_phrase('No earnings found') }}</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
