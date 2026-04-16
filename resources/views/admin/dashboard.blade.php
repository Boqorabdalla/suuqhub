@extends('layouts.admin')
@section('title', get_phrase('Admin Dashboard'))
@section('admin_layout')
<script src="{{asset('assets/backend/js/Chart.js')}}"></script>
<style>
    #myChart{
        width: 100%;
        height: 600px;
    }
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 30px;
        color: white;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    .dashboard-header h1 {
        font-weight: 700;
        margin-bottom: 5px;
    }
    .dashboard-header p {
        opacity: 0.9;
        margin-bottom: 0;
    }
    .stat-card {
        border-radius: 16px;
        border: none;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
    }
    .stat-card .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .stat-card .stat-number {
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
    }
    .stat-card .stat-label {
        font-size: 13px;
        opacity: 0.8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-gradient-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .stat-gradient-blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    .stat-gradient-green {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }
    .stat-gradient-orange {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }
    .stat-gradient-red {
        background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%);
        color: white;
    }
    .stat-gradient-teal {
        background: linear-gradient(135deg, #0fd850 0%, #f9f047 100%);
        color: #333;
    }
    .stat-gradient-indigo {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        color: white;
    }
    .stat-gradient-cyan {
        background: linear-gradient(135deg, #00c6fb 0%, #005bea 100%);
        color: white;
    }
    .section-title {
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title i {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .shop-quick-stats {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border-radius: 16px;
        padding: 25px;
        color: white;
    }
    .shop-quick-stats h5 {
        font-weight: 700;
        margin-bottom: 20px;
    }
    .mini-stat {
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s;
    }
    .mini-stat:hover {
        background: rgba(255,255,255,0.3);
    }
    .mini-stat .number {
        font-size: 24px;
        font-weight: 700;
    }
    .mini-stat .label {
        font-size: 11px;
        opacity: 0.9;
        text-transform: uppercase;
    }
    .listing-card {
        border-radius: 12px;
        transition: all 0.3s;
    }
    .listing-card:hover {
        transform: scale(1.02);
    }
</style>

{{-- Dashboard Header --}}
<div class="dashboard-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1><i class="fi-rr-rocket me-2"></i> {{ get_phrase('Welcome Back, Admin!') }}</h1>
            <p>{{ get_phrase('Here\'s what\'s happening with your marketplace today') }}</p>
        </div>
        <div class="col-md-4 text-end">
            <small>{{ date('l, F j, Y') }}</small>
        </div>
    </div>
</div>

{{-- Main Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card stat-gradient-purple h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-1">{{ get_phrase('Total Users') }}</p>
                        <p class="stat-number mb-0">{{ count($users) }}</p>
                    </div>
                    <div class="stat-icon bg-white bg-opacity-25">
                        <i class="fi-rr-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card stat-gradient-blue h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-1">{{ get_phrase('Active Agents') }}</p>
                        <p class="stat-number mb-0">
                            @php $agent = App\Models\User::where('is_agent',1)->get(); @endphp
                            {{ count($agent) }}
                        </p>
                    </div>
                    <div class="stat-icon bg-white bg-opacity-25">
                        <i class="fi-rr-briefcase"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card stat-gradient-green h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-1">{{ get_phrase('Subscribers') }}</p>
                        <p class="stat-number mb-0">
                            @php $subscriber = App\Models\Newsletter_subscriber::get(); @endphp
                            {{ count($subscriber) }}
                        </p>
                    </div>
                    <div class="stat-icon bg-white bg-opacity-25">
                        <i class="fi-rr-envelope"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card stat-card stat-gradient-orange h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-1">{{ get_phrase('Subscriptions') }}</p>
                        <p class="stat-number mb-0">
                            @php $totalPaidAmount = App\Models\Subscription::sum('paid_amount'); @endphp
                            {{ currency($totalPaidAmount) }}
                        </p>
                    </div>
                    <div class="stat-icon bg-white bg-opacity-25">
                        <i class="fi-rr-credit-card"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Shop Quick Stats --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="shop-quick-stats">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fi-rr-shop me-2"></i> {{ get_phrase('Shop Overview') }}</h5>
                <a href="{{ route('admin.shop.statistics') }}" class="btn btn-sm btn-light">{{ get_phrase('View Details') }} <i class="fi-rr-arrow-right ms-1"></i></a>
            </div>
            <div class="row g-3">
                @php
                    $shopTotalOrders = App\Models\ShopOrder::count();
                    $shopPendingOrders = App\Models\ShopOrder::where('order_status', 'pending')->count();
                    $shopCompletedOrders = App\Models\ShopOrder::where('order_status', 'delivered')->count();
                    $shopTotalRevenue = App\Models\ShopOrder::where('payment_status', 'paid')->sum('total');
                    $shopProducts = App\Models\Product::where('is_published', 1)->count();
                    $shopInventory = App\Models\Inventory::where('availability', 1)->count();
                @endphp
                <div class="col-6 col-md-2">
                    <div class="mini-stat">
                        <div class="number">{{ $shopTotalOrders }}</div>
                        <div class="label">{{ get_phrase('Orders') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="mini-stat">
                        <div class="number">{{ $shopPendingOrders }}</div>
                        <div class="label">{{ get_phrase('Pending') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="mini-stat">
                        <div class="number">{{ $shopCompletedOrders }}</div>
                        <div class="label">{{ get_phrase('Completed') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="mini-stat">
                        <div class="number">{{ currency($shopTotalRevenue) }}</div>
                        <div class="label">{{ get_phrase('Revenue') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="mini-stat">
                        <div class="number">{{ $shopProducts }}</div>
                        <div class="label">{{ get_phrase('Products') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="mini-stat">
                        <div class="number">{{ $shopInventory }}</div>
                        <div class="label">{{ get_phrase('Inventory') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php 
 
    $beauty = App\Models\BeautyListing::where('visibility', 'visible')->count();
    $hotel = App\Models\HotelListing::where('visibility', 'visible')->count();
    $restaurant = App\Models\RestaurantListing::where('visibility', 'visible')->count();
    $realEstate = App\Models\RealEstateListing::where('visibility', 'visible')->count();
    $car = App\Models\CarListing::where('visibility', 'visible')->count();
    $totalStatic = $beauty + $hotel + $restaurant + $realEstate + $car;

    // Dynamic listings and all types
    $customListings = App\Models\CustomListings::where('visibility', 'visible')->get();
    $customTypes = App\Models\CustomType::where('status', 1)->orderBy('sorting', 'asc')->get();

    $totalDynamic = $customListings->count();
    $totalListing = $totalStatic + $totalDynamic;

    // Define static types and counts
    $staticCounts = [
        'beauty' => $beauty,
        'hotel' => $hotel,
        'restaurant' => $restaurant,
        'real-estate' => $realEstate,
        'car' => $car,
    ];

    $staticSlugs = array_keys($staticCounts);

   // Calculate percentages
    $beautyPercentage = $totalListing ? ($beauty / $totalListing) * 100 : 0;
    $hotelPercentage = $totalListing ? ($hotel / $totalListing) * 100 : 0;
    $restaurantPercentage = $totalListing ? ($restaurant / $totalListing) * 100 : 0;
    $realEstatePercentage = $totalListing ? ($realEstate / $totalListing) * 100 : 0;
    $carPercentage = $totalListing ? ($car / $totalListing) * 100 : 0;

@endphp 
<div class="row mb-3 ">
    

    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
        <div class="row gx-3 gy-2">
            {{-- Total Listing Card --}}
            <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                <div class="ol-card card-hover">
                    <div class="ol-card-body px-20px py-3">
                        <h5 class="sub-title fs-16px mb-2">{{ get_phrase('Total Listing') }}</h5>
                        <h3 class="title card-title-hover fs-18px">{{ $totalListing }}</h3>
                    </div>
                </div>
            </div>
            @foreach($customTypes as $type)
                @php
                    $typeSlug = $type->slug;
                    $typeName = ucfirst(str_replace('-', ' ', $typeSlug));

                    $count = in_array($typeSlug, $staticSlugs)
                        ? ($staticCounts[$typeSlug] ?? 0)
                        : $customListings->where('type', $typeSlug)->count();
                @endphp

                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                    <div class="ol-card card-hover">
                        <div class="ol-card-body px-20px py-3">
                            <h5 class="sub-title fs-16px mb-2">{{ get_phrase($typeName) }}</h5>
                            <h3 class="title card-title-hover fs-18px">{{ $count }}</h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @php
    $staticCounts = [
        'beauty' => $beauty,
        'hotel' => $hotel,
        'restaurant' => $restaurant,
        'real-estate' => $realEstate,
        'car' => $car,
    ];

    $staticSlugs = array_keys($staticCounts);

    $allTypes = \App\Models\CustomType::where('status', 1)->orderBy('sorting', 'asc')->get();
    $customListings = \App\Models\CustomListings::where('visibility', 'visible')->get();

    $totalListing = array_sum($staticCounts) + $customListings->count();

    $labels = [];
    $percentages = [];
    $colors = [];

    // Predefined colors for first 5 static types
    $presetColors = ["#FF736A", "#124797", "#EF255C", "#44A1ED", "#0F0B0B"];
    $dynamicColorPool = ['#7F27FF', '#35A29F', '#F86F03', '#A34343', '#4C4B16', '#7286D3', '#43766C', '#FD8D14', '#B3005E', '#A0C49D'];

    $colorIndex = 0;

    foreach ($allTypes as $index => $type) {
        $slug = $type->slug;
        $name = ucfirst(str_replace('-', ' ', $slug));

        $count = in_array($slug, $staticSlugs)
            ? ($staticCounts[$slug] ?? 0)
            : $customListings->where('type', $slug)->count();

        $percentage = $totalListing > 0 ? round(($count / $totalListing) * 100, 2) : 0;

        $labels[] = $name;
        $percentages[] = $percentage;

        // Use static color if available, otherwise pick from dynamic pool
        $colors[] = $presetColors[$index] ?? $dynamicColorPool[$colorIndex++ % count($dynamicColorPool)];
    }
@endphp


    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
        <div class="ol-card h-100">
            <div class="ol-card-body p-4">
                <div class="chart-sm-item d-flex g-14px align-items-end justify-content-between">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


@php
    $currentYear = date('Y');
    $monthlyEarnings = DB::table('subscriptions')
        ->selectRaw("MONTH(created_at) as month, SUM(paid_amount) as total_earning")
        ->whereYear('created_at', $currentYear)
        ->groupBy(DB::raw("MONTH(created_at)"))
        ->orderBy(DB::raw("MONTH(created_at)"))
        ->get();
    $monthlyData = [];
    for ($i = 1; $i <= 12; $i++) {
        $earningsForMonth = $monthlyEarnings->firstWhere('month', $i);
        $monthlyData[$i] = $earningsForMonth ? $earningsForMonth->total_earning : 0;
    }
@endphp
<div class="row">
    <div class="col-12">
        <div class="ol-card h-100">
            <div class="ol-card-body p-4">
                <canvas id="myCharts" class="w-100"></canvas>
            </div>
        </div>
    </div>
</div>
<script>
    "use strict";
    const xValues = {!! json_encode($labels) !!};
    const yValues = {!! json_encode($percentages) !!};
    const barColors = {!! json_encode($colors) !!};

    new Chart("myChart", {
        type: "pie",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            title: {
                display: true,
                text: "{{ get_phrase('Visible Listings as Percentages') }}"
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        let label = data.labels[tooltipItem.index] || '';
                        if (label) {
                            label += ': ';
                        }
                        label += data.datasets[0].data[tooltipItem.index].toFixed(2) + '%';
                        return label;
                    }
                }
            }
        }
    });
</script>

{{-- <script>
    "use strict";
    const xValues = ["Beauty", "Hotel", "Restaurant", "Real-Estate", "Car"];
    const yValues = [
        {{ $beautyPercentage }},
        {{ $hotelPercentage }},
        {{ $restaurantPercentage }},
        {{ $realEstatePercentage }},
        {{ $carPercentage }}
    ];
    const barColors = [
        "#FF736A",
        "#124797",
        "#EF255C",
        "#44A1ED",
        "#0F0B0B"
    ];

    new Chart("myChart", {
        type: "pie",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            title: {
                display: true,
                text: "{{get_phrase('Visible Listings as Percentages')}}"
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        let label = data.labels[tooltipItem.index] || '';
                        if (label) {
                            label += ': ';
                        }
                        label += data.datasets[0].data[tooltipItem.index].toFixed(2) + '%';
                        return label;
                    }
                }
            }
        }
    });
</script> --}}

<script>
    "use strict";
    const months = ["January", "February", "March", "April", "May", "June", 
                    "July", "August", "September", "October", "November", "December"];
    const earnings = {!! json_encode(array_values($monthlyData)) !!};
    const barColors2 = ["#FF5733", "#33FF57", "#3357FF", "#F39C12", "#8E44AD", 
                        "#E74C3C", "#1ABC9C", "#2ECC71", "#3498DB", "#9B59B6", "#34495E", "#16A085"];
    
    const currentYear = new Date().getFullYear(); 

    new Chart("myCharts", {
      type: "bar",
      data: {
        labels: months,
        datasets: [{
          label: "Earnings", 
          backgroundColor: barColors2, 
          data: earnings
        }]
      },
      options: {
        legend: {display: false},
        title: {
          display: true,
          text: "{{get_phrase('Monthly Earnings for')}} " + currentYear 
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
</script>


@endsection