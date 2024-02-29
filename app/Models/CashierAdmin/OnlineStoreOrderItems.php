<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineStoreOrderItems extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'customer_id', 'order_id', 'product_id', 'variants_id', 'product_variants', 'quantity','sub_total','tax_amount', 'created_by', 'updated_by','status','is_deleted', 'deleted_at'
    ];
    protected $primaryKey = 'online_order_items_id';
    protected $table = 'online_store_order_items';
}
