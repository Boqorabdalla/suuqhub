@extends('layouts.admin')
@section('title', get_phrase('Service Manager'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-settings-sliders me-2"></i>
                {{ get_phrase('Service Manager ') }}
            </h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.service.manager.calendar') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-calendar"></i> {{ get_phrase('Calendar View') }}
                </a>
                <a href="{{ route('admin.service.manager.stats') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-graph-up"></i> {{ get_phrase('Statistics') }}
                </a>
                <a href="{{ route('admin.service.export.all_calendars') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-download"></i> {{ get_phrase('Export All to Calendar') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-3">
        @if(count($serviceManager))
        <table id="datatable" class=" table nowrap w-100">
            <thead>
               <tr>
                    <th>{{ get_phrase('ID') }}</th>
                    <th>{{ get_phrase("Provider") }}</th>
                    <th>{{ get_phrase('Customer Details') }}</th>
                    <th>{{ get_phrase('Service Details') }}</th>
                    <th>{{ get_phrase('Schedule') }}</th>
                    <th>{{ get_phrase('Action') }}</th>
                </tr>

            </thead>
            <tbody>
                @php $num = 1 @endphp
                @foreach ($serviceManager as $manager) 
                @php
                   $serviceinfo = App\Models\ServiceSelling::where('id', $manager->service_selling_id)->first();
                   $employeeinfo = App\Models\ServiceEmployee::where('id', $manager->employee_id)->first();
                   $agent = App\Models\User::find($manager->listing_creator_id);
                   $listing = App\Models\BeautyListing::find($manager->listing_id);
                @endphp
                  <tr>
                        <td>
                            {{$num++}}
                        </td>
                         <td>
                            <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                <div class="dAdmin_profile_name">
                                    @if($agent)
                                        <p class="sub-title2 text-13px">{{ $agent->name }}</p>
                                        <p class="sub-title2 text-12px text-muted">{{ $agent->email }}</p>
                                    @else
                                        <p class="sub-title2 text-13px">{{ get_phrase('N/A') }}</p>
                                    @endif
                                </div>
                            </div> 
                        </td>
                        <td>
                            <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                                <div class="dAdmin_profile_name">
                                    <p class="sub-title2 text-13px">{{get_phrase('Name : ')}} {{$manager->name}} </p>
                                    <p class="sub-title2 text-13px">{{get_phrase('Phone : ')}} {{$manager->phone}} </p>
                                    <p class="sub-title2 text-13px">{{get_phrase('Email : ')}} {{$manager->email}} </p>
                                    <p class="sub-title2 text-13px text-wrap">{{$manager->notes}} </p>
                                </div>
                            </div> 
                        </td>
                      <td>
                            <div class="dAdmin_profile d-flex flex-column min-w-200px">
                                <div class="dAdmin_profile_name">
                                    <p class="sub-title2 text-13px">{{$serviceinfo->name}} </p>
                                    @if($listing)
                                        <p class="sub-title2 text-12px text-muted">{{ get_phrase('Listing:') }} {{ $listing->name }}</p>
                                    @endif
                                    <p class="sub-title2 text-13px">{{get_phrase('Status : ')}}
                                        @if($manager->status == 1) 
                                                <span class="badge bg-success">{{ get_phrase('Approve') }}</span> 
                                            @else 
                                            <span class="badge bg-danger">{{ get_phrase('Pending') }}</span>
                                        @endif
                                     </p>
                                     <p class="sub-title2 text-13px">{{get_phrase('Pay : ')}} {{currency($serviceinfo->price)}} </p>
                                     <p class="sub-title2 text-13px">{{get_phrase('Status : ')}}
                                        @if($manager->payment_status == 1) 
                                                <span class="badge bg-success">{{ get_phrase('Paid') }}</span> 
                                            @else 
                                            <span class="badge bg-danger">{{ get_phrase('Unpaid') }}</span>
                                        @endif
                                     </p>
                                </div>
                            </div>
                      </td>
                  
                      <td>
                            <div class="dAdmin_profile d-flex flex-column min-w-200px">
                                <div class="dAdmin_profile_name">
                                    <p class="sub-title2 text-13px">{{$manager->service_date}} </p>
                                    <p class="sub-title2 text-13px">{{$manager->service_day}} </p>
                                    <p class="sub-title2 text-13px">{{$manager->service_time}} </p>
                                     @php
                                        $duration = $serviceinfo->duration;
                                        $hours = floor($duration / 60);
                                        $minutes = $duration % 60;
                                    @endphp
                                    <p class="sub-title2 text-13px">{{get_phrase('Duration : ')}}
                                        {{ $hours > 0 ? $hours . ' h' . ($hours > 1 ? 's' : '') : '' }}
                                        {{ $minutes > 0 ? $minutes . ' m' . ($minutes > 1 ? 's' : '') : '' }}
                                    </p>
                                </div>
                            </div>
                      </td>
                        <td>
                            <div class="dropdown ol-icon-dropdown">
                                <button class="px-2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="fi-rr-menu-dots-vertical"></span>
                                </button>
                                <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.service.export.calendar', ['id' => $manager->id]) }}">
                                                <i class="bi bi-calendar-plus"></i> {{ get_phrase('Add to Calendar') }}
                                            </a>
                                        </li>
                                        @if($manager->payment_status == 1) 
                                        <li>
                                             <a class="dropdown-item" onclick="delete_modal('{{route('admin.service_manager.unpaid',['id'=>$manager->id])}}')" href="javascript:void(0)">{{get_phrase('Mark as Unpaid')}}</a>
                                        </li>
                                        @else
                                        <li>
                                             <a class="dropdown-item" onclick="delete_modal('{{route('admin.service_manager.paid',['id'=>$manager->id])}}')" href="javascript:void(0)">{{get_phrase('Mark as Paid')}}</a>
                                        </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item" onclick="delete_modal('{{route('admin.service_manager.approve',['id'=>$manager->id])}}')" href="javascript:void(0)">{{get_phrase('Approve')}}</a>
                                        </li>
                                        <li>
                                             <a class="dropdown-item" onclick="delete_modal('{{route('admin.service_manager.delete',['id'=>$manager->id])}}')" href="javascript:void(0)">{{get_phrase('Delete')}}</a>
                                        </li>
                                    </ul>
                            </div>
                        </td>



                     </tr>
                @endforeach
            </tbody>
        </table>
        @else
            @include('layouts.no_data_found')
        @endif
    </div>
</div>

@endsection