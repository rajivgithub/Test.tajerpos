<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'category_number', 'category_name', 'order_number', 'banner', 'icon', 'created_by', 'updated_by', 'meta_title', 'meta_description', 'slug', 'category_image', 'category_url'
    ];
    protected $primaryKey = 'category_id';
    protected $table = 'store_category';
}
