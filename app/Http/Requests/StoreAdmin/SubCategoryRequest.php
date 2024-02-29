<?php

namespace App\Http\Requests\StoreAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubCategoryRequest extends FormRequest
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
            'sub_category_name' => ['required', 'max:100',Rule::unique('store_sub_category')->where(function ($query) {
                    if(!empty(request()->store_id))
                        $query->where('store_id', Auth::user()->store_id);
                    $query->where('is_deleted', 0);
                    if(!empty(request()->category_id))
                        $query->where('category_id', request()->category_id);
                    if(!empty(request()->sub_category_id))
                        $query->whereNotIn('sub_category_id', [request()->sub_category_id]);
                    return $query->where('sub_category_name', trim(request()->sub_category_name));
                }),
            ],
            'category_id' => 'required',
            'sub_category_url' => ['required', 'max:100',Rule::unique('store_sub_category')->where(function ($query) {
                    if(!empty(request()->store_id))
                        $query->where('store_id', Auth::user()->store_id);
                    $query->where('is_deleted', 0);
                    if(!empty(request()->category_id))
                        $query->where('category_id', request()->category_id);
                    if(!empty(request()->sub_category_id))
                        $query->whereNotIn('sub_category_id', [request()->sub_category_id]);
                    return $query->where('sub_category_url', trim(request()->sub_category_url));
                }),
            ],
            // 'banner_image' => 'required_if:mode.*,in:add',
            // 'icon_image' => 'required_if:mode.*,in:add',
        ];
    }

    public function attributes()
    {
        return [
            'sub_category_name' => __('store-admin.sub_category'),
            'category_id' => __('store-admin.category'),
        ];
    }
}
