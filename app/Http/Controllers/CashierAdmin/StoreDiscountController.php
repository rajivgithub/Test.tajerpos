<?php

namespace App\Http\Controllers\CashierAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashierAdmin\StoreDiscount;
use App\Models\CashierAdmin\ProductDiscount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Http\Controllers\CommonController;
use App\Models\StoreAdmin\Product;
use App\Models\StoreAdmin\VariantsOptionCombination;
use Illuminate\Support\Facades\DB;

class StoreDiscountController extends Controller
{
    protected $store_url,$prefix_url,$store_logo;
    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
        $this->prefix_url = config('app.module_prefix_url');
        $this->store_logo = CommonController::storeLogo();
    }

    public function index()
    {
        $store_url = $this->store_url;
        $store_logo = $this->store_logo;
        $currentDate = Carbon::now()->toDateString();
        $store_discount = StoreDiscount::select('store_type','discount_name','product_discount_type','discount_method','discount_valid_from','discount_valid_to','discount_value','discount_type','discount_id', DB::raw("DATE_FORMAT(discount_valid_from, '%d-%m-%Y') AS discount_valid_from"),
        DB::raw("DATE_FORMAT(discount_valid_to, '%d-%m-%Y') AS discount_valid_to"))
        ->selectRaw("CASE 
            WHEN discount_valid_from <= '{$currentDate}' AND (discount_valid_to IS NULL OR discount_valid_to >= '{$currentDate}') THEN 'Active'
            WHEN discount_valid_to IS NOT NULL AND discount_valid_to < '{$currentDate}' THEN 'Expired'
            WHEN discount_valid_from > '{$currentDate}' THEN 'Scheduled'
            ELSE 'Unknown'
        END AS discount_status")
        ->where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0]
        ])->orderBy("discount_id","desc")->get()->toArray();
        return view('cashier_admin.store_discount.list',compact('store_url','store_discount','store_logo'));
    }

    public function create($id=null)
    {
        $store_url = $this->store_url;
        $store_logo = $this->store_logo;
        $mode = !empty($id) ? 'edit' : 'add';
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
        $product_variant_details = $product_variant_details_query->whereRaw(('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END'))
        ->select('store_products.product_id','store_products.category_image','store_products.category_id','store_products.sub_category_id','type_of_product','trackable','variants_combination_id','variants_combination_name','variant_price','on_hand','available','store_product_variants_combination.sku','store_product_variants_combination.barcode','unit','product_name','variant_price','category_name','price')
        ->get()->toArray(); 
        $discount_details = []; $product_data = []; $save_product_variant = [];
        if(!empty($id)) {
            $discount_details = StoreDiscount::where([
                ['discount_id', '=', Crypt::decrypt($id)],
                ['is_deleted', '=', 0],
            ])->get(['discount_name','product_discount_type','discount_valid_from','discount_valid_to','discount_value','discount_type','discount_id','discount_method','min_require_type','min_value','max_discount_uses','max_value','once_per_order','store_type']);
            $product_discount_details = ProductDiscount::where([
                ['discount_id', '=', Crypt::decrypt($id)],
                ['is_deleted', '=', 0],
            ])->get(['product_id','variant_id','product_discount_id'])->toArray();
            if(!empty($product_discount_details)) {
                foreach($product_discount_details as $key=>$discount) {
                    $save_product_variant[$key]['product_id'] = $discount['product_id'];
                    $save_product_variant[$key]['variant_id'] = $discount['variant_id'];
                    $product_data[$discount['product_id']][] = $discount['variant_id'];
                }
            }
        }
        return view('cashier_admin.store_discount.create',compact('store_url','store_logo','product_variant_details','mode','discount_details','product_data','save_product_variant'));
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $specific_product_data = json_decode($input['specific_product_data']);
        $mode = $input['mode'];
        $discount_id = ($mode == "edit") ? Crypt::decrypt($input['discount_id']) : 0;
        $store_id = Auth::user()->store_id;
        $input['discount_name'] = ($input['discount_method'] == "code") ? $input['discount_code'] : $input['discount_name'];
        $input['once_per_order'] = ($input['discount_method'] == "code" && isset($input['once_per_order'])) ? $input['once_per_order'] : 0;
        $input['min_value'] = ($input['min_require_type'] == "quantity") ? $input['min_quantity'] : ($input['min_require_type'] == "amount") ? $input['min_amount'] : '';
        $input['max_value'] = ($input['max_discount_uses'] == "multiple") ? $input['discounts_limit'] : ($input['max_discount_uses'] == "max_discount") ? $input['discounts_amount_limit'] : '';
        $remove_array_values = array('_token','mode','specific_product_data','discount_id','discount_code','min_amount','min_quantity','discounts_limit','discounts_amount_limit');
        foreach($remove_array_values as $value) {
            unset($input[$value]);
        }
        DB::beginTransaction();
        if($mode == "add") {
            $input['created_by'] = Auth::user()->id;
            $input['store_id'] = Auth::user()->store_id;
            $discount_id = StoreDiscount::create($input)->discount_id;
        } else {
            $input['updated_by'] = Auth::user()->id;
            StoreDiscount::where('discount_id',$discount_id)->update($input);
            $update_discounts = array();
            $update_discounts['is_deleted'] = 1;
            ProductDiscount::where([
                ['discount_id', '=', $discount_id],
                ['store_id','=',Auth::user()->store_id]
            ])->update($update_discounts);
        }
        if(!empty($specific_product_data)) {
            foreach($specific_product_data as $product_data) {
                $insert_data = [];
                $insert_data['discount_id'] = $discount_id;
                $insert_data['product_id'] = $product_data->product_id;
                $insert_data['variant_id'] = (isset($product_data->variant_id)) ? $product_data->variant_id : 0;
                $insert_data['created_by'] = Auth::user()->id;
                $insert_data['store_id'] = Auth::user()->store_id;
                ProductDiscount::create($insert_data);
            }
        }
        DB::commit();
        $success_message = ($mode == "add") ? trans('store-admin.added_msg',['name'=>trans('store-admin.discount')]) : trans('store-admin.updated_msg',['name'=>trans('store-admin.discount')]);
        $prefix_url = config('app.module_prefix_url');
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.store-discount.index')->with('message',$success_message);
    }

    public function show($id)
    {
        //
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
        $discount_id = Crypt::decrypt($id);
        $delete_discount = array();
        $delete_discount['is_deleted'] = 1;  
        $delete_discount['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_discount['updated_by'] = Auth::user()->id;
        StoreDiscount::where('discount_id',$discount_id)->update($delete_discount);
        ProductDiscount::where('discount_id',$discount_id)->update($delete_discount);
        $prefix_url = config('app.module_prefix_url');
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.store-discount.index')->with('message',trans('store-admin.deleted_msg',['name'=>trans('store-admin.discount')]));
    }
}
