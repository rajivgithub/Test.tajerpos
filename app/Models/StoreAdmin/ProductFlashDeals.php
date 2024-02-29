<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFlashDeals extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id', 'flash_id', 'flash_discount', 'flash_discount_type', 'status','store_id','created_by','updated_by'
    ];
    protected $primaryKey = 'product_deals_id';
    protected $table = 'store_product_flash_deals';
}
