<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id', 'low_stock_quantity_count', 'stock_quantity', 'stock_with_text', 'hide_stock', 'cash_on_delivery', 'feature_status', 'today_deal_status', 'status', 'store_id','created_by','updated_by'
    ];
    protected $primaryKey = 'product_stock_id';
    protected $table = 'store_product_stock';
}
