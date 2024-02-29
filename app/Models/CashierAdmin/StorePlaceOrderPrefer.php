<?php

namespace App\Models\CashierAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorePlaceOrderPrefer extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_name', 'order_number', 'status', 'is_deleted','store_id', 'created_by', 'updated_by'
    ];
    protected $primaryKey = 'prefer_order_id';
    protected $table = 'store_place_order_prefer';
}
