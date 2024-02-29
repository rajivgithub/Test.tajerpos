<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'store_user_id', 'role_name', 'description', 'created_by','updated_by'
    ];
    protected $primaryKey = 'role_id';
    protected $table = 'store_user_roles';
}
