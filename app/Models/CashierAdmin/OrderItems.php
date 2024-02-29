<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'order_id', 'product_id', 'quantity', 'created_by', 'updated_by','sub_total','status','is_deleted','deleted_at', 'product_variants', 'tax_amount', 'variants_id', 'discount_amount'
    ];
    protected $primaryKey = 'id';
    protected $table = 'store_order_items';
}
