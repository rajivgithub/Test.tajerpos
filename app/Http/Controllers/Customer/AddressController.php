<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Customers\Address;
use App\Http\Requests\Customer\AddressRequest;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\CommonController;
use Carbon\Carbon;

class AddressController extends Controller
{
    protected $store_url;
    protected $guard = 'customer';

    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
    }
    
    public function index(Request $request)
    {
        $store_url = $this->store_url;
        $store_id = CommonController::get_store_id();
        $countries = Country::get(['id','name']);
        if ($request->_type != "") {
            if(session()->has('authenticate_user'))
                $user = session('authenticate_user');
            else 
                $user = Auth::guard('customer')->user();
            $address_details = Address::leftJoin('countries', 'customer_address.country_id', '=', 'countries.id')->leftJoin('states', 'customer_address.state_id', '=', 'states.id')->leftJoin('cities', 'customer_address.city_id', '=', 'cities.id')->where([
                ['store_id', '=', $user->store_id],
                ['customer_id', '=', $user->customer_id],
                ['is_deleted', '=', 0]
            ])->get(['address_id','customer_name','mobile_number','street_name','building_name','customer_address.country_id','customer_address.state_id','customer_address.city_id','pincode','address_type','landmark','countries.name as country_name','states.name as state_name','cities.name as city_name','is_default']);
            return response()->json(['address_details'=>$address_details]);
        } else {
            $breadcrumbs = [
                ['name' => trans('customer.your_account'), 'url' => route($store_url.'.customer.dashboard')],
                ['name' => trans('customer.your_addresses'), 'url' => "#"]
            ];
            return view('customer.address',compact('store_url','store_id','countries','breadcrumbs'));
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $mode = $request->mode;
            $input = $request->all();
            $address_id = ($mode == "edit" || $mode == "default_address") ? $request->address_id : 0;
            $store_id = CommonController::get_store_id();
            $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
            if($mode == "edit") {
                //Reset the input values
                $remove_array_values = array('_token','mode','address_id');
                foreach($remove_array_values as $value) {
                    unset($input[$value]);
                }
                Address::where('address_id',$address_id)->update($input);
                $success_message = trans('customer.updated_msg',['name'=>trans('customer.address')]);
            } else if($mode == "add") {
                $input['customer_id'] = $customer_id;
                $input['store_id'] = $store_id;
                $input['is_default'] = 1;
                $address_id = Address::create($input)->address_id;
                $success_message = trans('customer.added_msg',['name'=>trans('customer.address')]);
            }
            if($mode == "add" || $mode == "default_address") {
                $update_default_address = array();
                $update_default_address['is_default'] = 0; 
                Address::where('customer_id',$customer_id)->where('store_id',$store_id)->whereNotIn('address_id',[$address_id])->update($update_default_address);
                if($mode == "default_address") {
                    $update_default_address['is_default'] = 1; 
                    Address::where('customer_id',$customer_id)->where('store_id',$store_id)->where('address_id',[$address_id])->update($update_default_address);
                    $success_message = trans('customer.address_set_as_default_success');
                }
            }
            return response()->json(['message'=>$success_message]);
        } catch (Exception $e) {
            throw $e;
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

    public function destroy($id)
    {
        $delete_address = array();
        $delete_address['is_deleted'] = 1;  
        $delete_address['deleted_at'] = Carbon::now()->toDateTimeString();
        Address::where('address_id',$id)->update($delete_address);
        return response()->json(['message'=>trans('customer.deleted_msg',['name'=>trans('customer.address')])]);
    }
}
