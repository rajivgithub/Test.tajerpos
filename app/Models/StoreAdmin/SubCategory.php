<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'sub_category_number', 'category_id', 'sub_category_name', 'order_number', 'banner', 'icon', 'created_by', 'updated_by', 'meta_title', 'meta_description', 'slug','sub_category_image', 'sub_category_url'
    ];
    protected $primaryKey = 'sub_category_id';
    protected $table = 'store_sub_category';
}
