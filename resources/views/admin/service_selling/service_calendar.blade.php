@extends('layouts.admin')
@section('title', get_phrase('Booking Calendar'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="title fs-18px">{{ get_phrase('Booking Calendar') }}</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.service.manager') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list"></i> {{ get_phrase('List View') }}
                </a>
                <a href="{{ route('admin.service.manager.stats') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-graph-up"></i> {{ get_phrase('Statistics') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="title fs-18px">{{ get_phrase('Booking Calendar') }}</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.service.manager') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-list"></i> {{ get_phrase('List View') }}
                </a>
                <a href="{{ route('admin.employee.list') }}" class="btn btn-outline-primary">
                    <i class="bi bi-people"></i> {{ get_phrase('Employees') }}
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="calendar-header d-flex justify-content-between align-items-center mb-3">
                    <button class="btn btn-sm btn-secondary" onclick="changeMonth(-1)">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <h5 id="currentMonthYear" class="mb-0"></h5>
                    <button class="btn btn-sm btn-secondary" onclick="changeMonth(1)">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="calendar-grid" id="calendarGrid">
            <!-- Calendar will be rendered here -->
        </div>

        <div class="mt-4">
            <h6>{{ get_phrase('Booking Status Legend') }}</h6>
            <div class="d-flex gap-3 flex-wrap">
                <span class="badge bg-warning text-dark">{{ get_phrase('Pending') }}</span>
                <span class="badge bg-success">{{ get_phrase('Approved') }}</span>
                <span class="badge bg-danger">{{ get_phrase('Cancelled') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">{{ get_phrase('Booking Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="bookingModalBody">
                <!-- Booking details will be loaded here -->
            </div>
            <div class="modal-footer" id="bookingModalFooter">
                <!-- Action buttons will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.calendar-day-header {
    text-align: center;
    font-weight: bold;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
}

.calendar-day {
    min-height: 100px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 5px;
    background: white;
}

.calendar-day.other-month {
    background: #f8f9fa;
    opacity: 0.5;
}

.calendar-day.today {
    background: #e3f2fd;
    border: 2px solid #2196f3;
}

.calendar-day-number {
    font-weight: bold;
    margin-bottom: 5px;
}

.calendar-booking {
    font-size: 11px;
    padding: 2px 5px;
    margin: 2px 0;
    border-radius: 3px;
    cursor: pointer;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.calendar-booking.pending {
    background: #fff3cd;
    color: #856404;
    border-left: 3px solid #ffc107;
}

.calendar-booking.approved {
    background: #d4edda;
    color: #155724;
    border-left: 3px solid #28a745;
}

.calendar-booking:hover {
    opacity: 0.8;
}
</style>

<script>
let currentDate = new Date();
let bookings = @json($bookings);

document.addEventListener('DOMContentLoaded', function() {
    renderCalendar();
});

function renderCalendar() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                        'July', 'August', 'September', 'October', 'November', 'December'];
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    document.getElementById('currentMonthYear').textContent = `${monthNames[month]} ${year}`;
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startingDay = firstDay.getDay();
    const totalDays = lastDay.getDate();
    
    const prevLastDay = new Date(year, month, 0).getDate();
    
    let html = '';
    
    // Day headers
    dayNames.forEach(day => {
        html += `<div class="calendar-day-header">${day}</div>`;
    });
    
    // Previous month days
    for (let i = startingDay - 1; i >= 0; i--) {
        html += `<div class="calendar-day other-month"><div class="calendar-day-number text-muted">${prevLastDay - i}</div></div>`;
    }
    
    // Current month days
    const today = new Date();
    for (let day = 1; day <= totalDays; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const isToday = today.getDate() === day && today.getMonth() === month && today.getFullYear() === year;
        
        let dayClasses = 'calendar-day';
        if (isToday) dayClasses += ' today';
        
        let dayBookings = bookings.filter(b => b.service_date === dateStr);
        
        let bookingsHtml = '';
        dayBookings.forEach(booking => {
            const statusClass = booking.status == 1 ? 'approved' : 'pending';
            const statusText = booking.status == 1 ? 'Approved' : 'Pending';
            bookingsHtml += `<div class="calendar-booking ${statusClass}" onclick="showBookingDetails(${booking.id})">${booking.service_time} - ${statusText}</div>`;
        });
        
        html += `
            <div class="${dayClasses}">
                <div class="calendar-day-number">${day}</div>
                ${bookingsHtml}
            </div>
        `;
    }
    
    // Next month days
    const remainingDays = 42 - (startingDay + totalDays);
    for (let i = 1; i <= remainingDays; i++) {
        html += `<div class="calendar-day other-month"><div class="calendar-day-number text-muted">${i}</div></div>`;
    }
    
    document.getElementById('calendarGrid').innerHTML = html;
}

function changeMonth(direction) {
    currentDate.setMonth(currentDate.getMonth() + direction);
    renderCalendar();
}

function showBookingDetails(bookingId) {
    const booking = bookings.find(b => b.id === bookingId);
    if (!booking) return;
    
    let statusBadge = '';
    if (booking.status == 0) {
        statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
    } else if (booking.status == 1) {
        statusBadge = '<span class="badge bg-success">Approved</span>';
    } else {
        statusBadge = '<span class="badge bg-danger">Cancelled</span>';
    }
    
    const modalBody = document.getElementById('bookingModalBody');
    modalBody.innerHTML = `
        <div class="mb-3">
            <strong>{{ get_phrase('Customer') }}:</strong> ${booking.name}
        </div>
        <div class="mb-3">
            <strong>{{ get_phrase('Email') }}:</strong> ${booking.email}
        </div>
        <div class="mb-3">
            <strong>{{ get_phrase('Phone') }}:</strong> ${booking.phone}
        </div>
        <div class="mb-3">
            <strong>{{ get_phrase('Date') }}:</strong> ${booking.service_date}
        </div>
        <div class="mb-3">
            <strong>{{ get_phrase('Time') }}:</strong> ${booking.service_time}
        </div>
        <div class="mb-3">
            <strong>{{ get_phrase('Status') }}:</strong> ${statusBadge}
        </div>
        <div class="mb-3">
            <strong>{{ get_phrase('Notes') }}:</strong> ${booking.notes || 'N/A'}
        </div>
    `;
    
    let footerHtml = '';
    if (booking.status == 0) {
        footerHtml = `
            <a href="{{ url('admin/service_manager/approve') }}/${booking.id}" class="btn btn-success">
                {{ get_phrase('Approve') }}
            </a>
        `;
    }
    footerHtml += `
        <a href="{{ url('admin/service_manager/paid') }}/${booking.id}" class="btn btn-primary">
            {{ get_phrase('Mark as Paid') }}
        </a>
        <a href="{{ url('admin/service_manager/delete') }}/${booking.id}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this booking?')">
            {{ get_phrase('Delete') }}
        </a>
    `;
    
    document.getElementById('bookingModalFooter').innerHTML = footerHtml;
    
    const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
    modal.show();
}
</script>
@endsection
