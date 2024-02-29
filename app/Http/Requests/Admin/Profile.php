<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;

class Profile extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'company_name' => 'required|max:150',
            'phone_number' => 'required|numeric|digits_between:10,15',
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users')->where(function ($query) {
                    if (!empty(request()->store_id))
                        $query->where('store_id', Crypt::decrypt(request()->store_id));
        
                    $query->where('is_admin', request()->is_admin);
                    $query->whereNotIn('id', [Auth::user()->id]);
                    return $query->where('email', request()->email);
                }),
            ],
            'street_name' => 'required|max:100',
            'building_name' => 'required|max:100',
            'country_id' => 'required|numeric|max:99999999999',
            'state_id' => 'required|numeric|max:99999999999',
            'city_id' => 'required|numeric|max:99999999999',
            'postal_code' => 'required|numeric|max:99999999999',
        ];        
    }

    public function attributes()
    {
        return [
            'name' => __('admin.name'),
            'company_name' => __('admin.company_name'),
            'phone_number' => __('admin.phone_number'),
            'email' => __('admin.email'),
            'street_name' => __('admin.street_name'),
            'building_name' => __('admin.building_name'),
            'country_id' => __('admin.country'),
            'state_id' => __('admin.state'),
            'city_id' => __('admin.city'),
            'postal_code' => __('admin.postal_code'),
        ];
    }
}
