<?php

namespace App\Models\StoreAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLetters extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'user_id', 'subscriber_id', 'status', 'created_by', 'updated_by', 'subject', 'content'
    ];
    protected $primaryKey = 'newsletter_id';
    protected $table = 'store_newsletters';
}
