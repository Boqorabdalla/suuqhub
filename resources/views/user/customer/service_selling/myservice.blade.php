@extends('layouts.frontend')
@push('title', get_phrase('My Services'))
@push('meta')@endpush
@section('frontend_layout')

<style>
   .object {
	position: relative;
	padding-left: 10px;
}
    .object::after {
	position: absolute;
	content: "";
	top: 43%;
	left: 0;
	width: 5px;
	height: 5px;
	background: #000;
	border-radius: 50%;
}
.eMessage p {
	line-height: 23px;
	font-size: 13px;
}
.min-w-110px{
    min-width: 144px;
}
</style>

<!-- Start Main Area -->
<section class="ca-wraper-main mb-90px mt-4">
    <div class="container">
        <div class="row gx-20px">
            <div class="col-lg-4 col-xl-3">
                @include('user.navigation')
            </div>
            <div class="col-lg-8 col-xl-9">
                <!-- Header -->
                <div class="d-flex align-items-start justify-content-between gap-2 mb-20px">
                    <div class="d-flex justify-content-between align-items-start gap-12px flex-column flex-lg-row w-100">
                        <h1 class="ca-title-18px">{{get_phrase('My Service')}}</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb cap-breadcrumb">
                                <li class="breadcrumb-item cap-breadcrumb-item"><a href="{{route('home')}}">{{get_phrase('Home')}}</a></li>
                                <li class="breadcrumb-item cap-breadcrumb-item active" aria-current="page">{{get_phrase('My Service')}}</li>
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
                <div class="ca-content-card">
                    <div class="table-responsive pb-1">
                        <table class="table ca-table ca-table-width">
                            <thead class="ca-thead">
                              <tr class="ca-tr">
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase('ID') }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase("Provider") }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase('Service Info') }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase('Customer Details') }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark">{{ get_phrase('Scheduled Date') }}</th>
                                    <th scope="col" class="ca-title-14px ca-text-dark text-center">{{ get_phrase('Actions') }}</th>
                                </tr>

                            </thead>
                            <tbody class="ca-tbody" style="vertical-align: inherit;">
                                @php $num = 1 @endphp
                                @foreach ($myservice as $key => $service)
                                 @php
                                   $employeeinfo = App\Models\ServiceEmployee::where('id', $service->employee_id)->first();
                                    $serviceinfo = App\Models\ServiceSelling::where('id', $service->service_selling_id)->first();
                                @endphp
                               
                                <tr class="ca-tr">
                                  <td>
                                     {{++$key}}
                                  </td>
                                  <td class="ca-subtitle-14px ca-text-dark min-w-110px">
                                       <p> <span class="capitalize">{{$employeeinfo->name ?? ''}} </span></p>
                                    </td>
                                  <td class="ca-subtitle-14px ca-text-dark min-w-110px">
                                       <p> <span class="capitalize">{{$serviceinfo->name}}</span></p>
                                        <p >{{get_phrase('Status : ')}}
                                             @if($service->status == 1) 
                                                     <span class="badge bg-success">{{ get_phrase('Approve') }}</span> 
                                                 @elseif($service->status == 2)
                                                     <span class="badge bg-secondary">{{ get_phrase('Cancelled') }}</span>
                                                 @else 
                                                 <span class="badge bg-danger">{{ get_phrase('Pending') }}</span>
                                             @endif
                                         </p>
                                         @if($service->cancellation_status == 'pending')
                                             <span class="badge bg-warning text-dark">{{ get_phrase('Cancellation Pending') }}</span>
                                         @endif
                                        <p>{{get_phrase('Pay : ')}} {{currency($serviceinfo->price)}}</p>
                                         <p >{{get_phrase('Status : ')}}
                                                @if($service->payment_status == 1) 
                                                        <span class="badge bg-success">{{ get_phrase('Paid') }}</span> 
                                                    @else 
                                                    <span class="badge bg-danger">{{ get_phrase('Unpaid') }}</span>
                                                @endif
                                            </p>
                                    </td>
                                  <td>
                                     <div class="min-w-140px ca-subtitle-14px ca-text-dark">
                                        <p>{{get_phrase('Name : ')}} {{$service->name}}</p>
                                         <p style="display: flex; ">{{get_phrase('Email : ')}} <span style="text-transform: lowercase;">{{$service->email}}</span></p>
                                        <p>{{get_phrase('Phone : ')}} {{$service->phone}}</p>
                                        <div class="eMessage">
                                            <p class="ca-subtitle-14px ca-text-dark mb-6px mb-2">
                                                <span class="short-text">
                                                    {{ \Illuminate\Support\Str::words($service->notes, 60, '...') }}
                                                </span>
                                                <span class="full-text d-none">
                                                    {{ $service->notes }}
                                                </span>
                                                @if(str_word_count($service->notes) > 60)
                                                    <a href="javascript:void(0)" class="read-more-toggle read-more">{{ get_phrase('Read More') }}</a>
                                                @endif
                                            </p>
                                            
                                        </div>  
                                     </div>
                                  </td>
                                  <td >
                                        <div class="ca-subtitle-14px ca-text-dark text-nowrap min-w-110px">
                                             <p>{{$service->service_date}}</p>
                                             <p>{{$service->service_day}}</p>
                                             <p>{{get_phrase('Time : ')}}{{$service->service_time}}</p>
                                              @php
                                                    $duration = $serviceinfo->duration;
                                                    $hours = floor($duration / 60);
                                                    $minutes = $duration % 60;
                                              @endphp
                                             <p>{{get_phrase('Duration : ')}}
                                                {{ $hours > 0 ? $hours . ' h' . ($hours > 1 ? 's' : '') : '' }}
                                                {{ $minutes > 0 ? $minutes . ' m' . ($minutes > 1 ? 's' : '') : '' }}</p>
                                        </div>
                                  </td>
