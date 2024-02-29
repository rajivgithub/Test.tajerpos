<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'store_id', 'store_user_name', 'store_user_phone_number', 'store_user_email', 'created_by','updated_by', 'password', 'role_id'
    ];
    protected $primaryKey = 'store_user_id';
    protected $table = 'store_users';
}
