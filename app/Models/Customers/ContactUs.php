<?php

namespace App\Models\Customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id', 'contactor_id', 'contactor_name', 'contactor_email', 'contactor_phone_no', 'contactor_message', 'updated_by'
    ];
    protected $primaryKey = 'contactor_id';
    protected $table = 'customer_contact_us';
}
