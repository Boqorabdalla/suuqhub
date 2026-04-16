@extends('layouts.admin')
@section('title', get_phrase('Shop Products'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-shop me-2"></i>
                {{ get_phrase('Shop Products') }}
            </h4>
            <a href="{{ route('admin.shop.product.create') }}" class="btn ol-btn-primary">
                <i class="fi-rr-plus me-2"></i>{{ get_phrase('Add New Product') }}
            </a>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-3">
        <div class="row mb-3">
            <div class="col-md-4">
                <form action="{{ route('admin.shop.products') }}" method="get">
                    <input type="text" name="search" class="form-control ol-form-control" placeholder="{{ get_phrase('Search products...') }}" value="{{ request('search') }}">
                </form>
            </div>
            <div class="col-md-3">
                <form action="{{ route('admin.shop.products') }}" method="get">
                    <select name="category" class="form-control ol-form-control" onchange="this.form.submit()">
                        <option value="">{{ get_phrase('All Categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        @if(count($products))
        <table id="datatable" class="table nowrap w-100">
            <thead>
                <tr>
                    <th>{{ get_phrase('ID') }}</th>
                    <th>{{ get_phrase('Image') }}</th>
                    <th>{{ get_phrase('Product Name') }}</th>
                    <th>{{ get_phrase('Category') }}</th>
                    <th>{{ get_phrase('Price') }}</th>
                    <th>{{ get_phrase('Stock') }}</th>
                    <th>{{ get_phrase('Status') }}</th>
                    <th>{{ get_phrase('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $num = 1 @endphp
                @foreach($products as $product)
                <tr>
                    <td>{{ $num++ }}</td>
                    <td>
                        @if($product->featured_image)
                            <img src="{{ asset('uploads/shop/products/'.$product->featured_image) }}" alt="" class="img-fluid rounded" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="bg-secondary rounded" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="fi-rr-picture text-white"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <p class="sub-title2 text-13px">{{ $product->name }}</p>
                        <p class="text-muted text-11px">{{ $product->category->name ?? '-' }}</p>
                    </td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ currency($product->price) }}</td>
                    <td>
                        @if($product->track_stock)
                            @if($product->stock_quantity > 0)
                                <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                            @else
                                <span class="badge bg-danger">{{ get_phrase('Out of Stock') }}</span>
                            @endif
                        @else
                            <span class="badge bg-info">{{ get_phrase('Untracked') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($product->is_published)
                            <span class="badge bg-success">{{ get_phrase('Published') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ get_phrase('Draft') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown ol-icon-dropdown">
                            <button class="px-2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="fi-rr-menu-dots-vertical"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item fs-14px" href="{{ route('admin.shop.product.edit', $product->id) }}"><i class="fi-rr-edit me-2"></i>{{ get_phrase('Edit') }}</a></li>
                                <li><a class="dropdown-item fs-14px" onclick="delete_modal('{{route('admin.shop.product.delete', $product->id)}}')"><i class="fi-rr-trash me-2"></i>{{ get_phrase('Delete') }}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
        @else
            @include('layouts.no_data_found')
        @endif
    </div>
</div>

@endsection
