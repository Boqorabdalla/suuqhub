<style>
    .add-slot,
   .remove-slot {
	height: 40px;
	width: 40px !important;
    padding: 0;
}
</style>

<form action="{{route('admin.service.update', ['id' => $serviceSelling->id])}}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label ol-form-label cap-form-label"> {{get_phrase('Service Name')}} </label>
        <input type="text" class="form-control ol-form-control" name="name" id="name" value="{{$serviceSelling->name}}" placeholder="{{get_phrase('Enter Service name')}}" required>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label ol-form-label capitalize cap-form-label"> {{get_phrase('Service Price')}} </label>
        <input class="form-control ol-form-control cap-form-control" name="price" id="price" type="number" value="{{$serviceSelling->price}}" placeholder="{{get_phrase('Enter Service price')}}" required>
    </div>
    <div class="mb-3">
        <label for="duration" class="form-label ol-form-label capitalize cap-form-label"> {{ get_phrase('Service Duration') }} </label>
        <select class="form-control ol-form-control ol-select2" name="duration" id="duration" required>
            <option value="">{{get_phrase('Select duration')}}</option>
           @php
                $interval = 15; // minutes
                $maxMinutes = 360; // up to 6 hours
            @endphp

            @for ($minutes = $interval; $minutes <= $maxMinutes; $minutes += $interval)
                @php
                    $hours = floor($minutes / 60);
                    $mins = $minutes % 60;

                    if ($hours > 0 && $mins > 0) {
                        $label = $hours . ' hour' . ($hours > 1 ? 's ' : ' ') . $mins . ' minutes';
                    } elseif ($hours > 0) {
                        $label = $hours . ' hour' . ($hours > 1 ? 's' : '');
                    } else {
                        $label = $mins . ' minutes';
                    }
                @endphp
                <option value="{{ $minutes }}" @if($serviceSelling->duration == $minutes) selected @endif>
                    {{ get_phrase($label) }}
                </option>
            @endfor
        </select>
    </div>

     <div class="mb-3">
        <label for="employee" class="form-label ol-form-label capitalize cap-form-label"> {{ get_phrase('Select Employee') }} </label>
        <select class="form-control ol-form-control ol-select2" name="employee[]" id="employee"  multiple required>
            <option disabled>{{ get_phrase('Select Employee') }}</option>
            @php 
                $employees = App\Models\ServiceEmployee::where('creator_id', auth()->user()->id)->get(); 
                $selectedEmployees = json_decode($serviceSelling->service_employee, true) ?? [];
            @endphp

            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" {{ in_array($employee->id, $selectedEmployees) ? 'selected' : '' }}>
                    {{ $employee->name }}
                </option>
            @endforeach

        </select>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label ol-form-label cap-form-label"> {{get_phrase('Short Description')}} </label>
        <textarea class="form-control mform-control review-textarea cap-form-control"  name="description" required>{{$serviceSelling->description}}</textarea>
    </div>
    <div>
        <img src="{{get_all_image('service_selling/'.$serviceSelling->image)}}" style="height: 120px; width: 120px; object-fit: cover; border-radius: 5px; margin-bottom: 10px;" alt="">
    </div>
    <div class="mb-3">
        <label for="image" class="form-label ol-form-label cap-form-label"> {{get_phrase('Upload Service image')}} </label>
        <input class="form-control ol-form-control cap-form-control" name="image" id="image" type="file">
    </div>
    <div class="mb-3">
        <label for="video" class="form-label ol-form-label cap-form-label"> {{get_phrase('YouTube Video Link')}} </label>
        <input type="text" class="form-control ol-form-control" name="video" id="video" value="{{$serviceSelling->video}}" placeholder="{{ get_phrase('Paste a YouTube video link here') }}" required>
    </div>
      <div class="mb-3">
            <label for="status" class="form-label ol-form-label cap-form-label"> {{get_phrase('Status')}} </label>
            <select name="status" id="status" class="form-control ol-form-control" required>
                <option value=""> {{ get_phrase('Select Status') }} </option>
                <option value="1" @if($serviceSelling->status == 1) selected @endif> {{ get_phrase('Active') }} </option>
                <option value="0" @if($serviceSelling->status == 0) selected @endif> {{ get_phrase('Deactive') }} </option>
            </select>

        </div>

           @php
                $slots = [];

                if (!empty($serviceSelling->slot)) {
                    if (is_array($serviceSelling->slot)) {
                        $slots = $serviceSelling->slot;
                    } elseif (is_string($serviceSelling->slot)) {
                        $decoded = json_decode($serviceSelling->slot, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $slots = $decoded;
                        }
                    }
                }
            @endphp


            <div class="mb-3">
                <label class="form-label ol-form-label cap-form-label">{{ get_phrase('Select Day') }}</label>

                <div id="slot-container">
                    @forelse ($slots as $index => $slot)
                    <div class="row mb-2 slot-row">
                        <div class="col-md-4">
                            <select name="slots[{{ $index }}][day]" class="form-control ol-form-control cap-form-control" required>
                                <option value="">{{ get_phrase('Select Day') }}</option>
                                @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                    <option value="{{ $day }}" @if($slot['day'] == $day) selected @endif>{{ get_phrase($day) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="time" name="slots[{{ $index }}][start_time]" class="form-control ol-form-control cap-form-control" value="{{ $slot['start_time'] }}" required>
                        </div>
                        <div class="col-md-3">
                            <input type="time" name="slots[{{ $index }}][end_time]" class="form-control ol-form-control cap-form-control" value="{{ $slot['end_time'] }}" required>
                        </div>
                      
                        <div class="col-md-2 d-flex gap-1">
                            <button type="button" class="btn ol-btn-primary add-slot">+</button>
                            <button type="button" class="btn w-100 btn-danger remove-slot" @if($index == 0) disabled @endif>-</button>
                        </div>
                    </div>
                    @empty
                    {{-- Show a blank row if no slots exist --}}
                    <div class="row mb-2 slot-row">
                        <div class="col-md-4">
                            <select name="slots[0][day]" class="form-control ol-form-control cap-form-control" required>
                                <option value="">{{ get_phrase('Select Day') }}</option>
                                @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
                                    <option value="{{ $day }}">{{ get_phrase($day) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label ol-form-label cap-form-label">{{ get_phrase('Opening Time') }}</label>
                            <input type="time" name="slots[0][start_time]" class="form-control ol-form-control cap-form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label ol-form-label cap-form-label">{{ get_phrase('Closing Time') }}</label>
                            <input type="time" name="slots[0][end_time]" class="form-control ol-form-control cap-form-control" required>
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button type="button" class="btn ol-btn-primary add-slot">+</button>
                            <button type="button" class="btn w-100 btn-danger remove-slot" disabled>-</button>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            <input type="hidden"  name="type" value="{{$type}}">
            <input type="hidden"  name="listing_id" value="{{$listing_id}}">
        <button type="submit" class="btn ol-btn-primary "> {{get_phrase('Update')}} </button>
    </div>
</form>

<script>
    "use strict";
    $(document).ready(function() {
        $('.ol-select2').select2({
            dropdownParent: $('#ajax-modal')
        });
    });
</script>



<script>
    let slotIndex = document.querySelectorAll('.slot-row').length;

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-slot')) {
            const slotContainer = document.getElementById('slot-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-2', 'slot-row');
            newRow.innerHTML = `
                <div class="col-md-4">
                    <select name="slots[${slotIndex}][day]" class="form-control ol-form-control cap-form-control" required>
                        <option value="">{{ get_phrase('Select Day') }}</option>
                        <option value="Sunday">{{ get_phrase('Sunday') }}</option>
                        <option value="Monday">{{ get_phrase('Monday') }}</option>
                        <option value="Tuesday">{{ get_phrase('Tuesday') }}</option>
                        <option value="Wednesday">{{ get_phrase('Wednesday') }}</option>
                        <option value="Thursday">{{ get_phrase('Thursday') }}</option>
                        <option value="Friday">{{ get_phrase('Friday') }}</option>
                        <option value="Saturday">{{ get_phrase('Saturday') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="time" name="slots[${slotIndex}][start_time]" class="form-control ol-form-control cap-form-control" required>
                </div>
                <div class="col-md-3">
                    <input type="time" name="slots[${slotIndex}][end_time]" class="form-control ol-form-control cap-form-control" required>
                </div>
              
                <div class="col-md-2 d-flex gap-1">
                    <button type="button" class="btn ol-btn-primary add-slot">+</button>
                    <button type="button" class="btn w-100 btn-danger remove-slot">-</button>
                </div>
            `;
            slotContainer.appendChild(newRow);
            slotIndex++;
        }

        if (e.target.classList.contains('remove-slot')) {
            const row = e.target.closest('.slot-row');
            const allRows = document.querySelectorAll('.slot-row');
            if (allRows.length > 1) {
                row.remove();
            }
        }
    });
</script>


