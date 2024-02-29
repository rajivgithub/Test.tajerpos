<?php

namespace App\Models\Customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'customer_id', 'shipping_customer_name', 'shipping_mobile_number', 'shipping_email_address', 'shipping_street_name', 'shipping_building_name', 'shipping_country_id', 'shipping_state_id', 'shipping_city_id', 'shipping_pincode', 'shipping_landmark'
    ];
    protected $primaryKey = 'shipping_address_id';
    protected $table = 'shipping_addresses';
}
