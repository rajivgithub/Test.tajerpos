<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulesPermission extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'roles_id', 'modules_id', 'add', 'view', 'edit', 'delete', 'created_by', 'updated_by', 'created_at', 'updated_at'
    ];
    protected $primaryKey = 'module_permission_id';
    protected $table = 'store_module_permission';
}
