<?php

namespace App\Http\Requests\StoreAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CategoryRequest extends FormRequest
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
            'category_name' => ['required', 'max:100',Rule::unique('store_category')->where(function ($query) {
                    $query->where('store_id', Auth::user()->store_id);
                    $query->where('is_deleted', 0);
                    if(!empty(request()->category_id))
                        $query->whereNotIn('category_id', [request()->category_id]);
                    return $query->where('category_name', trim(request()->category_name));
                }),
            ],
            'category_url' => ['required', 'max:100',Rule::unique('store_category')->where(function ($query) {
                $query->where('store_id', Auth::user()->store_id);
                $query->where('is_deleted', 0);
                if(!empty(request()->category_id))
                    $query->whereNotIn('category_id', [request()->category_id]);
                return $query->where('category_url', trim(request()->category_url));
            }),
        ],
            // 'banner_image' => 'required_if:mode.*,in:add',
            // 'icon_image' => 'required_if:mode.*,in:add',
        ];
    }

    public function attributes()
    {
        return [
            'category_name' => __('store-admin.category_name'),
        ];
    }
}
