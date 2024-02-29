<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_number','account_password','api_account', 'private_key', 'store_id','created_by', 'updated_by'
    ];
    protected $primaryKey = 'api_credential_id';
    protected $table = 'store_shipping_api_credentials';
}
