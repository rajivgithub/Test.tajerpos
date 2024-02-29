<?php

namespace App\Http\Controllers\Api\Cashier;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Validator;
use App\Models\StoreAdmin\Category;
use App\Models\StoreAdmin\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\CashierAdmin\Cart;
use App\Models\StoreAdmin\SubCategory;

class PlaceorderController extends ApiController
{
    public function productList(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 
            'store_id' => 'required',
            // 'admin_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->createCashierResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $store_id = $request->store_id;
            $checkStoreId = $this->checkStoreId($store_id);
            if ($checkStoreId === true) {
                $category_id = !empty($request->category_id) ? $request->category_id : "all";
                $sub_category_id = $request->sub_category_id; 
                $product_id = $request->product_id;
                $sub_category_details = $product_variants_collection = $productVariantsOptions =  [];
                $category_details = Category::select('category_name', 'category_id', 'icon',
                    DB::raw('(SELECT COUNT(DISTINCT sp.product_id) 
                            FROM store_products AS sp 
                            LEFT JOIN store_sub_category ON sp.sub_category_id = store_sub_category.sub_category_id 
                            LEFT JOIN store_product_variants_combination AS spvc ON sp.product_id = spvc.product_id
                            WHERE sp.category_id = store_category.category_id 
                            AND sp.is_deleted = 0 
                            AND status_type = "publish" 
                            AND product_type IN ("instore","both")
                            AND (
                                CASE 
                                    WHEN sp.type_of_product = "single" AND sp.trackable = 1 THEN unit > 0
                                    WHEN sp.type_of_product = "variant" AND spvc.on_hand != "" THEN spvc.on_hand > 0
                                    ELSE TRUE 
                                END
                            )
                            AND (
                                CASE 
                                    WHEN sp.sub_category_id > 0 THEN store_sub_category.status = 1 AND store_sub_category.is_deleted = 0 
                                    ELSE TRUE 
                                END
                            )
                            AND (
                                CASE 
                                    WHEN sp.type_of_product = "variant" THEN spvc.is_deleted = 0 
                                    ELSE TRUE 
                                END
                            )) AS product_count'))
                    ->where('store_id', $store_id)
                    ->where('is_deleted', 0)
                    ->where('status', 1)
                    ->orderByDesc('category_id')
                    ->having('product_count', '>', 0)
                    ->get();
                $product_details = Product::select('store_products.product_id', 'type_of_product', 'product_name', 'store_products.category_id', 'category_name', 'unit_price', 'store_products.category_image', 'store_products.sub_category_id', 'unit', 'trackable', 'price', 'product_description','sub_category_name',DB::raw("4 as product_rating"))
                ->leftJoin('store_category', 'store_category.category_id', '=', 'store_products.category_id')
                ->leftJoin('store_sub_category',function($join) {
                    $join->on('store_sub_category.sub_category_id', '=', 'store_products.sub_category_id');
                })
                ->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')
                ->where('store_products.store_id', $store_id)
                ->where('store_products.is_deleted', 0)
                ->where('store_products.status_type', 'publish')
                ->where('store_products.status', 1)
                ->whereIn('store_products.product_type', ['instore', 'both'])
                ->where('store_category.is_deleted', 0)
                ->where('store_category.status', 1)
                ->when(($category_id != "all"), function ($query) use ($category_id) {
                    $query->where('store_products.category_id', $category_id);
                }) 
                ->when($sub_category_id, function ($query) use ($sub_category_id) {
                    $query->where('store_products.sub_category_id',$sub_category_id);
                })
                ->when($product_id, function ($query) use ($product_id) {
                    $query->where('store_products.product_id',$product_id);
                })
                ->whereRaw('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END')
                ->whereRaw('CASE WHEN store_products.type_of_product = "single" AND trackable = 1 THEN unit > 0 ELSE TRUE END') 
                ->distinct('store_products.product_id')
                ->orderByDesc('store_products.category_id')->get();
                if(!empty($product_details)) {
                    $product_variants_collection = Product::leftJoin('store_product_variants', 'store_products.product_id', '=', 'store_product_variants.product_id')
                        ->where('store_products.store_id', $store_id)
                        ->where('store_products.is_deleted', 0)
                        ->where('status_type', 'publish')
                        ->where('type_of_product', 'variant')
                        ->where('store_product_variants.is_deleted', 0)
                        ->whereIn('store_products.product_type', ['instore', 'both'])
                        ->when(($category_id != "all"), function ($query) use ($category_id) {
                            $query->where('store_products.category_id', $category_id);
                        }) 
                        ->when($sub_category_id, function ($query) use ($sub_category_id) {
                            $query->where('store_products.sub_category_id',$sub_category_id);
                        })
                        ->when($product_id, function ($query) use ($product_id) {
                            $query->where('store_products.product_id',$product_id);
                        })
                        ->select('variants_name','store_product_variants.product_id','variants_id')->get()
                        ->groupBy('product_id')->toArray();
                    $productVariantsOptions = Product::leftJoin('store_product_variants', 'store_products.product_id', '=', 'store_product_variants.product_id')
                        ->leftJoin('store_product_variants_options', 'store_product_variants_options.variants_id', '=', 'store_product_variants.variants_id')
                        ->where('store_products.store_id', $store_id)
                        ->where('store_products.is_deleted', 0)
                        ->whereIn('store_products.product_type', ['instore', 'both'])
                        ->where('status_type', 'publish')
                        ->where('type_of_product', 'variant')
                        ->where('store_product_variants.is_deleted', 0)
                        ->where('store_product_variants_options.is_deleted', 0)
                        ->when(($category_id != "all"), function ($query) use ($category_id) {
                            $query->where('store_products.category_id', $category_id);
                        }) 
                        ->when($sub_category_id, function ($query) use ($sub_category_id) {
                            $query->where('store_products.sub_category_id',$sub_category_id);
                        })
                        ->when($product_id, function ($query) use ($product_id) {
                            $query->where('store_products.product_id',$product_id);
                        })
                        ->select('variants_name','store_product_variants.product_id','variant_options_name','variant_options_id','store_product_variants.variants_id')->get()
                        ->groupBy('product_id')->toArray();
                }
                $sub_category_details = SubCategory::select('store_sub_category.category_id','sub_category_name','sub_category_id',DB::raw('(SELECT COUNT(DISTINCT sp.product_id) FROM store_products AS sp LEFT JOIN store_category on sp.category_id = store_category.category_id LEFT JOIN store_product_variants_combination as spvc on sp.product_id = spvc.product_id WHERE sp.sub_category_id = store_sub_category.sub_category_id AND sp.is_deleted = 0 AND status_type = "publish" AND store_sub_category.status = 1 AND store_sub_category.is_deleted = 0 AND (CASE WHEN type_of_product = "single" THEN (trackable = 1 AND unit > 0) OR trackable = 0 WHEN type_of_product = "variant" THEN (on_hand > 0 OR on_hand IS NULL OR on_hand = "") AND spvc.is_deleted = 0 AND spvc.variants_combination_id IS NOT NULL ELSE TRUE END)) AS product_count'))
                ->where([
                    ['store_sub_category.store_id', '=', $store_id],
                    ['store_sub_category.is_deleted', '=', 0],
                    ['status', '=', 1]
                ])
                ->where('store_sub_category.category_id',$category_id)
                ->orderBy('store_sub_category.category_id','desc')
                ->get()->toArray();
                $get_product_variants_combinations = Product::leftJoin('store_product_variants_combination', 'store_products.product_id', '=', 'store_product_variants_combination.product_id')
                    ->where('store_products.store_id', $store_id)
                    ->where('store_products.is_deleted', 0)
                    ->where('status_type', 'publish')
                    ->where('type_of_product', 'variant')
                    ->where('store_product_variants_combination.is_deleted', 0)
                    ->whereIn('product_type', ['instore', 'both'])
                    ->when($product_id, function ($query) use ($product_id) {
                        $query->where('store_products.product_id',$product_id);
                    })
                    ->select('variants_combination_id', 'variants_combination_name', 'store_products.product_id', 'variant_price', 'on_hand','variants_id')->get()->toArray();
                /*$cart_data = Cart::where([
                    ['store_id','=',$request->store_id],
                    ['admin_id','=',$request->admin_id],
                    ['is_deleted','=',0]
                ])
                ->get()->groupBy(['product_id','variants_id']);
                $cart_data->transform(function ($items) {
                    return $items->map(function ($item) {
                        return $item->first();
                    });
                });
                $cart_data = $cart_data->toArray();*/
                $product_variants_combinations = [];
                if(!empty($get_product_variants_combinations)) {
                    foreach($get_product_variants_combinations as $key => $variants) {
                        $variant_product_unit = $available_variants_quantity = $variants['on_hand'];
                        /*if(!empty($cart_data) && isset($cart_data[$variants['product_id']]) && isset($cart_data[$variants['product_id']][$variants['variants_id']])) {
                            $quantity = $cart_data[$variants['product_id']][$variants['variants_id']]['quantity'];
                            if(!empty($variant_product_unit) && is_numeric($variant_product_unit) && $variant_product_unit >= 0)
                                $available_variants_quantity = ($variant_product_unit - $quantity);
                        }*/
                        $variants['product_available'] = (is_numeric($available_variants_quantity) && ($available_variants_quantity <= 0)) ? "out-of-stock" : "";
                        if($variants['product_available'] == "out-of-stock")
                            unset($get_product_variants_combinations[$key]);
                        else
                            $product_variants_combinations[$variants['product_id']][] = $variants;
                    }
                }
                if(!empty($product_details)) {
                    foreach($product_details as $key => $product) {
                        if($product->type_of_product == 'variant') {
                            if(isset($product_variants_combinations[$product->product_id]) && count($product_variants_combinations[$variants['product_id']]) > 0 ) {
                                $product_details[$key]['product_variants_collection'] = isset($product_variants_collection[$product->product_id]) ? $product_variants_collection[$product->product_id] : [];
                                $product_details[$key]['product_variants_options'] = isset($productVariantsOptions[$product->product_id]) ?  $productVariantsOptions[$product->product_id] : [];
                                $product_details[$key]['product_variants_combinations'] = isset($product_variants_combinations[$product->product_id]) ? $product_variants_combinations[$product->product_id] : [];
                            } else {
                                unset($product_details[$key]);
                            }
                        }
                    }
                    $product_details = $product_details->toArray();
                    $product_details = array_values($product_details);
                }
                $result = array(
                    'category_details' => $category_details,
                    'sub_category_details' => $sub_category_details,
                    'product_details' => $product_details
                );
                return $this->createCashierResponse("Product list according to category", self::HTTP_OK, $store_id,'',$result);
            } else {
                return $checkStoreId;
            }
        }
    }
}
