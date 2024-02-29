<?php

namespace App\Http\Requests\StoreAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'phone_number' => 'required',
            'email' => 'required', 
            'password' => 'required',
            'role_id' => 'required',
        ];
    }

    public function messages()
    {
        return[
            'role_id.required' => 'The role is required.',
        ];
    }
}