<td>
                                       <div class="d-flex justify-content-center">
                                           <div class="dropdown">
                                               <button class="btn at-dropdown-icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                   <img src="{{ asset('assets/frontend/images/icons/menu-dots-vertical-14.svg') }}" alt="icon">
                                               </button>
                                               <ul class="dropdown-menu dropdown-menu-end at-dropdown-menu">
                                                   @if($service->status != 2 && $service->cancellation_status == 'none')
                                                       <li><a class="dropdown-item" href="javascript:void(0)" onclick="showCancelModal({{$service->id}})">{{get_phrase('Cancel Booking')}}</a></li>
                                                   @endif
                                                   @if($service->cancellation_status == 'pending')
                                                       <li><span class="dropdown-item text-warning">{{get_phrase('Cancellation Pending')}}</span></li>
                                                   @endif
                                                   <li><a class="dropdown-item text-danger" onclick="delete_modal('{{route('customer.service_manager.delete',['id'=>$service->id])}}')" href="javascript:void(0)">{{get_phrase('Delete')}}</a></li>
                                               </ul>
                                           </div>
                                       </div>
                                   </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-20px d-flex align-items-center gap-3 justify-content-between flex-wrap ePagination">
                            <p class="in-subtitle-12px">{{get_phrase('Showing').'  to '.count($myservice).' '.get_phrase('of').' '.count($myservice).' '.get_phrase('results')}} </p>
                            <div class="d-flex align-items-center gap-1 flex-wrap ">
                                {{$myservice->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('layouts.modal')

<!-- Cancellation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">{{ get_phrase('Cancel Booking') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="cancelForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">{{ get_phrase('Are you sure you want to cancel this booking? Please provide a reason for cancellation.') }}</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">{{ get_phrase('Cancellation Reason') }} *</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required placeholder="{{ get_phrase('Please explain why you need to cancel this booking...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ get_phrase('Close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ get_phrase('Submit Cancellation') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    "use strict";   
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.read-more').forEach(function (button) {
        button.addEventListener('click', function () {
            const parent = this.closest('p');
            const shortText = parent.querySelector('.short-text');
            const fullText = parent.querySelector('.full-text');

            if (shortText.classList.contains('d-inline')) {
                shortText.classList.remove('d-inline');
                shortText.classList.add('d-none');
                fullText.classList.remove('d-none');
                fullText.classList.add('d-inline');
                this.textContent = 'Show Less';
            } else {
                shortText.classList.remove('d-none');
                shortText.classList.add('d-inline');
                fullText.classList.remove('d-inline');
                fullText.classList.add('d-none');
                this.textContent = 'Read More';
            }
        });
    });
});

function showCancelModal(bookingId) {
    const form = document.getElementById('cancelForm');
    form.action = '{{ url("customer/service/cancel") }}/' + bookingId;
    const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    modal.show();
}

 </script>

@endsection