<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPayout extends Model
{
    protected $fillable = [
        'agent_id',
        'amount',
        'payment_method',
        'transaction_id',
        'notes',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
