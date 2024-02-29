<?php

namespace App\Http\Requests\CashierAdmin;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:50',
            'store_name' => 'required|string|max:100',
            'phone_number' => 'required|numeric|digits_between:10,15',
            'building_name' => 'required|max:100',
            'street_name' => 'required|max:100',
            'store_country' => 'required|numeric|max:99999999999',
            'store_state' => 'required|numeric|max:99999999999',
            'store_city' => 'required|numeric|max:99999999999',
            'store_postal_code' => 'required|numeric|max:99999999999',
        ];
        return $rules;
    }
    public function attributes()
    {
        return [
            'name' => __('store-admin.name'),
            'store_name' => __('store-admin.store_name'),
            'street_name' => __('store-admin.street_name'),
            'building_name' => __('store-admin.building_name'),
            'store_country' => __('admin.country'),
            'store_state' => __('admin.state'),
            'store_city' => __('admin.city'),
            'store_postal_code' => __('admin.postal_code'),
            'phone_number' => __('store-admin.phone_number'),
        ];
    }

}
