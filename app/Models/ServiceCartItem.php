<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCartItem extends Model
{
    protected $fillable = [
        'user_id',
        'listing_id',
        'listing_type',
        'service_selling_id',
        'employee_id',
        'service_date',
        'service_day',
        'service_time',
        'price',
    ];

    public function service()
    {
        return $this->belongsTo(ServiceSelling::class, 'service_selling_id');
    }

    public function employee()
    {
        return $this->belongsTo(ServiceEmployee::class, 'employee_id');
    }
}
