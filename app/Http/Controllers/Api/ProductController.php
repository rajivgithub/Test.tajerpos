<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\Product;
use App\Models\Customers\Wishlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Validator;
use Illuminate\Support\Collection;
use App\Models\StoreAdmin\Category;
use App\Models\StoreAdmin\SubCategory;

class ProductController extends ApiController
{
    
    public function productList(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 
            'store_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $store_id = $request->store_id;
            $checkStoreId = $this->checkStoreId($store_id);
            if ($checkStoreId === true) {
                $category_id = !empty($request->category_id) ? $request->category_id : "all";
                $sub_category_id = $request->sub_category_id;
                $search_text = $request->search_text;
                $product_id = $request->product_id;
                $_type = $request->type;
                $sorting_column = $request->sorting_column;
                $sorting_order = $request->sorting_order;
                $category_details = $sub_category_details = [];
                if($_type != "single_product") {
                    $category_details = Category::select('category_name', 'category_id', 'icon',
                    DB::raw('(SELECT COUNT(DISTINCT sp.product_id) 
                        FROM store_products AS sp 
                        LEFT JOIN store_sub_category ON sp.sub_category_id = store_sub_category.sub_category_id 
                        WHERE sp.category_id = store_category.category_id 
                        AND sp.is_deleted = 0 
                        AND status_type = "publish" 
                        AND product_type IN ("online","both")
                        AND (CASE WHEN sp.sub_category_id > 0 THEN store_sub_category.status = 1 AND store_sub_category.is_deleted = 0 ELSE TRUE END)) AS product_count'))
                    ->where('store_id', $store_id)
                    ->where('is_deleted', 0)
                    ->where('status', 1)
                    ->orderByDesc('category_id')
                    ->having('product_count', '>', 0)
                    ->get();
                }
                
                $product_details_query = Product::select('store_products.product_id', 'type_of_product', 'product_name', 'store_products.category_id', 'category_name', 'unit_price', 'store_products.category_image', 'store_products.sub_category_id', 'unit', 'trackable', 'price', 'product_description','sub_category_name',DB::raw("4 as product_rating"));
                if ($sorting_column == "low_to_high" || $sorting_column == "high_to_low") {
                    $product_details_query->selectRaw('
                        CASE 
                            WHEN store_products.type_of_product = "variant" THEN 
                                (SELECT variants_combination_name FROM store_product_variants_combination 
                                 WHERE product_id = store_products.product_id and store_product_variants_combination.is_deleted = 0
                                 ORDER BY CAST(variant_price AS DECIMAL(10,2)) '.$sorting_order.' LIMIT 1)
                            ELSE TRUE
                        END as variants_combination_name
                    ');
                } 
                $product_details_query->leftJoin('store_category', 'store_category.category_id', '=', 'store_products.category_id')
                ->leftJoin('store_sub_category',function($join) {
                    $join->on('store_sub_category.sub_category_id', '=', 'store_products.sub_category_id');
                })
                ->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')
                ->where('store_products.store_id', $store_id)
                ->where('store_products.is_deleted', 0)
                ->where('store_products.status_type', 'publish')
                ->where('store_products.status', 1)
                ->where('store_category.is_deleted', 0)
                ->where('store_category.status', 1)
                ->when($search_text, function ($query) use ($search_text) {
                    $query->where('store_products.product_name', 'LIKE', '%' . $search_text . '%');
                })
                ->when(($category_id != "all" && $_type != "single_product"), function ($query) use ($category_id) {
                    $query->where('store_products.category_id', $category_id);
                }) 
                ->when($sub_category_id, function ($query) use ($sub_category_id) {
                    $query->where('store_products.sub_category_id',$sub_category_id);
                })
                ->when($product_id, function ($query) use ($product_id) {
                    $query->where('store_products.product_id',$product_id);
                })
                ->whereRaw(('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END'))
                ->distinct('store_products.product_id');
                if (!empty($sorting_column) && !empty($sorting_order)) {
                    if ($sorting_column == "low_to_high" || $sorting_column == "high_to_low") {
                        $product_details_query->orderByRaw('
                            CASE 
                                WHEN store_products.type_of_product = "variant" THEN 
                                    (SELECT CAST(variant_price AS DECIMAL(10,2)) FROM store_product_variants_combination 
                                     WHERE product_id = store_products.product_id and store_product_variants_combination.is_deleted = 0
                                     ORDER BY CAST(variant_price AS DECIMAL(10,2)) '.$sorting_order.' LIMIT 1)
                                ELSE CAST(price AS DECIMAL(10,2))
                            END '.$sorting_order.'
                        ');
                    } else {
                        $product_details_query->orderBy($sorting_column, $sorting_order);
                    }
                } else {
                    $product_details_query->orderByDesc('store_products.category_id');
                }
                $product_details = $product_details_query->get();
                $product_variants_collection = $productVariantsOptions = [];
                if(!empty($product_details)) {
                    $productIds = $product_details->map(function ($product) {
                        return $product->product_id;
                    });
                    $product_variants = Product::leftJoin('store_product_variants', 'store_products.product_id', '=', 'store_product_variants.product_id')
                        ->where('store_products.store_id', $store_id)
                        ->where('store_products.is_deleted', 0)
                        ->where('status_type', 'publish')
                        ->where('type_of_product', 'variant')
                        ->where('store_product_variants.is_deleted', 0)
                        ->when($search_text, function ($query) use ($search_text) {
                            $query->where('store_products.product_name', 'LIKE', '%' . $search_text . '%');
                        })
                        ->when(($category_id != "all" && $_type != "single_product"), function ($query) use ($category_id) {
                            $query->where('store_products.category_id', $category_id);
                        }) 
                        ->when($sub_category_id, function ($query) use ($sub_category_id) {
                            $query->where('store_products.sub_category_id',$sub_category_id);
                        })
                        ->when($productIds, function ($query) use ($productIds) {
                            $query->whereIn('store_products.product_id',$productIds);
                        })
                        ->select('variants_name','store_product_variants.product_id','variants_id')->get()->toArray();
                    if(!empty($product_variants)) {
                        $variantsCollection = new Collection($product_variants);
                        $product_variants_collection = $variantsCollection->groupBy('product_id');
                    }
                    $product_variants_options = Product::leftJoin('store_product_variants', 'store_products.product_id', '=', 'store_product_variants.product_id')
                        ->leftJoin('store_product_variants_options', 'store_product_variants_options.variants_id', '=', 'store_product_variants.variants_id')
                        ->where('store_products.store_id', $store_id)
                        ->where('store_products.is_deleted', 0)
                        ->where('status_type', 'publish')
                        ->where('type_of_product', 'variant')
                        ->where('store_product_variants.is_deleted', 0)
                        ->where('store_product_variants_options.is_deleted', 0)
                        ->when($search_text, function ($query) use ($search_text) {
                            $query->where('store_products.product_name', 'LIKE', '%' . $search_text . '%');
                        })
                        ->when(($category_id != "all" && $_type != "single_product"), function ($query) use ($category_id) {
                            $query->where('store_products.category_id', $category_id);
                        }) 
                        ->when($sub_category_id, function ($query) use ($sub_category_id) {
                            $query->where('store_products.sub_category_id',$sub_category_id);
                        })
                        ->when($productIds, function ($query) use ($productIds) {
                            $query->whereIn('store_products.product_id',$productIds);
                        })
                        ->select('variants_name','store_product_variants.product_id','variant_options_name','variant_options_id','store_product_variants.variants_id')->get()->toArray();
                    if(!empty($product_variants_options)) {
                        $variantsOptionsCollection = new Collection($product_variants_options);
                        $productVariantsOptions = $variantsOptionsCollection->groupBy(function ($item) {
                            return $item['product_id'];
                        });
                    }
                }
                $product_variant_details = Product::leftJoin('store_product_variants_combination',function($join) {
                    $join->on('store_product_variants_combination.product_id', '=', 'store_products.product_id');
                    $join->where('store_product_variants_combination.is_deleted', '=', 0);
                })->join('store_category',function($join) {
                    $join->on('store_category.category_id', '=', 'store_products.category_id');
                })->leftJoin('store_sub_category',function($join) {
                    $join->on('store_sub_category.sub_category_id', '=', 'store_products.sub_category_id');
                })->where([
                    ['store_products.store_id', '=', $store_id],
                    ['store_products.is_deleted', '=', 0],
                    ['store_products.status', '=', 1],
                    ['store_products.status_type', '=', 'publish'],
                    ['store_category.is_deleted', '=', 0],
                    ['store_category.status', '=', 1],
                ])
                ->when($search_text, function ($query) use ($search_text) {
                    $query->where('store_products.product_name', 'LIKE', '%' . $search_text . '%');
                })
                ->when(($category_id != "all" && $_type != "single_product"), function ($query) use ($category_id) {
                    $query->where('store_products.category_id', $category_id);
                }) 
                ->when($sub_category_id, function ($query) use ($sub_category_id) {
                    $query->where('store_products.sub_category_id',$sub_category_id);
                })
                ->when($productIds, function ($query) use ($productIds) {
                    $query->whereIn('store_products.product_id',$productIds);
                })
                ->whereRaw(('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END'))
                ->select('store_products.product_id','store_products.category_image','store_products.category_id','store_products.sub_category_id','type_of_product','trackable','variants_combination_id','variants_combination_name','variant_price','on_hand','available','store_product_variants_combination.sku','store_product_variants_combination.barcode','unit','product_name')
                ->get()->toArray();    
                $category_array = array(); $sub_category_array = array(); $category_count = array();$sub_category_count = array();$variant_combinations = []; $variant_combination_data = [];
                if(!empty($product_variant_details) && $_type != "single_product") {
                    foreach($product_variant_details as $product) {
                        $category_array[$product['category_id']][$product['product_id']][] = $product;
                        $category_count[$product['category_id']] = count($category_array[$product['category_id']]);
                        if(!empty($product['sub_category_id'])) {
                            $sub_category_array[$product['category_id']][$product['sub_category_id']][$product['product_id']][] = $product;
                            $sub_category_count[$product['category_id']][$product['sub_category_id']] = count($sub_category_array[$product['category_id']][$product['sub_category_id']]);
                        }
                        if(!empty($product['variants_combination_id'])) {
                            $variant_combinations[$product['product_id']][] = $product;
                            $variant_combination_data[$product['variants_combination_id']] = $product;
                        }
                    }
                }
                if($_type != "single_product") {
                    $sub_category_details = SubCategory::select('store_sub_category.category_id','sub_category_name','sub_category_id',DB::raw('(SELECT COUNT(DISTINCT sp.product_id) FROM store_products AS sp LEFT JOIN store_category on sp.category_id = store_category.category_id LEFT JOIN store_product_variants_combination as spvc on sp.product_id = spvc.product_id WHERE sp.sub_category_id = store_sub_category.sub_category_id AND sp.is_deleted = 0 AND status_type = "publish" AND store_sub_category.status = 1 AND store_sub_category.is_deleted = 0 AND (CASE WHEN type_of_product = "single" THEN (trackable = 1 AND unit > 0) OR trackable = 0 WHEN type_of_product = "variant" THEN (on_hand > 0 OR on_hand IS NULL OR on_hand = "") AND spvc.is_deleted = 0 AND spvc.variants_combination_id IS NOT NULL ELSE TRUE END)) AS product_count'))
                    ->where([
                        ['store_sub_category.store_id', '=', $store_id],
                        ['store_sub_category.is_deleted', '=', 0],
                        ['status', '=', 1]
                    ])
                    ->where('store_sub_category.category_id',$category_id)
                    ->orderBy('store_sub_category.category_id','desc')
                    ->get()->toArray();
                }
                $get_product_variants_combinations = Product::leftJoin('store_product_variants_combination', 'store_products.product_id', '=', 'store_product_variants_combination.product_id')
                    ->where('store_products.store_id', $store_id)
                    ->where('store_products.is_deleted', 0)
                    ->where('status_type', 'publish')
                    ->where('type_of_product', 'variant')
                    ->where('store_product_variants_combination.is_deleted', 0)
                    ->when($product_id, function ($query) use ($product_id) {
                        $query->where('store_products.product_id',$product_id);
                    })
                    ->select('variants_combination_id', 'variants_combination_name', 'store_products.product_id', 'variant_price', 'on_hand','variants_id')->get()->toArray();
                $product_variants_combinations = [];
                if(!empty($get_product_variants_combinations)) {
                    foreach($get_product_variants_combinations as $key => $variants) {
                        $variant_product_unit = $available_variants_quantity = $variants['on_hand'];
                        if(!empty($cart_data) && isset($cart_data[$variants['product_id']]) && isset($cart_data[$variants['product_id']][$variants['variants_combination_id']])) {
                            $quantity = $cart_data[$variants['product_id']][$variants['variants_combination_id']]['quantity'];
                            if(!empty($variant_product_unit) && is_numeric($variant_product_unit) && $variant_product_unit >= 0)
                                $available_variants_quantity = ($variant_product_unit - $quantity);
                        }
                        $variants['product_available'] = (is_numeric($available_variants_quantity) && ($available_variants_quantity <= 0)) ? "out-of-stock" : "";
                        $product_variants_combinations[$variants['product_id']][] = $variants;
                    }
                }
                if(!empty($product_details)) {
                    foreach($product_details as $key => $product) {
                        $product_details[$key]['product_variants_collection'] = isset($product_variants_collection[$product->product_id]) ? $product_variants_collection[$product->product_id] : [];
                        $product_details[$key]['product_variants_options'] = isset($productVariantsOptions[$product->product_id]) ?  $productVariantsOptions[$product->product_id] : [];
                        $product_details[$key]['product_variants_combinations'] = isset($product_variants_combinations[$product->product_id]) ? $product_variants_combinations[$product->product_id] : [];
                    }
                }
                $result = array(
                    'category_details' => $category_details,
                    'sub_category_details' => $sub_category_details,
                    'product_details' => $product_details
                );
                return $this->createResponse("Product list according to category", self::HTTP_OK, $store_id,'',$result);
            } else {
                return $checkStoreId;
            }
        }
    }

    public function wishlist(Request $request) 
    {
        $product_details = Product::leftJoin('store_category', 'store_products.category_id', '=', 'store_category.category_id')->leftJoin('store_product_flash_deals', 'store_products.product_id', '=', 'store_product_flash_deals.product_id')->leftJoin('store_product_discount', 'store_products.product_id', '=', 'store_product_discount.product_id')->leftJoin('store_product_stock', 'store_products.product_id', '=', 'store_product_stock.product_id')->leftJoin('wishlist', 'store_products.product_id', '=', 'wishlist.product_id')->where('wishlist.store_id',$request->store_id)->where('store_products.is_deleted',0)->where('wishlist.is_deleted',0)->where('wishlist.customer_id',$request->customer_id)->get(['product_name','store_products.category_id','sub_category_id','unit','minimum_purchase_qty','tags','product_description','category_image','unit_price','discount_valid_from','discount_valid_to','discount_value','discount_type','quantity','sku','store_products.meta_title','store_products.meta_description','store_products.meta_image','low_stock_quantity_count','stock_quantity','stock_with_text','hide_stock','cash_on_delivery','feature_status','today_deal_status','store_products.flash_id','flash_discount','flash_discount_type','shipping_time','store_products.product_id','product_deals_id','product_discount_id','product_stock_id','category_name','wishlist_id',DB::raw("4 as product_rating")]);
        $response = array(
            'message' => 'Wishlist according to customer',
            'data' => $product_details,
            'status' => 200
        );
        return response()->json(['status' =>$response]);
    }

    public function addWishList(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'product_id'=> 'required',
            'customer_id'=> 'required',
            'store_id'=> 'required',
            'type'=> 'required',
        ]);
        if ($validator->fails()) 
            return response()->json(['error'=>$validator->errors()], 401);
        if($request->type == "add") {
            $wishlist_exist = Wishlist::where([
                ['product_id', '=', $request->product_id],
                ['customer_id', '=', $request->customer_id],
                ['store_id','=',$request->store_id],
                ['is_deleted','=',0]
            ])->get()->count();
            if($wishlist_exist == 0)
                Wishlist::create($request->all());
            $message = "Product was added to wishlist successfully";
        } else {
            $remove_wishlist = array();
            $remove_wishlist['is_deleted'] = 1;  
            $remove_wishlist['deleted_at'] = Carbon::now()->toDateTimeString();
            Wishlist::where([
                ['product_id', '=', $request->product_id],
                ['customer_id', '=', $request->customer_id],
                ['store_id','=',$request->store_id]
            ])->update($remove_wishlist);
            $message = "Product was removed from wishlist successfully";
        }
        
        $response = array(
            'message' => $message,
            'status' => 200
        );
        return response()->json(['status' =>$response]);
    } 

}
