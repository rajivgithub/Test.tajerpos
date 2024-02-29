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
use App\Models\CashierAdmin\StorePlaceOrderPrefer;
use App\Models\CashierAdmin\StoreDiscount;

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
        $product_name = Session::get('product_name');
        $product_variant_details_query = Product::leftJoin('store_product_variants_combination',function($join) {
            $join->on('store_product_variants_combination.product_id', '=', 'store_products.product_id');
            $join->where('store_product_variants_combination.is_deleted', '=', 0);
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
        ]);
        if($type == "barcode") {
            $barcode = $request->barcode;
            $product_variant_details_query->where('store_products.barcode',$barcode);
            $product_variant_details_query->orWhere('store_product_variants_combination.barcode',$barcode);
        }
        if($search_type == "search" && !empty($category_id_search) && $category_id_search[0] != 'all' && $category_id_search[0] != '')
            $product_variant_details_query->where('store_products.category_id',$category_id_search[0]);
        if($search_type == "search" && !empty($product_name) && $product_name[0] != '') 
            $product_variant_details_query->where('product_name','LIKE','%'.$product_name[0].'%');
        $product_variant_details = $product_variant_details_query->whereRaw(('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END'))
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
        $category_details_query = Category::select('category_name','category_id','icon')
            ->where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0],
                ['status', '=', 1]
            ]);
        if($search_type == "search" && !empty($category_id_search) && $category_id_search[0] != 'all' && $category_id_search[0] != '') 
            $category_details_query->where('store_category.category_id',$category_id_search[0]);
        $category_details =  $category_details_query->orderBy('category_id','desc')->get();
        $sub_category_details_query = SubCategory::select('store_sub_category.category_id','sub_category_name','sub_category_id')
        ->where([
            ['store_sub_category.store_id', '=', Auth::user()->store_id],
            ['store_sub_category.is_deleted', '=', 0],
            ['status', '=', 1]
        ]);
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
        ]);
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
        return view('cashier_admin.place_order.list',compact('store_url','category_details','all_product_details','type','variant_combinations','variant_combination_data','get_cart_data', 'product_ids', 'variant_ids', 'total_cart_quantity','all_sub_category_details','sub_category_product_details','category_count','sub_category_count','category_array','store_logo','product_details','search_type'));
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
        $product_id = []; $quantity = []; $product_details = [];$variant_combinations = []; $variant_combination_data = []; $variant_id = []; $get_quantity = [];
        if(!empty($get_cart_data)) {
            foreach($get_cart_data[0] as $k => $product) {
                if(!empty($product)) {
                    foreach($product as $key => $val) {
                        if(is_array($val)) {
                            $quantity[$k] = count($product);
                            $variant_id[$k][] = $key;
                            $get_quantity[$k][] = $val['quantity'];
                        } else {
                            $get_quantity[$k] = $val;
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
        $prefer_details = StorePlaceOrderPrefer::select('status_name','store_id')
            ->where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0],
                ['status', '=', 1]
            ])->get();
        $discount_details = StoreDiscount::select('discount_name','value','store_id')
            ->where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0],
                ['status', '=', 1]
            ])->get();
        return view('cashier_admin.place_order.view_cart',compact('store_url','product_details','variant_combinations','variant_combination_data','address_details','quantity','get_cart_data','variant_id','get_quantity','customer_name','customer_phone_number','store_logo','tax_details'));
    }

    public function edit($id)
    {
        //
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
