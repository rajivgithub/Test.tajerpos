<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\CommonController;
use App\Models\CashierAdmin\InStoreCustomer;
use DB;
use Auth;

class CustomerController extends Controller
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
    
    public function index(Request $request)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $prefix_url = config('app.module_prefix_url');
        if($request->_type != "") {
            $final_data=array();
            $columns = array( 
                0 =>'customer_id',
                1=> 'customer_name',
                2=> 'phone_number', 
                3=> 'email',
                4=> 'created_at',
                5=> 'status',
            );
            $limit = $request->length;
            $start = $request->start; 
            $order = $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $query = InstoreCustomer::select('customer_name', 'customer_id', 'phone_number', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as customer_created_at"),'email','status')
                ->where('is_deleted', 0)
                ->where('store_id', Auth::user()->store_id);
            if (!empty($request->search['value'])) {
                $search = trim($request->search['value']);
                $query->where(function ($query) use ($search) {
                    $query->where('customer_name', 'LIKE', '%' . $search . '%')
                        ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE '%" . $search . "%'")
                        ->orWhere('phone_number', 'LIKE', '%' . $search . '%');
                });
            }
            $query->orderBy($order, $dir)->offset($start)->limit($limit);
            $customer_details = $query->get();
            $filtered_customer_details = $query->count();
            $totalCount = InstoreCustomer::where('is_deleted', 0)
                ->where('store_id', Auth::user()->store_id)
                ->count();
            if(!empty($customer_details)) {
                $i=0;$j=0;
                foreach($customer_details as $customer) {
                    $status_checked = $customer->status == 1 ? 'checked' : '';
                    $final_data[$i]=array(
                        'id'=>++$j,
                        'customer_name'=> $customer->customer_name,
                        'phone_number'=> $customer->phone_number,
                        'email'=> $customer->email,
                        'created_at'=> $customer->customer_created_at,
                        'status' => "<div class='custom-control custom-switch'>
                                <input type='hidden' class='customer-id' value='$customer->customer_id' />
                                <input class='custom-control-input customer-status' data-type='status' type='checkbox' name='status' value='1' $status_checked id='feature-customSwitch" . $i . "'>
                                <label class='custom-control-label' for='feature-customSwitch" . $i . "'></label>
                            </div>",
                    );
                    $i++;
                }
            }
            $totalFiltered = !empty($filtered_customer_details) ? $filtered_customer_details : 0;
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => !empty($totalCount) ? $totalCount : 0,  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data, 
            );
            echo json_encode($json_data); 
        } else {
            return view('store_admin.customers.list',compact('store_url','store_logo'));
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
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request)
    {
        $customer_id = $request->customer_id;
        $update_access = array();
        $update_access["status"] = $request->status_value;
        $update_access['updated_by'] = Auth::user()->id;
        InStoreCustomer::where('customer_id',$customer_id)->update($update_access);
        return response()->json(['message'=>trans('store-admin.updated_msg',['name'=>trans('store-admin.status')])]);
    }

    public function destroy($id)
    {
        //
    }
}
