<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'role_id', 'permission_id', 'created_by','updated_by','store_user_id'
    ];
    protected $primaryKey = 'user_role_id';
    protected $table = 'store_user_role_permissions';
}
