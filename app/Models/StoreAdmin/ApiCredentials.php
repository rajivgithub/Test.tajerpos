<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiCredentials extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'google_recaptcha', 'site_key', 'status', 'created_by', 'updated_by'
    ];
    protected $primaryKey = 'api_credential_id';
    protected $table = 'store_api_credentials';
}
