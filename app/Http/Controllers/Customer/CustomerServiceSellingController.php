<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use App\Models\ServiceSelling;
use App\Models\ServiceBooking;
use App\Models\ServiceEmployee;
use App\Models\FavoriteEmployee;
use App\Services\BookingNotificationService;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CustomerServiceSellingController extends Controller
{
     
      public function myservice(){
         $page_data['active'] = 'myservice';
         $page_data['myservice'] = ServiceBooking::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate (10);
          return view('user.customer.service_selling.myservice', $page_data);
       }  

     public function service_manager_delete($id){
        $delete = ServiceBooking::where('id',$id)->first();
        $delete->delete();
        Session::flash('success', get_phrase('Service Booking Request Delete Successfully.'));
        return redirect()->back();
    }
    
    public function cancel_booking(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);
        
        $booking = ServiceBooking::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->first();
            
        if (!$booking) {
            Session::flash('error', get_phrase('Booking not found.'));
            return redirect()->back();
        }
        
        if ($booking->status == 2) {
            Session::flash('error', get_phrase('This booking has already been cancelled.'));
            return redirect()->back();
        }
        
        $booking->cancellation_status = 'pending';
        $booking->cancellation_reason = $request->cancellation_reason;
        $booking->cancelled_at = Carbon::now();
        $booking->save();
        
        BookingNotificationService::sendBookingCancelledNotification($id);
        
        Session::flash('success', get_phrase('Your cancellation request has been submitted. You will receive an email confirmation once processed.'));
        return redirect()->back();
    }
    
    public function favorite_providers()
    {
        $page_data['active'] = 'favorite_providers';
        $page_data['favorites'] = FavoriteEmployee::where('user_id', auth()->user()->id)
            ->with('employee')
            ->get();
        return view('user.customer.service_selling.favorite_providers', $page_data);
    }
    
    public function remove_favorite($id)
    {
        $favorite = FavoriteEmployee::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->first();
            
        if ($favorite) {
            $favorite->delete();
            Session::flash('success', get_phrase('Provider removed from favorites.'));
        }
        
        return redirect()->back();
    }
}
