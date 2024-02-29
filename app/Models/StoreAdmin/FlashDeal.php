<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashDeal extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'deal_title', 'background_color', 'text_color', 'banner_image', 'start_date', 'end_date', 'updated_by', 'created_by'
    ];
    protected $primaryKey = 'flash_deals_id';
    protected $table = 'store_flash_deals';
}
