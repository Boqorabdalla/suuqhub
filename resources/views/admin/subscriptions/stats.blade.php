@extends('layouts.admin')
@section('title', get_phrase('Subscription Statistics'))

@section('admin_layout')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Subscription Statistics</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.shop.subscriptions.plans') }}" class="btn btn-sm btn-outline-secondary">Plans</a>
            <a href="{{ route('admin.shop.subscriptions.payments') }}" class="btn btn-sm btn-outline-secondary">Payments</a>
            <a href="{{ route('admin.shop.subscriptions.subscriptions') }}" class="btn btn-sm btn-outline-secondary">Subscriptions</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6>Active Subscriptions</h6>
                        <h2>{{ $stats['active_subscriptions'] }}</h2>
                        <small>of {{ $stats['total_subscriptions'] }} total</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Total Revenue</h6>
                        <h2>{{ currency($stats['total_revenue']) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6>This Month</h6>
                        <h2>{{ currency($stats['revenue_this_month']) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h6>Pending Payments</h6>
                        <h2>{{ $stats['pending_payments'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Top Plans</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Subscriptions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPlans as $item)
                                <tr>
                                    <td>
                                        {{ $item->plan->name ?? 'N/A' }}
                                        <span class="badge {{ App\Models\SubscriptionPlan::getTierBadgeClass($item->plan->tier ?? 'basic') }}">
                                            {{ ucfirst($item->plan->tier ?? '') }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $item->count }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">No data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Payments</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->user->name ?? 'N/A' }}</td>
                                    <td>{{ $payment->plan->name ?? 'N/A' }}</td>
                                    <td>{{ currency($payment->amount) }}</td>
                                    <td>
                                        <span class="badge {{ $payment->status_badge_class }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No recent payments</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h6>Total Plans</h6>
                        <h3>{{ $stats['total_plans'] }}</h3>
                        <small>{{ $stats['active_plans'] }} active</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
