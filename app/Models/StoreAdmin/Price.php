<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id', 'price', 'compare_price', 'cost_per_item', 'profit', 'margin', 'store_id','created_by','updated_by'
    ];
    protected $primaryKey = 'price_id';
    protected $table = 'store_price';
}
