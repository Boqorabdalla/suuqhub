@extends('layouts.frontend')
@push('title', get_phrase('Service Cart'))
@section('frontend_layout')

<style>
    .agent-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        border-left: 4px solid #4CAF50;
    }
    .agent-header {
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
    }
    .conflict-warning {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 15px;
    }
</style>

<section class="ca-wraper-main mb-90px mt-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="ca-content-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="ca-title-18px">{{ get_phrase('Your Service Cart') }}</h4>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus"></i> {{ get_phrase('Add More Services') }}
                        </a>
                    </div>
                    
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i>
                        <strong>{{ get_phrase('Multi-Agent Booking:') }}</strong> 
                        {{ get_phrase('You can book services from different agents/businesses in a single cart. Each agent will receive their own booking notification.') }}
                    </div>
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if($cartItems->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x fs-1 text-muted"></i>
                            <p class="text-muted mt-3">{{ get_phrase('Your cart is empty') }}</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">{{ get_phrase('Browse Services') }}</a>
                        </div>
                    @else
                        @php
                            $hasConflicts = false;
                            $conflicts = [];
                            $timeSlots = [];
                        @endphp
                        
                        @foreach($cartItems as $item)
                            @php
                                $existingBooking = \App\Models\ServiceBooking::where('employee_id', $item->employee_id)
                                    ->where('service_date', $item->service_date)
                                    ->where('service_time', $item->service_time)
                                    ->whereIn('status', [0, 1])
                                    ->first();
                                    
                                if ($existingBooking) {
                                    $hasConflicts = true;
                                    $conflicts[] = $item->service->name . ' at ' . $item->service_time . ' (provider already booked)';
                                }
                                
                                $slotKey = $item->service_date . '_' . $item->service_time;
                                if (isset($timeSlots[$slotKey])) {
                                    $hasConflicts = true;
                                    $conflicts[] = $item->service->name . ' at ' . $item->service_time . ' (duplicate time slot)';
                                }
                                $timeSlots[$slotKey] = $item->service->name;
                            @endphp
                        @endforeach
                        
                        @if($hasConflicts)
                            <div class="conflict-warning mb-4">
                                <h6 class="text-warning mb-2"><i class="bi bi-exclamation-triangle"></i> {{ get_phrase('Booking Conflicts Detected') }}</h6>
                                @foreach($conflicts as $conflict)
                                    <p class="mb-1"><i class="bi bi-x-circle text-danger"></i> {{ $conflict }}</p>
                                @endforeach
                                <p class="mb-0 text-muted small">{{ get_phrase('Please remove conflicting items before checkout.') }}</p>
                            </div>
                        @endif
                        
                        @php
                            $groupedByAgent = $cartItems->groupBy(function($item) {
                                return $item->service->user_id ?? 'unknown';
                            });
                        @endphp
                        
                        @foreach($groupedByAgent as $agentId => $agentItems)
                            @php
                                $agentUser = \App\Models\User::find($agentId);
                            @endphp
                            <div class="agent-section">
                                <div class="agent-header">
                                    <i class="bi bi-building"></i> {{ get_phrase('Agent/Business') }}: {{ $agentUser->name ?? 'Unknown' }}
                                    <span class="badge bg-primary ms-2">{{ count($agentItems) }} {{ get_phrase('service(s)') }}</span>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ get_phrase('Service') }}</th>
                                                <th>{{ get_phrase('Provider') }}</th>
                                                <th>{{ get_phrase('Date & Time') }}</th>
                                                <th>{{ get_phrase('Price') }}</th>
                                                <th>{{ get_phrase('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($agentItems as $item)
                                            @php
                                                $existingBooking = \App\Models\ServiceBooking::where('employee_id', $item->employee_id)
                                                    ->where('service_date', $item->service_date)
                                                    ->where('service_time', $item->service_time)
                                                    ->whereIn('status', [0, 1])
                                                    ->first();
                                                $isConflict = $existingBooking ? true : false;
                                                
                                                $slotKey = $item->service_date . '_' . $item->service_time;
                                                $count = 0;
                                                foreach($cartItems as $checkItem) {
                                                    $checkKey = $checkItem->service_date . '_' . $checkItem->service_time;
                                                    if ($checkKey == $slotKey) {
                                                        $count++;
                                                    }
                                                }
                                                if ($count > 1) {
                                                    $isConflict = true;
                                                }
                                            @endphp
                                            <tr class="{{ $isConflict ? 'table-danger' : '' }}">
                                                <td>
                                                    <strong>{{ $item->service->name ?? 'N/A' }}</strong>
                                                    @if($isConflict)
                                                        <span class="badge bg-danger ms-2">{{ get_phrase('Conflict') }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->employee->name ?? 'N/A' }}</td>
                                                <td>
                                                    <div>{{ $item->service_date }}</div>
                                                    <small class="text-muted">{{ $item->service_day }} - {{ $item->service_time }}</small>
                                                </td>
                                                <td>{{ currency($item->price) }}</td>
                                                <td>
                                                    <a href="{{ route('service.remove_from_cart', ['id' => $item->id]) }}" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>{{ get_phrase('Agent Total:') }}</strong></td>
                                                <td><strong>{{ currency($agentItems->sum('price')) }}</strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        
        @if(!$cartItems->isEmpty())
        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="ca-content-card">
                    <h5 class="mb-3">{{ get_phrase('Booking Summary') }}</h5>
                    
                    @php
                        $groupedByAgent = $cartItems->groupBy(function($item) {
                            return $item->service->user_id ?? 'unknown';
                        });
                    @endphp
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ get_phrase('Total Services') }}</span>
                        <span>{{ count($cartItems) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ get_phrase('Agents/Businesses') }}</span>
                        <span>{{ $groupedByAgent->count() }}</span>
                    </div>
                    <hr>
                    @foreach($groupedByAgent as $agentId => $agentItems)
                        @php
                            $agentUser = \App\Models\User::find($agentId);
                        @endphp
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">{{ $agentUser->name ?? 'Unknown' }}</span>
                            <span>{{ currency($agentItems->sum('price')) }}</span>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong>{{ get_phrase('Total') }}</strong>
                        <strong>{{ currency($cartTotal) }}</strong>
                    </div>
                    
                    @if($hasConflicts)
                        <button class="btn btn-secondary w-100" disabled>
                            {{ get_phrase('Resolve Conflicts First') }}
                        </button>
                    @else
                        <button class="btn btn-primary w-100" onclick="showCheckoutForm()">
                            {{ get_phrase('Proceed to Checkout') }}
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="col-lg-8">
                @if(!$hasConflicts)
                <div class="ca-content-card" id="checkoutForm" style="display: none;">
                    <h5 class="mb-3">{{ get_phrase('Customer Details') }}</h5>
                    <p class="text-muted small">{{ get_phrase('This information will be shared with all agents you are booking with.') }}</p>
                    
                    <form action="{{ route('service.checkout') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ get_phrase('Full Name') }} *</label>
                                <input type="text" name="name" class="form-control" required value="{{ auth()->user()->name ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ get_phrase('Email') }} *</label>
                                <input type="email" name="email" class="form-control" required value="{{ auth()->user()->email ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ get_phrase('Phone') }} *</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">{{ get_phrase('Notes') }} *</label>
                                <textarea name="notes" class="form-control" rows="3" required placeholder="{{ get_phrase('Any special requests or notes...') }}"></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            {{ get_phrase('Confirm Bookings') }} ({{ count($cartItems) }} {{ get_phrase('services from') }} {{ $groupedByAgent->count() }} {{ get_phrase('agent(s)') }})
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</section>

<script>
function showCheckoutForm() {
    document.getElementById('checkoutForm').style.display = 'block';
    document.getElementById('checkoutForm').scrollIntoView({ behavior: 'smooth' });
}
</script>

@endsection
