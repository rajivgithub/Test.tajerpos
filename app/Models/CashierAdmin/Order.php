<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id', 'total_amount', 'payment_id', 'status', 'created_by', 'updated_by', 'store_id','is_deleted','deleted_at', 'no_of_products', 'tax_amount','sub_total_amount','order_type_id','paid_amount', 'store_order_status','total_discount_amount','coupon_code','coupon_discount'
    ];
    protected $primaryKey = 'order_id';
    protected $table = 'store_order_details';
}
