<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStore extends FormRequest
{
    
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'store_user_name' => 'required|string|max:50',
            'store_name' => 'required|string|max:100',
            'store_url' => 'required|max:150',
            'street_name' => 'required|max:100',
            'building_name' => 'required|max:100',
            'store_country' => 'required|numeric|max:99999999999',
            'store_state' => 'required|numeric|max:99999999999',
            'store_city' => 'required|numeric|max:99999999999',
            'store_postal_code' => 'required|numeric|max:99999999999',
            // 'store_validity_date' => 'required|date',
            'store_phone_number' => 'required|numeric|digits_between:10,15',
            'email' => 'sometimes|required_if:mode.*,in:add|email|max:100',
            'store_password' => 'required_if:mode.*,in:add|max:100',
            'store_logo_image' => 'required_if:mode.*,in:add',
            'store_background_image' => 'required_if:mode.*,in:add',
        ];
    }

    public function attributes()
    {
        return [
            'store_user_name' => __('admin.owner_name'),
            'store_name' => __('admin.shop_name'),
            'store_url' => __('admin.url'),
            'street_name' => __('admin.street_name'),
            'building_name' => __('admin.building_name'),
            'store_country' => __('admin.country'),
            'store_state' => __('admin.state'),
            'store_city' => __('admin.city'),
            'store_postal_code' => __('admin.postal_code'),
            'store_phone_number' => __('admin.phone_number'),
            // 'store_validity_date' => __('admin.validity'),
            'email' => __('admin.email_address'),
            'store_password' => __('admin.password'),
            'store_logo_image' => __('admin.store_logo'),
            'store_background_image' => __('admin.login_background_image'),
        ];
    }

    /*public function messages()
    {
        return[
            'store_user_name.required' => 'The user name is required.',
            'store_name.required' => 'The shop name is required.',
            'store_url.required' => 'The URL is required.',
            'store_address.required' => 'The address is required.',
            'store_country.required' => 'The country is required.',
            'store_city.required' => 'The city is required.',
            'store_postal_code.required' => 'The postal code is required.',
            'store_validity_date.required' => 'The validity date is required.',
            'store_phone_number.required' => 'The phone number is required.',
            'store_password.required' => 'The password is required.',
            'store_logo_image.required' => 'The  logo is required.',
            'store_background_image.required' => 'The  background image is required.',
        ];
    }*/
}
