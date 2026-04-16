<?php

namespace App\Notifications\Shop;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $approvedBy;

    public function __construct($order, $approvedBy = 'Order Manager')
    {
        $this->order = $order;
        $this->approvedBy = $approvedBy;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = $this->order->order_number;
        
        return (new MailMessage)
            ->subject('Order Approved - ' . $orderNumber)
            ->greeting('Hello ' . ($this->order->customer_name ?? 'Customer') . '!')
            ->line('Great news! Your order **' . $orderNumber . '** has been approved.')
            ->line('**Order Details:**')
            ->line('- Order Number: ' . $orderNumber)
            ->line('- Total: $' . number_format($this->order->total, 2))
            ->line('- Shipping Method: ' . ucfirst($this->order->shipping_method))
            ->line('')
            ->line('Your order is now being processed and will be prepared for ' . ($this->order->shipping_method == 'delivery' ? 'delivery' : 'pickup') . '.')
            ->action('Track Your Order', route('shop.track') . '?order=' . $orderNumber)
            ->line('Thank you for shopping with us!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'shop_order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => 'Order Approved',
            'message' => 'Your order ' . $this->order->order_number . ' has been approved and is being processed.',
            'status' => 'approved',
        ];
    }
}
