@php
use Carbon\Carbon;
Carbon::setLocale('en');
$eatTimezone = 'Africa/Nairobi';
$now = Carbon::now($eatTimezone);
$todayDate = $now->format('Y-m-d');
$currentHour = (int)$now->format('H');
$currentMinute = (int)$now->format('i');
@endphp
<link rel="stylesheet" href="{{ asset('assets/frontend/css/owl.carousel.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/frontend/css/owl.theme.default.min.css') }}"   />

<div class="owl-carousel slot-carousel">
    @foreach($slots as $slot)
        @php
            $nextDateFormatted = Carbon::parse($slot['next_date'])->format('D, M d');
            $startTime = isset($slot['start_time']) ? Carbon::createFromFormat('H:i', $slot['start_time']) : null;
            $endTime = isset($slot['end_time']) ? Carbon::createFromFormat('H:i', $slot['end_time']) : null;
            $durationInMinutes = $durationInMinutes ?? 0;
            $currentTime = $startTime ? $startTime->copy() : null;
            $radioIndex = 1;
            $hasAvailableSlots = false;
            $isPastDate = $slot['next_date'] < $todayDate;
            $isToday = $slot['next_date'] == $todayDate;
        @endphp

        @if(!$isPastDate)
        <div class="position-relative slot-card" 
            data-date="{{ $slot['next_date'] }}" 
            data-day="{{ $slot['day'] }}" 
            data-employee="{{ $employeeId }}">
            <div class="p-2 list-group-item mb-2 shadow-sm border rounded">
                <span>
                    <span class="text-muted">{{ $nextDateFormatted }}</span>
                    @if($isToday)
                        <span class="badge bg-primary ms-2">{{ get_phrase('Today') }}</span>
                    @endif
                </span>
                <br>

                @if($startTime && $endTime && $durationInMinutes > 0)
                    @while($currentTime->copy()->addMinutes($durationInMinutes)->lte($endTime))
                        @php
                            $formattedTimeDisplay = $currentTime->format('g:i A');
                            $formattedTimeForUrl = $currentTime->format('H:i');
                            $slotHour = (int)$currentTime->format('H');
                            $slotMinute = (int)$currentTime->format('i');
                            
                            $isPastTime = false;
                            if ($isToday) {
                                if ($slotHour < $currentHour || ($slotHour == $currentHour && $slotMinute <= $currentMinute)) {
                                    $isPastTime = true;
                                }
                            }
                            
                            $radioId = 'radio_' . str_replace('-', '', $slot['next_date']) . '_' . str_replace(':', '', $formattedTimeForUrl);
                            $slotKey = $slot['next_date'] . '_' . $formattedTimeForUrl;
                            $isBooked = isset($bookedSlots[$slotKey]) && $bookedSlots[$slotKey];
                            
                            $isDisabled = $isPastTime || $isBooked;
                            if (!$isDisabled) {
                                $hasAvailableSlots = true;
                            }
                            $radioIndex++;
                        @endphp

                        @if($isDisabled)
                            <label class="form-check mt-1 text-muted" for="{{ $radioId }}" style="opacity: 0.5; cursor: not-allowed;">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="slot_time" 
                                       id="{{ $radioId }}" 
                                       value="{{ $formattedTimeForUrl }}"
                                       disabled>
                                <p class="form-check-label">
                                    {{ $formattedTimeDisplay }} 
                                    <small>
                                        @if($isPastTime)
                                            ({{ get_phrase('Past') }})
                                        @elseif($isBooked)
                                            ({{ get_phrase('Booked') }})
                                        @endif
                                    </small>
                                </p>
                            </label>
                        @else
                            <label class="form-check mt-1" for="{{ $radioId }}">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="slot_time" 
                                       id="{{ $radioId }}" 
                                       value="{{ $formattedTimeForUrl }}">
                                <p class="form-check-label">{{ $formattedTimeDisplay }}</p>
                            </label>
                        @endif

                        @php
                            $currentTime->addMinutes($durationInMinutes);
                        @endphp
                    @endwhile
                    @if(!$hasAvailableSlots)
                        <p class="text-danger mt-2">
                            @if($isToday)
                                {{ get_phrase('No available slots for today') }}
                            @else
                                {{ get_phrase('All slots are taken') }}
                            @endif
                        </p>
                    @endif
                @else
                    <p class="text-muted">{{ get_phrase('No available time slots.') }}</p>
                @endif
            </div>
        </div>
        @endif
    @endforeach
</div>

<script src="{{ asset('assets/frontend/js/owl.carousel.min.js') }}"></script>

<div class="d-flex justify-content-center gap-2 mb-2">
    <a id="slotSubmitBtn" href="javascript:void(0);" class="btn btn-outline-primary" style="width: 120px;">{{get_phrase('Book Now')}}</a>
    <a id="addToCartBtn" href="javascript:void(0);" class="btn btn-primary" style="width: 140px;">{{get_phrase('Add to Cart')}}</a>
    <a href="{{ route('service.cart') }}" class="btn btn-warning" style="width: 120px;">{{get_phrase('View Cart')}}</a>
</div>

