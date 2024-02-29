<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleLogos extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'module_name', 'sidebar_logo','company_logo', 'created_by', 'updated_by'
    ];
    protected $primaryKey = 'logo_id';
    protected $table = 'module_logos';
}
