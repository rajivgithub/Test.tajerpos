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

class CustomerAddressController extends Controller
{
    protected $store_url;
    protected $guard = 'customer';

    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
    }

    public function index()
    {
        $store_url = $this->store_url;
        $category_details = [];
        $countries = Country::get(['id','name']);
        $address_details = Address::leftJoin('countries', 'customer_address.country_id', '=', 'countries.id')->leftJoin('states', 'customer_address.state_id', '=', 'states.id')->leftJoin('cities', 'customer_address.city_id', '=', 'cities.id')->where([
            ['store_id', '=', Auth::guard('customer')->user()->store_id],
            ['customer_id', '=', Auth::guard('customer')->user()->customer_id]
        ])->get(['address_id','customer_name','mobile_number','street_name','building_name','customer_address.country_id','customer_address.state_id','customer_address.city_id','pincode','address_type','landmark','countries.name as country_name','states.name as state_name','cities.name as city_name']);
        $mode = (count($address_details) > 0) ? 'edit' : 'add';
        return view('customer.address',compact('store_url','countries','address_details','mode','category_details'));
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

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
