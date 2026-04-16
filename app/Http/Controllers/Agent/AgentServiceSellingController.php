<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\ServiceSelling;
use App\Models\ServiceEmployee;
use App\Models\ServiceBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class AgentServiceSellingController extends Controller
{
    
    public function service_create($type,  $listing_id=""){
        $page_data['type'] = $type;
        $page_data['listing_id'] = $listing_id;
        return view('user.agent.service_selling.create', $page_data);
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
                'slots.*.end_time' => 'required|date_format:H:i|after:slots.*.start_time',
                
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
            $data['user_id'] = Auth::user()->id;
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
            Session::flash('success', get_phrase('Service created successfully!'));
            return redirect()->back();
        }

        public function service_edit($type, $listing_id, $id){
            $page_data['type'] = $type;
            $page_data['listing_id'] = $listing_id;
            $page_data['serviceSelling'] =ServiceSelling::find($id); 
            return view('user.agent.service_selling.edit', $page_data);
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
            $data['status'] = $request->status;
            $data['video'] = $request->video;
            $data['service_employee'] = json_encode(($request->employee));
            $data['slot'] = is_array($request->slots) ? json_encode($request->slots) : $request->slots;
       
            $serviceUpdate->update($data);
            Session::flash('success', get_phrase('Service   Updated Successful!'));
            return redirect()->back();

        }

        public function service_delete($id){
                 $delete = ServiceSelling::where('id',$id)->first();
                 if (is_file('public/uploads/service_selling/' . $delete->image)) {
                    unlink('public/uploads/service_selling/' . $delete->image);
                 }
                 $delete->delete();
                 Session::flash('success', get_phrase('Service  Delete Successful!'));
                 return redirect()->back();
             }

             public function my_created_services(Request $request){
                $page_data['active'] = 'my_created_services';
                $userId = auth()->user()->id;
                
                $query = ServiceSelling::where('user_id', $userId);
                
                if ($request->listing_id) {
                    $query->where('listing_id', $request->listing_id);
                }
                
                if ($request->status !== null && $request->status !== '') {
                    $query->where('status', $request->status);
                }
                
                $page_data['services'] = $query->orderBy('created_at', 'desc')->paginate(20);
                $page_data['listings'] = \App\Models\BeautyListing::where('user_id', $userId)->where('visibility', 'visible')->get();
                
                return view('user.agent.service_selling.my_created_services', $page_data);
             }
             
             public function my_created_services_delete($id){
                 $delete = ServiceSelling::where('id', $id)->where('user_id', auth()->user()->id)->first();
                 if ($delete) {
                     if (is_file('public/uploads/service_selling/' . $delete->image)) {
                        unlink('public/uploads/service_selling/' . $delete->image);
                     }
                     $delete->delete();
                     Session::flash('success', get_phrase('Service deleted successfully.'));
                 }
                 return redirect()->back();
             }
             
             public function my_created_services_status(Request $request, $id){
                 $service = ServiceSelling::where('id', $id)->where('user_id', auth()->user()->id)->first();
                 if ($service) {
                     $service->status = $request->status;
                     $service->save();
                     Session::flash('success', get_phrase('Service status updated.'));
                 }
                 return redirect()->back();
             }

         public function service_manager(){
           $page_data['active'] = 'service_manager';
          $page_data['serviceManager'] = ServiceBooking::where('listing_creator_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate(10);
          return view('user.agent.service_selling.service_manager', $page_data);
       }  

          public function myservice(){
             $page_data['active'] = 'myservice';
              $page_data['myservice'] = ServiceBooking::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate(10);
              return view('user.agent.service_selling.myservice', $page_data);
        }  
       
       public function service_manager_delete($id){
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
        Session::flash('success', get_phrase('The service request has been approved successfully.'));
        return redirect()->back();
    }    



     public function employee_list(){
          $page_data['active'] = 'employee_list';
        $page_data['employeeList'] = ServiceEmployee::where('creator_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate(10);
        return view('user.agent.service_selling.employee_list', $page_data);
    }
    public function add_employee_form(){
        return view('user.agent.service_selling.add_employee_form');
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
        return view('user.agent.service_selling.employee_edit', $page_data);
     }

      public function employee_delete($id){
            $delete = ServiceEmployee::where('id',$id)->first();
            $delete->delete();
            Session::flash('success', get_phrase('Employee  Delete Successfully.'));
            return redirect()->back();
        }




            
            
  


}
