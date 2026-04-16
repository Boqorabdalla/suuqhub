<?php

namespace App\Notifications\Shop;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderDelivered extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = $this->order->order_number;
        
        return (new MailMessage)
            ->subject('Order Delivered - ' . $orderNumber)
            ->greeting('Hello ' . ($this->order->customer_name ?? 'Customer') . '!')
            ->line('Great news! Your order **' . $orderNumber . '** has been delivered!')
            ->line('**Order Details:**')
            ->line('- Order Number: ' . $orderNumber)
            ->line('- Total: $' . number_format($this->order->total, 2))
            ->line('- Shipping Method: ' . ucfirst($this->order->shipping_method))
            ->line('')
            ->line('We hope you enjoy your purchase!')
            ->line('Please take a moment to leave a review for the products you ordered.')
            ->action('Leave a Review', route('shop.orders'))
            ->line('')
            ->line('Thank you for shopping with us!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'shop_order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => 'Order Delivered',
            'message' => 'Your order ' . $this->order->order_number . ' has been delivered! Thank you for shopping with us.',
            'status' => 'delivered',
        ];
    }
}
