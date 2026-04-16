<style>
.day-check-circle {
    width: 24px;
    height: 24px;
    background-color: var(--themeColor, #f26522);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s ease;
    cursor: pointer;
}
.day-checkbox:checked + .day-check-circle {
    background-color: var(--themeColor, #f26522);
}
.day-checkbox:not(:checked) + .day-check-circle {
     background-color: var(--themeColor, #f26522);
}
.day-checkbox + .day-check-circle i {
    display: block;
    font-size: 12px;
}
.day-checkbox:not(:checked) + .day-check-circle i {
    display: none;
}
.day-checkbox:checked + .day-check-circle i {
    display: block;
}
#filterForm .ua-form-control {
	padding: 12px 13px;
}
.service_selling span{
    color: #0B0D0F !important;
}
#filterForm .nice-select .option {
	font-weight: 400;
	line-height: 30px;
	min-height: 30px;
}
#filterForm .submit-fluid-btn {
	padding: 9px 24px;
	font-size: 14px;
	line-height: 24px;
}

#filterForm .nice-select {
    border-radius: 8px;
	border: solid 1px #D3D7E0;
	
}
#filterForm .nice-select:hover {
	border-color: #242D47A1;
}
#slotContainer .owl-dots{
    display: none;
}
</style>


@php
use Carbon\Carbon;
use Illuminate\Support\Collection;

$selectedDateInput = request()->input('date');
$referenceDate = $selectedDateInput ? Carbon::parse($selectedDateInput) : Carbon::today();
$today = Carbon::today();
$endLimit = $today->copy()->addMonths(6)->endOfMonth();

// Prevent user from selecting dates beyond next 6 months
if ($referenceDate->gt($endLimit)) {
    $referenceDate = $endLimit->copy();
}

// Only generate slots for selected month
$start = $referenceDate->copy()->startOfMonth();
$end = $referenceDate->copy()->endOfMonth();

$singleServiceItem = App\Models\ServiceSelling::find($id);
$slotsRaw = $singleServiceItem->slot ?? null;
$slots = [];

if ($slotsRaw) {
    $decodeAttempts = 0;
    $decoded = $slotsRaw;

    while ($decodeAttempts < 3 && !is_array($decoded)) {
        $decoded = json_decode($decoded, true);
        $decodeAttempts++;
    }

    $slots = is_array($decoded) ? $decoded : [];
}

 $durationInMinutes = (int) ($singleServiceItem->duration ?? 0);

$dayShortMap = [
    'Sunday' => 'Sun',
    'Monday' => 'Mon',
    'Tuesday' => 'Tue',
    'Wednesday' => 'Wed',
    'Thursday' => 'Thu',
    'Friday' => 'Fri',
    'Saturday' => 'Sat',
];

$monthlySlots = collect();

foreach ($slots as $slot) {
    $slotDay = $slot['day'] ?? '';
    if (!in_array($slotDay, array_keys($dayShortMap))) {
        continue;
    }

    $current = $start->copy();
    while ($current->lte($end)) {
        if ($current->dayName === $slotDay) {
            $slotWithDate = $slot;
            $slotWithDate['next_date'] = $current->format('Y-m-d');
            $monthlySlots->push($slotWithDate);
        }
        $current->addDay();
    }
}

$slots = $monthlySlots->sortBy('next_date')->values();

$selectedEmployees = json_decode($singleServiceItem->service_employee, true);
$employees = App\Models\ServiceEmployee::all();
@endphp

