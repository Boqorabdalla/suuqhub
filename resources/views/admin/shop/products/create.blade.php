@extends('layouts.admin')
@section('title', get_phrase('Add Product'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-plus me-2"></i>
                {{ get_phrase('Add New Product') }}
            </h4>
            <a href="{{ route('admin.shop.products') }}" class="btn ol-btn-outline-secondary">
                <i class="fi-rr-arrow-left me-2"></i>{{ get_phrase('Back') }}
            </a>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-20px">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <form action="{{ route('admin.shop.product.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Product Name') }} *</label>
                        <input type="text" name="name" class="form-control ol-form-control" required value="{{ old('name') }}">
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Price') }} *</label>
                                <input type="number" name="price" class="form-control ol-form-control" step="0.01" min="0" required value="{{ old('price') }}">
                                @error('price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Category') }}</label>
                                <select name="category_id" class="form-control ol-form-control">
                                    <option value="">{{ get_phrase('Select Category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Short Description') }}</label>
                        <textarea name="short_description" class="form-control ol-form-control" rows="3">{{ old('short_description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Description') }}</label>
                        <textarea name="description" class="form-control ol-form-control editor" rows="5">{{ old('description') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Stock Quantity') }}</label>
                                <input type="number" name="stock_quantity" class="form-control ol-form-control" value="{{ old('stock_quantity', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Status') }}</label>
                                <select name="is_published" class="form-control ol-form-control">
                                    <option value="1" {{ old('is_published', 1) == 1 ? 'selected' : '' }}>{{ get_phrase('Published') }}</option>
                                    <option value="0" {{ old('is_published') == 0 ? 'selected' : '' }}>{{ get_phrase('Draft') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="track_stock" class="form-check-input" id="track_stock" value="1" {{ old('track_stock') ? 'checked' : '' }}>
                            <label class="form-check-label" for="track_stock">{{ get_phrase('Track Stock') }}</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">{{ get_phrase('Featured Product') }}</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Featured Image') }}</label>
                        <input type="file" name="featured_image" class="form-control ol-form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Gallery Images') }}</label>
                        <input type="file" name="images[]" class="form-control ol-form-control" accept="image/*" multiple>
                        <small class="text-muted">{{ get_phrase('You can select multiple images') }}</small>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Save Product') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection
