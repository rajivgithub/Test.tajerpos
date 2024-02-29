<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreOrderStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_name', 'order_number', 'status', 'is_deleted','store_id', 'created_by', 'updated_by'
    ];
    protected $primaryKey = 'status_id';
    protected $table = 'store_order_status';
}
