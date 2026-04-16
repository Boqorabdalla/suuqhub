<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteEmployee extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'listing_id',
    ];

    public function employee()
    {
        return $this->belongsTo(ServiceEmployee::class, 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
