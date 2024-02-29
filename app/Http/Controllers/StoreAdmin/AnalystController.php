<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Models\CashierAdmin\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CashierAdmin\OrderItems;
use App\Models\StoreAdmin\Product;
use Illuminate\Support\Facades\Crypt;
use App\Models\CashierAdmin\InStoreCustomer;
use App\Models\User;

class AnalystController extends Controller
{
    protected $store_url,$store_logo;
    public function __construct() {
        $this->store_url = CommonController::storeURL();
        $this->store_logo = CommonController::storeLogo();
    }

    public function index() {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        return view('store_admin.analyst',compact('store_url','store_logo'));
    }

    public function analystReport(Request $request) {
        $analyst_start_date = $request->analyst_start_date;
        $analyst_end_date = $request->analyst_end_date;
        $analyst_filter_type = $request->analyst_filter_type;
        if($analyst_filter_type == "sales" || $analyst_filter_type == "sales_tax") {
            $total_sale_amount_query = Order::select('total_amount','tax_amount')->where([
                ['store_id','=',Auth::user()->store_id],
                ['status','=',1],
                ['is_deleted','=',0]
            ]);
            if(!empty($analyst_start_date))
                $total_sale_amount_query->where('created_at',">=",$analyst_start_date);
            if(!empty($analyst_end_date))
                $total_sale_amount_query->where('created_at',"<=",$analyst_end_date);
            $total_sale_amount_query->get();
            if($analyst_filter_type == "sales") {
                $total_sale_amount = $total_sale_amount_query->sum('total_amount');
                $title = trans('store-admin.sales_overview');
                $sub_title = trans('store-admin.sales');
            }
            if($analyst_filter_type == "sales_tax") {
                $total_sale_amount = $total_sale_amount_query->sum('tax_amount');
                $title = trans('store-admin.sales_tax_overview');
                $sub_title = trans('store-admin.sales_tax');
            }
            return response()->json(['total_sale_amount'=>$total_sale_amount, 'status'=>200,'title'=>$title,'sub_title'=>$sub_title]);
        } else if($analyst_filter_type == "top_products") {
            $product_id_query = OrderItems::leftJoin('store_products', 'store_order_items.product_id', '=', 'store_products.product_id')
                ->select('store_order_items.product_id', DB::raw('COUNT(store_order_items.product_id) as total_sales'))
                ->where([
                    ['store_products.status', '=', 1],
                    ['store_products.is_deleted', '=', 0],
                    ['store_order_items.store_id', '=', Auth::user()->store_id],
                    ['store_order_items.status', '=', 1],
                    ['store_order_items.is_deleted', '=', 0]
                ]);
            if(!empty($analyst_start_date))
                $product_id_query->where('store_order_items.created_at',">=",$analyst_start_date);
            if(!empty($analyst_end_date))
                $product_id_query->where('store_order_items.created_at',"<=",$analyst_end_date);
            $productIds = $product_id_query->groupBy('store_order_items.product_id')
                ->orderByDesc('total_sales')
                ->limit(3)
                ->pluck('store_order_items.product_id');
            $products = Product::select('product_id','product_name')->whereIn('product_id', $productIds)->get()
                ->map(function ($product) {
                    $encryptedId = Crypt::encrypt($product->product_id);
                    $url = route(config('app.prefix_url') . '.' . $this->store_url . '.' . config('app.module_prefix_url') . '.product.show', $encryptedId);
                    $product->url = $url;
                    return $product;
                });
            return response()->json(['analyst_result'=>$products, 'status'=>200,'title'=>trans('store-admin.top_products_overview'), 'sub_title'=>trans('store-admin.top_product')]);
        }  else if($analyst_filter_type == "payment_method") {
            $total_amount_query = DB::table('store_order_details')
                ->join('payment_details', 'store_order_details.order_id', '=', 'payment_details.order_id')
                ->select(
                    DB::raw('SUM(CASE WHEN payment_method = "cash" THEN amount ELSE 0 END) AS cash_amount'),
                    DB::raw('SUM(CASE WHEN payment_method = "mada_card" THEN amount ELSE 0 END) AS mada_card_amount'),
                    DB::raw('SUM(CASE WHEN payment_method = "visa_card" THEN amount ELSE 0 END) AS visa_card_amount')
                )
                ->where('payment_details.status', 1)
                ->where('payment_details.is_deleted', 0)
                ->where('store_order_details.store_id', Auth::user()->store_id)
                ->where('store_order_details.status', 1)
                ->where('store_order_details.is_deleted', 0)
                ->whereIn('payment_method', ['cash', 'mada_card', 'visa_card']);
            if(!empty($analyst_start_date))
                $total_amount_query->where('store_order_details.created_at',">=",$analyst_start_date);
            if(!empty($analyst_end_date))
                $total_amount_query->where('store_order_details.created_at',"<=",$analyst_end_date);
            $totalAmounts = $total_amount_query->get();
                $cashAmount = $totalAmounts->sum('cash_amount');
                $madaCardAmount = $totalAmounts->sum('mada_card_amount');
                $visaCardAmount = $totalAmounts->sum('visa_card_amount');
                $total_amount = [$cashAmount,$madaCardAmount,$visaCardAmount];
                $title = [trans('store-admin.cash_collection'),trans('store-admin.mada_card_collection'),trans('store-admin.visa_card_collection')];
                $sub_title = [trans('store-admin.cash'),trans('store-admin.mada_card'),trans('store-admin.visa_card')];
            return response()->json(['total_amount'=>$total_amount,'status'=>200,'title'=>$title,'sub_title'=>$sub_title]);
        } else if($analyst_filter_type == "top_customer") {
            $customer_id_query = Order::join('instore_customers', 'store_order_details.customer_id', '=', 'instore_customers.customer_id')
                ->select('store_order_details.customer_id', DB::raw('COUNT(store_order_details.customer_id) as total_customers'))
                ->where([
                    ['instore_customers.status', '=', 1],
                    ['instore_customers.is_deleted', '=', 0],
                    ['store_order_details.store_id', '=', Auth::user()->store_id],
                    ['store_order_details.status', '=', 1],
                    ['store_order_details.is_deleted', '=', 0]
                ]);
            if(!empty($analyst_start_date))
                $customer_id_query->where('store_order_details.created_at',">=",$analyst_start_date);
            if(!empty($analyst_end_date))
                $customer_id_query->where('store_order_details.created_at',"<=",$analyst_end_date);
            $customer_ids = $customer_id_query->groupBy('store_order_details.customer_id')
                ->orderByDesc('total_customers')
                ->limit(3)
                ->pluck('store_order_details.customer_id');
            $customers = InStoreCustomer::select('customer_name','phone_number')->whereIn('customer_id', $customer_ids)->get();
            return response()->json(['analyst_result'=>$customers, 'status'=>200,'title'=>trans('store-admin.customer_overview')]);
        } else if($analyst_filter_type == "sales_by_staff") {
            $customer_id_query = Order::join('users', 'store_order_details.created_by', '=', 'users.id')
                ->select('store_order_details.created_by', DB::raw('COUNT(store_order_details.created_by) as total_store_user'))
                ->where([
                    ['users.status', '=', 1],
                    ['users.is_deleted', '=', 0],
                    ['store_order_details.store_id', '=', Auth::user()->store_id],
                    ['store_order_details.status', '=', 1],
                    ['store_order_details.is_deleted', '=', 0]
                ]);
            if(!empty($analyst_start_date))
                $customer_id_query->where('store_order_details.created_at',">=",$analyst_start_date);
            if(!empty($analyst_end_date))
                $customer_id_query->where('store_order_details.created_at',"<=",$analyst_end_date);
            $customer_ids = $customer_id_query->groupBy('store_order_details.created_by')
                ->orderByDesc('total_store_user')
                ->limit(3)
                ->pluck('store_order_details.created_by');
            $customers = User::select('name','email','phone_number')->whereIn('id', $customer_ids)->get()->toArray();
            return response()->json(['analyst_result'=>$customers, 'status'=>200,'title'=>trans('store-admin.top_staff')]);
        }
    }
}
