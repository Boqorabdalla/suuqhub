@extends('layouts.admin')
@section('title', get_phrase('Edit Product'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-edit me-2"></i>
                {{ get_phrase('Edit Product') }}
            </h4>
            <a href="{{ route('admin.shop.products') }}" class="btn ol-btn-outline-secondary">
                <i class="fi-rr-arrow-left me-2"></i>{{ get_phrase('Back') }}
            </a>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-20px">
        <form action="{{ route('admin.shop.product.update', $product->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Product Name') }} *</label>
                        <input type="text" name="name" class="form-control ol-form-control" value="{{ $product->name }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Price') }} *</label>
                                <input type="number" name="price" class="form-control ol-form-control" step="0.01" min="0" value="{{ $product->price }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Category') }}</label>
                                <select name="category_id" class="form-control ol-form-control">
                                    <option value="">{{ get_phrase('Select Category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Short Description') }}</label>
                        <textarea name="short_description" class="form-control ol-form-control" rows="3">{{ $product->short_description }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Description') }}</label>
                        <textarea name="description" class="form-control ol-form-control editor" rows="5">{{ $product->description }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Stock Quantity') }}</label>
                                <input type="number" name="stock_quantity" class="form-control ol-form-control" value="{{ $product->stock_quantity }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Status') }}</label>
                                <select name="is_published" class="form-control ol-form-control">
                                    <option value="1" {{ $product->is_published ? 'selected' : '' }}>{{ get_phrase('Published') }}</option>
                                    <option value="0" {{ !$product->is_published ? 'selected' : '' }}>{{ get_phrase('Draft') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="track_stock" class="form-check-input" id="track_stock" value="1" {{ $product->track_stock ? 'checked' : '' }}>
                            <label class="form-check-label" for="track_stock">{{ get_phrase('Track Stock') }}</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">{{ get_phrase('Featured Product') }}</label>
                        </div>
                    </div>

                    <h5 class="mb-3">{{ get_phrase('Product Variations') }}</h5>
                    <div id="variations_container">
                        @foreach($product->variations as $index => $variation)
                        <div class="variation-row mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>{{ get_phrase('Variation') }} {{ $index + 1 }}</strong>
                                @if($index > 0)
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeVariation(this)">
                                    <i class="fi-rr-trash"></i>
                                </button>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <input type="text" name="variations[{{ $index }}][name]" class="form-control ol-form-control" placeholder="{{ get_phrase('Name') }}" value="{{ $variation->name }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <input type="text" name="variations[{{ $index }}][value]" class="form-control ol-form-control" placeholder="{{ get_phrase('Value') }}" value="{{ $variation->value }}">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <input type="number" name="variations[{{ $index }}][price_modifier]" class="form-control ol-form-control" placeholder="{{ get_phrase('Price +/-') }}" step="0.01" value="{{ $variation->price_modifier }}">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <input type="number" name="variations[{{ $index }}][stock_quantity]" class="form-control ol-form-control" placeholder="{{ get_phrase('Stock') }}" value="{{ $variation->stock_quantity }}">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="variations[{{ $index }}][is_default]" class="form-check-input" value="1" {{ $variation->is_default ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ get_phrase('Default') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addVariation()">
                        <i class="fi-rr-plus me-2"></i>{{ get_phrase('Add Variation') }}
                    </button>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Featured Image') }}</label>
                        @if($product->featured_image)
                            <div class="mb-2">
                                <img src="{{ asset('uploads/shop/products/'.$product->featured_image) }}" alt="" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        @endif
                        <input type="file" name="featured_image" class="form-control ol-form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Gallery Images') }}</label>
                        @if($product->images->count() > 0)
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @foreach($product->images as $img)
                                    <div class="position-relative">
                                        <img src="{{ asset('uploads/shop/products/'.$img->image) }}" alt="" class="img-thumbnail" style="height: 80px; width: 80px; object-fit: cover;">
                                        <a href="{{ route('admin.shop.product.image.delete', $img->id) }}" class="position-absolute top-0 end-0 btn btn-sm btn-danger rounded-circle">×</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <input type="file" name="new_images[]" class="form-control ol-form-control" accept="image/*" multiple>
                        <small class="text-muted">{{ get_phrase('Select new images to add') }}</small>
                    </div>

                    <div class="mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">{{ get_phrase('Shipping Options') }}</h6>
                                <div class="mb-2">
                                    <label class="form-label">{{ get_phrase('Pickup Cost') }}</label>
                                    <input type="number" name="pickup_cost" class="form-control ol-form-control" step="0.01" value="{{ $product->pickup_cost ?? 0 }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">{{ get_phrase('Delivery Cost') }}</label>
                                    <input type="number" name="delivery_cost" class="form-control ol-form-control" step="0.01" value="{{ $product->delivery_cost ?? 0 }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Update Product') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('script')
<script>
    let variationCount = {{ intval($product->variations->count()) }};
    
    function addVariation() {
        const container = document.getElementById('variations_container');
        const html = `
            <div class="variation-row mb-3 p-3 border rounded">
                <div class="d-flex justify-content-between mb-2">
                    <strong>{{ get_phrase('Variation') }}</strong>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeVariation(this)">
                        <i class="fi-rr-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <input type="text" name="variations[${variationCount}][name]" class="form-control ol-form-control" placeholder="{{ get_phrase('Name (e.g., Size)') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="text" name="variations[${variationCount}][value]" class="form-control ol-form-control" placeholder="{{ get_phrase('Value (e.g., Large)') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="number" name="variations[${variationCount}][price_modifier]" class="form-control ol-form-control" placeholder="{{ get_phrase('Price +/-') }}" step="0.01">
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="number" name="variations[${variationCount}][stock_quantity]" class="form-control ol-form-control" placeholder="{{ get_phrase('Stock') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <div class="form-check mt-2">
                            <input type="checkbox" name="variations[${variationCount}][is_default]" class="form-check-input" value="1">
                            <label class="form-check-label">{{ get_phrase('Default') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        variationCount++;
    }

    function removeVariation(btn) {
        btn.closest('.variation-row').remove();
    }
</script>
@endpush
