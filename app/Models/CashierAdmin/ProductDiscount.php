<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    use HasFactory;
    protected $fillable = [
        'discount_id','product_id', 'variant_id', 'status', 'store_id', 'created_by', 'updated_by', 'is_deleted', 'deleted_at'
    ];
    protected $primaryKey = 'product_discount_id';
    protected $table = 'store_product_discount';
}
