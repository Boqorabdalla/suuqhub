<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .details h4 { margin-top: 0; color: #4CAF50; }
        .details p { margin: 8px 0; }
        .label { font-weight: bold; color: #555; }
        .status-pending { color: #FF9800; }
        .status-approved { color: #4CAF50; }
        .status-cancelled { color: #F44336; }
        .footer { text-align: center; padding: 20px; color: #777; font-size: 12px; }
        .btn { display: inline-block; background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ config('app.name') }}</h2>
            <h3>
                @if($type == 'created')
                    Booking Received
                @elseif($type == 'approved')
                    Booking Approved!
                @elseif($type == 'cancelled')
                    Booking Cancelled
                @elseif($type == 'reminder')
                    Booking Reminder
                @endif
            </h3>
        </div>
        
        <div class="content">
            <p>Dear {{ $userName }},</p>
            
            @if($type == 'created')
                <p>Thank you for your booking! Your booking has been received and is pending approval.</p>
            @elseif($type == 'approved')
                <p>Great news! Your booking has been approved.</p>
            @elseif($type == 'cancelled')
                <p>Your booking has been cancelled as requested.</p>
            @elseif($type == 'reminder')
                <p>This is a reminder about your upcoming booking.</p>
            @endif
            
            <div class="details">
                <h4>Booking Details</h4>
                <p><span class="label">Service:</span> {{ $serviceName }}</p>
                <p><span class="label">Service Provider:</span> {{ $employeeName }}</p>
                <p><span class="label">Date:</span> {{ \Carbon\Carbon::parse($booking->service_date)->format('l, F j, Y') }}</p>
                <p><span class="label">Time:</span> {{ $booking->service_time }}</p>
                @if($booking->notes)
                <p><span class="label">Notes:</span> {{ $booking->notes }}</p>
                @endif
            </div>
            
            @if($type == 'approved')
                <p><strong>Please arrive 10 minutes before your scheduled time.</strong></p>
            @endif
            
            @if($type == 'cancelled')
                <p>If you have any questions about this cancellation, please contact us.</p>
            @endif
        </div>
        
        <div class="footer">
            <p>{{ config('app.name') }} - Business Directory & Booking System</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>
