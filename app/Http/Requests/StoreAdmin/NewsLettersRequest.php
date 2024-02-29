<?php

namespace App\Http\Requests\StoreAdmin;

use Illuminate\Foundation\Http\FormRequest;

class NewsLettersRequest extends FormRequest
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
            'user_id' => 'required',
            'subscriber_id' => 'required',
            'subject' => 'required',
            'content' => 'required'
        ];
    }
    public function messages()
    {
        return[
            'user_id.required' => 'The user email field is required.', 
            'subscriber_id.required' => 'The subscriber email field is required.', 
        ];
    }
}
