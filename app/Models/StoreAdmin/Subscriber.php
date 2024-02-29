<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'subscriber_email', 'subscription_date'
    ];
    protected $primaryKey = 'subscriber_id';
    protected $table = 'store_subscribers';
}
