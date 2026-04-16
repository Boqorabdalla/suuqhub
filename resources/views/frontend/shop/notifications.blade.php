@extends('layouts.frontend')
@push('title', get_phrase('My Notifications'))
@push('meta')@endpush
@section('frontend_layout')
<section class="mb-60px mt-3">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                @include('layouts.customer.nav')
            </div>
            <div class="col-lg-9">
                <div class="atn-shadow-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">{{ get_phrase('My Notifications') }}</h4>
                        @if($notifications->where('read_at', null)->count() > 0)
                            <a href="{{ route('shop.notifications.mark-all-read') }}" class="btn btn-sm btn-outline-primary">
                                {{ get_phrase('Mark All as Read') }}
                            </a>
                        @endif
                    </div>
                    
                    @if($notifications->count() > 0)
                        <div class="notification-list">
                            @foreach($notifications as $notification)
                                @php $notifData = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data; @endphp
                                <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }} p-3 mb-2 rounded {{ !$notification->read_at ? 'bg-light' : '' }}">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="notification-icon">
                                            @if($notifData['status'] == 'approved')
                                                <div class="bg-success bg-opacity-10 p-2 rounded-circle">
                                                    <i class="bi bi-check-circle text-success"></i>
                                                </div>
                                            @elseif($notifData['status'] == 'rejected')
                                                <div class="bg-danger bg-opacity-10 p-2 rounded-circle">
                                                    <i class="bi bi-x-circle text-danger"></i>
                                                </div>
                                            @elseif($notifData['status'] == 'shipped')
                                                <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                                    <i class="bi bi-truck text-primary"></i>
                                                </div>
                                            @elseif($notifData['status'] == 'delivered')
                                                <div class="bg-success bg-opacity-10 p-2 rounded-circle">
                                                    <i class="bi bi-box-seam text-success"></i>
                                                </div>
                                            @else
                                                <div class="bg-info bg-opacity-10 p-2 rounded-circle">
                                                    <i class="bi bi-info-circle text-info"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $notifData['title'] ?? 'Notification' }}</h6>
                                            <p class="mb-2 text-muted">{{ $notifData['message'] ?? '' }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">{{ $notification->created_at->format('M d, Y - h:i A') }}</small>
                                                @if($notification->read_at)
                                                    <span class="badge bg-secondary">{{ get_phrase('Read') }}</span>
                                                @else
                                                    <span class="badge bg-primary">{{ get_phrase('New') }}</span>
                                                @endif
                                            </div>
                                            @if(isset($notifData['order_id']))
                                                <a href="{{ route('shop.order', $notifData['order_id']) }}" class="btn btn-sm btn-outline-primary mt-2">
                                                    {{ get_phrase('View Order') }}
                                                </a>
                                            @endif
                                        </div>
                                        @if(!$notification->read_at)
                                            <a href="{{ route('shop.notifications.read', $notification->id) }}" class="text-muted" title="{{ get_phrase('Mark as read') }}">
                                                <i class="bi bi-x"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash mb-3" style="font-size: 48px; color: #ccc;"></i>
                            <p class="text-muted">{{ get_phrase('No notifications yet') }}</p>
                        </div>
                    @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $notifData['title'] ?? 'Notification' }}</h6>
                                            <p class="mb-2 text-muted">{{ $notifData['message'] ?? '' }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">{{ $notification->created_at->format('M d, Y - h:i A') }}</small>
                                                @if($notification->read_at)
                                                    <span class="badge bg-secondary">{{ get_phrase('Read') }}</span>
                                                @else
                                                    <span class="badge bg-primary">{{ get_phrase('New') }}</span>
                                                @endif
                                            </div>
                                            @if(isset($notification->data['order_id']))
                                                <a href="{{ route('shop.order', $notification->data['order_id']) }}" class="btn btn-sm btn-outline-primary mt-2">
                                                    {{ get_phrase('View Order') }}
                                                </a>
                                            @endif
                                        </div>
                                        @if(!$notification->read_at)
                                            <a href="{{ route('shop.notifications.read', $notification->id) }}" class="text-muted" title="{{ get_phrase('Mark as read') }}">
                                                <i class="fi-rr-cross"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fi-rr-bell-slash mb-3" style="font-size: 48px; color: #ccc;"></i>
                            <p class="text-muted">{{ get_phrase('No notifications yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
