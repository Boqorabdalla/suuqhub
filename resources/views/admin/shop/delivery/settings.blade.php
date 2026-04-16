@extends('layouts.admin')
@section('title', get_phrase('Delivery Settings'))
@section('admin_layout')

<div class="mb-2">
    <a href="{{ route('admin.shop.delivery') }}" class="btn btn-outline-secondary">
        <i class="fi-rr-arrow-left me-2"></i>{{ get_phrase('Back to Deliveries') }}
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="ol-card">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Delivery Settings') }}</h5>
            </div>
            <div class="ol-card-body p-20px">
                <form action="{{ route('admin.shop.delivery.settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Delivery Price') }} ({{ currency('') }})</label>
                        <input type="number" name="delivery_price" class="form-control ol-form-control" step="0.01" min="0" value="{{ $delivery_price }}" required>
                        <small class="text-muted">{{ get_phrase('This price will be added to all home delivery orders') }}</small>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ get_phrase('Save Settings') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
