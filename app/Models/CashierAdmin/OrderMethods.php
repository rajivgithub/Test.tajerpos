<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMethods extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_methods', 'order_number', 'status', 'is_deleted','store_id', 'created_by', 'updated_by', 'deleted_at'
    ];
    protected $primaryKey = 'order_methods_id';
    protected $table = 'store_order_methods';
}
