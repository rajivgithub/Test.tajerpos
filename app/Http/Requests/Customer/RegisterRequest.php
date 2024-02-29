<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\CommonController;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $store_id = CommonController::get_store_id();
        return [
            'customer_name' => 'required|max:150',
            'email' => 'required|email|unique:instore_customers,email,NULL,customer_id,store_id,' . $store_id.'|max:100',
            'phone_number' => 'nullable|max:20',
            'password' => 'required|max:255'
        ];
    }
}
