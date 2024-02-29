<?php

namespace App\Http\Requests\StoreAdmin;

use Illuminate\Foundation\Http\FormRequest;

class FlashDealRequest extends FormRequest
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
            'deal_title' => 'required',
            'banner_image' => 'required_if:mode,add',
            'start_date' => 'required',
            'end_date' => 'required'
        ];
    }
    public function messages()
    {
        return[
            'banner_image.required_if' => 'The banner image field is required.',
        ];
    }
}
