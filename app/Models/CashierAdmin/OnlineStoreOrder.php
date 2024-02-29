<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineStoreOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'customer_id', 'sub_total_amount', 'total_amount', 'tax_amount', 'payment_id', 'order_number','no_of_products','paid_amount', 'online_order_status', 'created_by','updated_by','status','discount_id', 'discount_amount', 'is_deleted', 'deleted_at','address_id', 'shipping_order_id'
    ];
    protected $primaryKey = 'online_order_id';
    protected $table = 'online_store_order_details';
}
