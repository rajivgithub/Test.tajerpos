<?php

namespace App\Http\Controllers\CashierAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashierAdmin\OrderMethods;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Http\Controllers\CommonController;

class OrderMethodsController extends Controller
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
        $store_order_methods = OrderMethods::select('order_methods','order_number','status','order_methods_id')->where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0]
        ])->get()->toArray();
        return view('cashier_admin.order_methods.list',compact('store_url','store_logo','store_order_methods'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $orderNumbers = $request->order_number;
        $orderMethods = $request->order_methods;
        $orderMethodStatus = $request->order_method_status;
        $orderMethodsID = $request->order_methods_id;
        if(!empty($orderMethods)) {
            for($i = 0;$i<count($orderMethods);$i++) {
                $orderMethodsData = [];
                $orderMethodsData['order_methods'] = $orderMethods[$i];
                $orderMethodsData['order_number'] = $orderNumbers[$i];
                $orderMethodsData['status'] = $orderMethodStatus[$i];
                if(!empty($orderMethodsID[$i])) {
                    OrderMethods::where([ 
                        ['store_id','=',Auth::user()->store_id],
                        ['order_methods_id','=',Crypt::decrypt($orderMethodsID[$i])],
                    ])->update($orderMethodsData);
                } else {
                    $orderMethodsData['created_by'] = Auth::user()->id;
                    $orderMethodsData['store_id'] = Auth::user()->store_id;
                    OrderMethods::create($orderMethodsData);
                }
            }
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$this->prefix_url.'.store-order-methods.index')->with('message',trans('store-admin.updated_msg',['name'=>trans('store-admin.order_methods_label')]));
        } else {
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$this->prefix_url.'.store-order-methods.index')->with('error',trans('store-admin.no_records_updated_error'));
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
        $order_methods_id = Crypt::decrypt($request->order_methods_id);
        $delete_order_methods = array();
        $delete_order_methods['is_deleted'] = 1;  
        $delete_order_methods['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_order_methods['updated_by'] = Auth::user()->id;
        OrderMethods::where([
            ['store_id','=',Auth::user()->store_id],
            ['order_methods_id','=',$order_methods_id]
        ])->update($delete_order_methods);
        return response()->json(['message'=>trans('store-admin.deleted_msg',['name'=>trans('store-admin.order_methods_label')])]);
    }
}
