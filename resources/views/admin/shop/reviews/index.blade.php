@extends('layouts.admin')
@section('title', get_phrase('Product Reviews'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-star me-2"></i>
                {{ get_phrase('Product Reviews') }}
            </h4>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-3">
        <div class="row mb-3">
            <div class="col-md-4">
                <form action="{{ route('admin.shop.reviews') }}" method="get">
                    <select name="status" class="form-control ol-form-control" onchange="this.form.submit()">
                        <option value="">{{ get_phrase('All Reviews') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ get_phrase('Approved') }}</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ get_phrase('Rejected') }}</option>
                    </select>
                </form>
            </div>
        </div>

        @if(count($reviews))
        <table id="datatable" class="table nowrap w-100">
            <thead>
                <tr>
                    <th>{{ get_phrase('ID') }}</th>
                    <th>{{ get_phrase('Product') }}</th>
                    <th>{{ get_phrase('Customer') }}</th>
                    <th>{{ get_phrase('Rating') }}</th>
                    <th>{{ get_phrase('Comment') }}</th>
                    <th>{{ get_phrase('Status') }}</th>
                    <th>{{ get_phrase('Date') }}</th>
                    <th>{{ get_phrase('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $num = 1 @endphp
                @foreach($reviews as $review)
                <tr>
                    <td>{{ $num++ }}</td>
                    <td>
                        <a href="{{ route('shop.product', $review->product->slug ?? '#') }}" class="text-decoration-none">
                            {{ $review->product->name ?? 'N/A' }}
                        </a>
                    </td>
                    <td>{{ $review->user->name ?? 'N/A' }}</td>
                    <td>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fi-rr-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                    </td>
                    <td>
                        <span class="d-inline-block text-truncate" style="max-width: 200px;">
                            {{ Str::limit($review->comment, 50) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusClass = match($review->status) {
                                'pending' => 'bg-warning text-dark',
                                'approved' => 'bg-success',
                                'rejected' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($review->status) }}</span>
                    </td>
                    <td>{{ date_formatter($review->created_at) }}</td>
                    <td>
                        <div class="dropdown ol-icon-dropdown">
                            <button class="px-2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="fi-rr-menu-dots-vertical"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @if($review->status != 'approved')
                                    <li>
                                        <form action="{{ route('admin.shop.review.status', $review->id) }}" method="post" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="dropdown-item fs-14px">
                                                <i class="fi-rr-check me-2"></i>{{ get_phrase('Approve') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                @if($review->status != 'rejected')
                                    <li>
                                        <form action="{{ route('admin.shop.review.status', $review->id) }}" method="post" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="dropdown-item fs-14px">
                                                <i class="fi-rr-cross me-2"></i>{{ get_phrase('Reject') }}
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                <li>
                                    <a class="dropdown-item fs-14px" onclick="delete_modal('{{ route('admin.shop.review.delete', $review->id) }}')">
                                        <i class="fi-rr-trash me-2"></i>{{ get_phrase('Delete') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {{ $reviews->links() }}
        </div>
        @else
            @include('layouts.no_data_found')
        @endif
    </div>
</div>

@endsection
