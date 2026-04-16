@extends('layouts.admin')
@push('title')
{{ get_phrase('Add Inventory Item') }}
@endpush
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ get_phrase('Add New Inventory Item') }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('agent.inventory') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>{{ get_phrase('Back to List') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('agent.inventory.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ get_phrase('Basic Information') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">{{ get_phrase('Listing') }} *</label>
                                    <select name="listing_id" class="form-select" required>
                                        <option value="">{{ get_phrase('Select Listing') }}</option>
                                        @foreach($listings as $listing)
                                            <option value="{{ $listing->id }}">{{ $listing->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ get_phrase('Item Name') }} *</label>
                                    <input type="text" name="name" class="form-control" required placeholder="{{ get_phrase('Enter item name') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ get_phrase('Description') }}</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="{{ get_phrase('Enter description') }}"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ get_phrase('Pricing') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">{{ get_phrase('Price') }} *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ get_currency_symbol() }}</span>
                                            <input type="number" name="price" class="form-control" required min="0" step="0.01" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ get_phrase('Discount Price') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ get_currency_symbol() }}</span>
                                            <input type="number" name="discount_price" class="form-control" min="0" step="0.01" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ get_phrase('Stock Management') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">{{ get_phrase('Stock Quantity') }}</label>
                                        <input type="number" name="stock_quantity" class="form-control" min="0" value="0" placeholder="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ get_phrase('SKU / Reference') }}</label>
                                        <input type="text" name="sku" class="form-control" placeholder="{{ get_phrase('Enter SKU') }}">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="track_stock" class="form-check-input" id="trackStock" value="1" checked>
                                        <label class="form-check-label" for="trackStock">{{ get_phrase('Track stock quantity') }}</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="availability" class="form-check-input" id="availability" value="1" checked>
                                        <label class="form-check-label" for="availability">{{ get_phrase('Item is available for sale') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ get_phrase('Image') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">{{ get_phrase('Featured Image') }}</label>
                                    <input type="file" name="featured_image" class="form-control" accept="image/*">
                                    <small class="text-muted">{{ get_phrase('Recommended size: 800x800px') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle me-2"></i>{{ get_phrase('Create Item') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
