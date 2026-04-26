@php 
$user_prefix = (user('is_agent') == 1) ? 'agent' : 'customer'; 
if (!isset($active)) $active = '';
@endphp

<style>
.sidebar-user-panel { width: 280px; }
.sidebar-user-panel .sidebar-logo-area { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.sidebar-user-panel .user-info { display: flex; align-items: center; gap: 12px; padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.sidebar-user-panel .circle-img-50px { width: 50px; height: 50px; border-radius: 50%; overflow: hidden; flex-shrink: 0; }
.sidebar-user-panel .circle-img-50px img { width: 100%; height: 100%; object-fit: cover; }
.sidebar-user-panel .in-title-14px { font-size: 14px; font-weight: 600; color: #fff; margin: 0; }
.sidebar-user-panel .in-subtitle-14px { font-size: 12px; color: #99A1B7; margin: 0; }
.sidebar-user-panel .offcanvas-body { padding: 0; overflow-y: auto; }
.sidebar-user-panel .sidebar-title { font-size: 12px; font-weight: 600; color: #99A1B7; text-transform: uppercase; padding: 20px 20px 10px; letter-spacing: 0.5px; margin: 0; }
.sidebar-user-panel .sidebar-nav { padding: 0 10px; }
.sidebar-user-panel .sidebar-nav ul { list-style: none; padding: 0; margin: 0; }
.sidebar-user-panel .sidebar-nav-item { margin-bottom: 2px; }
.sidebar-user-panel .sidebar-nav-link { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; color: #99A1B7; text-decoration: none; font-size: 14px; transition: all 0.2s; }
.sidebar-user-panel .sidebar-nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; }
.sidebar-user-panel .sidebar-nav-link.active { background: var(--themeColor); color: #fff; }
.sidebar-user-panel .sidebar-nav-link svg { flex-shrink: 0; }
.sidebar-user-panel .badge-secondary { background: rgba(255,255,255,0.15); color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
.sidebar-user-panel .sidebar-nav-link.active .badge-secondary { background: rgba(255,255,255,0.25); }
</style>

<div class="sidebar-user-panel">
    <div class="sidebar-logo-area">
        <a href="{{ route('home') }}">
            @if(get_frontend_settings('light_logo'))
            <img height="40px" src="{{ asset('uploads/logo/' . get_frontend_settings('light_logo')) }}" alt="">
            @else
            <img height="40px" src="{{ asset('assets/backend/images/logo-light-bg.svg') }}" alt=""> 
            @endif
        </a>
    </div>
    
    <div class="user-info">
        <div class="circle-img-50px">
            <img src="{{ get_user_image('users/' . user('image')) }}" alt="">
        </div>
        <div>
            <h2 class="in-title-14px">{{ user('name') }}</h2>
            <p class="in-subtitle-14px text-break">{{ user('email') }}</p>
        </div>
    </div>

    <div class="offcanvas-body">
        <div class="w-100">
            <div>
                <h3 class="sidebar-title">{{ get_phrase('My Customer Panel') }}</h3>
                <nav class="sidebar-nav">
                    <ul>
                        <li class="sidebar-nav-item"><a href="{{ route('customer.wishlist') }}" class="sidebar-nav-link {{ $active == 'wishlist' ? 'active' : '' }}">
                                <span><i class="bi bi-heart"></i></span>
                                <span>{{ get_phrase('Wishlist') }}</span>
                            </a></li>
                        @if(addon_status('shop') == 1)
                        <li class="sidebar-nav-item"><a href="{{ route('shop.subscription') }}" class="sidebar-nav-link {{ $active == 'shop_subscription' ? 'active' : '' }}">
                                <span><i class="bi bi-credit-card"></i></span>
                                <span>{{ get_phrase('Shop Subscription') }}</span>
                            </a></li>
                        @endif
                        <li class="sidebar-nav-item"><a href="{{ route('customer.appointment') }}" class="sidebar-nav-link {{ $active == 'userAppointment' ? 'active' : '' }}">
                                <span><i class="bi bi-calendar-check"></i></span>
                                <span>{{ get_phrase('Appointment') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('customer.following') }}" class="sidebar-nav-link {{ $active == 'following' ? 'active' : '' }}">
                                <span><i class="bi bi-people"></i></span>
                                <span>{{ get_phrase('Following agent') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('customer.orders') }}" class="sidebar-nav-link {{ $active == 'orders' ? 'active' : '' }}">
                                <span><i class="bi bi-bag"></i></span>
                                <span>{{ get_phrase('My Orders') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('customer.service') }}" class="sidebar-nav-link {{ $active == 'my_service' ? 'active' : '' }}">
                                <span><i class="bi bi-hand-thumbs-up"></i></span>
                                <span>{{ get_phrase('My Service') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('customer.inbox') }}" class="sidebar-nav-link {{ $active == 'inbox' ? 'active' : '' }}">
                                <span><i class="bi bi-envelope"></i></span>
                                <span>{{ get_phrase('Message') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('customer.profile') }}" class="sidebar-nav-link {{ $active == 'profile' ? 'active' : '' }}">
                                <span><i class="bi bi-person"></i></span>
                                <span>{{ get_phrase('Account') }}</span>
                            </a></li>
                    </ul>
                </nav>
            </div>

            @if (check_subscription(user('id')))
            <div>
                <h3 class="sidebar-title">{{ get_phrase('My Agent Panel') }}</h3>
                <nav class="sidebar-nav">
                    <ul>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.dashboard') }}" class="sidebar-nav-link {{ $active == 'dashboard' ? 'active' : '' }}">
                                <span><i class="bi bi-speedometer2"></i></span>
                                <span>{{ get_phrase('Dashboard') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.my_listings') }}" class="sidebar-nav-link {{ $active == 'agent_listing' ? 'active' : '' }}">
                                <span><i class="bi bi-list-ul"></i></span>
                                <span>{{ get_phrase('My Listing') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.add.listing') }}" class="sidebar-nav-link {{ $active == 'add_listing' ? 'active' : '' }}">
                                <span><i class="bi bi-plus-circle"></i></span>
                                <span>{{ get_phrase('Add Listing') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.appointment') }}" class="sidebar-nav-link {{ $active == 'appointment' ? 'active' : '' }}">
                                <span><i class="bi bi-calendar-check"></i></span>
                                <span>{{ get_phrase('Appointment') }}</span>
                            </a></li>
                        @if (addon_status('shop') == 1)
                        <li class="sidebar-nav-item"><a href="{{ route('agent.order.manager') }}" class="sidebar-nav-link {{ $active == 'order_manager' ? 'active' : '' }}">
                                <span><i class="bi bi-bag-check"></i></span>
                                <span>{{ get_phrase('Order Manager') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.order.delivery') }}" class="sidebar-nav-link {{ $active == 'order_delivery' ? 'active' : '' }}">
                                <span><i class="bi bi-truck"></i></span>
                                <span>{{ get_phrase('Delivered Orders') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.subscription') }}" class="sidebar-nav-link {{ $active == 'subscription' ? 'active' : '' }}">
                                <span><i class="bi bi-credit-card"></i></span>
                                <span>{{ get_phrase('Shop Subscription') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.earnings') }}" class="sidebar-nav-link {{ $active == 'earnings' ? 'active' : '' }}">
                                <span><i class="bi bi-graph-up-arrow"></i></span>
                                <span>{{ get_phrase('Earnings') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.inventory') }}" class="sidebar-nav-link {{ $active == 'inventory' ? 'active' : '' }}">
                                <span><i class="bi bi-box-seam"></i></span>
                                <span>{{ get_phrase('Inventory') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.shop.orders') }}" class="sidebar-nav-link {{ $active == 'shop_orders' ? 'active' : '' }}">
                                <span><i class="bi bi-bag"></i></span>
                                <span>{{ get_phrase('Shop Orders') }}</span>
                            </a></li>
                        @endif
                        @if (addon_status('service_selling') == 1)
                        <li class="sidebar-nav-item"><a href="{{ route('agent.service.manager') }}" class="sidebar-nav-link {{ $active == 'service_manager' ? 'active' : '' }}">
                                <span><i class="bi bi-shop"></i></span>
                                <span>{{ get_phrase('Service Manager') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.my_created_services') }}" class="sidebar-nav-link {{ $active == 'my_created_services' ? 'active' : '' }}">
                                <span><i class="bi bi-check-circle"></i></span>
                                <span>{{ get_phrase('My Created Services') }}</span>
                            </a></li>
                        @endif
                        <li class="sidebar-nav-item"><a href="{{ route('agent.employee.list') }}" class="sidebar-nav-link {{ $active == 'employee' ? 'active' : '' }}">
                                <span><i class="bi bi-person-badge"></i></span>
                                <span>{{ get_phrase('Employee') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.claim') }}" class="sidebar-nav-link {{ $active == 'claim' ? 'active' : '' }}">
                                <span><i class="bi bi-patch-check"></i></span>
                                <span>{{ get_phrase('Claim') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.subscription') }}" class="sidebar-nav-link {{ $active == 'agent_subscription' ? 'active' : '' }}">
                                <span><i class="bi bi-credit-card"></i></span>
                                <span>{{ get_phrase('Subscription') }}</span>
                            </a></li>
                        <li class="sidebar-nav-item"><a href="{{ route('agent.badges') }}" class="sidebar-nav-link {{ $active == 'badges' ? 'active' : '' }}">
                                <span><i class="bi bi-award"></i></span>
                                <span>{{ get_phrase('Badges') }}</span>
                            </a></li>
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>