@extends('layouts.admin')
@section('title', get_phrase('Agent Subscriptions'))

@section('admin_layout')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Agent Subscriptions</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.shop.subscriptions.plans') }}" class="btn btn-sm btn-outline-secondary">Plans</a>
            <a href="{{ route('admin.shop.subscriptions.payments') }}" class="btn btn-sm btn-outline-secondary">Payments</a>
            <a href="{{ route('admin.shop.subscriptions.stats') }}" class="btn btn-sm btn-outline-info">Statistics</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search user..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.shop.subscriptions.subscriptions') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Started</th>
                        <th>Expires</th>
                        <th>Days Left</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    <tr>
                        <td>
                            <strong>{{ $subscription->user->name ?? 'N/A' }}</strong><br>
                            <small>{{ $subscription->user->email ?? '' }}</small>
                        </td>
                        <td>
                            {{ $subscription->plan->name ?? 'N/A' }}<br>
                            <span class="badge {{ App\Models\SubscriptionPlan::getTierBadgeClass($subscription->plan->tier ?? 'basic') }}">
                                {{ ucfirst($subscription->plan->tier ?? '') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $subscription->status_badge_class }}">
                                {{ $subscription->isExpired() ? 'Expired' : ucfirst($subscription->status) }}
                            </span>
                        </td>
                        <td>{{ $subscription->starts_at->format('M d, Y') }}</td>
                        <td>
                            @if($subscription->expires_at)
                                {{ $subscription->expires_at->format('M d, Y') }}
                            @else
                                Never
                            @endif
                        </td>
                        <td>
                            @if($subscription->isActive())
                                <span class="text-success fw-bold">{{ $subscription->daysRemaining() }} days</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($subscription->status === 'active' && !$subscription->isExpired())
                                <a href="{{ route('admin.shop.subscriptions.cancel_get', $subscription->id) }}" class="btn btn-sm btn-warning" onclick="return confirm('Cancel this subscription?')">
                                    Cancel
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No subscriptions found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection
