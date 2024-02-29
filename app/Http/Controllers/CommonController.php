<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\Cities;
use App\Models\Country;
use App\Models\Admin\Store;
use App\Models\StoreAdmin\Category;
use Session;
use DB;
use App\Models\CashierAdmin\Order;
use Intervention\Image\Facades\Image;

class CommonController extends Controller
{

    public function stateList(Request $request) {
        $states = State::where('country_id',$request->country_id)->get(['id','name']);
        return response()->json(['states'=>$states]);
    } 

    public function cityList(Request $request) {
        $cities = Cities::where('state_id',$request->state_id)->get(['id','name']);
        return response()->json(['cities'=>$cities]);
    }

    public function countryList(Request $request) {
        $countries = Country::get(['id','name']);
        return response()->json(['countries'=>$countries]);
    }

    public function timezoneList(Request $request) {
        $timezone = Country::where('id',$request->country_id)->get(['id','zone_name']);
        return response()->json(['timezone'=>$timezone]);
    }

    public function get_store_id() {
        $store_url = CommonController::storeURL();
        $store = Store::where([
            'store_url' => $store_url,
            'web_status' => 1,
            'status' => 1,
            'is_deleted' => 0,
        ])->select('store_id')->first();
        if ($store) {
            return $store->store_id;
        } else {
            return 0;
        }
    }

    public function get_store_details() {
        $store_url = CommonController::storeURL();
        return Store::leftJoin('users', 'users.store_id', '=', 'stores.store_id')
        ->leftJoin('countries', 'stores.store_country', '=', 'countries.id')->leftJoin('states', 'stores.store_state', '=', 'states.id')->leftJoin('cities', 'stores.store_city', '=', 'cities.id')
        ->where([
            ['store_url', '=', $store_url], 
            ['web_status', '=', 1],
            ['stores.status', '=', 1],
            ['stores.is_deleted', '=', 0],
            ['is_store','=','Yes']
        ])->get(['stores.store_id','store_phone_number','email','store_address','cities.name as city_name','states.name as state_name','countries.name as country_name','store_logo','store_name'])->toArray();
    }

    public function get_category_details(Request $request) {
        $store_id = CommonController::get_store_id();
        $type = $request->input('type');
        $category_details = Category::where([
            ['store_id', '=', $store_id], 
            ['is_deleted', '=', 0],
            ['status','=',1]
        ])->get(['category_id','category_name','banner','category_image',DB::raw('(SELECT COUNT(DISTINCT sp.product_id) FROM store_products AS sp LEFT JOIN store_sub_category on sp.sub_category_id = store_sub_category.sub_category_id LEFT JOIN store_product_variants_combination as spvc on sp.product_id = spvc.product_id WHERE sp.category_id = store_category.category_id AND sp.is_deleted = 0 AND status_type = "publish" AND (CASE WHEN sp.sub_category_id > 0 THEN store_sub_category.status = 1 AND store_sub_category.is_deleted = 0 ELSE TRUE END) AND (CASE WHEN type_of_product = "single" THEN (trackable = 1 AND unit > 0) OR trackable = 0 WHEN type_of_product = "variant" THEN (on_hand > 0 OR on_hand IS NULL OR on_hand = "") AND spvc.is_deleted = 0 AND spvc.variants_combination_id IS NOT NULL ELSE TRUE END)) AS product_count')]);
        if($type == "category-list")
            return response()->json(['category_details'=>$category_details]);
        else
            return $category_details;
    }

    public function storeURL() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        return (!empty($split_url)) ?$split_url[$split_url_index] : '';
    }

    public function storeLogo() {
        $store_url = CommonController::storeURL();
        return Store::where([
            ['store_url', '=', $store_url], 
            ['web_status', '=', 1],
            ['status', '=', 1],
            ['is_deleted', '=', 0]
        ])->get('store_logo')->toArray();
    }

    public function categorySearch(Request $request) {
        Session::forget('category_id_search');
        Session::forget('product_name');
        $category_id = $request->category_id;
        $product_name = $request->product_name;
        $type = $request->type;
        $store_url = CommonController::storeURL();
        Session::push('category_id_search', $category_id);
        Session::push('product_name', $product_name);
        if($type == "save")
            return true;
        else {
            return redirect()->route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.index')->with([
                'search_type' => $type
            ]);
        }
    }

    public function invoice($id)
    {
        $store = DB::table('store_order_details')->leftJoin('stores','stores.store_id','=','store_order_details.store_id')->where('store_order_details.order_number',$id)->first();
        $store_url = $store->store_url; 
        $store_logo = $store->store_logo;
        $store_order_details = Order::leftJoin('store_place_order_prefer', 'store_place_order_prefer.prefer_order_id', '=', 'store_order_details.order_type_id')->leftJoin('store_order_items', 'store_order_items.order_id', '=', 'store_order_details.order_id')->leftJoin('instore_customers', 'instore_customers.customer_id', '=', 'store_order_details.customer_id')->leftJoin('store_products', 'store_products.product_id', '=', 'store_order_items.product_id')->where('store_order_details.order_number',$id)->get(['store_order_details.order_number','store_order_details.created_at','store_order_details.tax_amount as total_tax_amount','product_name','sub_total_amount','quantity','total_amount','store_order_items.product_id','status_name','product_variants','store_order_items.sub_total as product_price','store_order_items.tax_amount as product_tax','paid_amount','customer_name','phone_number']);
        $address_details = Store::leftJoin('countries', 'stores.store_country', '=', 'countries.id')->leftJoin('states', 'stores.store_state', '=', 'states.id')->leftJoin('cities', 'stores.store_city', '=', 'cities.id')->where('store_id',$store->store_id)->get(['store_name','store_address','cities.name as city_name','states.name as state_name','countries.name as country_name']);
        return view('cashier_admin.store_order.customerview',compact('store','store_url','store_order_details','address_details','store_logo'));
    }

    public function uploadImage($image, $destinationPath, $url, $store_id, $width = null, $height = null)
    {
        $filename = uniqid() . date('YmdHis') . '.' . $image->getClientOriginalExtension();
        $fullPath = base_path($destinationPath);

        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        $imagePath = $fullPath . '/' . $filename;
        $imageUrl = $url . $destinationPath . '/' . $filename;

        $img = Image::make($image->path());

        if ($width && $height) {
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            })->encode(null, 95);
        }
        $img->save($imagePath);

        return $imageUrl;
    }
}
