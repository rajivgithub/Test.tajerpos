<?php

namespace App\Http\Controllers\CashierAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashierAdmin\PlaceOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CashierAdmin\PlaceOrderRequest;
use Exception;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\Category;
use App\Http\Controllers\CommonController;
use App\Models\StoreAdmin\Product;
use App\Models\Admin\Store;
use Session;
use App\Models\StoreAdmin\SubCategory;
use App\Models\StoreAdmin\Tax;
use App\Models\CashierAdmin\OrderMethods;
use App\Models\CashierAdmin\StoreDiscount;
use Carbon\Carbon;
use App\Models\CashierAdmin\ProductDiscount;
use Illuminate\Support\Collection;

class PlaceOrderController extends Controller
{
    protected $store_url;
    protected $store_logo;
    public function __construct() {
        $this->store_url = CommonController::storeURL();
        $this->store_logo = CommonController::storeLogo();
    }

    public function index(Request $request,$type = null)
    {
        $search_type = session('search_type');
        $type = $request->type;
        $store_url = $this->store_url;
        $store_logo = $this->store_logo;
        $category_id_search = Session::get('category_id_search');
        $product_name = $search_text = Session::get('product_name');
        $discountsQuery = DB::table('store_discount')
            ->leftJoin('store_product_discount', function ($join) {
                $join->on('store_discount.discount_id', '=', 'store_product_discount.discount_id')
                    ->where('store_product_discount.is_deleted', '=', 0)
                    ->where('store_product_discount.status', '=', 1);
            })
            ->where('store_discount.discount_method', '=', 'automatic')
            ->where('store_discount.discount_valid_from', '<=', Carbon::now())
            ->where(function ($query) {
                $query->where('store_discount.discount_valid_to', '>=', Carbon::now())
                    ->orWhereNull('store_discount.discount_valid_to');
            })
            ->where(function ($query) {
                $query->where('store_discount.store_type', '=',"offline")
                    ->orWhere('store_discount.store_type',"both");
            })
            ->where(function ($query) {
                $query->where('store_discount.product_discount_type', 'specific')
                    ->where(function ($query) {
                        $query->whereNotNull('store_product_discount.product_id')
                            ->orWhereNotNull('store_product_discount.variant_id');
                    });
            })
            ->select('store_discount.discount_id','product_discount_type','discount_value','discount_type','product_discount_id','product_id','variant_id');
        $discounts = $discountsQuery->get()->toArray();
        $productDiscounts = array();
        if(!empty($discounts)) {
            foreach ($discounts as $item) {
                $productId = $item->product_id;
                $variantId = $item->variant_id;
                if (!isset($productDiscounts[$productId][$variantId])) {
                    $productDiscounts[$productId][$variantId] = array();
                }
                $productDiscounts[$productId][$variantId][] = $item;
            }
        }
        $subquery = DB::table('store_discount')
            ->where('store_discount.product_discount_type', '=', 'all')
            ->where('store_discount.discount_method', '=', 'automatic')
            ->where('store_discount.discount_valid_from', '<=', Carbon::now())
            ->where(function ($query) {
                $query->where('store_discount.discount_valid_to', '>=', Carbon::now())
                    ->orWhereNull('store_discount.discount_valid_to');
            })
            ->where(function ($query) {
                $query->where('store_discount.store_type', '=',"offline")
                    ->orWhere('store_discount.store_type',"both");
            })
            ->select('store_discount.discount_id', DB::raw('MAX(store_discount.discount_value) as max_discount_value'))
            ->groupBy('store_discount.discount_id');
        $all_discount = DB::table('store_discount AS sd')
            ->joinSub($subquery, 'sub', function ($join) {
                $join->on('sd.discount_id', '=', 'sub.discount_id');
            })
            ->select('sd.discount_id', 'sd.discount_type', 'sub.max_discount_value as max_discount_value')->get()->toArray();        
        $product_variant_details = Product::leftJoin('store_product_variants_combination',function($join) {
            $join->on('store_product_variants_combination.product_id', '=', 'store_products.product_id');
        })->join('store_category',function($join) {
            $join->on('store_category.category_id', '=', 'store_products.category_id');
        })->leftJoin('store_sub_category',function($join) {
            $join->on('store_sub_category.sub_category_id', '=', 'store_products.sub_category_id');
        })->leftJoin('store_product_tax',function($join) {
            $join->on('store_product_tax.product_id', '=', 'store_products.product_id'); 
        })->leftJoin('store_price',function($join) {
            $join->on('store_price.product_id', '=', 'store_products.product_id'); 
        })->where([
            ['store_products.store_id', '=', Auth::user()->store_id],
            ['store_products.is_deleted', '=', 0],
            ['store_products.status', '=', 1],
            ['store_products.status_type', '=', 'publish'],
            ['store_category.is_deleted', '=', 0],
            ['store_category.status', '=', 1],
        ])
        ->whereIn('store_products.product_type', ['instore', 'both'])
        ->when($type == "barcode" && $request->has('barcode') && !empty($request->barcode), function ($query) use ($request) {
            $query->where('store_products.barcode', $request->barcode)
                  ->orWhere('store_product_variants_combination.barcode', $request->barcode);
        })
        ->when($search_type == "search" && !empty($category_id_search) && $category_id_search[0] != 'all' && $category_id_search[0] != '',function ($query) use ($category_id_search) {
            $query->where('store_products.category_id', $category_id_search[0]);
        })
        ->when($search_type == "search" && !empty($product_name) && $product_name[0] != '', function ($query) use ($product_name) {
            $query->where('product_name', 'LIKE', '%' . $product_name[0] . '%');
        })
        ->whereRaw(('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END'))
        ->whereRaw(('case WHEN (store_products.type_of_product = "variant") THEN store_product_variants_combination.is_deleted = 0 ELSE TRUE END'))
        ->select('store_products.product_id','store_products.category_image','store_products.category_id','store_products.sub_category_id','type_of_product','trackable','variants_combination_id','variants_combination_name','variant_price','on_hand','available','store_product_variants_combination.sku','store_product_variants_combination.barcode','unit','product_name','variant_price','category_name','price')
        // ->selectRaw("CASE WHEN taxable = 0 AND tax_type = 'incl_of_tax' THEN variant_price + (variant_price * tax_amount / 100) ELSE variant_price END AS variant_price")
        ->get()->toArray();    
        if($type == "barcode") {
            return response()->json(['product_variant_details' =>$product_variant_details]);exit;
        }
        $category_array = array(); $sub_category_array = array(); $category_count = array();$sub_category_count = array();$variant_combinations = []; $variant_combination_data = [];
        if(!empty($product_variant_details)) {
            foreach($product_variant_details as $product) {
                if(($product['type_of_product'] == 'single' && (($product['trackable'] == 1 && $product['unit'] > 0) || ($product['trackable'] == 0))) || (($product['type_of_product'] == 'variant') && ($product['on_hand'] == NULL || $product['on_hand'] > 0))) {
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
        }
        $category_details_query = Category::select('category_name','category_id','icon','order_number')
            ->where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0],
                ['status', '=', 1]
            ])->orderBy('order_number','asc');
        if($search_type == "search" && !empty($category_id_search) && $category_id_search[0] != 'all' && $category_id_search[0] != '') 
            $category_details_query->where('store_category.category_id',$category_id_search[0]);
        $category_details =  $category_details_query->orderBy('category_id','desc')->get();
        $sub_category_details_query = SubCategory::select('store_sub_category.category_id','sub_category_name','sub_category_id')
        ->where([
            ['store_sub_category.store_id', '=', Auth::user()->store_id],
            ['store_sub_category.is_deleted', '=', 0],
            ['status', '=', 1]
        ])->orderBy('order_number','asc');
        if($search_type == "search" && !empty($category_id_search) && $category_id_search[0] != 'all' && $category_id_search[0] != '')
            $sub_category_details_query->where('store_sub_category.category_id',$category_id_search[0]);
        $sub_category_details = $sub_category_details_query->orderBy('store_sub_category.category_id','desc')->get()->toArray();
        $all_sub_category_details = [];
        if(!empty($sub_category_details)) {
            foreach($sub_category_details as $sub_category) {
                if(array_key_exists($sub_category['category_id'],$sub_category_count) && array_key_exists($sub_category['sub_category_id'],$sub_category_count[$sub_category['category_id']]) && ($sub_category_count[$sub_category['category_id']][$sub_category['sub_category_id']] > 0)) {
                    $sub_category_data = [];
                    $sub_category_data = $sub_category;
                    $all_sub_category_details[$sub_category['category_id']][] = $sub_category_data;
                }
            }
        }
        $product_details_query = Product::leftJoin('store_category', function ($join) {
            $join->on('store_category.category_id', '=', 'store_products.category_id');
        })
        ->leftJoin('store_product_variants_combination as spvc', 'store_products.product_id', '=', 'spvc.product_id')
        ->leftJoin('store_sub_category', 'store_products.sub_category_id', '=', 'store_sub_category.sub_category_id')
        ->leftJoin('store_product_tax', 'store_products.product_id', '=', 'store_product_tax.product_id')
        ->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')
        ->where([
            ['store_products.store_id', '=', Auth::user()->store_id],
            ['store_products.is_deleted', '=', 0],
            ['store_products.status_type', '=', 'publish'],
            ['store_products.status', '=', 1],
            ['store_category.is_deleted', '=', 0],
            ['store_category.status', '=', 1]
        ])
        ->whereIn('store_products.product_type', ['instore', 'both']);
        if ($search_type == "search" && !empty($category_id_search) && $category_id_search[0] != 'all' && $category_id_search[0] != '') {
            $product_details_query->where('store_products.category_id', $category_id_search[0]);
        }
        if ($search_type == "search" && !empty($product_name) && $product_name[0] != '') {
            $product_details_query->where('product_name', 'LIKE', '%' . $product_name[0] . '%');
        }
        $product_details = $product_details_query
            ->whereRaw('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END')
            ->where(function ($query) {
                $query->whereRaw('(CASE WHEN type_of_product = "single" THEN (trackable = 1 AND unit > 0) OR trackable = 0 WHEN type_of_product = "variant" THEN (on_hand > 0 OR on_hand IS NULL  OR on_hand = "") AND spvc.is_deleted = 0 ELSE TRUE END)');
            })
            ->orderBy('category_id', 'desc')
            ->select('store_products.product_id', 'type_of_product', 'product_name', 'store_products.category_id', 'category_name', 'unit_price', 'store_products.category_image', 'store_products.sub_category_id', 'unit', 'trackable', 'tax_amount','price')
            // ->selectRaw("CASE WHEN taxable = 0 AND tax_type = 'incl_of_tax' THEN price + (price * tax_amount / 100) ELSE price END AS price")
            ->distinct('store_products.product_id')
            ->get()->toArray();
        $all_product_details = []; $sub_category_product_details = [];
        if(!empty($product_details)) {
            foreach($product_details as $product) {
                $product_data = [];
                $product_data = $product;
                if($product['sub_category_id'] != "" && !empty($sub_category_array) && array_key_exists($product['category_id'],$sub_category_array) && array_key_exists($product['sub_category_id'],$sub_category_array[$product['category_id']]) && array_key_exists($product['product_id'],$sub_category_array[$product['category_id']][$product['sub_category_id']])) {
                    $sub_category_product_details[$product['category_id']][$product['sub_category_id']][] = $product_data;
                } else if($product['category_id'] != "" && $product['sub_category_id'] == "" && !empty($category_array) && array_key_exists($product['category_id'],$category_array) && array_key_exists($product['product_id'],$category_array[$product['category_id']])) {
                    $all_product_details[$product['category_id']][] = $product_data;
                }
            }
        }
        $get_cart_data = Session::get('cart_data');
        $product_ids = Session::get('product_ids');
        $variant_ids = Session::get('variant_ids');
        $total_cart_quantity = Session::get('total_cart_quantity');
        if(!empty($product_ids)) {
            $product_ids = isset($product_ids[0]) ? $product_ids[0] : $product_ids;
            $variant_ids = (!empty($variant_ids) && isset($variant_ids[0])) ? $variant_ids[0] : $variant_ids;
            $filter_product_details = Product::leftJoin('store_category', 'store_products.category_id', '=', 'store_category.category_id')->leftJoin('store_sub_category', 'store_products.sub_category_id', '=', 'store_sub_category.sub_category_id')->leftJoin('store_product_tax', 'store_products.product_id', '=', 'store_product_tax.product_id')->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')
            ->leftJoin('store_product_variants_combination', 'store_products.product_id', '=', 'store_product_variants_combination.product_id')
            ->where([
                ['store_products.store_id', '=', Auth::user()->store_id],
                ['store_products.is_deleted', '=', 0],
                ['store_products.status_type', '=', 'publish'],
                ['store_products.status', '=', 1],
                ['store_category.is_deleted', '=', 0],
                ['store_category.status', '=', 1],
            ])
            ->whereIn('store_products.product_type', ['instore', 'both'])
            ->whereRaw(('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END'))
            ->when(!empty($variant_ids), function ($query) use ($variant_ids) {
                $query->whereRaw('
                    CASE
                        WHEN store_products.type_of_product = "variant" THEN store_product_variants_combination.variants_combination_id IN (' . implode(',', $variant_ids) . ')
                        ELSE TRUE
                    END
                ');
            })
            ->whereRaw(('case WHEN (store_products.type_of_product = "variant") THEN store_product_variants_combination.is_deleted = 0 ELSE TRUE END'))
            ->whereRaw('case WHEN (type_of_product = "single" AND trackable = 1) THEN store_products.unit > 0 ELSE TRUE END')
            ->whereIn('store_products.product_id',$product_ids)->orderBy('store_products.category_id','desc')
            ->select('product_name','store_products.category_id','category_name','price','store_products.product_id','store_products.category_image','tax_type','tax_amount','taxable','type_of_product','unit','trackable','variants_combination_name','variants_combination_id','variant_price','on_hand')
            ->selectRaw('CASE WHEN (on_hand <= 0 AND on_hand IS NOT NULL AND on_hand != "") THEN "out-of-stock" ELSE "" END as product_available')
            ->get();
            if(!empty($filter_product_details)) {
                $product_array = $filter_product_details->toArray();
                $cart_data = (!empty($get_cart_data) && isset($get_cart_data[0])) ? array_filter($get_cart_data[0]) : [];
                $cart = [];
                if(!empty($product_array) && !empty($cart_data)) {
                    foreach ($cart_data as $key => $cart_item) {
                        foreach($cart_item as $item) {
                            if (isset($item['variants_id'])) {
                                foreach ($product_array as $product) {
                                    if ($product['variants_combination_id'] == $item['variants_id']) {
                                        $cart[$key][$product['variants_combination_id']] = $item;
                                    }
                                }
                            }else {
                                foreach ($product_array as $product) {
                                    if ($product['product_id'] == $key) {
                                        $cart[$key] = $cart_item;
                                    }
                                }
                            }
                        }
                    }
                    Session::forget('cart_data');
                    Session::push('cart_data', $cart);
                    $get_cart_data = Session::get('cart_data');
                    $total_cart_quantity = 0; $filter_product_ids = []; $filter_variants_ids = [];
                    if(!empty($get_cart_data) && isset($get_cart_data[0])) {
                        foreach ($get_cart_data[0] as $key => $cart) {
                            $filter_product_ids[] = $key;
                            if (isset($cart['quantity'])) {
                                $total_cart_quantity += $cart['quantity'];
                            } else {
                                foreach ($cart as $variant) {
                                    $filter_variants_ids[] = $variant['variants_id'];
                                    $total_cart_quantity += $variant['quantity'];
                                }
                            }
                        }
                    }
                    Session::forget('total_cart_quantity');
                    Session::push('total_cart_quantity', $total_cart_quantity);
                    Session::forget('product_ids');
                    Session::push('product_ids', $filter_product_ids);
                    Session::forget('variant_ids');
                    Session::push('variant_ids', $filter_variants_ids);
                    $total_cart_quantity = Session::get('total_cart_quantity');
                    $product_ids = Session::get('product_ids');
                    $variant_ids = Session::get('variant_ids');
                }
            }
        }
        return view('cashier_admin.place_order.list',compact('store_url','category_details','all_product_details','type','variant_combinations','variant_combination_data','get_cart_data', 'product_ids', 'variant_ids', 'total_cart_quantity','all_sub_category_details','sub_category_product_details','category_count','sub_category_count','category_array','store_logo','product_details','search_type','search_text','all_discount','productDiscounts'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $cart_data = $request->cart_data;
        $product_ids = $request->product_ids;
        $variant_ids = $request->variant_ids;
        $total_cart_quantity = $request->total_cart_quantity;
        $customer_name = $request->customer_name;
        $customer_phone_number = $request->customer_phone_number;
        Session::forget('cart_data');
        Session::forget('product_ids');
        Session::forget('variant_ids');
        if($cart_data != "")
            Session::push('cart_data', $cart_data);
        if($product_ids != "")
            Session::push('product_ids', $product_ids);
        if($variant_ids != "")
            Session::push('variant_ids', $variant_ids);
        if(!empty($total_cart_quantity) || $total_cart_quantity == 0) {
            Session::forget('total_cart_quantity');
            Session::push('total_cart_quantity', $total_cart_quantity);
        }
        if(!empty($customer_name)) {
            Session::forget('customer_name');
            Session::push('customer_name', $customer_name);
        }
        if(!empty($customer_phone_number)) {
            Session::forget('customer_phone_number');
            Session::push('customer_phone_number', $customer_phone_number);
        }
        return response()->json(['status' =>200]);
    }

    public function view_cart()
    {
        $store_url = $this->store_url;
        $store_logo = $this->store_logo;
        $get_cart_data = Session::get('cart_data');
        $product_ids = Session::get('product_ids');
        $variant_ids = Session::get('variant_ids');
        $customer_name = Session::get('customer_name');
        $customer_phone_number = Session::get('customer_phone_number');
        $discountsQuery = DB::table('store_discount')
        ->leftJoin('store_product_discount', function ($join) {
            $join->on('store_discount.discount_id', '=', 'store_product_discount.discount_id')
                ->where('store_product_discount.is_deleted', '=', 0)
                ->where('store_product_discount.status', '=', 1);
        })
        ->where('store_discount.discount_method', '=', 'automatic')
        ->where('store_discount.discount_valid_from', '<=', Carbon::now())
        ->where(function ($query) {
            $query->where('store_discount.discount_valid_to', '>=', Carbon::now())
                ->orWhereNull('store_discount.discount_valid_to');
        })
        ->where(function ($query) {
            $query->where('store_discount.store_type', '=',"offline")
                ->orWhere('store_discount.store_type',"both");
        })
        ->where(function ($query) {
            $query->where('store_discount.product_discount_type', 'specific')
                ->where(function ($query) {
                    $query->whereNotNull('store_product_discount.product_id')
                        ->orWhereNotNull('store_product_discount.variant_id');
                });
        })
        ->select('store_discount.discount_id','product_discount_type','discount_value','discount_type','product_discount_id','product_id','variant_id');
        $discounts = $discountsQuery->get()->toArray();
        $productDiscounts = array();
        if(!empty($discounts)) {
            foreach ($discounts as $item) {
                $productId = $item->product_id;
                $variantId = $item->variant_id;
                if (!isset($productDiscounts[$productId][$variantId])) {
                    $productDiscounts[$productId][$variantId] = array();
                }
                $productDiscounts[$productId][$variantId][] = $item;
            }
        }
        $subquery = DB::table('store_discount')
            ->where('store_discount.product_discount_type', '=', 'all')
            ->where('store_discount.discount_method', '=', 'automatic')
            ->where('store_discount.discount_valid_from', '<=', Carbon::now())
            ->where(function ($query) {
                $query->where('store_discount.discount_valid_to', '>=', Carbon::now())
                    ->orWhereNull('store_discount.discount_valid_to');
            })
            ->where(function ($query) {
                $query->where('store_discount.store_type', '=',"offline")
                    ->orWhere('store_discount.store_type',"both");
            })
            ->select('store_discount.discount_id', DB::raw('MAX(store_discount.discount_value) as max_discount_value'))
            ->groupBy('store_discount.discount_id');

        $all_discount = DB::table('store_discount AS sd')
            ->joinSub($subquery, 'sub', function ($join) {
                $join->on('sd.discount_id', '=', 'sub.discount_id');
                    // ->on('sd.discount_value', '=', 'sub.max_discount_value');
            })
            ->select('sd.discount_id', 'sd.discount_type', 'sub.max_discount_value as max_discount_value')->get()->toArray();
        $product_id = []; $quantity = []; $product_details = [];$variant_combinations = []; $variant_combination_data = []; $variant_id = []; $get_quantity = []; $discount_data = [];
        if(!empty($get_cart_data)) {
            foreach($get_cart_data[0] as $k => $product) {
                if(!empty($product)) {
                    foreach($product as $key => $val) {
                        if(is_array($val)) {
                            $quantity[$k] = count($product);
                            $variant_id[$k][] = $key;
                            $get_quantity[$k][] = $val['quantity'];
                            if(isset($val['discount_id']))
                                $discount_data[$k][$key] = $val['discount_id'];
                        } else {
                            $get_quantity[$k] = $product['quantity'];
                            if(isset($product['discount_id']))
                                $discount_data[$k] = $product['discount_id'];
                        }
                    }
                }
            }
        }
        if(!empty($product_ids)) {
            $product_details = Product::leftJoin('store_category', 'store_products.category_id', '=', 'store_category.category_id')->leftJoin('store_sub_category', 'store_products.sub_category_id', '=', 'store_sub_category.sub_category_id')->leftJoin('store_product_tax', 'store_products.product_id', '=', 'store_product_tax.product_id')->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')->where([
                ['store_products.store_id', '=', Auth::user()->store_id],
                ['store_products.is_deleted', '=', 0],
                ['store_products.status_type', '=', 'publish'],
                ['store_products.status', '=', 1],
                ['store_category.is_deleted', '=', 0],
                ['store_category.status', '=', 1],
            ])
            ->whereRaw(('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END'))
            ->whereRaw('case WHEN (type_of_product = "single" AND trackable = 1) THEN store_products.unit > 0 ELSE TRUE END')
            ->whereIn('store_products.product_id',$product_ids[0])->orderBy('category_id','desc')
            ->select('product_name','store_products.category_id','category_name','price','store_products.product_id','store_products.category_image','tax_type','tax_amount','taxable','type_of_product','unit','trackable','price')
            // ->selectRaw("CASE WHEN taxable = 0 AND tax_type = 'incl_of_tax' THEN price + (price * tax_amount / 100) ELSE price END AS price")
            ->get();
            $product_variant_details = Product::leftJoin('store_product_variants_combination', 'store_products.product_id', '=', 'store_product_variants_combination.product_id')
            ->leftJoin('store_product_tax', 'store_products.product_id', '=', 'store_product_tax.product_id')->where([
                ['store_products.store_id', '=', Auth::user()->store_id],
                ['store_products.is_deleted', '=', 0],
                ['store_product_variants_combination.is_deleted', '=', 0],
                ['store_products.status_type', '=', 'publish']
            ])->whereIn('store_products.product_id',$product_ids[0])
            ->select('store_products.product_id','trackable','unit','variants_combination_id','variants_combination_name','variant_price','on_hand','available','store_product_variants_combination.sku','store_product_variants_combination.barcode','type_of_product')
            // ->selectRaw("CASE WHEN taxable = 0 AND tax_type = 'incl_of_tax' THEN variant_price + (variant_price * tax_amount / 100) ELSE variant_price END AS variant_price")
            ->get()->toArray();
            if(!empty($product_variant_details)) {
                foreach($product_variant_details as $variant) {
                    if(($variant['type_of_product'] == 'single' && (($variant['trackable'] == 1 && $variant['unit'] > 0) || ($variant['trackable'] == 0))) || (($variant['type_of_product'] == 'variant') && ($variant['on_hand'] == NULL || $variant['on_hand'] > 0))) {
                        if(!empty($variant['variants_combination_id']))
                            $variant_combinations[$variant['product_id']][] = $variant;
                        if(!empty($variant['variants_combination_id']))
                            $variant_combination_data[$variant['variants_combination_id']] = $variant;
                    }
                }
            }
        }
        $address_details = Store::leftJoin('countries', 'stores.store_country', '=', 'countries.id')->leftJoin('states', 'stores.store_state', '=', 'states.id')->leftJoin('cities', 'stores.store_city', '=', 'cities.id')->where('store_id',Auth::user()->store_id)->get(['store_name','store_address','cities.name as city_name','states.name as state_name','countries.name as country_name','store_logo']);        
        $tax_details = Tax::where('store_id',Auth::user()->store_id)->get(['tax_percentage','tax_id']);
        $prefer_details = OrderMethods::select('order_methods','order_methods_id')
            ->where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0],
                ['status', '=', 1]
            ])->orderBy('order_number','asc')->get();
        return view('cashier_admin.place_order.view_cart',compact('store_url','product_details','variant_combinations','variant_combination_data','address_details','quantity','get_cart_data','variant_id','get_quantity','customer_name','customer_phone_number','store_logo','tax_details','prefer_details','productDiscounts','all_discount','discount_data'));
    }

    public function couponCodeDetails(Request $request)
    {
        $discountsQuery = DB::table('store_discount')
        ->leftJoin('store_product_discount', function ($join) {
            $join->on('store_discount.discount_id', '=', 'store_product_discount.discount_id')
                ->where('store_product_discount.is_deleted', '=', 0)
                ->where('store_product_discount.status', '=', 1);
        })
        ->where('store_discount.discount_method', '=', 'code')
        ->where('store_discount.discount_name', '=', $request->coupon_code)
        ->where('store_discount.discount_valid_from', '<=', Carbon::now())
        ->where(function ($query) {
            $query->where('store_discount.discount_valid_to', '>=', Carbon::now())
                ->orWhereNull('store_discount.discount_valid_to');
        })
        ->where(function ($query) {
            $query->where('store_discount.store_type', '=',"offline")
                ->orWhere('store_discount.store_type',"both");
        })
        ->select('store_discount.discount_id','product_discount_type','discount_value','discount_type','product_discount_id','product_id','variant_id','min_require_type','min_value','max_discount_uses','max_value','once_per_order');
        $discounts = $discountsQuery->get()->toArray();
        return response()->json(['store_discount' =>$discounts]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
