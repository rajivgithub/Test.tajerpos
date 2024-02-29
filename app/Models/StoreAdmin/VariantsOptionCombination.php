<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantsOptionCombination extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id', 'variants_combination_name', 'variant_price', 'on_hand', 'available', 'sku', 'barcode','store_id','created_by','updated_by', 'variants_id'
    ];
    protected $primaryKey = 'variants_combination_id';
    protected $table = 'store_product_variants_combination';
}
