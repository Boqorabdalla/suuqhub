<?php

namespace App\Console\Commands;

use App\Models\ServiceBooking;
use App\Services\BookingNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send reminders for upcoming bookings';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $nextWeek = Carbon::now()->addDays(7)->format('Y-m-d');

        $upcomingBookings = ServiceBooking::whereIn('status', [1])
            ->whereBetween('service_date', [$tomorrow, $nextWeek])
            ->get();

        $count = 0;
        foreach ($upcomingBookings as $booking) {
            $bookingDate = Carbon::parse($booking->service_date);
            
            $hoursUntilBooking = Carbon::now()->diffInHours($bookingDate);
            
            if ($hoursUntilBooking <= 24) {
                BookingNotificationService::sendBookingReminderNotification($booking->id);
                $count++;
            }
        }

        $this->info("Sent {$count} booking reminders.");
    }
}
