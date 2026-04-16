<?php

  
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;

use App\Models\ServiceSelling;
use App\Models\ServiceBooking;
use App\Models\ServiceCartItem;
use App\Models\ServiceEmployee;
use App\Models\FavoriteEmployee;
use App\Services\BookingNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class FrontendServiceController extends Controller
{
    public function service_slot($type,$listing_id, $id){
       $page_data['id'] = $id;
       $page_data['type'] = $type; 
       $page_data['listing_id'] = $listing_id; 
       
       if (auth()->check()) {
           $page_data['cartItems'] = ServiceCartItem::where('user_id', auth()->user()->id)
               ->where('listing_id', $listing_id)
               ->get();
           $page_data['cartTotal'] = $page_data['cartItems']->sum('price');
       }
       
       return view('frontend.service_selling.service_slot', $page_data);
    }

public function fetchSlots(Request $request, $id, $listing_id)
{
    $selectedDateInput = $request->input('date');
    $employeeId = $request->input('employee_id');

    $referenceDate = $selectedDateInput ? Carbon::parse($selectedDateInput) : Carbon::today();

    $singleServiceItem = ServiceSelling::findOrFail($id);
    $slotsRaw = $singleServiceItem->slot ?? null;
    $slots = [];

    if ($slotsRaw) {
        $decodeAttempts = 0;
        $decoded = $slotsRaw;

        while ($decodeAttempts < 3 && !is_array($decoded)) {
            $decoded = json_decode($decoded, true);
            $decodeAttempts++;
        }

        $slots = is_array($decoded) ? $decoded : [];
    }

$durationInMinutes = (int) ($singleServiceItem->duration ?? 0);

    $dayShortMap = [
        'Sunday' => 'Sun',
        'Monday' => 'Mon',
        'Tuesday' => 'Tue',
        'Wednesday' => 'Wed',
        'Thursday' => 'Thu',
        'Friday' => 'Fri',
        'Saturday' => 'Sat',
    ];
    $start = $referenceDate->copy();
    $end = $referenceDate->copy()->endOfMonth();

    $monthlySlots = collect();

    foreach ($slots as $slot) {
        $slotDay = $slot['day'] ?? '';
        if (!in_array($slotDay, array_keys($dayShortMap))) {
            continue;
        }

        $current = $start->copy();
        while ($current->lte($end)) {
            if ($current->dayName === $slotDay) {
                $slotWithDate = $slot;
                $slotWithDate['next_date'] = $current->format('Y-m-d');
                $monthlySlots->push($slotWithDate);
            }
            $current->addDay();
        }
    }

    $slots = $monthlySlots->sortBy('next_date')->values();

    $filterDays = $request->input('days', []);
    if (!empty($filterDays)) {
        $slots = $slots->filter(function ($slot) use ($filterDays, $dayShortMap) {
            $slotShortDay = $dayShortMap[$slot['day']] ?? '';
            return in_array($slotShortDay, $filterDays);
        })->values();
    }
    
    $bookedSlots = [];
    if ($employeeId) {
        $bookedBookings = ServiceBooking::where('employee_id', $employeeId)
            ->whereIn('status', [0, 1])
            ->where('service_date', '>=', $referenceDate->format('Y-m-d'))
            ->where('service_date', '<=', $end->format('Y-m-d'))
            ->get();
            
        foreach ($bookedBookings as $booking) {
            $key = $booking->service_date . '_' . $booking->service_time;
            $bookedSlots[$key] = true;
        }
    }
    
    $serviceInfo = [
        'id' => $singleServiceItem->id,
        'name' => $singleServiceItem->name,
        'price' => $singleServiceItem->price,
    ];

    $html = view('frontend.service_selling.slots', [
        'slots' => $slots,
        'durationInMinutes' => $durationInMinutes,
        'dayShortMap' => $dayShortMap,
        'singleServiceItem' => $singleServiceItem,
        'listing_id' => $listing_id,
        'employeeId' => $employeeId,
        'bookedSlots' => $bookedSlots,
        'serviceInfo' => $serviceInfo,
    ])->render();

    return response()->json(['html' => $html]);
}


    public function service_booking_form($type, $id, $day,$date,$slot_time, $listing_id,$employeeid){
       $page_data['id'] = $id;
       $page_data['type'] = $type; 
       $page_data['day'] = $day; 
       $page_data['slot_date'] = $date; 
       $page_data['slot_time'] = $slot_time; 
       $page_data['listing_id'] = $listing_id; 
       $page_data['employeeid'] = $employeeid; 
       return view('frontend.service_selling.service_booking_form', $page_data);
    }
    
    public function add_to_cart(Request $request)
    {
        $request->validate([
            'service_id' => 'required',
            'listing_id' => 'required',
            'employee_id' => 'required',
            'service_date' => 'required',
            'service_day' => 'required',
            'service_time' => 'required',
        ]);
        
        $service = ServiceSelling::find($request->service_id);
        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service not found']);
        }
        
        $existingBooking = ServiceBooking::where('employee_id', $request->employee_id)
            ->where('service_date', $request->service_date)
            ->where('service_time', $request->service_time)
            ->whereIn('status', [0, 1])
            ->first();
            
        if ($existingBooking) {
            $employee = ServiceEmployee::find($request->employee_id);
            return response()->json([
                'success' => false, 
                'message' => $employee->name . ' is already booked at ' . $request->service_time . '. Please choose another time or provider.'
            ]);
        }
        
        if (auth()->check()) {
            $existingCartItem = ServiceCartItem::where('user_id', auth()->user()->id)
                ->where('service_selling_id', $request->service_id)
                ->first();
                
            if ($existingCartItem) {
                return response()->json([
                    'success' => false, 
                    'message' => 'This service is already in your cart'
                ]);
            }
            
            $timeSlotCartItem = ServiceCartItem::where('user_id', auth()->user()->id)
                ->where('service_date', $request->service_date)
                ->where('service_time', $request->service_time)
                ->first();
                
            if ($timeSlotCartItem) {
                $existingService = ServiceSelling::find($timeSlotCartItem->service_selling_id);
                $existingEmployee = ServiceEmployee::find($timeSlotCartItem->employee_id);
                return response()->json([
                    'success' => false, 
                    'message' => 'You already have ' . ($existingService->name ?? 'another service') . ' at ' . $request->service_time . ' with ' . ($existingEmployee->name ?? 'another provider') . '. Please choose a different time slot.'
                ]);
            }
            
            ServiceCartItem::create([
                'user_id' => auth()->user()->id,
                'listing_id' => $request->listing_id,
                'listing_type' => $request->type,
                'service_selling_id' => $request->service_id,
                'employee_id' => $request->employee_id,
                'service_date' => $request->service_date,
                'service_day' => $request->service_day,
                'service_time' => $request->service_time,
                'price' => $service->price,
            ]);
            
            $cartCount = ServiceCartItem::where('user_id', auth()->user()->id)->count();
            return response()->json([
                'success' => true, 
                'message' => 'Service added to cart', 
                'cart_count' => $cartCount
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Please login to add to cart']);
    }
    
    public function view_cart()
    {
        if (!auth()->check()) {
            Session::flash('error', 'Please login first');
            return redirect()->route('login');
        }
        
        $cartItems = ServiceCartItem::where('user_id', auth()->user()->id)
            ->with(['service', 'employee'])
            ->get();
            
        $page_data['cartItems'] = $cartItems;
        $page_data['cartTotal'] = $cartItems->sum('price');
        
        return view('frontend.service_selling.cart', $page_data);
    }
    
    public function remove_from_cart($id)
    {
        $item = ServiceCartItem::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->first();
            
        if ($item) {
            $item->delete();
            return redirect()->back();
        }
        
        return redirect()->back()->with('error', 'Item not found');
    }
    
    public function checkout(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'notes' => 'required|string',
        ]);
        
        $cartItems = ServiceCartItem::where('user_id', auth()->user()->id)
            ->with(['service', 'employee'])
            ->get();
            
        if ($cartItems->isEmpty()) {
            Session::flash('error', 'Your cart is empty');
            return redirect()->back();
        }
        
        $conflicts = [];
        $timeSlots = [];
        
        foreach ($cartItems as $item) {
            $existingBooking = ServiceBooking::where('employee_id', $item->employee_id)
                ->where('service_date', $item->service_date)
                ->where('service_time', $item->service_time)
                ->whereIn('status', [0, 1])
                ->first();
                
            if ($existingBooking) {
                $conflicts[] = $item->service->name . ' at ' . $item->service_time . ' (provider already booked)';
                continue;
            }
            
            $slotKey = $item->service_date . '_' . $item->service_time;
            if (isset($timeSlots[$slotKey])) {
                $conflicts[] = 'You have ' . $item->service->name . ' at ' . $item->service_time . ' (duplicate time slot)';
                continue;
            }
            $timeSlots[$slotKey] = $item->service->name;
        }
        
        if (!empty($conflicts)) {
            Session::flash('error', 'Some slots have conflicts: ' . implode(', ', $conflicts) . '. Please remove conflicting items and try again.');
            return redirect()->back();
        }
        
        $data = $request->all();
        $bookingIds = [];
        $agentsNotified = [];
        
        foreach ($cartItems as $item) {
            $bookings = new ServiceBooking();
            $bookings->user_id = auth()->user()->id;
            $bookings->employee_id = $item->employee_id;
            $bookings->listing_creator_id = $item->service->user_id;
            $bookings->listing_id = $item->listing_id;
            $bookings->service_selling_id = $item->service_selling_id;
            $bookings->type = $item->listing_type;
            $bookings->service_day = $item->service_day;
            $bookings->service_time = $item->service_time;
            $bookings->service_date = $item->service_date;
            $bookings->name = $data['name'];
            $bookings->email = $data['email'];
            $bookings->phone = $data['phone'];
            $bookings->notes = $data['notes'];
            $bookings->status = 0;
            $bookings->payment_status = 0;
            $bookings->save();
            
            BookingNotificationService::sendBookingCreatedNotification($bookings->id);
            $bookingIds[] = $bookings->id;
        }
        
        ServiceCartItem::where('user_id', auth()->user()->id)->delete();
        
        $uniqueAgents = $cartItems->pluck('service.user_id')->unique()->count();
        Session::flash('success', 'Your service bookings have been submitted successfully! (' . count($bookingIds) . ' services from ' . $uniqueAgents . ' agent(s))');
        
        if(user('role') == 1){
            return redirect()->route('admin.service.myservice');
        }else{
            return redirect()-> route('customer.myservice');
        }
    }

    public function service_booking_store(Request $request){
          $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'notes' => 'required|string',
        ]);
    
        $data = $request->all();
        
        $existingBooking = ServiceBooking::where('employee_id', $data['employeeid'])
            ->where('service_date', $data['service_date'])
            ->where('service_time', $data['service_time'])
            ->whereIn('status', [0, 1])
            ->first();
            
        if ($existingBooking) {
            Session::flash('error', 'This service provider is already booked at this time. Please choose another time or service provider.');
            return redirect()->back()->withInput();
        }
        
        $bookings = new ServiceBooking();
        $bookings->user_id = auth()->user()->id;
        $bookings->employee_id = $data['employeeid'];
        $bookings->listing_creator_id = $data['listing_creator_id'];
        $bookings->listing_id = $data['listing_id'];
        $bookings->service_selling_id = $data['service_selling_id'];
        $bookings->type = $data['type'];
        $bookings->service_day = $data['service_day'];
        $bookings->service_time = $data['service_time'];
        $bookings->service_date = $data['service_date'];
        $bookings->name = $data['name'];
        $bookings->email = $data['email'];
        $bookings->phone = $data['phone'];
        $bookings->notes = $data['notes'];
        $bookings->status = 0;
        $bookings->payment_status = 0;
        $bookings->save();
        
        BookingNotificationService::sendBookingCreatedNotification($bookings->id);
     
        Session::flash('success', 'Your service booking has been submitted successfully. Waiting for approval.');
        if(user('role') == 1){
            return redirect()->route('admin.service.myservice');
        }else{
            return redirect()-> route('customer.myservice');
        }

    }
    
    public function toggle_favorite_employee(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Please login first']);
        }
        
        $employeeId = $request->employee_id;
        $listingId = $request->listing_id ?? null;
        
        $existing = FavoriteEmployee::where('user_id', auth()->user()->id)
            ->where('employee_id', $employeeId)
            ->first();
            
        if ($existing) {
            $existing->delete();
            return response()->json([
                'success' => true, 
                'message' => 'Provider removed from favorites',
                'is_favorite' => false
            ]);
        }
        
        FavoriteEmployee::create([
            'user_id' => auth()->user()->id,
            'employee_id' => $employeeId,
            'listing_id' => $listingId,
        ]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Provider added to favorites',
            'is_favorite' => true
        ]);
    }
    
    public function get_favorite_employees()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'favorites' => []]);
        }
        
        $favorites = FavoriteEmployee::where('user_id', auth()->user()->id)
            ->with('employee')
            ->get();
            
        return response()->json([
            'success' => true, 
            'favorites' => $favorites
        ]);
    }
    
    public function get_similar_services($serviceId, $listingId)
    {
        $service = ServiceSelling::find($serviceId);
        
        if (!$service) {
            return response()->json(['success' => false, 'services' => []]);
        }
        
        $similarServices = ServiceSelling::where('listing_id', $listingId)
            ->where('id', '!=', $serviceId)
            ->where('status', 1)
            ->limit(6)
            ->get();
            
        return response()->json([
            'success' => true, 
            'services' => $similarServices
        ]);
    }
}
