@extends('layouts.admin')
@section('title', get_phrase('All Employees'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-settings-sliders me-2"></i>
                {{ get_phrase('All Employees') }}
            </h4>
                <a href="javascript:;"  onclick="modal('modal-md', '{{route('admin.add_employee_form')}}', '{{get_phrase('Add New Employee')}}')" class="btn ol-btn-outline-secondary d-flex align-items-center cg-10px">
                <span class="fi-rr-plus"></span>
                <span> {{get_phrase('Add new Employee')}} </span>
            </a>
        </div>
    </div>
</div>

 <div class="ol-card mt-3">
        <div class="ol-card-body table-responsive p-3">
            @if (count($employeeList))
                <table id="datatable" class="table nowrap w-100">
                    <thead>
                        <tr>
                            <th> {{ get_phrase('ID') }} </th>
                            <th> {{ get_phrase('Name') }} </th>
                            <th> {{ get_phrase('Email') }} </th>
                            <th> {{ get_phrase('Phone') }} </th>
                            <th> {{ get_phrase('Action') }} </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $num = 1 @endphp
                        @foreach ($employeeList as $list)
                            <tr>
                                <td> {{ $num++ }} </td>
                                <td> {{ $list->name }} </td>
                                <td> {{ $list->email }} </td>
                                <td> {{ $list->phone }} </td>
                                
                                <td>
                                    <div class="dropdown ol-icon-dropdown">
                                        <button class="px-2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="fi-rr-menu-dots-vertical"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item fs-14px" onclick="edit_modal('modal-md','{{ route('admin.employee.edit', ['id' => $list->id]) }}','{{ get_phrase('Edit Emaployee') }}')" href="javascript:void(0);"> {{ get_phrase('Edit') }} </a></li>
                                            <li><a class="dropdown-item fs-14px" onclick="delete_modal('{{ route('admin.employee.delete', ['id' => $list->id]) }}')" href="javascript:void(0);"> {{ get_phrase('Delete') }} </a></a></li>
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