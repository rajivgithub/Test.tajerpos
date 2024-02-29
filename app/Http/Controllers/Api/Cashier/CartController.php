<?php

namespace App\Http\Controllers\API\Cashier;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\CashierAdmin\Cart;
use Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\StoreAdmin\Tax;
use App\Models\StoreAdmin\VariantsOptionCombination;
use App\Models\StoreAdmin\Product;

class CartController extends ApiController
{
    public function addToCart(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required',
            'admin_id' => [
                'required',
                'numeric',
                Rule::exists('users', 'id')->where(function ($query) use ($request) {
                    $query->where('store_id', $request->store_id)
                        ->where('is_admin', 3)
                        ->where('id', $request->admin_id);
                }),
            ],
            'product_id'=> 'required|numeric',
            'product_type'=> 'required',
            'variants_id' => 'required_if:product_type,variant',
            // 'variants_combination_id' => 'required_if:product_type,variant',
            'mode'=> 'required|in:add,remove',
            'quantity'=> 'required_if:mode,add|numeric',
        ]);
        if ($validator->fails()) {
            return $this->createCashierResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            // $variants_id = "";
            // if($request->product_type == "variant" && !empty($request->variants_combination_id)) {
            //     $variants_id = VariantsOptionCombination::where([
            //         ['store_id', '=', $request->store_id],
            //         ['variants_combination_id', '=', $request->variants_combination_id],
            //         ['is_deleted', '=', 0],
            //     ])->first('variants_id');
            //     $request['variants_id'] = $variants_id = !empty($variants_id) ? $variants_id['variants_id'] : '';
            // }
            if($request->mode == "add") {
                $cart_data = Cart::where([
                    ['product_id', '=', $request->product_id],
                    ['store_id','=',$request->store_id],
                    ['admin_id','=',$request->admin_id],
                    ['is_deleted','=',0]
                ])
                ->when(!empty($request->variants_id), function ($query) use ($request) {
                    $query->where('variants_id',$request->variants_id);
                })
                // ->when(!empty($variants_id), function ($query) use ($variants_id) {
                //     $query->where('variants_id',$variants_id);
                // })
                ->first();
                if ($cart_data) {
                    if($request->type == "cart") 
                        $cart_data->quantity = $request->quantity;
                    else 
                        $cart_data->quantity += $request->quantity;
                    $cart_data->save();
                } else {
                    Cart::create($request->except('mode'));
                }
                $message = "Product added to cart successfully";
            } elseif($request->mode == "remove") {
                $remove_cart = array();
                $remove_cart['is_deleted'] = 1;  
                $remove_cart['deleted_at'] = Carbon::now()->toDateTimeString();
                Cart::where([
                    ['product_id', '=', $request->product_id],
                    ['store_id','=',$request->store_id],
                    ['admin_id','=',$request->admin_id]
                ])
                // ->when(!empty($request->variants_id), function ($query) use ($request) {
                //     $query->where('variants_id',$request->variants_id);
                // })
                ->when(!empty($request->variants_id), function ($query) use ($request) {
                    $query->where('variants_id',$request->variants_id);
                })
                ->update($remove_cart);
                $message = "Product removed from cart successfully";
            }
            return $this->createCashierResponse($message, self::HTTP_OK, $request->store_id);
        } else {
            return $checkStoreId;
        }
    }

    public function cartlist(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required',
            'admin_id' => [
                'required',
                'numeric',
                Rule::exists('users', 'id')->where(function ($query) use ($request) {
                    $query->where('store_id', $request->store_id)
                        ->where('is_admin', 3)
                        ->where('id', $request->admin_id);
                }),
            ],
        ]);
        if ($validator->fails()) {
            return $this->createCashierResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            $checkAdmin = $this->checkAdminInStore($request->store_id,$request->admin_id);
            if($checkAdmin){
                $product_details = Cart::leftJoin('store_products', 'store_products.product_id', '=', 'instore_cart_data.product_id')
                    ->leftJoin('store_category', 'store_products.category_id', '=', 'store_category.category_id')
                    ->leftJoin('store_sub_category', 'store_products.sub_category_id', '=', 'store_sub_category.sub_category_id')
                    ->leftJoin('store_product_tax', 'store_products.product_id', '=', 'store_product_tax.product_id')
                    ->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')
                    ->leftJoin('store_product_variants_combination', 'store_product_variants_combination.variants_combination_id', '=', 'instore_cart_data.variants_id')
                    ->where([
                        ['store_products.store_id', '=', $request->store_id],
                        ['store_products.is_deleted', '=', 0],
                        ['store_products.status_type', '=', 'publish'],
                        ['store_products.status', '=', 1],
                        ['store_category.is_deleted', '=', 0],
                        ['store_category.status', '=', 1],
                        ['instore_cart_data.is_deleted', '=', 0],
                        ['instore_cart_data.admin_id', '=', $request->admin_id],
                    ])
                    ->where(function ($query) {
                        $query->whereRaw('CASE WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END')
                            ->orWhereNull('store_products.sub_category_id');
                    })
                    ->whereRaw(('case WHEN (instore_cart_data.variants_id > 0) THEN store_product_variants_combination.is_deleted = 0 ELSE TRUE END'))
                    // ->select('product_name', 'store_products.category_id', 'category_name', 'price', 'store_products.product_id', 'store_products.category_image', 'tax_type', 'tax_amount', 'taxable', 'type_of_product', 'unit', 'trackable', 'variants_combination_name', 'variants_combination_id', 'variant_price', 'instore_cart_data.quantity as cart_quantity')
                    ->select('product_name', 'store_products.category_id', 'category_name', 'price', 'store_products.product_id', 'store_products.category_image', 'tax_type', 'tax_amount', 'taxable', 'type_of_product', 'unit', 'trackable', 'instore_cart_data.variants_id','instore_cart_data.quantity as cart_quantity')
                    // ->selectRaw('CASE WHEN (on_hand <= 0 AND on_hand IS NOT NULL AND on_hand != "") THEN "out-of-stock" ELSE "" END as product_available')
                    ->get();
                if(!empty($product_details)) {
                    $productIDs = $product_details->map(function ($item) {
                        if($item['type_of_product'] == 'variant')
                            return $item['product_id'];
                    });
                } else 
                    $productIDs = [];
                if(!empty($productIDs)) {
                    $get_product_variants_combinations = Product::leftJoin('store_product_variants_combination', 'store_products.product_id', '=', 'store_product_variants_combination.product_id')
                        ->where('store_products.store_id', $request->store_id)
                        ->where('store_products.is_deleted', 0)
                        ->where('status_type', 'publish')
                        ->where('type_of_product', 'variant')
                        ->where('store_product_variants_combination.is_deleted', 0)
                        ->whereIn('product_type', ['instore', 'both'])
                        ->whereIn('store_products.product_id', $productIDs)
                        ->select('variants_combination_id', 'variants_combination_name', 'store_products.product_id', 'variant_price', 'on_hand','variants_id')
                        ->get()->toArray();
                    if(!empty($get_product_variants_combinations)) {
                        $product_variants_combinations = [];
                        foreach ($get_product_variants_combinations as $variant) {
                            $productID = $variant['product_id'];
                            if (!isset($product_variants_combinations[$productID])) {
                                $product_variants_combinations[$productID] = [];
                            }
                            $product_variants_combinations[$productID][] = $variant;
                        }
                        if(!empty($product_details)) {
                            foreach($product_details as $key => $product) {
                                if($product->type_of_product == 'variant') {
                                    if(isset($product_variants_combinations[$product->product_id]) && count($product_variants_combinations[$product->product_id]) > 0 ) {
                                        $product_details[$key]['product_variants_combinations'] = isset($product_variants_combinations[$product->product_id]) ? $product_variants_combinations[$product->product_id] : [];
                                    }
                                }
                            }
                            $product_details = $product_details->toArray();
                            $product_details = array_values($product_details);
                        }
                    }
                }
                $tax_details = Tax::where('store_id',$request->store_id)->get(['tax_percentage','tax_id'])->toArray();
                $result = array(
                    'tax_details' => $tax_details,
                    'product_details' => $product_details
                );
                return $this->createCashierResponse('Cart list according to the store', self::HTTP_OK, $request->store_id,'',$result);
            } else {
                return $checkAdmin;
            }
        } else {
            return $checkStoreId;
        }
    }
}
