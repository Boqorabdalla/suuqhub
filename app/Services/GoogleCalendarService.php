<?php

namespace App\Services;

use App\Models\ServiceBooking;
use App\Models\ServiceSelling;
use App\Models\ServiceEmployee;
use App\Models\User;
use Carbon\Carbon;

class GoogleCalendarService
{
    public static function generateICS($bookingId)
    {
        $booking = ServiceBooking::find($bookingId);
        
        if (!$booking) {
            return false;
        }
        
        $service = ServiceSelling::find($booking->service_selling_id);
        $employee = ServiceEmployee::find($booking->employee_id);
        $user = User::find($booking->user_id);
        
        $serviceName = $service ? $service->name : 'Service';
        $employeeName = $employee ? $employee->name : 'Service Provider';
        $customerName = $booking->name;
        
        $startDateTime = Carbon::parse($booking->service_date . ' ' . $booking->service_time);
        $endDateTime = $startDateTime->copy()->addMinutes($service->duration ?? 60);
        
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Atlas Business Directory//EN\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:booking-" . $booking->id . "@atlas.local\r\n";
        $ics .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $ics .= "DTSTART:" . $startDateTime->format('Ymd\THis') . "\r\n";
        $ics .= "DTEND:" . $endDateTime->format('Ymd\THis') . "\r\n";
        $ics .= "SUMMARY:" . self::escapeICS($serviceName) . " - " . self::escapeICS($employeeName) . "\r\n";
        $ics .= "DESCRIPTION:" . self::escapeICS("Customer: " . $customerName . "\\nPhone: " . $booking->phone . "\\nEmail: " . $booking->email . "\\n\\n" . $booking->notes) . "\r\n";
        $ics .= "LOCATION:" . self::escapeICS("Service Provider: " . $employeeName) . "\r\n";
        $ics .= "STATUS:CONFIRMED\r\n";
        $ics .= "BEGIN:VALARM\r\n";
        $ics .= "TRIGGER:-PT1H\r\n";
        $ics .= "ACTION:DISPLAY\r\n";
        $ics .= "DESCRIPTION:Reminder: " . $serviceName . " in 1 hour\r\n";
        $ics .= "END:VALARM\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";
        
        return $ics;
    }
    
    public static function generateAllBookingsICS($userId)
    {
        $bookings = ServiceBooking::where('listing_creator_id', $userId)
            ->whereIn('status', [0, 1])
            ->orderBy('service_date', 'asc')
            ->get();
            
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Atlas Business Directory//EN\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";
        
        foreach ($bookings as $booking) {
            $service = ServiceSelling::find($booking->service_selling_id);
            $employee = ServiceEmployee::find($booking->employee_id);
            
            if (!$service || !$employee) continue;
            
            $startDateTime = Carbon::parse($booking->service_date . ' ' . $booking->service_time);
            $endDateTime = $startDateTime->copy()->addMinutes($service->duration ?? 60);
            
            $status = $booking->status == 1 ? 'CONFIRMED' : 'TENTATIVE';
            
            $ics .= "BEGIN:VEVENT\r\n";
            $ics .= "UID:booking-" . $booking->id . "@atlas.local\r\n";
            $ics .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
            $ics .= "DTSTART:" . $startDateTime->format('Ymd\THis') . "\r\n";
            $ics .= "DTEND:" . $endDateTime->format('Ymd\THis') . "\r\n";
            $ics .= "SUMMARY:" . self::escapeICS($service->name) . " - " . self::escapeICS($booking->name) . "\r\n";
            $ics .= "DESCRIPTION:" . self::escapeICS("Customer: " . $booking->name . "\\nPhone: " . $booking->phone . "\\nService: " . $service->name . "\\nProvider: " . $employee->name) . "\r\n";
            $ics .= "STATUS:" . $status . "\r\n";
            $ics .= "END:VEVENT\r\n";
        }
        
        $ics .= "END:VCALENDAR\r\n";
        
        return $ics;
    }
    
    private static function escapeICS($text)
    {
        $text = str_replace("\\", "\\\\", $text);
        $text = str_replace(",", "\\,", $text);
        $text = str_replace(";", "\\;", $text);
        $text = str_replace("\n", "\\n", $text);
        $text = str_replace("\r", "", $text);
        return $text;
    }
    
    public static function downloadICS($bookingId)
    {
        $ics = self::generateICS($bookingId);
        
        if (!$ics) {
            return false;
        }
        
        $booking = ServiceBooking::find($bookingId);
        $filename = 'booking-' . $booking->id . '-' . date('Ymd') . '.ics';
        
        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    public static function downloadAllBookingsICS($userId)
    {
        $ics = self::generateAllBookingsICS($userId);
        $filename = 'all-bookings-' . date('Ymd') . '.ics';
        
        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
