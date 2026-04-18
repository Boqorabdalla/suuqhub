<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'listing_id',
        'listing_type',
        'employee_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'total_amount',
        'notes',
        'is_cancelled',
        'cancellation_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(ServiceSelling::class, 'service_id');
    }

    public function listing()
    {
        return $this->belongsTo(BeautyListing::class, 'listing_id');
    }

    public function employee()
    {
        return $this->belongsTo(ServiceEmployee::class, 'employee_id');
    }
}