<?php

namespace App\Http\Controllers\CashierAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashierAdmin\StoreOrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Http\Controllers\CommonController;

class StoreOrderStatusController extends Controller
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
        $store_order_status = StoreOrderStatus::select('status_name','order_number','status','status_id')->where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0]
        ])->get()->toArray();
        return view('cashier_admin.store_order_status.list',compact('store_url','store_order_status','store_logo'));
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
        $status_id = $request->status_id;
        if(!empty($add_status_order)) {
            for($i = 0;$i<count($add_status_order);$i++) {
                $status_data = [];
                $status_data['status_name'] = $add_status_name[$i];
                $status_data['order_number'] = $add_status_order[$i];
                $status_data['status'] = $add_status_condition[$i];
                if(!empty($status_id[$i])) {
                    StoreOrderStatus::where([ 
                        ['store_id','=',Auth::user()->store_id],
                        ['status_id','=',Crypt::decrypt($status_id[$i])],
                    ])->update($status_data);
                } else {
                    $status_data['created_by'] = Auth::user()->id;
                    $status_data['store_id'] = Auth::user()->store_id;
                    StoreOrderStatus::create($status_data);
                }
            }
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$this->prefix_url.'.store-order-status.index')->with('message',trans('store-admin.updated_msg',['name'=>trans('store-admin.store_order_status')]));
        } else {
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$this->prefix_url.'.store-order-status.index')->with('error',trans('store-admin.no_records_updated_error'));
        }
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
        StoreOrderStatus::where([
            ['store_id','=',Auth::user()->store_id],
            ['status_id','=',$status_id]
        ])->update($delete_order_status);
        return response()->json(['message'=>trans('store-admin.deleted_msg',['name'=>trans('store-admin.store_order_status')])]);
    }
}
