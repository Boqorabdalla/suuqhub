@extends('layouts.frontend')
@push('title', get_phrase('Track Order'))
@push('meta')@endpush
@section('frontend_layout')

<section class="shop-header bg-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2 class="mb-2">{{ get_phrase('Track Your Order') }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ get_phrase('Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ get_phrase('Track Order') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="shop-content py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ get_phrase('Enter Your Order Number') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('shop.track') }}" method="GET">
                            <div class="mb-3">
                                <label class="form-label">{{ get_phrase('Order Number') }}</label>
                                <input type="text" name="order_number" class="form-control" placeholder="{{ get_phrase('e.g., ORD-ABC123-20240101') }}" value="{{ request('order_number') }}" required>
                                <small class="text-muted">{{ get_phrase('Find your order number in your confirmation email') }}</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fi-rr-search me-2"></i>{{ get_phrase('Track Order') }}
                            </button>
                        </form>
                        
                        @if(session('error'))
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="fi-rr-info me-1"></i>{{ session('error') }}
                            </div>
                        @endif
                    </div>
                </div>
                
                @auth
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ get_phrase('Your Recent Orders') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ get_phrase('Order') }}</th>
                                    <th>{{ get_phrase('Date') }}</th>
                                    <th>{{ get_phrase('Total') }}</th>
                                    <th>{{ get_phrase('Status') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $recentOrders = \App\Models\ShopOrder::where('user_id', auth()->user()->id)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(5)
                                    ->get();
                                @endphp
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td><strong>{{ $order->order_number }}</strong></td>
                                    <td>{{ date_formatter($order->created_at, 3) }}</td>
                                    <td>{{ currency($order->total) }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($order->order_status) {
                                                'pending' => 'bg-warning text-dark',
                                                'processing' => 'bg-info',
                                                'shipped' => 'bg-primary',
                                                'delivered' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($order->order_status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('shop.order', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fi-rr-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">{{ get_phrase('No orders yet') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endauth
            </div>
        </div>
        
        @if(isset($order) && $order)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ get_phrase('Order') }}: {{ $order->order_number }}</h5>
                        <a href="{{ route('shop.order', $order->id) }}" class="btn btn-sm btn-primary">
                            {{ get_phrase('View Details') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h6 class="text-muted">{{ get_phrase('Order Status') }}</h6>
                                @php
                                    $statusClass = match($order->order_status) {
                                        'pending' => 'bg-warning text-dark',
                                        'processing' => 'bg-info',
                                        'shipped' => 'bg-primary',
                                        'delivered' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} fs-6">{{ ucfirst($order->order_status) }}</span>
                            </div>
                            
                            <div class="col-md-3">
                                <h6 class="text-muted">{{ get_phrase('Payment Status') }}</h6>
                                @php
                                    $paymentClass = match($order->payment_status) {
                                        'paid' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'failed' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $paymentClass }}">{{ ucfirst($order->payment_status) }}</span>
                            </div>
                            
                            @if($order->shipping_method == 'delivery')
                            <div class="col-md-3">
                                <h6 class="text-muted">{{ get_phrase('Approval Status') }}</h6>
                                <span class="badge {{ $order->approval_badge_class }}">{{ $order->approval_status_label }}</span>
                            </div>
                            
                            @if($order->approval_status == 'approved')
                            <div class="col-md-3">
                                <h6 class="text-muted">{{ get_phrase('Delivery Status') }}</h6>
                                <span class="badge {{ $order->delivery_status_badge_class }}">{{ $order->delivery_status_label }}</span>
                            </div>
                            @endif
                            @else
                            <div class="col-md-3">
                                <h6 class="text-muted">{{ get_phrase('Shipping') }}</h6>
                                <span class="badge bg-info">{{ get_phrase('Store Pickup') }}</span>
                            </div>
                            @endif
                        </div>
                        
                        @if($order->shipping_method == 'delivery' && $order->approval_status == 'approved')
                        <hr>
                        <h6 class="mb-3">{{ get_phrase('Delivery Timeline') }}</h6>
                        <div class="delivery-timeline">
                            <div class="d-flex justify-content-between">
                                <div class="text-center">
                                    <div class="delivery-step {{ in_array($order->delivery_status, ['pending', 'picked_up', 'in_transit', 'delivered']) ? 'active' : '' }}">
                                        <i class="fi-rr-check-circle"></i>
                                    </div>
                                    <small>{{ get_phrase('Approved') }}</small>
                                </div>
                                <div class="delivery-line {{ in_array($order->delivery_status, ['picked_up', 'in_transit', 'delivered']) ? 'active' : '' }}"></div>
                                <div class="text-center">
                                    <div class="delivery-step {{ in_array($order->delivery_status, ['picked_up', 'in_transit', 'delivered']) ? 'active' : '' }}">
                                        <i class="fi-rr-box"></i>
                                    </div>
                                    <small>{{ get_phrase('Picked Up') }}</small>
                                </div>
                                <div class="delivery-line {{ in_array($order->delivery_status, ['in_transit', 'delivered']) ? 'active' : '' }}"></div>
                                <div class="text-center">
                                    <div class="delivery-step {{ in_array($order->delivery_status, ['in_transit', 'delivered']) ? 'active' : '' }}">
                                        <i class="fi-rr-truck"></i>
                                    </div>
                                    <small>{{ get_phrase('In Transit') }}</small>
                                </div>
                                <div class="delivery-line {{ $order->delivery_status == 'delivered' ? 'active' : '' }}"></div>
                                <div class="text-center">
                                    <div class="delivery-step {{ $order->delivery_status == 'delivered' ? 'active' : '' }}">
                                        <i class="fi-rr-home"></i>
                                    </div>
                                    <small>{{ get_phrase('Delivered') }}</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

@push('css')
<style>
    .delivery-timeline {
        padding: 20px 0;
    }
    .delivery-timeline .d-flex {
        align-items: flex-start;
    }
    .delivery-step {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #adb5bd;
        margin: 0 auto 8px;
    }
    .delivery-step.active {
        background: #198754;
        color: #fff;
    }
    .delivery-line {
        flex-grow: 1;
        height: 3px;
        background: #e9ecef;
        margin-top: 23px;
        max-width: 80px;
    }
    .delivery-line.active {
        background: #198754;
    }
</style>
@endpush

@endsection
