<?php

namespace App\Http\Requests\StoreAdmin;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingsRequest extends FormRequest
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
            'system_name' => 'required',
            'system_white_logo_image' => 'required_if:mode,add',
            'system_black_logo_image' => 'required_if:mode,add',
            'email_logo_image' => 'required_if:mode,add',
            'country_id' => 'required',
            'system_timezone' => 'required',
            'admin_login_image' => 'required_if:mode,add',
        ];
    }
}
