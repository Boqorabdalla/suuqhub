<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSelling extends Model
{
   protected $fillable = [
        'name',
        'price',
        'duration',
        'user_id',
        'description',
        'image',
        'status',
        'video',
        'type',
        'listing_id',
        'service_employee',
        'slot', 
    ];

    protected $casts = [
        'slot' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function listing()
    {
        return $this->belongsTo(BeautyListing::class, 'listing_id');
    }
}
