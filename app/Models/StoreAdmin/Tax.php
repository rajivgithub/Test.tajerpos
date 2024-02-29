<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'tax_percentage', 'created_by', 'updated_by'
    ];
    protected $primaryKey = 'tax_id';
    protected $table = 'store_tax';
}
