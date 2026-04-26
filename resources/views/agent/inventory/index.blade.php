@extends('layouts.admin')
@push('title')
{{ get_phrase('Inventory Management') }}
@endpush
@push('styles')
<style>
    .stock-low { color: #ffc107; }
    .stock-out { color: #dc3545; }
    .stock-ok { color: #198754; }
</style>
@endpush
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{ get_phrase('Inventory Management') }}</h4>
                        <div class="page-title-right">
                            <a href="{{ route('agent.inventory.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>{{ get_phrase('Add New Item') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('agent.inventory') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ get_phrase('Search') }}</label>
                            <input type="text" name="search" class="form-control" placeholder="{{ get_phrase('Item name...') }}" value="{{ $filters['search'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ get_phrase('Listing') }}</label>
                            <select name="listing_id" class="form-select">
                                <option value="">{{ get_phrase('All Listings') }}</option>
                                @foreach($listings as $listing)
                                    <option value="{{ $listing->id }}" {{ ($filters['listing_id'] ?? '') == $listing->id ? 'selected' : '' }}>{{ $listing->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ get_phrase('Stock Status') }}</label>
                            <select name="stock_status" class="form-select">
                                <option value="">{{ get_phrase('All Stock') }}</option>
                                <option value="in_stock" {{ ($filters['stock_status'] ?? '') == 'in_stock' ? 'selected' : '' }}>{{ get_phrase('In Stock') }}</option>
                                <option value="low_stock" {{ ($filters['stock_status'] ?? '') == 'low_stock' ? 'selected' : '' }}>{{ get_phrase('Low Stock (≤5)') }}</option>
                                <option value="out_of_stock" {{ ($filters['stock_status'] ?? '') == 'out_of_stock' ? 'selected' : '' }}>{{ get_phrase('Out of Stock') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ get_phrase('Availability') }}</label>
                            <select name="availability" class="form-select">
                                <option value="">{{ get_phrase('All') }}</option>
                                <option value="1" {{ ($filters['availability'] ?? '') == '1' ? 'selected' : '' }}>{{ get_phrase('Available') }}</option>
                                <option value="0" {{ ($filters['availability'] ?? '') == '0' ? 'selected' : '' }}>{{ get_phrase('Unavailable') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-funnel me-2"></i>{{ get_phrase('Filter') }}
                            </button>
                            <a href="{{ route('agent.inventory') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="card">
                <div class="card-body">
                    @if($inventories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ get_phrase('Item') }}</th>
                                        <th>{{ get_phrase('Listing') }}</th>
                                        <th>{{ get_phrase('Price') }}</th>
                                        <th>{{ get_phrase('Stock') }}</th>
                                        <th>{{ get_phrase('SKU') }}</th>
                                        <th>{{ get_phrase('Status') }}</th>
                                        <th>{{ get_phrase('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventories as $inventory)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                @if($inventory->featured_image)
                                                    <img src="{{ asset('uploads/listing/'.$inventory->featured_image) }}" alt="" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $inventory->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($inventory->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($inventory->listing)
                                                <span class="badge bg-info">{{ $inventory->listing->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ currency($inventory->price) }}</strong>
                                            @if($inventory->discount_price && $inventory->discount_price < $inventory->price)
                                                <br>
                                                <small class="text-decoration-line-through text-muted">{{ currency($inventory->discount_price) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inventory->track_stock)
                                                <span class="stock-qty {{ $inventory->stock_quantity <= 0 ? 'stock-out' : ($inventory->stock_quantity <= 5 ? 'stock-low' : 'stock-ok') }}">
                                                    {{ $inventory->stock_quantity }}
                                                </span>
                                            @else
                                                <span class="text-muted">{{ get_phrase('Not tracked') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inventory->sku)
                                                <code>{{ $inventory->sku }}</code>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inventory->availability)
                                                <span class="badge bg-success">{{ get_phrase('Available') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ get_phrase('Unavailable') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('agent.inventory.edit', $inventory->id) }}" class="btn btn-sm btn-outline-primary" title="{{ get_phrase('Edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="{{ route('agent.inventory.stock.update', $inventory->id) }}" class="btn btn-sm btn-outline-warning" title="{{ get_phrase('Quick Stock Edit') }}" onclick="event.preventDefault(); document.getElementById('stock-form-{{ $inventory->id }}').classList.toggle('d-none')">
                                                    <i class="bi bi-box-seam"></i>
                                                </a>
                                                <a href="{{ route('agent.inventory.delete', $inventory->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')" title="{{ get_phrase('Delete') }}">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                            <form id="stock-form-{{ $inventory->id }}" action="{{ route('agent.inventory.stock.update', $inventory->id) }}" method="POST" class="d-none mt-2 stock-ajax-form">
                                                @csrf
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="stock_quantity" class="form-control" value="{{ $inventory->stock_quantity }}" min="0">
                                                    <button type="submit" class="btn btn-success">{{ get_phrase('Save') }}</button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $inventories->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-box-seam text-muted" style="font-size: 64px;"></i>
                            <h5 class="mt-3 text-muted">{{ get_phrase('No inventory items found') }}</h5>
                                @if($listings->count() == 0)
                                <div class="alert alert-warning mt-3">
                                    <strong>No listings found!</strong> You need to create a listing first before adding inventory.
                                    <br><a href="{{ route('agent.create.category', ['type' => 'beauty']) }}" class="btn btn-sm btn-primary mt-2">Create Listing</a>
                                </div>
                                @endif
                            <p class="text-muted">
                                @if(request()->has('search') || request()->has('listing_id') || request()->has('stock_status') || request()->has('availability'))
                                    {{ get_phrase('Try adjusting your filters') }}
                                @else
                                    {{ get_phrase('Start adding inventory items to your listings') }}
                                @endif
                            </p>
                            <a href="{{ route('agent.inventory.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>{{ get_phrase('Add First Item') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).on('submit', '.stock-ajax-form', function(e) {
    e.preventDefault();
    var form = $(this);
    var formData = new FormData(this);
    var url = form.attr('action');
    var row = form.closest('tr');
    
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            row.find('.stock-qty').text(form.find('input[name="stock_quantity"]').val());
            form.addClass('d-none');
            toastr.success('Stock updated successfully');
        },
        error: function(xhr) {
            toastr.error('Error updating stock');
        }
    });
});
</script>
@endpush
@endsection
