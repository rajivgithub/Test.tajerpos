<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ChangePassword extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'current_password' => 'required',
            'new_password' => 'required|min:8|max:100',
            'confirm_password' => 'required|min:8|same:new_password|max:100',
        ];
    }

    public function attributes()
    {
        return [
            'current_password' => __('admin.current_password'),
            'new_password' => __('admin.new_password'),
            'confirm_password' => __('admin.confirm_password'),
        ];
    }
}
