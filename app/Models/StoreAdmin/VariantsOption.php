<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantsOption extends Model
{
    use HasFactory;
    protected $fillable = [
        'variants_id', 'variant_options_name', 'product_id', 'store_id', 'created_by', 'updated_by','variants_option_image'
    ];
    protected $primaryKey = 'variant_options_id';
    protected $table = 'store_product_variants_options';
}
