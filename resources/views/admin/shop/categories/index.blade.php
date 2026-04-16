@extends('layouts.admin')
@section('title', get_phrase('Product Categories'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-list me-2"></i>
                {{ get_phrase('Product Categories') }}
            </h4>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-20px">
        <div class="row">
            <div class="col-md-4">
                <div class="card border">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">{{ get_phrase('Add New Category') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.shop.category.store') }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Category Name') }} *</label>
                                <input type="text" name="name" class="form-control ol-form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Description') }}</label>
                                <textarea name="description" class="form-control ol-form-control" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Sort Order') }}</label>
                                <input type="number" name="sort_order" class="form-control ol-form-control" value="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Status') }}</label>
                                <select name="status" class="form-control ol-form-control">
                                    <option value="1">{{ get_phrase('Active') }}</option>
                                    <option value="0">{{ get_phrase('Inactive') }}</option>
                                </select>
                            </div>
                            <button type="submit" class="btn ol-btn-primary w-100">{{ get_phrase('Add Category') }}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                @if(count($categories))
                <table class="table nowrap w-100">
                    <thead>
                        <tr>
                            <th>{{ get_phrase('ID') }}</th>
                            <th>{{ get_phrase('Name') }}</th>
                            <th>{{ get_phrase('Description') }}</th>
                            <th>{{ get_phrase('Sort Order') }}</th>
                            <th>{{ get_phrase('Status') }}</th>
                            <th>{{ get_phrase('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $num = 1 @endphp
                        @foreach($categories as $category)
                        <tr>
                            <td>{{ $num++ }}</td>
                            <td><strong>{{ $category->name }}</strong></td>
                            <td>{{ Str::limit($category->description, 50) ?? '-' }}</td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                @if($category->status)
                                    <span class="badge bg-success">{{ get_phrase('Active') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ get_phrase('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown ol-icon-dropdown">
                                    <button class="px-2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="fi-rr-menu-dots-vertical"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item fs-14px" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}">
                                                <i class="fi-rr-edit me-2"></i>{{ get_phrase('Edit') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item fs-14px" onclick="delete_modal('{{ route('admin.shop.category.delete', $category->id) }}')">
                                                <i class="fi-rr-trash me-2"></i>{{ get_phrase('Delete') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.shop.category.update', $category->id) }}" method="post">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ get_phrase('Edit Category') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Category Name') }} *</label>
                                                <input type="text" name="name" class="form-control ol-form-control" value="{{ $category->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Description') }}</label>
                                                <textarea name="description" class="form-control ol-form-control" rows="2">{{ $category->description }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Sort Order') }}</label>
                                                <input type="number" name="sort_order" class="form-control ol-form-control" value="{{ $category->sort_order }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">{{ get_phrase('Status') }}</label>
                                                <select name="status" class="form-control ol-form-control">
                                                    <option value="1" {{ $category->status ? 'selected' : '' }}>{{ get_phrase('Active') }}</option>
                                                    <option value="0" {{ !$category->status ? 'selected' : '' }}>{{ get_phrase('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ get_phrase('Close') }}</button>
                                            <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Update') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
                @else
                    @include('layouts.no_data_found')
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
