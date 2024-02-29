<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'permission_name', 'description', 'created_by','updated_by','store_user_id'
    ];
    protected $primaryKey = 'permission_id';
    protected $table = 'store_user_permissions';
}
