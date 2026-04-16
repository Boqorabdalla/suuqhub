
<style>
    table .dt-type-numeric img{
        width: 45px !important;
        height: 45px !important;
    }
   .at-dropdown-icon-btn {
	height: 31px !important;
	width: 31px !important;
}
</style>


<div class="row">
    <div class="col-lg-12 ">
       
        @php 
            $serviceSellings = App\Models\ServiceSelling::where('type',$type)->where('listing_id', $listing->id)->get();
       @endphp
       @if(count($serviceSellings) > 0)
       <div class="table-responsive pb-1">
            <table id="datatable" class="table nowrap responsive w-100 p-4  ca-table ca-table-width">
                <thead class="ca-thead"> 
                    <tr class="ca-tr">
                        <th class="ca-title-14px ca-text-dark"> {{ get_phrase('ID') }} </th>
                        <th class="ca-title-14px ca-text-dark"> {{ get_phrase('Image') }} </th>
                        <th class="ca-title-14px ca-text-dark"> {{ get_phrase('Service Name') }} </th>
                        <th class="ca-title-14px ca-text-dark"> {{ get_phrase("Provider") }} </th>
                        <th class="ca-title-14px ca-text-dark capitalize"> {{ get_phrase('Price') }} </th>
                        <th class="ca-title-14px ca-text-dark"> {{ get_phrase('Slots') }} </th>
                        <th class="ca-title-14px ca-text-dark"> {{ get_phrase('Status') }} </th>
                        <th class="ca-title-14px ca-text-dark"> {{ get_phrase('Action') }} </th>
                    </tr> 
                </thead>
                <tbody class="ca-tbody">
                    @php $i = 1; @endphp
                    @foreach($serviceSellings as  $selling)
                        <tr class="ca-tr">
                            <td>{{$i++}}</td>
                            <td>
                                <div class="sm2-banner-wrap" style="height: auto; width: auto; min-width: auto;"> 
                                     <img  style="width: 45px; height: 45px;" src="{{ get_image('uploads/service_selling/' .$selling->image) }}"  alt="" class="rounded">
                                </div>
                             </td>
                            <td>
                                <div class="ca-subtitle-14px ca-text-dark" style="font-size: 14px;">
                                    {{$selling->name}}
                                </div>
                                 @php
                                    $duration = $selling->duration;
                                    $hours = floor($duration / 60);
                                    $minutes = $duration % 60;
                                @endphp
                                    <p style="font-size: 14px;">{{get_phrase('Duration : ')}}
                                    {{ $hours > 0 ? $hours . ' h' . ($hours > 1 ? 's' : '') : '' }}
                                    {{ $minutes > 0 ? $minutes . ' m' . ($minutes > 1 ? 's' : '') : '' }}</p>
                            </td>
                            <td>
                                <div class="ca-subtitle-14px ca-text-dark" style="font-size: 14px;">
                                  @php
                                    $employees = App\Models\ServiceEmployee::get(); 
                                        $selectedEmployees = json_decode($selling->service_employee, true);
                                    @endphp
                                    <ol style="list-style: inherit">
                                        @foreach($employees as $employee)
                                        @if(in_array($employee->id, $selectedEmployees))
                                            <li class="ca-subtitle-14px ca-text-dark">
                                                {{$employee->name}}
                                            </li>
                                        @endif
                                    @endforeach
                                    </ol>
                                    
                                </div>
                            </td>
                            <td>
                                <div class="ca-subtitle-14px ca-text-dark" style="font-size: 14px;">
                                      {{currency($selling->price)}}  
                                </div>
                            </td>
                            <td>
                               @php
                                    $slots = is_string($selling->slot) ? json_decode($selling->slot, true) : $selling->slot;
                                @endphp
                                @foreach($slots as $slot)
                                    <div class="d-flex flex-column" >
                                        <p style="font-size: 14px;">
                                            {{ $slot['day'] }} :  
                                            {{ date('h:i A', strtotime($slot['start_time'])) }} - {{ date('h:i A', strtotime($slot['end_time'])) }} 
                                        </p>
                                    </div>
                                @endforeach

                                </td>

                            <td>
                                @if($selling->status == 1)
                                    <span class="badge bg-success">{{get_phrase('Active')}}</span>
                                @else 
                                   <span class="badge bg-warning">{{get_phrase('Deactive')}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex ">
                                     <div class="dropdown ol-icon-dropdown">
                                        <button   class="px-2 btn at-dropdown-icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <img src="{{ asset('assets/frontend/images/icons/menu-dots-vertical-14.svg') }}" alt="icon">
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end at-dropdown-menu">
                                            <li><a class="dropdown-item fs-14px" onclick="modal('modal-lg','{{route('agent.service.edit',['type' => $type, 'listing_id'=>$listing->id,'id'=>$selling->id])}}','{{get_phrase('Update Service')}}')" href="javascript:void(0);"> {{get_phrase('Edit')}} </a></li>
                                            <li><a class="dropdown-item fs-14px" onclick="delete_modal('{{route('agent.service.delete',[ 'id'=>$selling->id])}}')" href="javascript:void(0);"> {{get_phrase('Delete')}} </a></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach 

                </tbody>
            </table>
        </div>
        @endif
      
    </div>
</div>