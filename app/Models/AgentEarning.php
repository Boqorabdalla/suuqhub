<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentEarning extends Model
{
    protected $fillable = [
        'agent_id',
        'order_id',
        'type',
        'amount',
        'commission_rate',
        'commission_amount',
        'status',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'order_id');
    }

    public static function calculateCommission($order, $agentId)
    {
        $commissionRate = get_settings('default_commission_rate') ?? 10;
        $orderTotal = $order->total;
        $commissionAmount = ($orderTotal * $commissionRate) / 100;
        
        return self::create([
            'agent_id' => $agentId,
            'order_id' => $order->id,
            'type' => 'sale',
            'amount' => $orderTotal,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'status' => 'approved',
            'description' => 'Commission from order ' . $order->order_number,
        ]);
    }

    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