<script>
    document.getElementById('slotSubmitBtn').addEventListener('click', function() {
    const isLoggedIn = @json(Auth::check());

    if (!isLoggedIn) {
        warning('{{ get_phrase('Please login first!') }}');
        return;
    }

    const selectedEmployee = document.getElementById('filterEmployee').value;
    if (!selectedEmployee) {
        warning('{{ get_phrase('Please select an employee before Booking.') }}');
        return;
    }

    const selectedSlot = document.querySelector('input[name="slot_time"]:checked:not(:disabled)');
    if (!selectedSlot) {
        warning('{{ get_phrase('Please select an available time slot.') }}');
        return;
    }

    const slotTimeDisplay = selectedSlot.nextElementSibling.textContent.trim();
    const slotCard = selectedSlot.closest('.slot-card');

    const date = slotCard.getAttribute('data-date');
    const day = slotCard.getAttribute('data-day');
    const employeeId = slotCard.getAttribute('data-employee');

    serviceModal(
        'modal-lg',
        `{{ route('service.booking.form', [
            'type' => $singleServiceItem->type,
            'id' => $singleServiceItem->id,
            'day' => '__DAY__',
            'date' => '__DATE__',
            'slot_time' => '__SLOTTIME__',
            'listing_id' => $listing_id ?? null,
            'employeeid' => '__EMPLOYEE__'
        ]) }}`
        .replace('__DAY__', day)
        .replace('__DATE__', date)
        .replace('__SLOTTIME__', slotTimeDisplay)
        .replace('__EMPLOYEE__', employeeId),
        `{{ get_phrase('Service Booking Form') }}`
    );
});

document.getElementById('addToCartBtn').addEventListener('click', function() {
    const isLoggedIn = @json(Auth::check());

    if (!isLoggedIn) {
        warning('{{ get_phrase('Please login first!') }}');
        return;
    }

    const selectedEmployee = document.getElementById('filterEmployee').value;
    if (!selectedEmployee) {
        warning('{{ get_phrase('Please select an employee before Adding to Cart.') }}');
        return;
    }

    const selectedSlot = document.querySelector('input[name="slot_time"]:checked:not(:disabled)');
    if (!selectedSlot) {
        warning('{{ get_phrase('Please select an available time slot.') }}');
        return;
    }

    const slotCard = selectedSlot.closest('.slot-card');
    const date = slotCard.getAttribute('data-date');
    const day = slotCard.getAttribute('data-day');
    const employeeId = slotCard.getAttribute('data-employee');

    const serviceId = {{ $singleServiceItem->id ?? 0 }};
    const type = '{{ $singleServiceItem->type ?? '' }}';

    const formData = new FormData();
    formData.append('service_id', serviceId);
    formData.append('listing_id', {{ $listing_id ?? 0 }});
    formData.append('type', type);
    formData.append('employee_id', employeeId);
    formData.append('service_date', date);
    formData.append('service_day', day);
    formData.append('service_time', selectedSlot.value);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route('service.add_to_cart') }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            success(data.message);
            setTimeout(() => {
                window.location.href = '{{ route('service.cart') }}';
            }, 1000);
        } else {
            warning(data.message);
        }
    })
    .catch(error => {
        warning('{{ get_phrase('An error occurred. Please try again.') }}');
    });
});

function toggleFavorite(employeeId, listingId) {
    const isLoggedIn = @json(Auth::check());
    if (!isLoggedIn) {
        warning('{{ get_phrase('Please login first!') }}');
        return;
    }
    
    const formData = new FormData();
    formData.append('employee_id', employeeId);
    formData.append('listing_id', listingId);
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch('{{ route('service.favorite.toggle') }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.querySelector(`[data-favorite="${employeeId}"]`);
            if (btn) {
                if (data.is_favorite) {
                    btn.innerHTML = '<i class="bi bi-heart-fill text-danger"></i>';
                    btn.classList.add('btn-warning');
                    btn.classList.remove('btn-outline-warning');
                } else {
                    btn.innerHTML = '<i class="bi bi-heart"></i>';
                    btn.classList.remove('btn-warning');
                    btn.classList.add('btn-outline-warning');
                }
            }
            success(data.message);
        }
    })
    .catch(error => {
        warning('{{ get_phrase('An error occurred.') }}');
    });
}

function loadSimilarServices(serviceId, listingId) {
    fetch('{{ url('/') }}/service/similar/' + serviceId + '/' + listingId)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.services.length > 0) {
            const container = document.getElementById('similarServices');
            let html = '<h5 class="mt-4 mb-3">{{ get_phrase("Similar Services") }}</h5><div class="row">';
            data.services.forEach(service => {
                html += `
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6>${service.name}</h6>
                                <p class="text-muted small">${service.description ? service.description.substring(0, 60) + '...' : ''}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary fw-bold">${service.price}</span>
                                    <a href="{{ url('/') }}/service/slot/${service.type}/${listingId}/${service.id}" class="btn btn-sm btn-outline-primary">
                                        {{ get_phrase('Book') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        }
    })
    .catch(error => {
        console.log('Error loading similar services');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const serviceId = {{ $singleServiceItem->id ?? 0 }};
    const listingId = {{ $listing_id ?? 0 }};
    if (serviceId && listingId) {
        loadSimilarServices(serviceId, listingId);
    }
});
</script>
