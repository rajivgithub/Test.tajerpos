<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlinePayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id','store_id', 'order_id', 'online_payment_id', 'payment_method', 'amount', 'status', 'is_deleted','created_by','updated_by', 'deleted_at'
    ];
    protected $primaryKey = 'online_payment_id';
    protected $table = 'online_payment_details';
}
