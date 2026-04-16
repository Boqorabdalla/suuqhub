<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopOrder extends Model
{
    protected $table = 'shop_orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'seller_id',
        'listing_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_method',
        'shipping_cost',
        'delivery_price',
        'delivery_status',
        'approval_status',
        'rejection_reason',
        'subtotal',
        'total',
        'payment_method',
        'payment_status',
        'order_status',
        'notes',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'delivery_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(BeautyListing::class, 'listing_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class, 'order_id');
    }

    public static function generateOrderNumber()
    {
        return 'ORD-' . strtoupper(uniqid()) . '-' . date('Ymd');
    }

    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeForDelivery($query)
    {
        return $query->where('shipping_method', 'delivery');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopePendingDelivery($query)
    {
        return $query->where('delivery_status', 'pending');
    }

    public function getApprovalStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
        return $labels[$this->approval_status] ?? 'Unknown';
    }

    public function getDeliveryStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending Delivery',
            'picked_up' => 'Picked Up',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'failed' => 'Delivery Failed',
        ];
        return $labels[$this->delivery_status] ?? 'Unknown';
    }

    public function getDeliveryStatusBadgeClassAttribute()
    {
        $classes = [
            'pending' => 'bg-warning text-dark',
            'picked_up' => 'bg-info',
            'in_transit' => 'bg-primary',
            'delivered' => 'bg-success',
            'failed' => 'bg-danger',
        ];
        return $classes[$this->delivery_status] ?? 'bg-secondary';
    }

    public function getApprovalBadgeClassAttribute()
    {
        $classes = [
            'pending' => 'bg-warning text-dark',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
        ];
        return $classes[$this->approval_status] ?? 'bg-secondary';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
        return $labels[$this->order_status] ?? 'Unknown';
    }

    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
        ];
        return $labels[$this->payment_status] ?? 'Unknown';
    }
}
