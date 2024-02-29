<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    use HasFactory;
    protected $fillable = [
        'modules_name', 'status', 'created_at', 'updated_at'
    ];
    protected $primaryKey = 'modules_id';
    protected $table = 'store_modules';
}
