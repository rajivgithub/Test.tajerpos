<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'product_number', 'product_name', 'category_id', 'sub_category_id', 'unit', 'minimum_purchase_qty', 'category_image', 'unit_price', 'sku', 'product_description','shipping_time','product_type','tags','meta_title','meta_description','meta_image','flash_id','status','created_by','updated_by','status_type','taxable','sell_out_of_stock','barcode','trackable','is_sku_barcode', 'type_of_product', 'product_url'
    ];
    protected $primaryKey = 'product_id';
    protected $table = 'store_products';
}
