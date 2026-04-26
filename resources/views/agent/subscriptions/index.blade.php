@extends('layouts.frontend')
@push('title', get_phrase('Shop Subscription'))
@push('meta')@endpush

@section('frontend_layout')

<style>
    .pricing-card {
        border: 2px solid #eee;
        border-radius: 16px;
        transition: all 0.3s;
    }
    .pricing-card:hover {
        border-color: var(--themeColor);
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .pricing-card.featured {
        border-color: var(--themeColor);
        position: relative;
    }
    .pricing-card.featured::before {
        content: 'Featured';
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--themeColor);
        color: white;
        padding: 4px 20px;
        border-radius: 20px;
        font-size: 12px;
    }
    .current-plan {
        border: 2px solid var(--themeColor);
        background: linear-gradient(135deg, rgba(var(--themeColorRgb), 0.05) 0%, rgba(var(--themeColorRgb), 0.1) 100%);
    }
</style>

<section class="ca-wraper-main mb-90px mt-4">
    <div class="container">
        <div class="row gx-20px">
            <div class="col-lg-4 col-xl-3">
                @include('user.navigation')
            </div>
            <div class="col-lg-8 col-xl-9">
                <div class="ca-content-main">
                    <div class="eqa-content-wrap">
                        <div class="aiz-main-content">
                            <div class="px-4 px-md-5 py-5">
                                <h4 class="mb-4">{{ get_phrase('Shop Subscription') }}</h4>
                                
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                @if($currentSubscription && !$currentSubscription->isExpired())
                                <div class="alert alert-success mb-4">
                                    <h5><i class="bi bi-check-circle"></i> Current Plan: {{ $currentSubscription->plan->name }}</h5>
                                    <p class="mb-0">
                                        Status: <span class="badge bg-success">{{ ucfirst($currentSubscription->status) }}</span>
                                        @if($currentSubscription->expires_at)
                                            | Expires: {{ $currentSubscription->expires_at->format('M d, Y') }}
                                            | Days Remaining: <strong>{{ $currentSubscription->daysRemaining() }}</strong>
                                        @else
                                            | <span class="badge bg-primary">Lifetime</span>
                                        @endif
                                    </p>
                                </div>
                                @endif

                                <h5 class="mb-3">{{ get_phrase('Available Plans') }}</h5>
                                <div class="row g-4">
                                    @foreach($plans as $plan)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card pricing-card {{ $plan->is_featured ? 'featured' : '' }} {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'current-plan' : '' }} h-100">
                                            <div class="card-body text-center p-4">
                                                <h5 class="mb-2">{{ $plan->name }}</h5>
                                                <p class="text-muted small mb-3">{{ $plan->description }}</p>
                                                
                                                <h3 class="mb-3" style="color: var(--themeColor);">
                                                    @if($plan->price == 0)
                                                        FREE
                                                    @else
                                                        {{ currency($plan->price) }}<small>/{{ $plan->billing_period }}</small>
                                                    @endif
                                                </h3>
                                                
                                                <ul class="list-unstyled text-start small mb-3">
                                                    <li class="mb-2">
                                                        <i class="bi bi-check text-success me-2"></i>
                                                        @if($plan->max_products > 0)
                                                            {{ $plan->max_products }} Products
                                                        @else
                                                            Unlimited Products
                                                        @endif
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="bi bi-check text-success me-2"></i>
                                                        {{ $plan->commission_rate }}% Commission Rate
                                                    </li>
                                                    @if($plan->has_analytics)
                                                        <li class="mb-2"><i class="bi bi-check text-success me-2"></i> Analytics</li>
                                                    @endif
                                                    @if($plan->has_priority_support)
                                                        <li class="mb-2"><i class="bi bi-check text-success me-2"></i> Priority Support</li>
                                                    @endif
                                                </ul>
                                                
                                                @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                                                    <button class="btn btn-secondary w-100" disabled>
                                                        <i class="bi bi-check-circle"></i> Current Plan
                                                    </button>
                                                @elseif($currentSubscription && $currentSubscription->plan->price < $plan->price)
                                                    <form action="{{ route('shop.subscription.upgrade', $plan->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary w-100">
                                                            <i class="bi bi-arrow-up-circle"></i> Upgrade
                                                        </button>
                                                    </form>
                                                @elseif($currentSubscription)
                                                    <button class="btn btn-outline-secondary w-100" disabled>
                                                        Downgrade
                                                    </button>
                                                @else
                                                    <form action="{{ route('shop.subscription.subscribe', $plan->id) }}" method="POST" class="w-100">
                                                        @csrf
                                                        <div class="mb-2">
                                                            <label class="form-label small">Payment Method</label>
                                                            <select name="payment_method" class="form-select form-select-sm">
                                                                <option value="cod">Cash on Delivery (COD) - Pay with agent</option>
                                                                <option value="manual">Request Admin Approval</option>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary w-100 py-2">
                                                            <i class="bi bi-cart-plus"></i> Subscribe Now
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                @if($subscriptionHistory->count() > 0)
                                <h5 class="mt-5 mb-3">{{ get_phrase('Subscription History') }}</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Plan</th>
                                                <th>Status</th>
                                                <th>Started</th>
                                                <th>Expires</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subscriptionHistory as $subscription)
                                            <tr>
                                                <td>{{ $subscription->plan->name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge {{ $subscription->status_badge_class }}">
                                                        {{ $subscription->isExpired() ? 'Expired' : ucfirst($subscription->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : 'N/A' }}</td>
                                                <td>{{ $subscription->expires_at ? $subscription->expires_at->format('M d, Y') : 'Never' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
