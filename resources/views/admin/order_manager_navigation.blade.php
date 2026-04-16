@php $current_route = Route::currentRouteName(); @endphp

<div class="sidebar-logo-area">
    <a href="{{ route('home') }}" class="sidebar-logos">
        @if(get_frontend_settings('light_logo'))
        <img class="sidebar-logo-lg" height="50px" src="{{ asset('uploads/logo/' . get_frontend_settings('light_logo')) }}" alt="">
        @else
          <img class="sidebar-logo-lg" height="50px" src="{{ asset('assets/backend/images/logo-light-bg.svg') }}" alt="">
        @endif
    </a>
    <button class="sidebar-cross menu-toggler d-block d-lg-none">
        <span class="fi-rr-cross"></span>
    </button>
</div>
<div class="sidebar-nav-area">
    <nav class="sidebar-nav">
        <h3 class="sidebar-title fs-12px px-30px pb-20px text-uppercase mt-4">{{get_phrase('ORDER MANAGEMENT')}}</h3>
        <ul class="px-14px pb-24px">
            <li class="sidebar-first-li {{request()->is('order-manager/dashboard')?'active':''}}">
                <a href="{{ route('order.manager.dashboard') }}">
                    <span><svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M14.8243 18.9584H5.17435C2.89102 18.9584 1.04102 17.1001 1.04102 14.8167V8.64173C1.04102 7.50839 1.74102 6.08339 2.64102 5.38339L7.13268 1.88339C8.48268 0.833393 10.641 0.783393 12.041 1.76673L17.191 5.37506C18.1827 6.06673 18.9577 7.55006 18.9577 8.75839V14.8251C18.9577 17.1001 17.1077 18.9584 14.8243 18.9584ZM7.89935 2.86673L3.40768 6.36673C2.81602 6.83339 2.29102 7.89173 2.29102 8.64173V14.8167C2.29102 16.4084 3.58268 17.7084 5.17435 17.7084H14.8243C16.416 17.7084 17.7077 16.4167 17.7077 14.8251V8.75839C17.7077 7.95839 17.1327 6.85006 16.4743 6.40006L11.3243 2.79173C10.3743 2.12506 8.80768 2.15839 7.89935 2.86673Z" fill="#99A1B7"/><path d="M10 15.625C9.65833 15.625 9.375 15.3417 9.375 15V12.5C9.375 12.1583 9.65833 11.875 10 11.875C10.3417 11.875 10.625 12.1583 10.625 12.5V15C10.625 15.3417 10.3417 15.625 10 15.625Z" fill="#99A1B7"/></svg></span>
                    <div class="text"><span>{{get_phrase('Dashboard')}}</span></div>
                </a>
            </li>
            <li class="sidebar-first-li first-li-have-sub {{request()->is('order-manager/approval*')?'active':''}}">
                <a href="javascript:void(0);">
                    <span><svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M17.8167 10.8334H15.1667V5.00008C15.1667 4.08341 14.4167 2.50008 12.5 2.50008C10.5833 2.50008 9.83333 4.08341 9.83333 5.00008V8.33341H2.18333C1.69167 8.33341 1.25 8.77508 1.25 9.26675C1.25 9.75841 1.69167 10.2001 2.18333 10.2001H9.83333V15.0001C9.83333 16.3334 11.0833 18.3334 12.5 18.3334C13.9167 18.3334 15.1667 16.3334 15.1667 15.0001V10.8334H17.8167C18.3083 10.8334 18.75 10.3917 18.75 9.90008C18.75 9.40841 18.3083 8.96675 17.8167 8.96675Z" fill="#99A1B7"/></svg></span>
                    <div class="text"><span>{{get_phrase('Approve Orders')}}</span></div>
                </a>
                <ul class="first-sub-menu">
                    <li class="sidebar-second-li {{request()->is('order-manager/approval')?'active':''}}"><a href="{{ route('order.manager.approval')}}">{{get_phrase('Pending Approval')}}</a></li>
                </ul>
            </li>
            <li class="sidebar-first-li first-li-have-sub {{request()->is('order-manager/delivery*')?'active':''}}">
                <a href="javascript:void(0);">
                    <span><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M10.5179 14.5V17.5C10.5179 17.776 10.2939 18 10.0179 18C9.74194 18 9.51794 17.776 9.51794 17.5V14.5C9.51794 14.224 9.74194 14 10.0179 14C10.2939 14 10.5179 14.224 10.5179 14.5ZM14.0179 14C13.7419 14 13.5179 14.224 13.5179 14.5V17.5C13.5179 17.776 13.7419 18 14.0179 18C14.2939 18 14.5179 17.776 14.5179 17.5V14.5C14.5179 14.224 14.2939 14 14.0179 14ZM19.991 11.293L19.286 18.349C19.074 20.469 17.935 21.5 15.804 21.5H8.23401C5.39401 21.5 4.88595 19.701 4.75195 18.349L4.04797 11.31C3.14097 10.935 2.50098 10.041 2.50098 9C2.50098 7.622 3.62198 6.5 5.00098 6.5H7.29895L9.573 2.741C9.717 2.504 10.025 2.43001 10.26 2.57201C10.496 2.71501 10.572 3.022 10.429 3.259L8.46802 6.5H15.502L13.572 3.256C13.43 3.019 13.509 2.71201 13.746 2.57001C13.98 2.43001 14.288 2.506 14.432 2.744L16.666 6.5H19C20.379 6.5 21.5 7.622 21.5 9C21.5 10.026 20.878 10.908 19.991 11.293Z" fill="#99A1B7"/></svg></span>
                    <div class="text"><span>{{get_phrase('Delivery Orders')}}</span></div>
                </a>
                <ul class="first-sub-menu">
                    <li class="sidebar-second-li {{request()->is('order-manager/delivery') && !request('settings')?'active':''}}"><a href="{{ route('order.manager.delivery')}}">{{get_phrase('All Deliveries')}}</a></li>
                    <li class="sidebar-second-li {{request()->is('order-manager/delivery/settings')?'active':''}}"><a href="{{ route('order.manager.delivery.settings')}}">{{get_phrase('Settings')}}</a></li>
                </ul>
            </li>
            <li class="sidebar-first-li {{request()->is('order-manager/orders')?'active':''}}">
                <a href="{{ route('order.manager.orders') }}">
                    <span><svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M6.66602 4.79175C6.32435 4.79175 6.04102 4.50841 6.04102 4.16675V1.66675C6.04102 1.32508 6.32435 1.04175 6.66602 1.04175C7.00768 1.04175 7.29102 1.32508 7.29102 1.66675V4.16675C7.29102 4.50841 7.00768 4.79175 6.66602 4.79175Z" fill="#99A1B7"/><path d="M12.2333 18.9584H6.66667C3.625 18.9584 1.875 17.2084 1.875 14.1667V7.08341C1.875 4.04175 3.625 2.29175 6.66667 2.29175H13.3333C16.375 2.29175 18.125 4.04175 18.125 7.08341V11.3584C18.125 11.6001 17.9833 11.8167 17.775 11.9251C17.5583 12.0251 17.3 12.0001 17.1167 11.8501C15.8 10.8001 13.775 10.8001 12.4583 11.8751C11.5583 12.5834 11.05 13.6418 11.05 14.8001C11.05 15.4834 11.2333 16.1501 11.5917 16.7251C11.8667 17.1751 12.2083 17.5501 12.6083 17.8334C12.825 17.9918 12.925 18.2751 12.8417 18.5334C12.7417 18.7834 12.5083 18.9584 12.2333 18.9584Z" fill="#99A1B7"/></svg></span>
                    <div class="text"><span>{{get_phrase('All Orders')}}</span></div>
                </a>
            </li>
            <li class="sidebar-first-li {{request()->is('order-manager/analytics')?'active':''}}">
                <a href="{{ route('order.manager.analytics') }}">
                    <span><svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M3 17V10M7 17V7M11 17V13M15 17V3" stroke="#99A1B7" stroke-width="2" stroke-linecap="round"/></svg></span>
                    <div class="text"><span>{{get_phrase('Analytics')}}</span></div>
                </a>
            </li>
        </ul>
    </nav>
</div>
