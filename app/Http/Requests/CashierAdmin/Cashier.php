<?php

namespace App\Http\Requests\CashierAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;

class Cashier extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:50',
            'phone_number' => 'required|numeric|digits_between:10,15',
            // 'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg',
        ];
        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => __('store-admin.name'),
            /*'store_name' => __('store-admin.store_name'),
            'street_name' => __('store-admin.street_name'),
            'building_name' => __('store-admin.building_name'),
            'store_country' => __('admin.country'),
            'store_state' => __('admin.state'),
            'store_city' => __('admin.city'),
            'store_postal_code' => __('admin.postal_code'),*/
            'phone_number' => __('store-admin.phone_number'),
        ];
    }
}
