<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'old_tax_value', 'new_tax_value', 'created_by', 'updated_by'
    ];
    protected $primaryKey = 'tax_history_id';
    protected $table = 'store_tax_history';
}
