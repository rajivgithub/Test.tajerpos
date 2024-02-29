<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_name','discount_method','product_discount_type','discount_valid_from', 'discount_valid_to', 'discount_value', 'discount_type','status', 'store_id', 'created_by', 'updated_by', 'is_deleted', 'deleted_at', 'min_require_type', 'min_value', 'max_discount_uses','max_value', 'once_per_order', 'store_type'
    ];
    protected $primaryKey = 'discount_id';
    protected $table = 'store_discount';
}
