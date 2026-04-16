@extends('layouts.admin')
@section('title', get_phrase('Inventory Items'))
@section('admin_layout')
<div class="mb-2 d-flex justify-content-between align-items-center">
    <div class="d-flex gap-2">
        <a href="{{ route('admin.shop.inventory.create') }}" class="btn btn-primary">
            <i class="fi-rr-plus"></i> {{ get_phrase('Add New Item') }}
        </a>
        <a href="{{ route('admin.shop.inventory.categories') }}" class="btn btn-outline-secondary">
            <i class="fi-rr-list"></i> {{ get_phrase('Categories') }}
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ get_phrase('Inventory Items') }}</h5>
            <form action="{{ route('admin.shop.inventory') }}" method="get" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ get_phrase('Search...') }}" value="{{ request('search') }}">
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">{{ get_phrase('All Types') }}</option>
                    <option value="beauty" {{ request('type') == 'beauty' ? 'selected' : '' }}>Beauty</option>
                    <option value="hotel" {{ request('type') == 'hotel' ? 'selected' : '' }}>Hotel</option>
                    <option value="restaurant" {{ request('type') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                    <option value="car" {{ request('type') == 'car' ? 'selected' : '' }}>Car</option>
                    <option value="real-estate" {{ request('type') == 'real-estate' ? 'selected' : '' }}>Real Estate</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">{{ get_phrase('Filter') }}</button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ get_phrase('Image') }}</th>
                        <th>{{ get_phrase('Name') }}</th>
                        <th>{{ get_phrase('Type') }}</th>
                        <th>{{ get_phrase('Price') }}</th>
                        <th>{{ get_phrase('Stock') }}</th>
                        <th>{{ get_phrase('Status') }}</th>
                        <th>{{ get_phrase('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>
                            @if($item->featured_image)
                                <img src="{{ asset('uploads/shop/inventory/'.$item->featured_image) }}" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fi-rr-picture text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            @if($item->sku)
                                <br><small class="text-muted">SKU: {{ $item->sku }}</small>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary">{{ ucfirst($item->type ?? 'General') }}</span></td>
                        <td>
                            {{ currency($item->price) }}
                            @if($item->discount_price)
                                <br><small class="text-danger"><del>{{ currency($item->discount_price) }}</del></small>
                            @endif
                        </td>
                        <td>
                            @if($item->track_stock)
                                {{ $item->stock_quantity }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->availability)
                                <span class="badge bg-success">{{ get_phrase('Available') }}</span>
                            @else
                                <span class="badge bg-danger">{{ get_phrase('Unavailable') }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.shop.inventory.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fi-rr-edit"></i>
                            </a>
                            <a href="{{ route('admin.shop.inventory.delete', $item->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                <i class="fi-rr-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fi-rr-box-open text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2">{{ get_phrase('No items found') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->withQueryString()->links() }}
    </div>
</div>
@endsection
