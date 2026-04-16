@extends('layouts.admin')
@section('title', get_phrase('All Services'))
@section('admin_layout')

<style>
    .service-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .min-w-150px {
        min-width: 150px;
    }
</style>

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-list ml-2"></i>
                {{ get_phrase('All Services') }}
            </h4>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> {{ get_phrase('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </div>
</div>

<form method="GET" class="mb-3 mt-3">
    <div class="row g-3">
        <div class="col-md-3">
            <select name="user_id" class="form-control select2">
                <option value="">{{ get_phrase('Select Agent') }}</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" {{ request('user_id') == $agent->id ? 'selected' : '' }}>
                        {{ $agent->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="listing_id" class="form-control select2">
                <option value="">{{ get_phrase('Select Listing') }}</option>
                @foreach($listings as $listing)
                    <option value="{{ $listing->id }}" {{ request('listing_id') == $listing->id ? 'selected' : '' }}>
                        {{ $listing->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">{{ get_phrase('All Status') }}</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ get_phrase('Active') }}</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{ get_phrase('Inactive') }}</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">{{ get_phrase('Filter') }}</button>
        </div>
        <div class="col-md-2 text-end">
            <a href="{{ route('admin.all_services') }}" class="btn btn-secondary">{{ get_phrase('Reset') }}</a>
        </div>
    </div>
</form>

<div class="ol-card mt-3">
    <div class="ol-card-body p-3">
        @if(count($services))
        <table id="datatable" class="table nowrap w-100">
            <thead>
                <tr>
                    <th>{{ get_phrase('ID') }}</th>
                    <th>{{ get_phrase('Image') }}</th>
                    <th>{{ get_phrase('Service Name') }}</th>
                    <th>{{ get_phrase('Agent') }}</th>
                    <th>{{ get_phrase('Listing') }}</th>
                    <th>{{ get_phrase('Price') }}</th>
                    <th>{{ get_phrase('Duration') }}</th>
                    <th>{{ get_phrase('Status') }}</th>
                    <th>{{ get_phrase('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $num = 1 @endphp
                @foreach($services as $service)
                @php
                    $listing = App\Models\BeautyListing::find($service->listing_id);
                    $agent = App\Models\User::find($service->user_id);
                @endphp
                <tr>
                    <td>{{ $num++ }}</td>
                    <td>
                        @if($service->image)
                            <img src="{{ asset('uploads/service_selling/' . $service->image) }}" alt="{{ $service->name }}" class="service-img">
                        @else
                            <div class="service-img bg-light d-flex align-items-center justify-content-center">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td class="min-w-150px">
                        <p class="text-dark fw-bold">{{ $service->name }}</p>
                        <p class="text-muted small">{{ Str::limit($service->description, 50) }}</p>
                    </td>
                    <td>
                        @if($agent)
                            <p class="mb-0">{{ $agent->name }}</p>
                            <p class="text-muted small">{{ $agent->email }}</p>
                        @else
                            <span class="text-muted">{{ get_phrase('N/A') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($listing && $listing->name)
                            <p class="mb-0">{{ $listing->name }}</p>
                        @else
                            <span class="text-muted">{{ get_phrase('N/A') }}</span>
                        @endif
                    </td>
                    <td>{{ currency($service->price) }}</td>
                    <td>
                        @php
                            $hours = floor($service->duration / 60);
                            $minutes = $service->duration % 60;
                        @endphp
                        {{ $hours > 0 ? $hours . 'h ' : '' }}{{ $minutes > 0 ? $minutes . 'm' : '' }}
                    </td>
                    <td>
                        @if($service->status == 1)
                            <span class="badge bg-success">{{ get_phrase('Active') }}</span>
                        @else
                            <span class="badge bg-danger">{{ get_phrase('Inactive') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown ol-icon-dropdown">
                            <button class="px-2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="fi-rr-menu-dots-vertical"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.service.edit', ['type' => 'beauty', 'listing_id' => $service->listing_id, 'id' => $service->id]) }}">
                                        <i class="bi bi-pencil"></i> {{ get_phrase('Edit') }}
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('admin.all_services.status', $service->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $service->status == 1 ? 0 : 1 }}">
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-toggle-on"></i> {{ $service->status == 1 ? get_phrase('Deactivate') : get_phrase('Activate') }}
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" onclick="delete_modal('{{ route('admin.all_services.delete', $service->id) }}')" href="javascript:void(0)">
                                        <i class="bi bi-trash"></i> {{ get_phrase('Delete') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            {{ $services->links() }}
        </div>
        @else
        @include('layouts.no_data_found')
        @endif
    </div>
</div>

@endsection