<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'store_user_name', 'store_validity_date', 'store_name', 'store_phone_number','store_address', 'store_country', 'store_city', 'store_postal_code', 'store_logo', 'created_by', 'updated_by', 'store_url', 'store_state','is_store'
    ];
    protected $primaryKey = 'store_id';
    protected $table = 'stores';
}
