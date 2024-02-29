<?php

namespace App\Models\Customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id', 'customer_id', 'is_deleted', 'deleted_at', 'store_id', 'variants_id'
    ];
    protected $primaryKey = 'wishlist_id';
    protected $table = 'wishlist';
}
