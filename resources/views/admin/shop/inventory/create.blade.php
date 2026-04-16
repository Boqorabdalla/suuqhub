@extends('layouts.admin')
@section('title', get_phrase('Add Inventory Item'))
@section('admin_layout')
<div class="mb-2">
    <a href="{{ route('admin.shop.inventory') }}" class="btn btn-outline-secondary">
        <i class="fi-rr-arrow-left"></i> {{ get_phrase('Back') }}
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ get_phrase('Add New Inventory Item') }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.shop.inventory.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Name') }} *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Price') }} *</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Discount Price') }}</label>
                                <input type="number" name="discount_price" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Type') }}</label>
                                <select name="type" class="form-select">
                                    <option value="">{{ get_phrase('General') }}</option>
                                    <option value="beauty">Beauty</option>
                                    <option value="hotel">Hotel</option>
                                    <option value="restaurant">Restaurant</option>
                                    <option value="car">Car</option>
                                    <option value="real-estate">Real Estate</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Listing ID') }}</label>
                                <input type="number" name="listing_id" class="form-control" placeholder="{{ get_phrase('Link to specific listing') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('SKU') }}</label>
                                <input type="text" name="sku" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Stock Quantity') }}</label>
                                <input type="number" name="stock_quantity" class="form-control" value="0" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Description') }}</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Variations') }}</label>
                        <div id="variations-container">
                            <div class="row g-2 mb-2 variation-row">
                                <div class="col-md-3">
                                    <input type="text" name="variation_names[]" class="form-control" placeholder="{{ get_phrase('Name (e.g. Size)') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="variation_values[]" class="form-control" placeholder="{{ get_phrase('Value (e.g. Large)') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="variation_prices[]" class="form-control" placeholder="{{ get_phrase('+Price') }}" step="0.01">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="variation_stocks[]" class="form-control" placeholder="{{ get_phrase('Stock') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-variation">{{ get_phrase('Remove') }}</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" id="add-variation">{{ get_phrase('+ Add Variation') }}</button>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Featured Image') }}</label>
                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Status') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="availability" value="1" checked>
                            <label class="form-check-label">{{ get_phrase('Available for sale') }}</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1">
                            <label class="form-check-label">{{ get_phrase('Featured Item') }}</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="track_stock" value="1" checked>
                            <label class="form-check-label">{{ get_phrase('Track Stock') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            <button type="submit" class="btn btn-primary">{{ get_phrase('Save Item') }}</button>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
    document.getElementById('add-variation').addEventListener('click', function() {
        const container = document.getElementById('variations-container');
        const newRow = document.createElement('div');
        newRow.className = 'row g-2 mb-2 variation-row';
        newRow.innerHTML = `
            <div class="col-md-3">
                <input type="text" name="variation_names[]" class="form-control" placeholder="{{ get_phrase('Name (e.g. Size)') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="variation_values[]" class="form-control" placeholder="{{ get_phrase('Value (e.g. Large)') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="variation_prices[]" class="form-control" placeholder="{{ get_phrase('+Price') }}" step="0.01">
            </div>
            <div class="col-md-2">
                <input type="number" name="variation_stocks[]" class="form-control" placeholder="{{ get_phrase('Stock') }}">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-variation">{{ get_phrase('Remove') }}</button>
            </div>
        `;
        container.appendChild(newRow);
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variation')) {
            e.target.closest('.variation-row').remove();
        }
    });
</script>
@endpush
