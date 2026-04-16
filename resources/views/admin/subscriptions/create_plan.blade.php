@extends('layouts.admin')
@section('title', get_phrase('Create Subscription Plan'))

@section('admin_layout')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Create Subscription Plan</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.shop.subscriptions.store_plan') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Plan Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Price *</label>
                        <input type="number" name="price" step="0.01" min="0" class="form-control" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Billing Period *</label>
                        <select name="billing_period" class="form-select" required>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="lifetime">Lifetime</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Duration (Days) *</label>
                        <input type="number" name="duration_days" value="30" min="1" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tier *</label>
                        <select name="tier" class="form-select" required>
                            <option value="basic">Basic</option>
                            <option value="standard">Standard</option>
                            <option value="premium">Premium</option>
                            <option value="enterprise">Enterprise</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Max Listings *</label>
                        <input type="number" name="max_listings" value="5" min="0" class="form-control" required>
                        <small class="text-muted">0 = Unlimited</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Max Products *</label>
                        <input type="number" name="max_products" value="20" min="0" class="form-control" required>
                        <small class="text-muted">0 = Unlimited</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Commission Rate (%) *</label>
                        <input type="number" name="commission_rate" value="10" min="0" max="100" class="form-control" required>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Features</label>
                        <div class="form-check">
                            <input type="checkbox" name="has_analytics" class="form-check-input" id="has_analytics">
                            <label class="form-check-label" for="has_analytics">Analytics Dashboard</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="has_custom_branding" class="form-check-input" id="has_custom_branding">
                            <label class="form-check-label" for="has_custom_branding">Custom Branding</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="has_priority_support" class="form-check-input" id="has_priority_support">
                            <label class="form-check-label" for="has_priority_support">Priority Support</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="has_api_access" class="form-check-input" id="has_api_access">
                            <label class="form-check-label" for="has_api_access">API Access</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Options</label>
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured">
                            <label class="form-check-label" for="is_featured">Featured Plan</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="status" class="form-check-input" id="status" checked>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" value="0" class="form-control">
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create Plan</button>
                <a href="{{ route('admin.shop.subscriptions.plans') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
