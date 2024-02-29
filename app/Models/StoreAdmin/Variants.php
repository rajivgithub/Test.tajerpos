<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variants extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id', 'variants_name', 'store_id', 'created_by', 'updated_by'
    ];
    protected $primaryKey = 'variants_id';
    protected $table = 'store_product_variants';
}
