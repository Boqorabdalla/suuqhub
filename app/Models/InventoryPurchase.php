<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InventoryPurchase extends Model
{
    protected $table = 'inventory_purchase';
    protected $fillable = ['listing_creator_id', 'delivery_status', 'order_id', 'product_id', 'quantity', 'price', 'status'];
}