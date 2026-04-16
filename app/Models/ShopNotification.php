<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopNotification extends Model
{
    protected $table = 'shop_notifications';
    
    protected $fillable = [
        'user_id',
        'type',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead()
    {
        return $this->read_at !== null;
    }

    public static function sendTo($userId, $type, $data)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'data' => json_encode($data),
        ]);
    }
}
