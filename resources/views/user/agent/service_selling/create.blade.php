
<style>
    .add-slot,
   .remove-slot {
	height: 40px;
	width: 40px !important;
    padding: 0;
}
</style>


<form id="ajax-modal" action="{{route('agent.service.store')}}" method="post" class="e_services" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label ol-form-label cap-form-label"> {{get_phrase('Service Name')}} </label>
        <input type="text" class="form-control ol-form-control cap-form-control" name="name" id="name" placeholder="{{get_phrase('Enter Service name')}}" required>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label ol-form-label cap-form-label capitalize"> {{get_phrase('Service Price')}} </label>
        <input class="form-control ol-form-control cap-form-control" name="price" id="price" type="number" placeholder="{{get_phrase('Enter Service price')}}" required>
    </div>
    <div class="mb-3">
        <label for="duration" class="form-label ol-form-label capitalize cap-form-label"> {{ get_phrase('Service Duration') }} </label>
        <select class="form-control ol-form-control cap-form-control" name="duration" id="duration" required>
            <option value="">{{get_phrase('Select duration')}}</option>
            @php
                $interval = 15; // minutes
                $maxMinutes = 360; // 6 hours (change if needed)
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
                <option value="{{ $minutes }}">{{ get_phrase($label) }}</option>
            @endfor

        </select>
    </div>

     <div class="mb-3">
        <label for="employee" class="form-label ol-form-label capitalize cap-form-label"> {{ get_phrase('Select Employee') }} </label>
        <select class="at-select2 cap-select2 select2-hidden-accessible ol-select2" name="employee[]" id="employee"  multiple required>
            <option disabled>{{ get_phrase('Select Employee') }}</option>
            @php 
                $employees = App\Models\ServiceEmployee::where('creator_id', auth()->user()->id)->get(); 
            @endphp
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
            @endforeach
        </select>

    </div>

    <div class="mb-3">
        <label for="description" class="form-label ol-form-label cap-form-label"> {{get_phrase('Description')}} </label>
        <textarea class="form-control mform-control review-textarea cap-form-control" name="description" required></textarea>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label ol-form-label cap-form-label"> {{get_phrase('Upload Service image')}} </label>
        <input class="form-control ol-form-control cap-form-control" name="image" id="image" type="file" required>
    </div>
    <div class="mb-3">
        <label for="video" class="form-label ol-form-label cap-form-label"> {{get_phrase('YouTube Video Link')}} </label>
        <input type="text" class="form-control ol-form-control cap-form-control" name="video" id="video"  placeholder="{{ get_phrase('Paste a YouTube video link here') }}" required>
    </div>
      <div class="mb-3">
            <label for="status" class="form-label ol-form-label cap-form-label"> {{get_phrase('Status')}} </label>
            <select name="status" id="status" class="form-control  cap-form-control" required>
                <option value=""> {{get_phrase('Select Status')}} </option>
                <option value="1"> {{get_phrase('Active')}} </option>
                <option value="0"> {{get_phrase('Deactive')}} </option>
            </select>
        </div>

            <div class="mb-3">
                
                <div id="slot-container">
                    <div class="row mb-2 slot-row">
                        <div class="col-md-4">
                            <label class="form-label ol-form-label cap-form-label">{{ get_phrase('Select Day') }}</label>
                            <select name="slots[0][day]" class="form-control ol-form-control cap-form-control" style="height: 46px;" required>
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
                            <label class="form-label ol-form-label cap-form-label">{{ get_phrase('Opening Time') }}</label>
                            <input type="time" name="slots[0][start_time]" class="form-control ol-form-control cap-form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label ol-form-label cap-form-label">{{ get_phrase('Closing Time') }}</label>
                            <input type="time" name="slots[0][end_time]" class="form-control ol-form-control cap-form-control" required>
                        </div>
                        {{-- <div class="col-md-3">
                            <input type="number" name="slots[0][capacity]" class="form-control ol-form-control cap-form-control" placeholder="{{ get_phrase('Capacity') }}" min="1" required>
                        </div> --}}
                        <div class="col-md-2 d-flex gap-1">
                            <button type="button" class="btn ol-btn-primary add-slot">+</button>
                            <button type="button" class="btn w-100 btn-danger remove-slot" disabled>-</button>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden"  name="type" value="{{$type}}">
            <input type="hidden"  name="listing_id" value="{{$listing_id}}">
        <button type="submit" class="btn ol-btn-primary "> {{get_phrase('Create')}} </button>
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
    let slotIndex = 1;

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-slot')) {
            const slotContainer = document.getElementById('slot-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-2', 'slot-row');
            newRow.innerHTML = `
                <div class="col-md-4">
                    <select name="slots[${slotIndex}][day]" class="form-control ol-form-control cap-form-control" style="height: 46px;" required>
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