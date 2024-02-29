<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'product_id', 'image_path', 'created_by', 'updated_by', 'is_deleted'
    ];
    protected $primaryKey = 'image_id';
    protected $table = 'product_images';
}
