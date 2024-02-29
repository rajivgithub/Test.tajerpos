<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = [
        'msg_id', 'incoming_msg_id', 'outgoing_msg_id', 'message', 'store_id'
    ];
    protected $primaryKey = 'msg_id';
    protected $table = 'store_messages';
}
