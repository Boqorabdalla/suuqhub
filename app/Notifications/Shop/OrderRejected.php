<?php

namespace App\Notifications\Shop;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $reason;
    protected $rejectedBy;

    public function __construct($order, $reason = '', $rejectedBy = 'Order Manager')
    {
        $this->order = $order;
        $this->reason = $reason;
        $this->rejectedBy = $rejectedBy;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = $this->order->order_number;
        
        $mail = (new MailMessage)
            ->subject('Order Cancelled - ' . $orderNumber)
            ->greeting('Hello ' . ($this->order->customer_name ?? 'Customer') . '!')
            ->line('Unfortunately, your order **' . $orderNumber . '** has been cancelled.')
            ->line('**Order Details:**')
            ->line('- Order Number: ' . $orderNumber)
            ->line('- Total: $' . number_format($this->order->total, 2));

        if (!empty($this->reason)) {
            $mail->line('')
                 ->line('**Reason:** ' . $this->reason);
        }

        $mail->line('')
             ->line('If you have any questions, please contact our support team.')
             ->line('We hope to serve you again soon!');

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'shop_order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => 'Order Cancelled',
            'message' => 'Your order ' . $this->order->order_number . ' has been cancelled.' . ($this->reason ? ' Reason: ' . $this->reason : ''),
            'status' => 'rejected',
        ];
    }
}
