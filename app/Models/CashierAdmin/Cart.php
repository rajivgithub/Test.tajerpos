<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'customer_id', 'product_id', 'variants_id', 'quantity', 'is_deleted', 'deleted_at','admin_id'
    ];
    protected $primaryKey = 'cart_id';
    protected $table = 'instore_cart_data';
}
