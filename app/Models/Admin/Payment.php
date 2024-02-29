<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'package_amount', 'paid_amount','tax_percentage', 'tax_amount', 'discount', 'discount_type', 'discount_amount', 'balance_amount', 'created_by', 'ip_address', 'total_amount', 'amount_payable', 'invoice_number'
    ];
    protected $primaryKey = 'payment_id';
    protected $table = 'payment';
}
