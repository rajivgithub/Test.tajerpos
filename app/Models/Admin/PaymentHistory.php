<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'payment_method', 'package_amount', 'paid_amount','tax_percentage', 'tax_amount', 'discount', 'discount_type', 'discount_amount', 'balance_amount', 'created_by', 'ip_address', 'total_amount', 'amount_payable','payment_id'
    ];
    protected $primaryKey = 'payment_history_id';
    protected $table = 'payment_history';
}
