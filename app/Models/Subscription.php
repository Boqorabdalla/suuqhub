<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'paid_amount',
        'payment_method',
        'transaction_keys',
        'auto_subscription',
        'status',
        'expire_date',
        'date_added',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Pricing::class, 'package_id');
    }
}
