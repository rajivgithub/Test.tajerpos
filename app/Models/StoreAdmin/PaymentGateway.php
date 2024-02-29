<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'payment_type', 'client_id', 'client_secret','webhook_key','sandbox_mode', 'status','created_by', 'updated_by'
    ];
    protected $primaryKey = 'payment_credential_id';
    protected $table = 'store_payment_credentials';
}
