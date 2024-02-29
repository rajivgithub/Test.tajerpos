<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id', 'discount_valid_from', 'discount_valid_to', 'discount_value', 'discount_type', 'quantity', 'status', 'store_id', 'created_by', 'updated_by'
    ];
    protected $primaryKey = 'product_discount_id';
    protected $table = 'store_product_discount';
}
