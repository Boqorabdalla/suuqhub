@extends('layouts.frontend')
@push('title', get_phrase('Agent Dashboard'))
@section('frontend_layout')
    
    <section class="mb-4">
        <div class="container">
            <div class="row gx-20px">
                <div class="col-lg-4 col-xl-3">
                    @include('user.navigation')
                </div>
                <div class="col-lg-8 col-xl-9">
                    <!-- Header -->
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-20px">
                        <div class="d-flex justify-content-between align-items-start gap-12px flex-column flex-lg-row w-100">
                            <h1 class="ca-title-18px">{{ get_phrase('Dashboard') }}</h1>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row g-3 mb-4">
                        <!-- Total Listings -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-white border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="icon-box bg-primary-subtle rounded p-2">
                                            <i class="bi bi-list-ul text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-0 small">{{ get_phrase('Total Listings') }}</p>
                                            <h4 class="mb-0">{{ $total_listings }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Bookings -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-white border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="icon-box bg-success-subtle rounded p-2">
                                            <i class="bi bi-calendar-check text-success fs-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-0 small">{{ get_phrase('Total Bookings') }}</p>
                                            <h4 class="mb-0">{{ $total_bookings }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Bookings -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-white border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="icon-box bg-warning-subtle rounded p-2">
                                            <i class="bi bi-hourglass-split text-warning fs-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-0 small">{{ get_phrase('Pending') }}</p>
                                            <h4 class="mb-0">{{ $pending_bookings }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Completed -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card bg-white border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="icon-box bg-info-subtle rounded p-2">
                                            <i class="bi bi-check-circle text-info fs-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-0 small">{{ get_phrase('Completed') }}</p>
                                            <h4 class="mb-0">{{ $completed_bookings }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Earnings Row -->
                    <div class="row g-3 mb-4">
                        <!-- Total Earnings -->
                        <div class="col-md-6">
                            <div class="card bg-primary border-0 shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="icon-box bg-white rounded p-2">
                                            <i class="bi bi-currency-dollar text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-white-50 mb-0 small">{{ get_phrase('Total Earnings') }}</p>
                                            <h3 class="mb-0 text-white">{{ currency($total_earnings) }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Earnings -->
                        <div class="col-md-6">
                            <div class="card bg-success border-0 shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="icon-box bg-white rounded p-2">
                                            <i class="bi bi-graph-up text-success fs-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-white-50 mb-0 small">{{ get_phrase('This Month') }}</p>
                                            <h3 class="mb-0 text-white">{{ currency($monthly_earnings) }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0">{{ get_phrase('Recent Bookings') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            @if($recent_bookings->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ get_phrase('Service') }}</th>
                                                <th>{{ get_phrase('Date') }}</th>
                                                <th>{{ get_phrase('Time') }}</th>
                                                <th>{{ get_phrase('Amount') }}</th>
                                                <th>{{ get_phrase('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recent_bookings as $booking)
                                                <tr>
                                                    <td>
                                                        @if($booking->service)
                                                            {{ $booking->service->name }}
                                                        @else
                                                            {{ get_phrase('Service') }} #{{ $booking->service_id }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $booking->date }}</td>
                                                    <td>{{ $booking->start_time }}</td>
                                                    <td>{{ currency($booking->total_amount) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($booking->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                    <p class="mt-2 text-muted">{{ get_phrase('No bookings yet') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection