<?php

namespace App\Http\Requests\StoreAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'products.type_of_product' => 'required|in:single,variant',
            'products.product_name' => ['required','max:150',
                Rule::unique('store_products', 'product_name')->where(function ($query) {
                    if(!empty(request()->store_id))
                        $query->where('store_id', Auth::user()->store_id);
                    $query->where('is_deleted', 0);
                    if(!empty(request()->products['category_id']))
                        $query->where('category_id', [request()->products['category_id']]);
                    if(!empty(request()->products['sub_category_id']))
                        $query->where('sub_category_id', [request()->products['sub_category_id']]);
                    if(!empty(request()->product_id))
                        $query->whereNotIn('product_id', [Crypt::decrypt(request()->product_id)]);
                    return $query->where('product_name', trim(request()->products['product_name']));
                })
            ], 
            'products.product_url' => ['required','max:150',
                Rule::unique('store_products', 'product_url')->where(function ($query) {
                    if(!empty(request()->store_id))
                        $query->where('store_id', Auth::user()->store_id);
                    $query->where('is_deleted', 0);
                    if(!empty(request()->products['category_id']))
                        $query->where('category_id', [request()->products['category_id']]);
                    if(!empty(request()->products['sub_category_id']))
                        $query->where('sub_category_id', [request()->products['sub_category_id']]);
                    if(!empty(request()->product_id))
                        $query->whereNotIn('product_id', [Crypt::decrypt(request()->product_id)]);
                    return $query->where('product_url', trim(request()->products['product_url']));
                })
            ], 
            'price_details.price' => 'required_if:type_of_product,single|nullable|numeric|min:0|max:99999999999|regex:/^\d+(\.\d{1,2})?$/',
            'products.unit' => [
                'nullable','numeric','min:0','max:99999999999','integer',
                Rule::requiredIf(function () {
                    // Check if trackable is 1 and type_of_product is single
                    return request('products.trackable') == 1 && request('products.type_of_product') == 'single';
                }),
            ],
            'products.barcode' => [
                'nullable','numeric','regex:/^[0-9]+$/','max:999999999999999',
                Rule::requiredIf(function () {
                    // Check if trackable is 1 and type_of_product is single
                    return request('products.is_sku_barcode') == 1 && request('products.type_of_product') == 'single';
                }),
            ],
            'products.status_type' => 'required|in:draft,publish,unpublish',
            'products.product_type' => 'required|in:online,instore,both',
            'products.category_id' => 'required|numeric',
        ];
        // if (empty($this->post('get_product_images'))) {
        //     $rules['category_image'] = 'required|image|mimes:jpeg,png,jpg'; 
        // }

        return $rules;
    }

    // public function messages()
    // {
    //     return[
    //         'category_id.required' => 'The category is required.',
    //     ];
    // }

    public function attributes()
    {
        return [
            'products.type_of_product' => __('store-admin.type_of_product'),
            'products.product_name' => __('store-admin.product'),
            'products.product_url' => __('store-admin.product_url'),
            'category_image' => __('store-admin.media'),
            'price_details.price' => __('store-admin.price'),
            'products.unit' => __('store-admin.quantity'),
            'products.barcode' => __('store-admin.barcode'),
            'products.status_type' => __('store-admin.status'),
            'products.product_type' => __('store-admin.sales_channels'),
            'products.category_id' => __('store-admin.category'),
        ];
    }

}
