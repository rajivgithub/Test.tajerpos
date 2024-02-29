<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\CommonController;
use App\Models\CashierAdmin\OnlineStoreOrder;
use Illuminate\Support\Facades\Auth;
use App\Models\CashierAdmin\OnlineOrderStatus;
use DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\CashierAdmin\OnlineStoreOrderItems;
use App\Models\CashierAdmin\OnlinePayment;
use Carbon\Carbon;

class OnlineOrderController extends Controller
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
        $online_order_status = OnlineOrderStatus::select('status_name','order_number','status','order_status_id')->where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
            ['status', '=', 1]
        ])->orderBy('order_number','asc')->get()->toArray();
        if($request->type != "") {
            $final_data=array();
            $columns = array( 
              0=> 'checkbox', 
              1=> 'online_order_id',
              1=> 'order_number',
              2=> 'customer_name',
              3=> 'email',
              4=> 'ordered_at',
              5=> 'status_name',
              6=> 'total_amount',
              7=> 'action'
            );
            $limit = $request->length;
            $start = $request->start; 
            $order = ($columns[$request->order[0]['column']] == "online_order_id") ? 'online_order_id' : 
                ($columns[$request->order[0]['column']] == "order_number") ? 'online_store_order_details.order_number' :
                ($columns[$request->order[0]['column']] == "ordered_at") ? 'online_store_order_details.created_at' : $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $where_cond = 'where online_store_order_details.is_deleted = 0 AND online_store_order_details.store_id = "'.Auth::user()->store_id.'"';
            if(($request->type == 'online_order_status') && !empty($request->filter_value))
                $where_cond .= ' AND status_name = "'.$request->filter_value.'"';
            if(!empty($request->search['value'])) {
                $search_value = $request->search['value'];
                $search = (strpos($search_value, "SAR") !== false) ? trim($search_value,"SAR") : $search_value;
                $search = ((strpos($search, ".00") !== false) || (strpos($search, ".0")) !== false) ? round($search) : $search;
                $where_cond .= " AND (online_store_order_details.order_number LIKE '%".trim($search)."%' or status_name LIKE '%".trim($search)."%' or no_of_products LIKE '%".trim($search)."%' or total_amount LIKE '%".trim($search)."%' or customer_name LIKE '%".trim($search)."%' or DATE_FORMAT(online_store_order_details.created_at, '%d-%m-%Y %H:%i') LIKE '%".trim($search)."%')";
            }
            $online_order_details = DB::select('SELECT online_order_id,status_name,online_order_status, no_of_products, total_amount,online_store_order_details.order_number,customer_name,email,DATE_FORMAT(online_store_order_details.created_at, "%d-%m-%Y %H:%i") as ordered_at FROM online_store_order_details LEFT JOIN instore_customers on instore_customers.customer_id = online_store_order_details.customer_id LEFT JOIN online_order_status on online_order_status.order_status_id = online_store_order_details.online_order_status '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $online_order_filtered_count = DB::select('SELECT online_order_id FROM online_store_order_details LEFT JOIN online_order_status on online_order_status.order_status_id = online_store_order_details.online_order_status LEFT JOIN instore_customers on instore_customers.customer_id = online_store_order_details.customer_id '.$where_cond.' ORDER BY '.$order.' '.$dir);
            $count_query = OnlineStoreOrder::where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0]
            ]);
            $totalCount = $count_query->get()->count();
            if(!empty($online_order_details)) {
                $i=0;$j=0;
                foreach($online_order_details as $order) {
                    $final_data[$i]=array(
                        'checkbox'=>'<div class="form-check"><input type="checkbox" name="online_order_checkbox" class="form-check-input online-order-checkbox" value="'.$order->online_order_id.'"></div>',
                        'online_order_id'=>++$j,
                        'order_number'=>$order->order_number,
                        'ordered_at'=>$order->ordered_at,
                        'customer_name'=>$order->customer_name,
                        'total_amount'=>"SAR ".number_format((float)($order->total_amount), 2, '.', ''),
                        'email'=>$order->email, 
                        'status_name'=>$order->status_name,
                        // 'status_name' => "<select class='form-control change-online-order-status' data-type='single-bulk-action'>
                        //     <option value=''>Select Status</option>" .
                        //     (!empty($online_order_status) ? 
                        //         collect($online_order_status)->map(function ($status) use ($order) {
                        //                 $selected = ($status['order_status_id'] == $order->online_order_status) ? 'selected' : '';
                        //             return "<option value={$status['order_status_id']} $selected>{$status['status_name']}</option>";
                        //         })->implode('') 
                        //         : 
                        //         ''
                        //     ) .
                        // "</select>",
                        'action'=>
                            "<a class='btn btn-circle btn-success btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.online-orders.show', Crypt::encrypt($order->online_order_id))."'><i class='fa fa-eye'></i></a>
                            <a class='btn btn-circle btn-primary btn-xs online-order-delete' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.online-orders.destroy', Crypt::encrypt($order->online_order_id))."'><i class='fa fa-trash'></i></a>
                            <input type='hidden' class='encrypted_order_id' value='".Crypt::encrypt($order->online_order_id)."'><input type='hidden' class='order_id' value='".$order->online_order_id."'>" 
                    );
                    $i++;
                }
            }
            $totalFiltered = count($online_order_filtered_count);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data   
            );
            echo json_encode($json_data); 
        } else {
            return view('cashier_admin.online_order.list',compact('store_url','online_order_status','store_logo'));
        } 
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $address_details = [];
        $online_order_details = OnlineStoreOrder::leftJoin('online_store_order_items', 'online_store_order_items.order_id', '=', 'online_store_order_details.online_order_id')
        ->leftJoin('instore_customers', 'online_store_order_details.customer_id', '=', 'instore_customers.customer_id')
        ->leftJoin('online_order_status', 'online_store_order_details.online_order_status', '=', 'online_order_status.order_status_id')
        ->leftJoin('store_products', 'online_store_order_items.product_id', '=', 'store_products.product_id')
        ->leftJoin('customer_address', 'online_store_order_details.address_id', '=', 'customer_address.address_id')
        ->leftJoin('countries', 'customer_address.country_id', '=', 'countries.id')
        ->leftJoin('states', 'customer_address.state_id', '=', 'states.id')
        ->leftJoin('cities', 'customer_address.city_id', '=', 'cities.id')
        ->where([
            ['online_store_order_details.online_order_id', '=', Crypt::decrypt($id)],
            ['online_store_order_details.store_id', '=', Auth::user()->store_id],
        ])->select('type_of_product','product_name','category_image','product_variants','quantity','sub_total','online_store_order_items.tax_amount','online_order_items_id','online_order_id','online_store_order_details.order_number','instore_customers.customer_name','total_amount','no_of_products','sub_total_amount','discount_amount','customer_address.customer_name as shipping_customer_name','customer_address.mobile_number as shipping_mobile_number','customer_address.email_address as shipping_email_address','customer_address.street_name','customer_address.building_name','customer_address.landmark','customer_address.address_type','customer_address.pincode','cities.name as city_name','states.name as state_name','countries.name as country_name','online_order_status','address_type','online_store_order_items.product_id','email','phone_number','status_name')
        ->selectRaw("DATE_FORMAT(online_store_order_details.created_at, '%d-%m-%Y %H:%i') as ordered_at")
        ->get()->toArray();  
        $online_order_status = OnlineOrderStatus::select('status_name','order_number','status','order_status_id')->where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
            ['status', '=', 1]
        ])->orderBy('order_number','asc')->get();
        return view('cashier_admin.online_order.view',compact('store_url','online_order_details','address_details','store_logo','online_order_status'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request)
    {
        $update_order_status = array();
        $update_order_status['online_order_status'] = $request->status_id;
        $order_ids = $request->order_ids;
        OnlineStoreOrder::whereIn('online_order_id',$order_ids)->update($update_order_status);
        return response()->json(['message'=>trans('store-admin.status')]);
    }

    public function destroy($id)
    {
        $order_id = Crypt::decrypt($id);
        $delete_order = array();
        $delete_order['is_deleted'] = 1;  
        $delete_order['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_order['updated_by'] = Auth::user()->id;
        OnlineStoreOrder::where('online_order_id',$order_id)->update($delete_order);
        OnlinePayment::where('order_id',$order_id)->update($delete_order);
        OnlineStoreOrderItems::where('order_id',$order_id)->update($delete_order);
        $prefix_url = config('app.module_prefix_url');  
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.online-orders.index')->with('message',trans('store-admin.deleted_msg',['name'=>trans('store-admin.order')]));
    }
}