<div class="service-details-card service_selling p-4 border rounded shadow-sm bg-white">
    <p class="mb-3">{{ get_phrase('Here are the available time slots for') }} <strong>{{ $singleServiceItem->name }}</strong>. {{ get_phrase('Please select a time slot to proceed with your booking.') }}</p>

    <div class="row">
        <div class="col-lg-7">
            <form class="row mb-4 align-items-center" id="filterForm">
                <div class="col-md-6 col-sm-6 mb-2">
                    <div class="d-flex flex-column">
                        <label class="form-label ua-form-label mb-2" style="font-size: 14px;">{{ get_phrase("Select Date") }}</label>
                        <input type="date" class="form-control ua-form-control" name="date" id="date"
                        value="{{ $selectedDateInput ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 mb-2">
                    <div class="d-flex flex-column">
                        <label class="form-label ua-form-label mb-2" style="font-size: 14px;">{{ get_phrase('Employee') }}</label>
                        <select class="at-nice-select  select" name="employee_id" id="filterEmployee">
                            <option value="">{{ get_phrase('Select Employee') }}</option>
                            @foreach($employees as $employee)
                                @if(in_array($employee->id, $selectedEmployees))
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-8 col-sm-6 mb-2">
                    <div style="margin-top: 12px;">
                        <label class="form-label ua-form-label d-block mb-2" style="font-size: 14px;">{{ get_phrase('Days') }}</label>
                        <div class="d-flex flex-wrap" style="gap:8px;">
                            @php $availableDays = collect($slots)->pluck('day')->unique(); @endphp
                            @foreach($availableDays as $day)
                                @php $shortDay = $dayShortMap[$day] ?? $day; @endphp
                                <label class="text-center">
                                    <input type="checkbox" name="days[]" value="{{ $shortDay }}" class="d-none day-checkbox" checked data-day="{{ $shortDay }}">
                                    <div class="day-check-circle">
                                        <i class="fas fa-check text-white"></i>
                                    </div>
                                    <div class="text-orange mt-1" style="font-size: 14px;">{{ $shortDay }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-md-4  col-sm-6 mb-2 d-flex align-items-center">
                    <button type="button" class="submit-fluid-btn w-100" id="applyFilter">{{ get_phrase('Filter') }}</button>
                </div>
            </form>

            <div class="row" id="slotContainer">
                <!-- Slots will load here via AJAX -->
            </div>
        </div>
            @php
            $videoUrl = trim($singleServiceItem->video);
            $videoId = null;
            if (preg_match('#(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|v/|shorts/))([a-zA-Z0-9_-]{11})#', $videoUrl, $matches)) {
                $videoId = $matches[1];
            }
            $embedUrl = $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
        @endphp

        <div class="col-lg-5">
            <div class="realdetails-video">
                @if($embedUrl)
                    <div class="">
                        <iframe style="width: 100%; height: 370px; border-radius: 12px;" src="{{ $embedUrl }}" title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen></iframe>
                    </div>
                @endif
            </div>
        </div>



    </div>
</div>

<script>

$(document).ready(function() {
    $('#filterEmployee').niceSelect();
    $('#slotContainer').html(''); // Empty before load

    loadSlots(true);

    $('#applyFilter').on('click', function(e) {
        e.preventDefault();
        loadSlots(false);
    });
});

function loadSlots(initialLoad = false) {
    const selectedDate = $('#date').val();
    const selectedEmployee = $('#filterEmployee').val();

    const formData = $('#filterForm').serialize();

    if (!initialLoad) {
        if (!selectedEmployee) {
            warning('Please select an employee before filtering.');
            return;
        }
        if ($('.day-checkbox:checked').length === 0) {
            warning('Please select at least one day to filter.');
            return; 
        }
    }

    $.ajax({
        url: "{{ route('service.fetchSlots', ['id' => $id, 'listing_id' => $listing_id]) }}",
        type: 'GET',
        data: formData,
        success: function(response) {
            $('#slotContainer').html(response.html);
            var $carousel = $('.slot-carousel');
            if ($carousel.hasClass('owl-loaded')) {
                $carousel.trigger('destroy.owl.carousel');
                $carousel.removeClass('owl-loaded');
                $carousel.find('.owl-stage-outer').children().unwrap();
            }

            initSlotCarousel();
        },
        error: function() {
            alert('Failed to load slots. Please try again.');
        }
    });
}

function initSlotCarousel() {
    var $carousel = $('.slot-carousel');

    if ($carousel.length && !$carousel.hasClass('owl-loaded')) {
        $carousel.owlCarousel({
            loop: false,
            margin: 10,
            nav: true,
            dots: true,
            responsive: {
                0: { items: 1 },
                600: { items: 2 },
                1000: { items: 4 }
            }
        });
    }
}

$('#filterEmployee').on('change', function() {
    loadSlots();
});

</script>



<script>
    // When global modal closes, stop any iframe videos inside it
    $('#service-modal').on('hidden.bs.modal', function () {
        const $iframes = $(this).find('iframe');
        $iframes.each(function () {
            const src = $(this).attr('src');
            $(this).attr('src', src); 
        });
    });
</script>