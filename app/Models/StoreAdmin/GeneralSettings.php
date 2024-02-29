<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSettings extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'system_name', 'system_white_logo', 'system_black_logo','email_logo','country_id', 'system_timezone','background_image', 'created_by', 'updated_by'
    ];
    protected $primaryKey = 'settings_id';
    protected $table = 'store_general_settings';
}
