@extends('layouts.admin')
@section('title', get_phrase('Subscription Plans'))

@section('admin_layout')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Subscription Plans</h4>
        <a href="{{ route('admin.shop.subscriptions.create_plan') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Plan
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Tier</th>
                        <th>Price</th>
                        <th>Billing</th>
                        <th>Listings</th>
                        <th>Products</th>
                        <th>Commission</th>
                        <th>Features</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                    <tr>
                        <td>
                            <strong>{{ $plan->name }}</strong>
                            @if($plan->is_featured)
                                <span class="badge bg-warning text-dark">Featured</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ App\Models\SubscriptionPlan::getTierBadgeClass($plan->tier) }}">
                                {{ ucfirst($plan->tier) }}
                            </span>
                        </td>
                        <td>{{ currency($plan->price) }}</td>
                        <td>{{ ucfirst($plan->billing_period) }}</td>
                        <td>{{ $plan->max_listings ?: 'Unlimited' }}</td>
                        <td>{{ $plan->max_products ?: 'Unlimited' }}</td>
                        <td>{{ $plan->commission_rate }}%</td>
                        <td>
                            @if($plan->has_analytics)<span class="badge bg-info">Analytics</span>@endif
                            @if($plan->has_custom_branding)<span class="badge bg-secondary">Branding</span>@endif
                            @if($plan->has_priority_support)<span class="badge bg-success">Priority</span>@endif
                            @if($plan->has_api_access)<span class="badge bg-dark">API</span>@endif
                        </td>
                        <td>
                            @if($plan->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.shop.subscriptions.edit_plan', $plan->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.shop.subscriptions.destroy_plan', $plan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">No plans found. <a href="{{ route('admin.shop.subscriptions.create_plan') }}">Create one</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
