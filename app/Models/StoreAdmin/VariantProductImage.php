<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantProductImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'product_id', 'variant_ids', 'created_by', 'is_deleted', 'variant_combination_id', 'product_image_id'
    ];
    protected $primaryKey = 'variant_img_id';
    protected $table = 'variant_product_images';
    const UPDATED_AT = null;
}
