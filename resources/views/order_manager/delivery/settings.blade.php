@extends('layouts.admin')
@push('title', get_phrase('Delivery Settings'))
@push('meta')@endpush
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-20px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <h4 class="title fs-16px">
                <i class="fi-rr-settings me-2"></i>{{ get_phrase('Delivery Settings') }}
            </h4>
            <a href="{{ route('order.manager.delivery') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fi-rr-arrow-left me-1"></i>{{ get_phrase('Back') }}
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-lg-6">
        <div class="ol-card">
            <div class="ol-card-header">
                <h5 class="mb-0">{{ get_phrase('Delivery Price Configuration') }}</h5>
            </div>
            <div class="ol-card-body p-3">
                <form action="{{ route('order.manager.delivery.settings.update') }}" method="post">
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
