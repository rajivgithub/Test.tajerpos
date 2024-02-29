<?php

namespace App\Models\Customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'customer_id', 'customer_name', 'mobile_number', 'street_name', 'building_name', 'country_id', 'state_id', 'city_id', 'address_type', 'landmark', 'pincode'
    ];
    protected $primaryKey = 'address_id';
    protected $table = 'customer_address';
}
