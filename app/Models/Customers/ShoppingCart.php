<?php

namespace App\Models\Customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'customer_id', 'product_id', 'variants_id', 'quantity', 'is_deleted', 'deleted_at'
    ];
    protected $primaryKey = 'cart_id';
    protected $table = 'shopping_cart';
}
