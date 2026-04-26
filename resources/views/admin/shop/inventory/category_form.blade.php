<form action="{{ $prefix == 'agent' ? route('admin.shop.inventory.category.store') : route('admin.shop.inventory.category.store') }}" method="POST" class="category-form">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" name="listing_id" value="{{ $listing_id }}">
    
    <div class="mb-3">
        <label class="form-label">{{ get_phrase('Name') }} *</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">{{ get_phrase('Description') }}</label>
        <textarea name="description" class="form-control" rows="2"></textarea>
    </div>
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="status" value="1" checked>
            <label class="form-check-label">{{ get_phrase('Active') }}</label>
        </div>
    </div>
    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ get_phrase('Close') }}</button>
        <button type="submit" class="btn btn-primary">{{ get_phrase('Save') }}</button>
    </div>
</form>