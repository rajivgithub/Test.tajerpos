<?php

namespace App\Http\Controllers\CashierAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashierAdmin\Order;
use App\Models\CashierAdmin\OrderItems;
use App\Models\CashierAdmin\PaymentDetails;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\Http\Controllers\CommonController;
use App\Models\Admin\Store;
use Session;
use App\Models\StoreAdmin\Product;
use App\Models\StoreAdmin\VariantsOptionCombination;
use App\Models\CashierAdmin\StoreOrderStatus;
use App\Models\CashierAdmin\InStoreCustomer;
use URL;
use App\Models\CashierAdmin\OrderMethods;

class StoreOrderController extends Controller
{
    protected $store_url;
    protected $store_logo;
    public function __construct() {
        $this->store_url = CommonController::storeURL();
        $this->store_logo = CommonController::storeLogo();
    }

    public function index(Request $request)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $prefix_url = config('app.module_prefix_url');
        $store_order_status = OrderMethods::select('order_methods','order_methods_id')
        ->where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
            ['status', '=', 1]
        ])->orderBy('order_number','asc')->get();
        if($request->type != "") {
            $final_data=array();
            $columns = array( 
              0=> 'checkbox', 
              1=> 'order_id',
              2=> 'order_number',
              3=> 'customer_name',
            //   4=> 'email',
              5=> 'ordered_at',
              6=> 'status_name',
              7=> 'total_amount',
              8=> 'action'
            );
            $limit = $request->length;
            $start = $request->start; 
            $order = ($columns[$request->order[0]['column']] == "order_id") ? 'order_id' : $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $where_cond = 'where store_order_details.is_deleted = 0 AND store_order_details.store_id = "'.Auth::user()->store_id.'"';
            if(($request->type == 'store_order_status') && !empty($request->filter_value))
                $where_cond .= ' AND order_methods = "'.$request->filter_value.'"';
            if(!empty($request->search['value'])) {
                $search_value = $request->search['value'];
                $search = (strpos($search_value, "SAR") !== false) ? trim($search_value,"SAR") : $search_value;
                $search = ((strpos($search, ".00") !== false) || (strpos($search, ".0")) !== false) ? round($search) : $search;
                $where_cond .= " AND (store_order_details.order_number LIKE '%".trim($search)."%' or order_methods LIKE '%".trim($search)."%' or no_of_products LIKE '%".trim($search)."%' or total_amount LIKE '%".trim($search)."%'  or payment_method LIKE '%".trim($search)."%')";
            }
            $store_order_details = DB::select('SELECT store_order_details.order_id,order_methods,store_order_status, no_of_products, total_amount, payment_method,store_order_details.order_number,customer_name,email,DATE_FORMAT(store_order_details.created_at,"%d-%m-%Y %H:%i") as ordered_at FROM store_order_details LEFT JOIN payment_details on payment_details.payment_id = store_order_details.payment_id LEFT JOIN store_order_methods on store_order_methods.order_methods_id = store_order_details.store_order_status LEFT JOIN instore_customers on instore_customers.customer_id = store_order_details.customer_id '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $store_order_filtered_count = DB::select('SELECT store_order_details.order_id FROM store_order_details LEFT JOIN payment_details on payment_details.payment_id = store_order_details.payment_id LEFT JOIN store_order_methods on store_order_methods.order_methods_id = store_order_details.store_order_status LEFT JOIN instore_customers on instore_customers.customer_id = store_order_details.customer_id '.$where_cond.' ORDER BY '.$order.' '.$dir);
            $count_query = Order::where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0]
            ]);
            $totalCount = $count_query->get()->count();
            if(!empty($store_order_details)) {
                $i=0;$j=0;
                foreach($store_order_details as $order) {
                    $final_data[$i]=array(
                        'checkbox'=>'<div class="form-check"><input type="checkbox" name="store_order_checkbox" class="form-check-input store-order-checkbox" value="'.$order->order_id.'"></div>',
                        'order_id'=>++$j,
                        'order_number'=>$order->order_number,
                        'customer_name'=>$order->customer_name,
                        // 'email'=>$order->email,
                        'ordered_at'=>$order->ordered_at,
                        'status_name'=>$order->order_methods,
                        // 'no_of_products'=>$order->no_of_products,
                        // 'status_name' => "<select class='form-control bulk-action' data-type='single-bulk-action'>
                        //     <option value=''>Select Status</option>" .
                        //     (!empty($store_order_status) ? 
                        //         collect($store_order_status)->map(function ($status) use ($order) {
                        //                 $selected = ($status['status_id'] == $order->store_order_status) ? 'selected' : '';
                        //             return "<option value={$status['status_id']} $selected>{$status['status_name']}</option>";
                        //         })->implode('') 
                        //         : 
                        //         ''
                        //     ) .
                        // "</select>",
                        'total_amount'=>"SAR ".number_format((float)($order->total_amount), 2, '.', ''),
                        // 'payment_method'=>$order->payment_method,
                        // 'action'=>
                        //     "<a class='btn btn-circle btn-success btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.store-order.show', Crypt::encrypt($order->order_id))."'><i class='fa fa-eye'></i></a>
                        //     <form action='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.store-order.destroy', Crypt::encrypt($order->order_id))."' class='delete-order-form'>
                        //         <button class='btn btn-danger rounded font-sm order-delete'><i class='fa fa-trash'></i></button>
                        //     </form>  
                        //     <a class='btn btn-circle btn-danger btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.store-order.download-invoice', Crypt::encrypt($order->order_id))."'><i class='fa fa-download'></i></a>
                        //     <input type='hidden' class='encrypted_order_id' value='".Crypt::encrypt($order->order_id)."'><input type='hidden' class='order_id' value='".$order->order_id."'>" 
                        'action'=>
                            "<a class='btn btn-circle btn-success btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.store-order.show', Crypt::encrypt($order->order_id))."'><i class='fa fa-eye'></i></a>
                            <a class='btn btn-circle btn-primary btn-xs order-delete' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.store-order.destroy', Crypt::encrypt($order->order_id))."'><i class='fa fa-trash'></i></a>
                            <input type='hidden' class='encrypted_order_id' value='".Crypt::encrypt($order->order_id)."'><input type='hidden' class='order_id' value='".$order->order_id."'>" 
                    );
                    $i++;
                }
            }
            $totalFiltered = count($store_order_filtered_count);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data   
            );
            echo json_encode($json_data); 
        } else {
            return view('cashier_admin.store_order.list',compact('store_url','store_order_status','store_logo'));
        }
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $product_data = $request->product_item;
            $variants_item_name = $request->variants_item_name;
            $variants_item = $request->variants_item;
            $product_tax_amount = $request->product_tax_amount;
            $product_amount = $request->product_amount;
            $product_discount = $request->product_discount; 
            DB::beginTransaction();
            $store_order_status = OrderMethods::select('order_methods_id')->where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0]
            ])->orderBy('order_number','asc')->limit(1)->get()->toArray();
            $status_id = !empty($store_order_status) ? $store_order_status[0]['order_methods_id'] : 0;
            $data = [];
            $product_id = []; $quantity = []; $product_details = [];
            $customer_id = 0;
            if(!empty($request->customer_name) || !empty($request->phone_number)) {
                $instore_customer = [];
                $instore_customer['store_id'] = Auth::user()->store_id;
                $instore_customer['customer_name'] = $request->customer_name;
                $instore_customer['phone_number'] = $request->phone_number;
                if(isset($request->customer_id) && !empty($request->customer_id)) {
                    $customer_id = $request->customer_id;
                    $instore_customer['updated_by'] = Auth::user()->id;
                    InStoreCustomer::where('customer_id',$customer_id)->update($instore_customer);
                } else {
                    $instore_customer['created_by'] = Auth::user()->id;
                    $customer_id = InStoreCustomer::create($instore_customer)->customer_id;
                }
            }
            $insert_order = [];
            $insert_order['store_id'] = Auth::user()->store_id;
            $insert_order['customer_id'] = $customer_id;
            $insert_order['total_amount'] = $request->total_cart_amount;
            $insert_order['tax_amount'] = $request->total_cart_tax_amount;
            $insert_order['total_discount_amount'] = $request->cart_total_discount;
            $insert_order['sub_total_amount'] = $request->total_cart_sub_total;
            $insert_order['no_of_products'] = $request->no_of_products;
            $insert_order['order_type_id'] = $request->pickup;
            $insert_order['paid_amount'] = $request->cash_in_hand; 
            $insert_order['store_order_status'] = $status_id;
            $insert_order['coupon_code'] = $request->coupon_code;
            $insert_order['coupon_discount'] = $request->coupon_discount_value;
            $insert_order['created_by'] = Auth::user()->id;
            $order_id = Order::create($insert_order)->order_id;
            $insert_payment = [];
            $insert_payment['store_id'] = Auth::user()->store_id;
            $insert_payment['order_id'] = $order_id;
            $insert_payment['payment_method'] = !empty($request->payment_method) ? $request->payment_method : 'cash';
            $insert_payment['amount'] = $request->total_cart_amount;
            $insert_payment['created_by'] = Auth::user()->id;
            $payment_id = PaymentDetails::create($insert_payment)->payment_id;
            $update_order = array();
            $update_order['order_number'] = $order_number = "ORDER".sprintf("%03d",$order_id);
            $update_order['payment_id'] = $payment_id;
            Order::where('order_id',$order_id)->update($update_order);
            $ordered_date_time = Order::where('order_id',$order_id)->select('created_at')->get();
            $ordered_date_time = (!empty($ordered_date_time) && !empty($ordered_date_time[0]->created_at)) ? date('d M Y H:i:s', strtotime(trim($ordered_date_time[0]->created_at))) : '-';
            if(isset($product_data) && !empty($product_data)) {
                foreach($product_data as $p_id => $product) {
                    foreach($product as $key => $val) {
                        if(!empty($variants_item_name) && array_key_exists($p_id,$variants_item_name) && array_key_exists($key,$variants_item_name[$p_id])) {
                            $get_variants = $variants_item_name[$p_id][$key];
                            foreach($get_variants as $v_key => $variant) {
                                if($val[$v_key] > 0) {
                                    $insert_order_item = [];
                                    $insert_order_item['store_id'] = Auth::user()->store_id;
                                    $insert_order_item['order_id'] = $order_id;
                                    $insert_order_item['product_id'] = $key;
                                    $insert_order_item['quantity'] = $val[$v_key]; 
                                    $insert_order_item['product_variants'] = $variant; 
                                    $insert_order_item['variants_id'] = $variants_item[$p_id][$key][$v_key];
                                    $insert_order_item['sub_total'] = $product_amount[$p_id][$key][$v_key];
                                    $insert_order_item['tax_amount'] = $product_tax_amount[$p_id][$key][$v_key];
                                    $insert_order_item['discount_amount'] = $product_discount[$p_id][$key][$v_key];
                                    $insert_order_item['created_by'] = Auth::user()->id;
                                    OrderItems::create($insert_order_item);
                                    $product_details = VariantsOptionCombination::where([
                                        ['store_id', '=', Auth::user()->store_id],
                                        ['variants_combination_id', '=', $variants_item[$p_id][$key][$v_key]]
                                    ])->get(['on_hand']);
                                    if(!empty($product_details) && !empty($product_details[0]['on_hand'])) {
                                        $unit = $product_details[0]['on_hand'] - $val[$v_key];
                                        $update_product = array();
                                        $update_product['on_hand'] = ($unit > 0) ? $unit : 0;
                                        VariantsOptionCombination::where('variants_combination_id',$variants_item[$p_id][$key][$v_key])->update($update_product);
                                    }
                                }
                            }
                        } else {
                            if($val > 0) {
                                $insert_order_item = [];
                                $insert_order_item['store_id'] = Auth::user()->store_id;
                                $insert_order_item['order_id'] = $order_id;
                                $insert_order_item['product_id'] = $key;
                                $insert_order_item['quantity'] = $val[0];
                                $insert_order_item['sub_total'] = $product_amount[$p_id][$key][0];
                                $insert_order_item['tax_amount'] = $product_tax_amount[$p_id][$key][0];
                                $insert_order_item['discount_amount'] = $product_discount[$p_id][$key][0];
                                $insert_order_item['created_by'] = Auth::user()->id;
                                OrderItems::create($insert_order_item);
                                $product_details = Product::where([
                                    ['store_id', '=', Auth::user()->store_id],
                                    ['product_id', '=', $key]
                                ])->get(['unit']);
                                if(!empty($product_details)) {
                                    $unit = $product_details[0]['unit'] - $val[0];
                                    $update_product = array();
                                    $update_product['unit'] = ($unit > 0) ? $unit : 0;
                                    Product::where('product_id',$key)->update($update_product);
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            Session::forget('cart_data');
            Session::forget('product_ids');
            Session::forget('variant_ids');
            Session::forget('total_cart_quantity');
            $url = URL::to("/");
            $qr_url = $url.'/customer-invoice/'.$order_number; 
            return response()->json(['message'=>trans('store-admin.order_created_success'),'order_number' => $order_number, 'ordered_date_time'=>$ordered_date_time,'qr_url'=>$qr_url]);
        } catch (Exception $e) {
            //Rollback Database Entry
            DB::rollback();
            throw $e;
        }
    }


    public function show($id)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $store_order_details = Order::leftJoin('store_order_methods', 'store_order_methods.order_methods_id', '=', 'store_order_details.order_type_id')->leftJoin('store_order_items', 'store_order_items.order_id', '=', 'store_order_details.order_id')->leftJoin('instore_customers', 'instore_customers.customer_id', '=', 'store_order_details.customer_id')->leftJoin('store_products', 'store_products.product_id', '=', 'store_order_items.product_id')->where('store_order_details.order_id',Crypt::decrypt($id))->select('store_order_details.order_number','store_order_details.tax_amount as total_tax_amount','product_name','sub_total_amount','quantity','total_amount','store_order_items.product_id','order_methods','product_variants','store_order_items.sub_total','store_order_items.tax_amount as product_tax','paid_amount','customer_name','phone_number','email','category_image')
        ->selectRaw("DATE_FORMAT(store_order_details.created_at, '%d-%m-%Y %H:%i') as ordered_at")->get()->toArray(); 
        // $address_details = Store::leftJoin('countries', 'stores.store_country', '=', 'countries.id')->leftJoin('states', 'stores.store_state', '=', 'states.id')->leftJoin('cities', 'stores.store_city', '=', 'cities.id')->where('store_id',Auth::user()->store_id)->get(['store_name','store_address','cities.name as city_name','states.name as state_name','countries.name as country_name']);
        return view('cashier_admin.store_order.view',compact('store_url','store_order_details','store_logo'));
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request)
    {
        $update_order_status = array();
        $update_order_status['store_order_status'] = $request->status_id;
        $order_ids = $request->order_ids;
        Order::whereIn('order_id',$order_ids)->update($update_order_status);
        return response()->json(['message'=>trans('store-admin.updated_msg',['name'=>trans('store-admin.order_status')])]);
    }

    public function destroy($id)
    {
        $order_id = Crypt::decrypt($id);
        $delete_order = array();
        $delete_order['is_deleted'] = 1;  
        $delete_order['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_order['updated_by'] = Auth::user()->id;
        Order::where('order_id',$order_id)->update($delete_order);
        PaymentDetails::where('order_id',$order_id)->update($delete_order);
        OrderItems::where('order_id',$order_id)->update($delete_order);
        $prefix_url = config('app.module_prefix_url'); 
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.store-order.index')->with('message',trans('store-admin.deleted_msg',['name'=>trans('store-admin.order')]));
    }

    public function downloadInvoice($id) {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $store_order_details = Order::leftJoin('store_order_items', 'store_order_items.order_id', '=', 'store_order_details.order_id')->leftJoin('store_products', 'store_products.product_id', '=', 'store_order_items.product_id')->where('store_order_details.order_id',Crypt::decrypt($id))->get(['order_number','store_order_details.created_at','product_name','sub_total','quantity','total_amount','store_order_items.product_id']);
        $data = [
            'store_url' => $store_url,
            'store_order_details' => $store_order_details
        ];
        $pdf = PDF::loadView('cashier_admin.store_order.download_invoice', $data);
        return $pdf->download('download-invoice.pdf');
    }

    public function isPhoneNumberExist(Request $request) {
        $customer_phone_number = $request->customer_phone_number;
        $customer_data = InStoreCustomer::where([
            ['store_id', '=', Auth::user()->store_id],
            ['phone_number', '=' , $customer_phone_number],
            ['status', '=', 1],
            ['is_deleted', '=', 0]
        ])->select('customer_name','customer_id')->get();
        return response()->json(['customer_data'=>$customer_data]);
    }
}
