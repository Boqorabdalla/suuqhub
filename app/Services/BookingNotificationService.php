<?php

namespace App\Services;

use App\Mail\BookingNotification;
use App\Models\ServiceBooking;
use App\Models\ServiceSelling;
use App\Models\ServiceEmployee;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class BookingNotificationService
{
    public static function sendBookingCreatedNotification($bookingId)
    {
        return self::sendNotification($bookingId, 'created');
    }

    public static function sendBookingApprovedNotification($bookingId)
    {
        return self::sendNotification($bookingId, 'approved');
    }

    public static function sendBookingCancelledNotification($bookingId)
    {
        return self::sendNotification($bookingId, 'cancelled');
    }

    public static function sendBookingReminderNotification($bookingId)
    {
        return self::sendNotification($bookingId, 'reminder');
    }

    private static function sendNotification($bookingId, $type)
    {
        try {
            $booking = ServiceBooking::find($bookingId);
            
            if (!$booking) {
                return false;
            }

            $user = User::find($booking->user_id);
            $service = ServiceSelling::find($booking->service_selling_id);
            $employee = ServiceEmployee::find($booking->employee_id);

            $userName = $user ? $user->name : $booking->name;
            $serviceName = $service ? $service->name : 'Service';
            $employeeName = $employee ? $employee->name : 'Service Provider';

            $recipientEmail = $booking->email ?? ($user ? $user->email : null);

            if ($recipientEmail) {
                Mail::to($recipientEmail)->send(new BookingNotification(
                    $booking,
                    $type,
                    $userName,
                    $serviceName,
                    $employeeName
                ));

                if ($employee) {
                    $agent = User::find($employee->user_id);
                    if ($agent && $agent->email) {
                        Mail::to($agent->email)->send(new BookingNotification(
                            $booking,
                            $type,
                            $userName,
                            $serviceName,
                            $employeeName
                        ));
                    }
                }

                return true;
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Booking Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}
