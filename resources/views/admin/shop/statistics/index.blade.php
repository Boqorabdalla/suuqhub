@extends('layouts.admin')
@section('title', get_phrase('Shop Statistics'))
@section('admin_layout')
<style>
    .stats-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 35px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    .stats-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .stats-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: 20%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .stats-header h1 {
        font-weight: 800;
        font-size: 32px;
        margin-bottom: 8px;
    }
    .stats-header p {
        opacity: 0.9;
        margin-bottom: 0;
    }
    
    .stat-card {
        border-radius: 20px;
        border: none;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
        position: relative;
    }
    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(0,0,0,0.15) !important;
    }
    .stat-card .icon-wrap {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        position: relative;
    }
    .stat-card .icon-wrap::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 20px;
        opacity: 0.2;
    }
    .stat-card .stat-value {
        font-size: 36px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 5px;
    }
    .stat-card .stat-label {
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.8;
    }
    .stat-card .stat-trend {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .stat-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .stat-purple .icon-wrap { background: rgba(255,255,255,0.2); color: white; }
    
    .stat-blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
    .stat-blue .icon-wrap { background: rgba(255,255,255,0.2); color: white; }
    
    .stat-green { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
    .stat-green .icon-wrap { background: rgba(255,255,255,0.2); color: white; }
    
    .stat-orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
    .stat-orange .icon-wrap { background: rgba(255,255,255,0.2); color: white; }
    
    .stat-red { background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%); color: white; }
    .stat-red .icon-wrap { background: rgba(255,255,255,0.2); color: white; }
    
    .stat-pink { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
    .stat-pink .icon-wrap { background: rgba(255,255,255,0.2); color: white; }
    
    .stat-indigo { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; }
    .stat-indigo .icon-wrap { background: rgba(255,255,255,0.2); color: white; }
    
    .stat-cyan { background: linear-gradient(135deg, #00c6fb 0%, #005bea 100%); color: white; }
    .stat-cyan .icon-wrap { background: rgba(255,255,255,0.2); color: white; }
    
    .section-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .section-card .card-header {
        background: white;
        border: none;
        padding: 20px 25px;
        font-weight: 700;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .section-card .card-header i {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .progress-card {
        background: white;
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 12px;
        border: 1px solid #f0f0f0;
        transition: all 0.3s;
    }
    .progress-card:hover {
        border-color: #667eea;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.1);
    }
    .progress-card .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-weight: 600;
    }
    .progress-card .progress {
        height: 10px;
        border-radius: 10px;
        background: #f0f0f0;
    }
    .progress-card .progress-bar {
        border-radius: 10px;
        transition: width 1s ease;
    }
    
    .quick-action-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        border: 2px solid transparent;
        transition: all 0.3s;
        cursor: pointer;
    }
    .quick-action-card:hover {
        border-color: #667eea;
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(102, 126, 234, 0.15);
    }
    .quick-action-card .action-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 24px;
    }
    .quick-action-card .action-count {
        font-size: 28px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 5px;
    }
    .quick-action-card .action-label {
        font-size: 13px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-modern thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 15px 20px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 1px;
    }
    .table-modern thead th:first-child {
        border-radius: 12px 0 0 12px;
    }
    .table-modern thead th:last-child {
        border-radius: 0 12px 12px 0;
    }
    .table-modern tbody td {
        border: none;
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 20px;
        vertical-align: middle;
    }
    .table-modern tbody tr:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    }
    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }
    
    .badge-modern {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .monthly-chart {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 16px;
        padding: 25px;
    }
    .monthly-bar {
        display: flex;
        align-items: flex-end;
        gap: 15px;
        height: 200px;
        padding-top: 20px;
    }
    .monthly-bar .bar-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    .monthly-bar .bar {
        width: 100%;
        background: linear-gradient(to top, #667eea, #764ba2);
        border-radius: 8px 8px 0 0;
        transition: all 0.5s ease;
        position: relative;
        min-height: 10px;
    }
    .monthly-bar .bar:hover {
        background: linear-gradient(to top, #764ba2, #667eea);
        transform: scaleY(1.05);
        transform-origin: bottom;
    }
    .monthly-bar .bar-label {
        font-size: 11px;
        font-weight: 600;
        color: #666;
        text-align: center;
    }
    .monthly-bar .bar-value {
        font-size: 10px;
        color: #999;
        text-align: center;
    }
</style>

{{-- Stats Header --}}
<div class="stats-header">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1><i class="fi-rr-chart-line me-3"></i>{{ get_phrase('Shop Statistics Dashboard') }}</h1>
            <p>{{ get_phrase('Comprehensive analytics and insights for your marketplace') }}</p>
        </div>
        <div class="text-end">
            <small class="opacity-75">{{ date('l, F j, Y') }}</small>
        </div>
    </div>
</div>

{{-- Main Stats Grid --}}
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card stat-purple h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">{{ get_phrase('Total Orders') }}</p>
                        <p class="stat-value">{{ $totalOrders }}</p>
                        <span class="stat-trend bg-white bg-opacity-25">
                            <i class="fi-rr-arrow-up"></i> {{ $monthOrders }} {{ get_phrase('this month') }}
                        </span>
                    </div>
                    <div class="icon-wrap">
                        <i class="fi-rr-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card stat-blue h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">{{ get_phrase('Total Revenue') }}</p>
                        <p class="stat-value">{{ currency($totalRevenue) }}</p>
                        <span class="stat-trend bg-white bg-opacity-25">
                            <i class="fi-rr-arrow-up"></i> {{ currency($monthRevenue) }} {{ get_phrase('this month') }}
                        </span>
                    </div>
                    <div class="icon-wrap">
                        <i class="fi-rr-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card stat-green h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">{{ get_phrase('Products') }}</p>
                        <p class="stat-value">{{ $totalProducts + $totalInventory }}</p>
                        <span class="stat-trend bg-white bg-opacity-25">
                            {{ $totalProducts }} {{ get_phrase('admin') }} + {{ $totalInventory }} {{ get_phrase('inventory') }}
                        </span>
                    </div>
                    <div class="icon-wrap">
                        <i class="fi-rr-box"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card stat-pink h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">{{ get_phrase('Reviews') }}</p>
                        <p class="stat-value">{{ $totalReviews }}</p>
                        <span class="stat-trend bg-white bg-opacity-25">
                            {{ $pendingReviews }} {{ get_phrase('pending') }}
                        </span>
                    </div>
                    <div class="icon-wrap">
                        <i class="fi-rr-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Period Stats --}}
    <div class="col-lg-6">
        <div class="card section-card h-100">
            <div class="card-header">
                <i class="fi-rr-calendar"></i>
                {{ get_phrase('Performance by Period') }}
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-4 bg-gradient-purple text-white rounded-4 text-center">
                            <div style="font-size: 36px; font-weight: 800;">{{ $todayOrders }}</div>
                            <small class="text-uppercase" style="opacity: 0.8;">{{ get_phrase('Today Orders') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-gradient-blue text-white rounded-4 text-center">
                            <div style="font-size: 36px; font-weight: 800;">{{ currency($todayRevenue) }}</div>
                            <small class="text-uppercase" style="opacity: 0.8;">{{ get_phrase('Today Revenue') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-gradient-success text-white rounded-4 text-center">
                            <div style="font-size: 36px; font-weight: 800;">{{ $weekOrders }}</div>
                            <small class="text-uppercase" style="opacity: 0.8;">{{ get_phrase('This Week Orders') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-gradient-warning text-dark rounded-4 text-center">
                            <div style="font-size: 36px; font-weight: 800;">{{ currency($weekRevenue) }}</div>
                            <small class="text-uppercase" style="opacity: 0.8;">{{ get_phrase('This Week Revenue') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Order Status --}}
    <div class="col-lg-6">
        <div class="card section-card h-100">
            <div class="card-header">
                <i class="fi-rr-chart-pie"></i>
                {{ get_phrase('Order Status Distribution') }}
            </div>
            <div class="card-body p-4">
                <div class="progress-card">
                    <div class="progress-label">
                        <span class="text-muted">{{ get_phrase('Pending') }}</span>
                        <span class="fw-bold">{{ $pendingOrders }} ({{ $totalOrders > 0 ? round(($pendingOrders / $totalOrders) * 100) : 0 }}%)</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: {{ $totalOrders > 0 ? ($pendingOrders / $totalOrders) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="progress-card">
                    <div class="progress-label">
                        <span class="text-muted">{{ get_phrase('Processing') }}</span>
                        <span class="fw-bold">{{ $processingOrders }} ({{ $totalOrders > 0 ? round(($processingOrders / $totalOrders) * 100) : 0 }}%)</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" style="width: {{ $totalOrders > 0 ? ($processingOrders / $totalOrders) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="progress-card">
                    <div class="progress-label">
                        <span class="text-muted">{{ get_phrase('Shipped') }}</span>
                        <span class="fw-bold">{{ $shippedOrders }} ({{ $totalOrders > 0 ? round(($shippedOrders / $totalOrders) * 100) : 0 }}%)</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width: {{ $totalOrders > 0 ? ($shippedOrders / $totalOrders) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="progress-card">
                    <div class="progress-label">
                        <span class="text-muted">{{ get_phrase('Delivered') }}</span>
                        <span class="fw-bold">{{ $deliveredOrders }} ({{ $totalOrders > 0 ? round(($deliveredOrders / $totalOrders) * 100) : 0 }}%)</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: {{ $totalOrders > 0 ? ($deliveredOrders / $totalOrders) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="progress-card mb-0">
                    <div class="progress-label">
                        <span class="text-muted">{{ get_phrase('Cancelled') }}</span>
                        <span class="fw-bold">{{ $cancelledOrders }} ({{ $totalOrders > 0 ? round(($cancelledOrders / $totalOrders) * 100) : 0 }}%)</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-danger" style="width: {{ $totalOrders > 0 ? ($cancelledOrders / $totalOrders) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Quick Actions --}}
    <div class="col-lg-3">
        <div class="card section-card h-100">
            <div class="card-header">
                <i class="fi-rr-bell"></i>
                {{ get_phrase('Pending Actions') }}
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="quick-action-card" style="border-color: #ffc107;">
                            <div class="action-icon bg-warning bg-opacity-10 text-warning">
                                <i class="fi-rr-clock"></i>
                            </div>
                            <div class="action-count text-warning">{{ $pendingApproval }}</div>
                            <div class="action-label">{{ get_phrase('Approval') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="quick-action-card" style="border-color: #0dcaf0;">
                            <div class="action-icon bg-info bg-opacity-10 text-info">
                                <i class="fi-rr-truck"></i>
                            </div>
                            <div class="action-count text-info">{{ $pendingDelivery }}</div>
                            <div class="action-label">{{ get_phrase('Delivery') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="quick-action-card" style="border-color: #dc3545;">
                            <div class="action-icon bg-danger bg-opacity-10 text-danger">
                                <i class="fi-rr-warning"></i>
                            </div>
                            <div class="action-count text-danger">{{ $outOfStockProducts + $outOfStockInventory }}</div>
                            <div class="action-label">{{ get_phrase('Out of Stock') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="quick-action-card" style="border-color: #6c757d;">
                            <div class="action-icon bg-secondary bg-opacity-10 text-secondary">
                                <i class="fi-rr-star"></i>
                            </div>
                            <div class="action-count text-secondary">{{ $pendingReviews }}</div>
                            <div class="action-label">{{ get_phrase('Reviews') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Shipping Methods --}}
    <div class="col-lg-5">
        <div class="card section-card h-100">
            <div class="card-header">
                <i class="fi-rr-truck"></i>
                {{ get_phrase('Shipping Methods') }}
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-4 bg-gradient-primary text-white rounded-4 text-center">
                            <i class="fi-rr-shop" style="font-size: 40px; margin-bottom: 10px;"></i>
                            <div style="font-size: 36px; font-weight: 800;">{{ $pickupOrders }}</div>
                            <small class="text-uppercase" style="opacity: 0.8;">{{ get_phrase('Store Pickup') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-gradient-success text-white rounded-4 text-center">
                            <i class="fi-rr-truck" style="font-size: 40px; margin-bottom: 10px;"></i>
                            <div style="font-size: 36px; font-weight: 800;">{{ $deliveryOrders }}</div>
                            <small class="text-uppercase" style="opacity: 0.8;">{{ get_phrase('Home Delivery') }}</small>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <div style="font-size: 24px; font-weight: 800; color: #666;">{{ $codOrders }}</div>
                            <small class="text-muted">{{ get_phrase('Cash on Delivery') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 text-center">
                            <div style="font-size: 24px; font-weight: 800; color: #666;">{{ $onlineOrders }}</div>
                            <small class="text-muted">{{ get_phrase('Online Payment') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Top Products --}}
    <div class="col-lg-4">
        <div class="card section-card h-100">
            <div class="card-header">
                <i class="fi-rr-trophy"></i>
                {{ get_phrase('Top Selling Products') }}
            </div>
            <div class="card-body p-0">
                @if($topProducts->count() > 0)
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ get_phrase('Product') }}</th>
                            <th class="text-center">{{ get_phrase('Sold') }}</th>
                            <th class="text-end">{{ get_phrase('Revenue') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $index => $product)
                        <tr>
                            <td>
                                @if($index == 0)
                                    <span class="badge bg-warning"><i class="fi-rr-crown"></i></span>
                                @else
                                    <span class="text-muted">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ Str::limit($product->product_name, 25) }}</td>
                            <td class="text-center"><span class="badge-modern bg-primary text-white">{{ $product->total_sold }}</span></td>
                            <td class="text-end fw-bold text-success">{{ currency($product->total_revenue) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fi-rr-inbox fs-1"></i>
                    <p class="mt-2">{{ get_phrase('No sales data yet') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Recent Orders --}}
    <div class="col-lg-6">
        <div class="card section-card">
            <div class="card-header">
                <i class="fi-rr-time-forward"></i>
                {{ get_phrase('Recent Orders') }}
            </div>
            <div class="card-body p-0">
                @if($recentOrders->count() > 0)
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>{{ get_phrase('Order') }}</th>
                            <th>{{ get_phrase('Customer') }}</th>
                            <th class="text-center">{{ get_phrase('Total') }}</th>
                            <th class="text-center">{{ get_phrase('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td>
                                <span class="text-muted">#</span><strong>{{ Str::limit($order->order_number, 8, '') }}</strong>
                                <br><small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                            </td>
                            <td class="fw-semibold">{{ Str::limit($order->customer_name, 20) }}</td>
                            <td class="text-center fw-bold">{{ currency($order->total) }}</td>
                            <td class="text-center">
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
                                <span class="badge-modern {{ $statusClass }}">{{ ucfirst($order->order_status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fi-rr-inbox fs-1"></i>
                    <p class="mt-2">{{ get_phrase('No orders yet') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Monthly Overview --}}
    <div class="col-lg-6">
        <div class="card section-card">
            <div class="card-header">
                <i class="fi-rr-calendar"></i>
                {{ get_phrase('Monthly Orders (Last 6 Months)') }}
            </div>
            <div class="card-body p-4">
                <div class="monthly-chart">
                    <div class="monthly-bar">
                        @php $maxOrders = max(array_column($monthlyOrders, 'orders')); @endphp
                        @foreach($monthlyOrders as $monthData)
                        <div class="bar-item">
                            <div class="bar-value">{{ $monthData['orders'] }}</div>
                            <div class="bar" style="height: {{ $maxOrders > 0 ? ($monthData['orders'] / $maxOrders) * 150 : 10 }}px;"></div>
                            <div class="bar-label">{{ explode(' ', $monthData['month'])[0] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>{{ get_phrase('Month') }}</th>
                                <th class="text-center">{{ get_phrase('Orders') }}</th>
                                <th class="text-end">{{ get_phrase('Revenue') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyOrders as $monthData)
                            <tr>
                                <td class="fw-semibold">{{ $monthData['month'] }}</td>
                                <td class="text-center"><span class="badge-modern bg-primary text-white">{{ $monthData['orders'] }}</span></td>
                                <td class="text-end fw-bold text-success">{{ currency($monthData['revenue']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
