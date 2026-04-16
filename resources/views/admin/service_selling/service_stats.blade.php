@extends('layouts.admin')
@section('title', get_phrase('Booking Statistics'))
@section('admin_layout')

<div class="ol-card radius-8px">
    <div class="ol-card-body my-2 py-18px px-20px">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
            <h4 class="title fs-16px">
                <i class="fi-rr-settings-sliders me-2"></i>
                {{ get_phrase('Booking Statistics & Reports') }}
            </h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.service.manager') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list"></i> {{ get_phrase('List View') }}
                </a>
                <a href="{{ route('admin.service.manager.calendar') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-calendar"></i> {{ get_phrase('Calendar') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="ol-card mt-3">
    <div class="ol-card-body p-4">
        
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stats-card bg-gradient-primary text-white rounded-3 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 mb-1 small">{{ get_phrase('Total Bookings') }}</p>
                            <h2 class="mb-0 text-white">{{ $totalBookings }}</h2>
                        </div>
                        <div class="stats-icon bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-calendar-check text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card bg-gradient-warning text-dark rounded-3 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-dark-50 mb-1 small">{{ get_phrase('Pending Approval') }}</p>
                            <h2 class="mb-0 text-dark">{{ $pendingBookings }}</h2>
                        </div>
                        <div class="stats-icon bg-dark bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-hourglass-split text-dark fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card bg-gradient-success text-white rounded-3 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 mb-1 small">{{ get_phrase('Approved') }}</p>
                            <h2 class="mb-0 text-white">{{ $approvedBookings }}</h2>
                        </div>
                        <div class="stats-icon bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-check-circle text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stats-card bg-gradient-info text-white rounded-3 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 mb-1 small">{{ get_phrase('Monthly Revenue') }}</p>
                            <h2 class="mb-0 text-white">{!! currency($monthlyRevenue) !!}</h2>
                        </div>
                        <div class="stats-icon bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-currency-dollar text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-6 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>{{ get_phrase('Popular Services') }}</h5>
                    </div>
                    <div class="card-body">
                        @if(count($popularServices) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr class="small text-muted text-uppercase">
                                            <th>{{ get_phrase('Service') }}</th>
                                            <th class="text-center">{{ get_phrase('Bookings') }}</th>
                                            <th class="text-end">{{ get_phrase('Revenue') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($popularServices as $index => $service)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark me-2">#{{ $index + 1 }}</span>
                                                {{ $service['name'] }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill">{{ $service['count'] }}</span>
                                            </td>
                                            <td class="text-end fw-semibold">{!! currency($service['price'] * $service['count']) !!}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p class="mb-0">{{ get_phrase('No service data available') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-xl-6 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0"><i class="bi bi-calendar-week me-2 text-success"></i>{{ get_phrase('Bookings by Day of Week') }}</h5>
                    </div>
                    <div class="card-body d-flex align-items-center">
                        @if(count($dailyBookings) > 0)
                            <div class="chart-wrapper w-100" style="height: 220px;">
                                <canvas id="dailyChart"></canvas>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted w-100">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                <p class="mb-0">{{ get_phrase('No data available') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-6 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0"><i class="bi bi-clock me-2 text-info"></i>{{ get_phrase('Bookings by Hour') }}</h5>
                    </div>
                    <div class="card-body d-flex align-items-center">
                        @if(count($hourlyBookings) > 0)
                            <div class="chart-wrapper w-100" style="height: 220px;">
                                <canvas id="hourlyChart"></canvas>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted w-100">
                                <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                                <p class="mb-0">{{ get_phrase('No data available') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-xl-6 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0"><i class="bi bi-people me-2 text-warning"></i>{{ get_phrase('Recent Bookings') }}</h5>
                    </div>
                    <div class="card-body">
                        @if(count($recentBookings) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr class="small text-muted text-uppercase">
                                            <th>{{ get_phrase('Customer') }}</th>
                                            <th>{{ get_phrase('Date') }}</th>
                                            <th class="text-center">{{ get_phrase('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentBookings as $booking)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-person text-muted"></i>
                                                    </div>
                                                    {{ $booking->name }}
                                                </div>
                                            </td>
                                            <td class="text-muted">{{ $booking->service_date }}</td>
                                            <td class="text-center">
                                                @if($booking->status == 1)
                                                    <span class="badge bg-success-subtle text-success">{{ get_phrase('Approved') }}</span>
                                                @elseif($booking->status == 2)
                                                    <span class="badge bg-secondary-subtle text-secondary">{{ get_phrase('Cancelled') }}</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">{{ get_phrase('Pending') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p class="mb-0">{{ get_phrase('No recent bookings') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>{{ get_phrase('Monthly Overview') }}</h5>
                    <span class="badge bg-primary rounded-pill px-3 py-2">{{ date('F Y') }}</span>
                </div>
            </div>
            <div class="card-body py-4">
                <div class="row text-center g-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light">
                            <h3 class="text-primary mb-1">{{ $monthlyBookings }}</h3>
                            <p class="text-muted mb-0 small">{{ get_phrase('Bookings This Month') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light">
                            <h3 class="text-success mb-1">{!! currency($monthlyRevenue) !!}</h3>
                            <p class="text-muted mb-0 small">{{ get_phrase('Revenue This Month') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-light">
                            <h3 class="text-info mb-1">{{ $totalBookings > 0 ? round(($approvedBookings / $totalBookings) * 100) : 0 }}%</h3>
                            <p class="text-muted mb-0 small">{{ get_phrase('Approval Rate') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.stats-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.card {
    border-radius: 12px;
    overflow: hidden;
}
.card-header {
    border-bottom: 1px solid #f0f0f0;
}
.badge.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1);
}
.badge.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1);
}
.badge.bg-secondary-subtle {
    background-color: rgba(108, 117, 125, 0.1);
}
.avatar {
    width: 32px;
    height: 32px;
}
.table th {
    font-weight: 600;
    border-bottom: 2px solid #f0f0f0;
}
.table td {
    vertical-align: middle;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = 'system-ui, -apple-system, sans-serif';
    Chart.defaults.color = '#6c757d';
    
    @if(count($dailyBookings) > 0)
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($dailyBookings->pluck('day')->toArray()) !!},
            datasets: [{
                label: '{{ get_phrase('Bookings') }}',
                data: {!! json_encode($dailyBookings->pluck('count')->toArray()) !!},
                backgroundColor: 'rgba(25, 135, 84, 0.8)',
                borderColor: '#11998e',
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
    @endif

    @if(count($hourlyBookings) > 0)
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($hourlyBookings->pluck('hour')->map(function($h) { return $h . ':00'; })->toArray()) !!},
            datasets: [{
                label: '{{ get_phrase('Bookings') }}',
                data: {!! json_encode($hourlyBookings->pluck('count')->toArray()) !!},
                borderColor: '#4facfe',
                backgroundColor: 'rgba(79, 172, 254, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#4facfe'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
    @endif
});
</script>
@endsection
