<?php

namespace App\Models\CashierAdmin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;


class InStoreCustomer extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $fillable = [
        'customer_name', 'phone_number', 'status', 'is_deleted','store_id', 'created_by', 'updated_by', 'email', 'password', 'plain_password', 'profile_image'
    ];
    protected $primaryKey = 'customer_id';
    protected $table = 'instore_customers';

    public function getAuthIdentifierName()
    {
        return 'email'; 
    }

    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

}
