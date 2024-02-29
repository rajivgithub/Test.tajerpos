<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'order_id', 'payment_id', 'payment_method', 'amount', 'status', 'is_deleted','created_by','updated_by'
    ];
    protected $primaryKey = 'payment_id';
    protected $table = 'payment_details';
}
