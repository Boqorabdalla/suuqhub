@extends('layouts.frontend')
@push('title', get_phrase('My Created Services'))
@push('meta')@endpush
@section('frontend_layout')

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

<section class="ca-wraper-main mb-90px mt-4">
    <div class="container">
        <div class="row gx-20px">
            <div class="col-lg-4 col-xl-3">
                @include('user.navigation')
            </div>
            <div class="col-lg-8 col-xl-9">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-20px">
                    <div class="d-flex justify-content-between align-items-start gap-12px flex-column flex-lg-row w-100">
                        <h1 class="ca-title-18px">{{get_phrase('My Created Services')}}</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb cap-breadcrumb">
                                <li class="breadcrumb-item cap-breadcrumb-item"><a href="{{route('home')}}">{{get_phrase('Home')}}</a></li>
                                <li class="breadcrumb-item cap-breadcrumb-item active" aria-current="page">{{ get_phrase('My Created Services') }}</li>
                            </ol>
                        </nav> 
                    </div>
                    <button class="btn ca-menu-btn-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#user-sidebar-offcanvas" aria-controls="user-sidebar-offcanvas">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 5.25H3C2.59 5.25 2.25 4.91 2.25 4.5C2.25 4.09 2.59 3.75 3 3.75H21C21.41 3.75 21.75 4.09 21.75 4.5C21.75 4.91 21.41 5.25 21 5.25Z" fill="#242D47"/>
                            <path d="M21 10.25H3C2.59 10.25 2.25 9.91 2.25 9.5C2.25 9.09 2.59 8.75 3 8.75H21C21.41 8.75 21.75 9.09 21.75 9.5C21.75 9.91 21.41 10.25 21 10.25Z" fill="#242D47"/>
                            <path d="M21 15.25H3C2.59 15.25 2.25 14.91 2.25 14.5C2.25 14.09 2.59 13.75 3 13.75H21C21.41 13.75 21.75 14.09 21.75 14.5C21.75 14.91 21.41 15.25 21 15.25Z" fill="#242D47"/>
                            <path d="M21 20.25H3C2.59 20.25 2.25 19.91 2.25 19.5C2.25 19.09 2.59 18.75 3 18.75H21C21.41 18.75 21.75 19.09 21.75 19.5C21.75 19.91 21.41 20.25 21 20.25Z" fill="#242D47"/>
                        </svg>
                    </button>
                </div>

                <form method="GET" class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select name="listing_id" class="form-control select2">
                                <option value="">{{ get_phrase('Select Listing') }}</option>
                                @foreach($listings as $listing)
                                    <option value="{{ $listing->id }}" {{ request('listing_id') == $listing->id ? 'selected' : '' }}>
                                        {{ $listing->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">{{ get_phrase('All Status') }}</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ get_phrase('Active') }}</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{ get_phrase('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn ol-btn-primary">{{ get_phrase('Filter') }}</button>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="{{ route('agent.my_created_services') }}" class="btn btn-secondary">{{ get_phrase('Reset') }}</a>
                        </div>
                    </div>
                </form>

                <div class="ca-content-card">
                    @if(count($services))
                    <div class="table-responsive pb-1">
                        <table class="table ca-table ca-table-width">
                            <thead class="ca-thead">
                                <tr class="ca-tr">
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase('ID') }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase('Image') }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase('Service Name') }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase('Listing') }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase('Price') }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase('Duration') }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase('Status') }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark text-center">{{ get_phrase('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="ca-tbody" style="vertical-align: inherit;">
                                @php $num = 1 @endphp
                                @foreach($services as $service)
                                @php
                                    $listing = App\Models\BeautyListing::find($service->listing_id);
                                @endphp
                                <tr class="ca-tr">
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
                                        <p class="ca-subtitle-14px ca-text-dark fw-bold mb-1">{{ $service->name }}</p>
                                        <p class="ca-subtitle-12px text-muted mb-0">{{ Str::limit($service->description, 40) }}</p>
                                    </td>
                                    <td>
                                        @if($listing)
                                            <p class="ca-subtitle-14px ca-text-dark mb-0">{{ $listing->name }}</p>
                                        @else
                                            <span class="text-muted">{{ get_phrase('N/A') }}</span>
                                        @endif
                                    </td>
                                    <td class="ca-subtitle-14px ca-text-dark">{{ currency($service->price) }}</td>
                                    <td class="ca-subtitle-14px ca-text-dark">
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
                                        <div class="dropdown">
                                            <button class="btn at-dropdown-icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <img src="{{ asset('assets/frontend/images/icons/menu-dots-vertical-14.svg') }}" alt="icon">
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end at-dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('agent.service.edit', ['type' => 'beauty', 'listing_id' => $service->listing_id, 'id' => $service->id]) }}">
                                                        <i class="bi bi-pencil"></i> {{ get_phrase('Edit') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('agent.my_created_services.status', $service->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="{{ $service->status == 1 ? 0 : 1 }}">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-toggle-on"></i> {{ $service->status == 1 ? get_phrase('Deactivate') : get_phrase('Activate') }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" onclick="delete_modal('{{ route('agent.my_created_services.delete', $service->id) }}')" href="javascript:void(0)">
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
                        <div class="mt-20px d-flex align-items-center gap-3 justify-content-between flex-wrap ePagination">
                            <p class="in-subtitle-12px">{{ get_phrase('Showing') }} {{ $services->firstItem() }} {{ get_phrase('to') }} {{ $services->lastItem() }} {{ get_phrase('of') }} {{ $services->total() }} {{ get_phrase('results') }} </p>
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                {{ $services->links() }}
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">{{ get_phrase('No services found') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@include('layouts.modal')

@endsection