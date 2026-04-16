@extends('layouts.admin')
@section('title', get_phrase('My Services '))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-settings-sliders me-2"></i>
                {{ get_phrase('My Services') }}
            </h4>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-3">
        @if(count($myservice))
        <table id="datatable" class=" table nowrap w-100">
            <thead>
                <tr>
                    <th> {{get_phrase('ID')}} </th>
                    <th> {{ get_phrase("Provider") }} </th>
                    <th> {{get_phrase('Service Name')}} </th>
                    <th> {{get_phrase('Amount Payable')}} </th>
                    <th> {{get_phrase('Customer  Details')}} </th>
                    <th> {{get_phrase('Scheduled Date')}} </th>
                    <th> {{get_phrase('Action')}} </th>
                </tr>
            </thead>
            <tbody>
                @php $num = 1 @endphp
                @foreach ($myservice as $service) 
               @php
                   $serviceinfo = App\Models\ServiceSelling::where('id', $service->service_selling_id)->first();
                   $employeeinfo = App\Models\ServiceEmployee::where('id', $service->employee_id)->first();
               @endphp
                <tr>
                    <td> {{$num++}} </td>
                    <td>
                        <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                            <div class="dAdmin_profile_name">
                                <p class="sub-title2 text-13px">{{$employeeinfo->name ?? ''}} </p>
                            </div>
                        </div> 
                    </td>
                    <td>
                        <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                            <div class="dAdmin_profile_name">
                                <p class="sub-title2 text-13px"> <span class="capitalize">{{$serviceinfo->name}}</span></p>
                                    <p class="sub-title2 text-13px">{{get_phrase('Status : ')}}
                                        @if($service->status == 1) 
                                                <span class="badge bg-success">{{ get_phrase('Approve') }}</span> 
                                            @else 
                                            <span class="badge bg-danger">{{ get_phrase('Pending') }}</span>
                                        @endif
                                     </p>
                                
                            </div>
                        </div> 
                    </td>
                    <td>
                        <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                            <div class="dAdmin_profile_name">
                                <p class="sub-title2 text-13px"><b>{{get_phrase('Amount Pay : ')}}</b> {{currency($serviceinfo->price)}}</p>
                                <p class="sub-title2 text-13px"><b>{{get_phrase(' Status : ')}}</b>
                                   @if($service->payment_status == 1) 
                                        <span class="badge bg-success">{{ get_phrase('Paid') }}</span> 
                                    @else 
                                       <span class="badge bg-danger">{{ get_phrase('Pending') }}</span>
                                   @endif
                                </p>
                                
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                            <div class="dAdmin_profile_name">
                                <p class="sub-title2 text-13px"><b>{{get_phrase('Name : ')}}</b> {{$service->name}}</p>
                                <p class="sub-title2 text-13px"><b>{{get_phrase('Email : ')}}</b> {{$service->email}}</p>
                                <p class="sub-title2 text-13px"><b>{{get_phrase('Phone : ')}} </b>{{$service->phone}}</p>
                                <p class="sub-title2 text-13px text-wrap"><b>{{get_phrase('Note : ')}}</b> {{$service->notes}}</p>
                                
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="dAdmin_profile d-flex align-items-center min-w-200px">
                            <div class="dAdmin_profile_name">
                               <p class="sub-title2 text-13px">{{$service->service_date}} </p>
                                    <p class="sub-title2 text-13px">{{$service->service_day}} </p>
                                    <p class="sub-title2 text-13px">{{$service->service_time}} </p>
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
                              <li><a class="dropdown-item fs-14px" onclick="delete_modal('{{route('admin.service_manager.delete',['id'=>$service->id])}}')" > {{get_phrase('Delete')}} </a></a></li>
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