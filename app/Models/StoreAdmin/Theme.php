<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'color_name', 'color_code', 'status', 'created_by', 'updated_by'
    ];
    protected $primaryKey = 'theme_id';
    protected $table = 'store_themes';
}
