<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $type;
    public $userName;
    public $serviceName;
    public $employeeName;

    public function __construct($booking, $type, $userName, $serviceName, $employeeName)
    {
        $this->booking = $booking;
        $this->type = $type;
        $this->userName = $userName;
        $this->serviceName = $serviceName;
        $this->employeeName = $employeeName;
    }

    public function build()
    {
        $subject = $this->getSubject();
        
        return $this->subject($subject)
                    ->view('emails.booking-notification');
    }

    private function getSubject()
    {
        switch($this->type) {
            case 'created':
                return 'Booking Confirmation - ' . $this->serviceName;
            case 'approved':
                return 'Booking Approved - ' . $this->serviceName;
            case 'cancelled':
                return 'Booking Cancelled - ' . $this->serviceName;
            case 'reminder':
                return 'Booking Reminder - ' . $this->serviceName;
            default:
                return 'Booking Update - ' . $this->serviceName;
        }
    }
}
