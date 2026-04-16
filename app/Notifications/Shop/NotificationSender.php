<?php

namespace App\Notifications\Shop;

use App\Models\ShopNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationSender
{
    public static function send($userId, $type, $data)
    {
        ShopNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'data' => json_encode($data),
        ]);

        self::sendEmail($userId, $type, $data);
    }

    public static function sendToUser($user, $type, $data)
    {
        if (is_numeric($user)) {
            $userId = $user;
        } else {
            $userId = $user->id;
        }
        
        self::send($userId, $type, $data);
    }

    protected static function sendEmail($userId, $type, $data)
    {
        $user = User::find($userId);
        if (!$user || !$user->email) {
            return;
        }

        $orderNumber = $data['order_number'] ?? 'N/A';
        $subject = 'Order Update - ' . $data['title'];

        try {
            Mail::send([], [], function($message) use ($user, $subject, $data, $orderNumber) {
                $message->to($user->email, $user->name)
                    ->subject($subject)
                    ->html('
                        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                            <div style="background: #6C1CFF; color: white; padding: 20px; text-align: center;">
                                <h1 style="margin: 0;">Order Update</h1>
                            </div>
                            <div style="padding: 20px; background: #f9f9f9;">
                                <h2 style="color: #333;">' . ($data['title'] ?? 'Notification') . '</h2>
                                <p style="color: #666; font-size: 16px;">' . ($data['message'] ?? '') . '</p>
                                <div style="background: white; padding: 15px; border-radius: 8px; margin: 20px 0;">
                                    <p><strong>Order Number:</strong> ' . $orderNumber . '</p>
                                    <p><strong>Status:</strong> ' . ucfirst($data['status'] ?? 'N/A') . '</p>
                                </div>
                                <a href="' . url('/shop/track') . '?order=' . $orderNumber . '" style="display: inline-block; background: #6C1CFF; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">Track Order</a>
                            </div>
                            <div style="padding: 20px; text-align: center; color: #999; font-size: 12px;">
                                <p>Thank you for shopping with us!</p>
                            </div>
                        </div>
                    ');
            });
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }
    }

    public static function orderApproved($order, $approvedBy = 'System')
    {
        $data = [
            'title' => 'Order Approved',
            'message' => 'Your order ' . $order->order_number . ' has been approved.',
            'status' => 'approved',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ];

        self::sendToUser($order->user_id, 'order_approved', $data);
    }

    public static function orderRejected($order, $reason = '', $rejectedBy = 'System')
    {
        $data = [
            'title' => 'Order Cancelled',
            'message' => 'Your order ' . $order->order_number . ' has been cancelled.' . ($reason ? ' Reason: ' . $reason : ''),
            'status' => 'rejected',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'reason' => $reason,
        ];

        self::sendToUser($order->user_id, 'order_rejected', $data);
    }

    public static function orderShipped($order, $deliveryStatus = 'in_transit')
    {
        $statusText = $deliveryStatus == 'picked_up' ? 'picked up' : 'shipped';
        
        $data = [
            'title' => 'Order Shipped',
            'message' => 'Your order ' . $order->order_number . ' has been ' . $statusText . '!',
            'status' => 'shipped',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ];

        self::sendToUser($order->user_id, 'order_shipped', $data);
    }

    public static function orderDelivered($order)
    {
        $data = [
            'title' => 'Order Delivered',
            'message' => 'Your order ' . $order->order_number . ' has been delivered!',
            'status' => 'delivered',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ];

        self::sendToUser($order->user_id, 'order_delivered', $data);
    }
}
