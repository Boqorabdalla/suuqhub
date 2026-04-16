<?php

namespace App\Notifications\Shop;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShipped extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $deliveryStatus;

    public function __construct($order, $deliveryStatus = 'in_transit')
    {
        $this->order = $order;
        $this->deliveryStatus = $deliveryStatus;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $orderNumber = $this->order->order_number;
        $statusText = $this->deliveryStatus == 'picked_up' ? 'picked up' : 'shipped';
        
        return (new MailMessage)
            ->subject('Order ' . ucwords($statusText) . ' - ' . $orderNumber)
            ->greeting('Hello ' . ($this->order->customer_name ?? 'Customer') . '!')
            ->line('Your order **' . $orderNumber . '** has been ' . $statusText . '!')
            ->line('**Order Details:**')
            ->line('- Order Number: ' . $orderNumber)
            ->line('- Total: $' . number_format($this->order->total, 2))
            ->line('- Shipping Method: ' . ucfirst($this->order->shipping_method))
            ->line('');

        if ($this->order->shipping_method == 'delivery') {
            $mail = (new MailMessage)
                ->line('Your order is on its way to your delivery address.')
                ->line('**Delivery Address:**')
                ->line($this->order->shipping_address ?? 'N/A')
                ->line(($this->order->shipping_city ?? '') . ($this->order->shipping_postal_code ? ', ' . $this->order->shipping_postal_code : ''));
        } else {
            $mail = (new MailMessage)
                ->line('Your order is ready for pickup at our location.');
        }

        return $mail
            ->action('Track Your Order', route('shop.track') . '?order=' . $orderNumber)
            ->line('Thank you for your patience!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'shop_order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => 'Order Shipped',
            'message' => 'Your order ' . $this->order->order_number . ' has been shipped and is on its way!',
            'status' => 'shipped',
        ];
    }
}
