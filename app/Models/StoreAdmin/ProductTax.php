<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTax extends Model
{
    use HasFactory;
    protected $fillable = [
        'tax_type', 'tax_amount', 'status', 'product_id', 'store_id','created_by','updated_by'
    ];
    protected $primaryKey = 'product_tax_id';
    protected $table = 'store_product_tax';
}
