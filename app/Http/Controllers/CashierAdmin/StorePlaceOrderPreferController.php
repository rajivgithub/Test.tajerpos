<?php

namespace App\Http\Controllers\CashierAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashierAdmin\StorePlaceOrderPrefer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Http\Controllers\CommonController;

class StorePlaceOrderPreferController extends Controller
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
        $store_order_status = StorePlaceOrderPrefer::select('status_name','order_number','status','prefer_order_id')->where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0]
        ])->get()->toArray();
        return view('cashier_admin.store_place_order_status.list',compact('store_url','store_order_status','store_logo'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $add_status_order = $request->add_status_order;
        $add_status_name = $request->add_status_name;
        $add_status_condition = $request->add_status_condition;
        $update_order_status = array();
        $update_order_status['is_deleted'] = 1;
        StorePlaceOrderPrefer::where([
            ['store_id','=',Auth::user()->store_id]
        ])->update($update_order_status);
        for($i = 0;$i<count($add_status_order);$i++) {
            $insert_status_row = [];
            $insert_status_row['status_name'] = $add_status_name[$i];
            $insert_status_row['order_number'] = $add_status_order[$i];
            $insert_status_row['status'] = $add_status_condition[$i];
            $insert_status_row['created_by'] = Auth::user()->id;
            $insert_status_row['store_id'] = Auth::user()->store_id;
            StorePlaceOrderPrefer::create($insert_status_row);
        }
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$this->prefix_url.'.store-place-order-prefer.index')->with('message',"Store order status updated successfully");
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

    public function destroy(Request $request)
    {
        $status_id = Crypt::decrypt($request->remove_status_id);
        $delete_order_status = array();
        $delete_order_status['is_deleted'] = 1;  
        $delete_order_status['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_order_status['updated_by'] = Auth::user()->id;
        StorePlaceOrderPrefer::where([
            ['store_id','=',Auth::user()->store_id],
            ['prefer_order_id','=',$status_id]
        ])->update($delete_order_status);
        return response()->json(['message'=>'Order status deleted successfully.']);
    }
}
