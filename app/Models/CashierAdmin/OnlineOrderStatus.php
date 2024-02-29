<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineOrderStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_name', 'order_number', 'status', 'is_deleted','store_id', 'created_by', 'updated_by'
    ];
    protected $primaryKey = 'order_status_id';
    protected $table = 'online_order_status';
}
