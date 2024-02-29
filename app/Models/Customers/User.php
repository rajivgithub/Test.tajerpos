<?php

namespace App\Models\Customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'customer_name', 'customer_phone_number', 'customer_email', 'password', 'email_verified_at', 'remember_token', 'status'
    ];
    protected $primaryKey = 'customer_id';
    protected $table = 'customer_users';
}
