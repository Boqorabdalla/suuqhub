<form action="{{route('agent.employee.update', ['id' => $employeeEdit->id ])}}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label ol-form-label cap-form-label"> {{get_phrase('Name')}} </label>
        <input type="text" class="form-control ol-form-control cap-form-control" name="name" id="name" value='{{$employeeEdit->name}}' placeholder="{{get_phrase('Enter Name')}}" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label ol-form-label cap-form-label"> {{get_phrase('Email')}} </label>
        <input type="email" class="form-control ol-form-control cap-form-control" name="email" id="email" value='{{$employeeEdit->email}}' placeholder="{{get_phrase('Enter Email')}}" required>
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label ol-form-label cap-form-label"> {{get_phrase('Phone')}} </label>
        <input type="number" class="form-control ol-form-control cap-form-control" name="phone" id="phone" value='{{$employeeEdit->phone}}' placeholder="{{get_phrase('Enter Phone')}}" required>
    </div>

    <button type="submit" class="btn ol-btn-primary "> {{get_phrase('Update')}} </button>
</form>

