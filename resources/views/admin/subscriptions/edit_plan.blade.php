@extends('layouts.admin')
@section('title', get_phrase('Edit Subscription Plan'))

@section('admin_layout')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Subscription Plan</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.shop.subscriptions.update_plan', $plan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Plan Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ $plan->name }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Price *</label>
                        <input type="number" name="price" step="0.01" min="0" class="form-control" value="{{ $plan->price }}" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Billing Period *</label>
                        <select name="billing_period" class="form-select" required>
                            <option value="monthly" {{ $plan->billing_period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ $plan->billing_period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            <option value="lifetime" {{ $plan->billing_period == 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Duration (Days) *</label>
                        <input type="number" name="duration_days" min="1" class="form-control" value="{{ $plan->duration_days }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tier *</label>
                        <select name="tier" class="form-select" required>
                            <option value="basic" {{ $plan->tier == 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="standard" {{ $plan->tier == 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="premium" {{ $plan->tier == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="enterprise" {{ $plan->tier == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Max Listings *</label>
                        <input type="number" name="max_listings" min="0" class="form-control" value="{{ $plan->max_listings }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Max Products *</label>
                        <input type="number" name="max_products" min="0" class="form-control" value="{{ $plan->max_products }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Commission Rate (%) *</label>
                        <input type="number" name="commission_rate" min="0" max="100" class="form-control" value="{{ $plan->commission_rate }}" required>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $plan->description }}</textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Features</label>
                        <div class="form-check">
                            <input type="checkbox" name="has_analytics" class="form-check-input" id="has_analytics" {{ $plan->has_analytics ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_analytics">Analytics Dashboard</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="has_custom_branding" class="form-check-input" id="has_custom_branding" {{ $plan->has_custom_branding ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_custom_branding">Custom Branding</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="has_priority_support" class="form-check-input" id="has_priority_support" {{ $plan->has_priority_support ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_priority_support">Priority Support</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="has_api_access" class="form-check-input" id="has_api_access" {{ $plan->has_api_access ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_api_access">API Access</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Options</label>
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" {{ $plan->is_featured ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Featured Plan</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="status" class="form-check-input" id="status" {{ $plan->status ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ $plan->sort_order }}">
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Plan</button>
                <a href="{{ route('admin.shop.subscriptions.plans') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
