<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\ServiceSelling;
use App\Models\ServiceBooking;
use App\Models\ServiceEmployee;
use App\Services\BookingNotificationService;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminServiceSellingController extends Controller
{
     public function service_create($type,  $listing_id=""){
        $page_data['type'] = $type;
        $page_data['listing_id'] = $listing_id;
        return view('admin.service_selling.create', $page_data);
    }

        public function service_store(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'name'        => 'required|string|max:255',
                'price'       => 'required|numeric|min:0',
                'duration'    => 'required',
                'description' => 'required|string',
                'image'       => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'status'      => 'required|in:0,1',
                'slots.*.day' => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                'slots.*.start_time' => 'required|date_format:H:i',
                'slots.*.end_time' => 'required|date_format:H:i|after:slots.*.start_time'
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $data['name'] = $request->name;
            $data['price'] = $request->price;
            $data['duration'] = $request->duration;
            $data['description'] = $request->description;
            $data['type'] = $request->type;
            $data['status'] = $request->status;
            $data['video'] = $request->video;
            $data['listing_id'] = $request->listing_id;
            $listing = \App\Models\BeautyListing::find($request->listing_id);
            $data['user_id'] = $listing ? $listing->user_id : Auth::user()->id;
            $data['service_employee'] = json_encode(($request->employee));
            $data['slot'] = is_array($request->slots) ? json_encode($request->slots) : $request->slots;

            //  Image Upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/service_selling'), $imageName);
                $data['image'] = $imageName;
            }

            //  Save to Database
            ServiceSelling::create($data);
            Session::flash('success', get_phrase('Service created Successfully.'));
            return redirect()->back();
        }

        public function service_edit($type, $listing_id, $id){
            $page_data['type'] = $type;
            $page_data['listing_id'] = $listing_id;
            $page_data['serviceSelling'] =ServiceSelling::find($id); 
            return view('admin.service_selling.edit', $page_data);
        }

        public function service_update(Request $request, $id){
             $serviceUpdate =  ServiceSelling::where('id', $id);

             if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/service_selling'), $imageName);
                $data['image'] = $imageName;
                $service = ServiceSelling::where('id', $id)->first();
                if ($service && $service->image && is_file(public_path('uploads/service_selling/'.$service->image))) {
                     unlink(public_path('uploads/service_selling/'.$service->image));
                }
            }

            $data['name'] = $request->name;
            $data['price'] = $request->price;
            $data['duration'] = $request->duration;
            $data['description'] = $request->description;
            $data['video'] = $request->video;
            $data['status'] = $request->status;
            $data['service_employee'] = json_encode(($request->employee));
            $data['slot'] = is_array($request->slots) ? json_encode($request->slots) : $request->slots;
       
            $serviceUpdate->update($data);
            Session::flash('success', get_phrase('Service   Updated Successfully.'));
            return redirect()->back();

        }

        public function service_delete($id){
            $delete = ServiceSelling::where('id',$id)->first();
            if (is_file('public/uploads/service_selling/' . $delete->image)) {
                unlink('public/uploads/service_selling/' . $delete->image);
            }
            $delete->delete();
            Session::flash('success', get_phrase('Service  Delete Successfully.'));
            return redirect()->back();
        }

       public function service_manager(){
            $page_data['serviceManager'] = ServiceBooking::orderBy('created_at', 'desc')->get();
            return view('admin.service_selling.service_manager', $page_data);
         }  
         
         public function service_manager_calendar(){
             $page_data['bookings'] = ServiceBooking::whereIn('status', [0, 1])
                 ->orderBy('service_date', 'asc')
                 ->get();
             return view('admin.service_selling.service_calendar', $page_data);
         }
        
        public function service_stats(){
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            
            $page_data['totalBookings'] = ServiceBooking::count();
            $page_data['pendingBookings'] = ServiceBooking::where('status', 0)->count();
            $page_data['approvedBookings'] = ServiceBooking::where('status', 1)->count();
            $page_data['cancelledBookings'] = ServiceBooking::where('status', 2)->count();
            
            $monthlyBookings = ServiceBooking::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $page_data['monthlyBookings'] = $monthlyBookings;
            
            $monthlyRevenue = ServiceBooking::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('payment_status', 1)
                ->get();
            
            $totalRevenue = 0;
            foreach ($monthlyRevenue as $booking) {
                $service = ServiceSelling::find($booking->service_selling_id);
                if ($service) {
                    $totalRevenue += $service->price;
                }
            }
            $page_data['monthlyRevenue'] = $totalRevenue;
            
            $popularServices = ServiceBooking::select('service_selling_id')
                ->groupBy('service_selling_id')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(5)
                ->get();
            
            $page_data['popularServices'] = [];
            foreach ($popularServices as $item) {
                $service = ServiceSelling::find($item->service_selling_id);
                if ($service) {
                    $count = ServiceBooking::where('service_selling_id', $service->id)->count();
                    $page_data['popularServices'][] = [
                        'name' => $service->name,
                        'count' => $count,
                        'price' => $service->price
                    ];
                }
            }
            
            $hourlyBookings = ServiceBooking::selectRaw('HOUR(service_time) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();
            $page_data['hourlyBookings'] = $hourlyBookings;
            
            $dailyBookings = ServiceBooking::selectRaw('DAYNAME(service_date) as day, COUNT(*) as count')
                ->groupBy('day')
                ->orderByRaw('FIELD(day, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday")')
                ->get();
            $page_data['dailyBookings'] = $dailyBookings;
            
            $recentBookings = ServiceBooking::orderBy('created_at', 'desc')->limit(10)->get();
            $page_data['recentBookings'] = $recentBookings;
            
            return view('admin.service_selling.service_stats', $page_data);
        }

       public function myservice(){
            $page_data['myservice'] = ServiceBooking::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
            return view('admin.service_selling.myservice', $page_data);
        }  
       
       public function service_manager_delete($id){
            BookingNotificationService::sendBookingCancelledNotification($id);
            $delete = ServiceBooking::where('id',$id)->first();
            $delete->delete();
            Session::flash('success', get_phrase('Service Booking Request Delete Successfully.'));
            return redirect()->back();
        }

         public function service_manager_paid($id){
            $manager = ServiceBooking::find($id);

            if ($manager && $manager->status != 1) {
                Session::flash('error', get_phrase('Please approve the service status before marking it as paid.'));
                return redirect()->back();
            }

            $manager->payment_status = 1;
            $manager->updated_at = now();
            $manager->save();

            Session::flash('success', get_phrase('The service has been successfully marked as paid.'));
            return redirect()->back();
        }
       public function service_manager_Unpaid($id){
            $manager = ServiceBooking::find($id);

            if ($manager && $manager->status != 1) {
                Session::flash('error', get_phrase('Please approve the service status before marking it as Unpaid.'));
                return redirect()->back();
            }

            $manager->payment_status = 0;
            $manager->updated_at = now();
            $manager->save();

            Session::flash('success', get_phrase('The service has been successfully marked as Unpaid.'));
            return redirect()->back();
        }


      public function service_manager_approve($id){
        ServiceBooking::where('id', $id)->update(['status' => 1]);
        
        BookingNotificationService::sendBookingApprovedNotification($id);
        
        Session::flash('success', get_phrase('The service request has been approved successfully.'));
        return redirect()->back();
    }


    public function employee_list(){
        $page_data['employeeList'] = ServiceEmployee::where('creator_id', auth()->user()->id)->get();
        return view('admin.service_selling.employee_list', $page_data);
    }
    public function add_employee_form(){
        return view('admin.service_selling.add_employee_form');
    }

      public function employee_store(Request $request){
          $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|numeric',
        ]);
    
        $data = $request->all();
        $employee = new ServiceEmployee();
        $employee->creator_id = auth()->user()->id;
        $employee->name = $data['name'];
        $employee->email = $data['email'];
        $employee->phone = $data['phone'];
        $employee->save();
    
        Session::flash('success', 'Employee Create Successfully.');
        return redirect()->back();
    }

     public function employee_update(Request $request, $id){
        $employeeUpdate =  ServiceEmployee::where('id', $id);
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['phone'] = $request->phone;
        $employeeUpdate->update($data);
        Session::flash('success', get_phrase('Employee  Updated Successfully.'));
        return redirect()->back();
    }
      public function employee_edit($id){
        $page_data['employeeEdit'] = ServiceEmployee::find($id); 
        return view('admin.service_selling.employee_edit', $page_data);
     }

      public function employee_delete($id){
            $delete = ServiceEmployee::where('id',$id)->first();
            $delete->delete();
            Session::flash('success', get_phrase('Employee  Delete Successfully.'));
            return redirect()->back();
        }
        
        public function export_calendar($bookingId)
        {
            return GoogleCalendarService::downloadICS($bookingId);
        }
        
        public function export_all_calendars()
        {
            return GoogleCalendarService::downloadAllBookingsICS(auth()->user()->id);
        }

        public function all_services(Request $request)
        {
            $page_data['active'] = 'all_services';
            
            $query = ServiceSelling::with(['user', 'listing']);
            
            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }
            
            if ($request->listing_id) {
                $query->where('listing_id', $request->listing_id);
            }
            
            if ($request->status !== null && $request->status !== '') {
                $query->where('status', $request->status);
            }
            
            $page_data['services'] = $query->orderBy('created_at', 'desc')->paginate(20);
            $page_data['agents'] = \App\Models\User::where('is_agent', 1)->where('status', 'active')->get();
            $page_data['listings'] = \App\Models\BeautyListing::where('visibility', 'visible')->get();
            
            return view('admin.service_selling.all_services', $page_data);
        }

        public function all_services_delete($id)
        {
            $delete = ServiceSelling::where('id', $id)->first();
            if ($delete) {
                if (is_file('public/uploads/service_selling/' . $delete->image)) {
                    unlink('public/uploads/service_selling/' . $delete->image);
                }
                $delete->delete();
                Session::flash('success', get_phrase('Service deleted successfully.'));
            }
            return redirect()->back();
        }

        public function all_services_status(Request $request, $id)
        {
            $service = ServiceSelling::find($id);
            if ($service) {
                $service->status = $request->status;
                $service->save();
                Session::flash('success', get_phrase('Service status updated.'));
            }
            return redirect()->back();
        }



}
