@extends('layouts.admin')
@section('title', get_phrase('Inventory Categories'))
@section('admin_layout')
<div class="mb-2">
    <a href="{{ route('admin.shop.inventory') }}" class="btn btn-outline-secondary">
        <i class="fi-rr-arrow-left"></i> {{ get_phrase('Back to Inventory') }}
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ get_phrase('Inventory Categories') }}</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fi-rr-plus"></i> {{ get_phrase('Add Category') }}
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ get_phrase('Name') }}</th>
                        <th>{{ get_phrase('Type') }}</th>
                        <th>{{ get_phrase('Items') }}</th>
                        <th>{{ get_phrase('Status') }}</th>
                        <th>{{ get_phrase('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td><span class="badge bg-secondary">{{ ucfirst($category->type ?? 'General') }}</span></td>
                        <td>{{ $category->items->count() }}</td>
                        <td>
                            @if($category->status)
                                <span class="badge bg-success">{{ get_phrase('Active') }}</span>
                            @else
                                <span class="badge bg-danger">{{ get_phrase('Inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.shop.inventory.category.delete', $category->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                <i class="fi-rr-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <p class="text-muted">{{ get_phrase('No categories found') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.shop.inventory.category.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ get_phrase('Add Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ get_phrase('Name') }} *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
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
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                            <label class="form-check-label">{{ get_phrase('Active') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ get_phrase('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ get_phrase('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
