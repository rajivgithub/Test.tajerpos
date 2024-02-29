<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBanners extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'banner_image', 'banner_url', 'start_date', 'end_date', 'banner_type', 'created_by', 'updated_by', 'status'
    ];
    protected $primaryKey = 'banner_id';
    protected $table = 'customer_banner_settings';
}
