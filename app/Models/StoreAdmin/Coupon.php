<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'coupon_number', 'coupon_code', 'product_id', 'coupon_type', 'discount', 'discount_type', 'start_up_date', 'expiration_date','status','created_by','updated_by'
    ];
    protected $primaryKey = 'coupon_id';
    protected $table = 'store_coupons';
}
