<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\State;
use App\Models\Cities;
use App\Models\Country;
use Validator;
use App\Models\Admin\Store;

class CommonController extends ApiController
{
    public function countryList(Request $request) {
        $countries = Country::get(['id','name']);
        return $this->createResponse('List of All Countries', self::HTTP_OK, '', '', $countries);
    }

    public function stateList(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'country_id'=> 'required',
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $states = State::where('country_id',$request->country_id)->get(['id','name']);
            return $this->createResponse('List of states by country', self::HTTP_OK, '', '', $states);
        }
        
    } 

    public function cityList(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'state_id'=> 'required',
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $cities = Cities::where('state_id',$request->state_id)->get(['id','name']);
            return $this->createResponse('List of cities by state', self::HTTP_OK, '', '', $cities);
        }
    }

    public function get_store_details($store_id) {
        return Store::leftJoin('users', 'users.store_id', '=', 'stores.store_id')
        ->leftJoin('countries', 'stores.store_country', '=', 'countries.id')->leftJoin('states', 'stores.store_state', '=', 'states.id')->leftJoin('cities', 'stores.store_city', '=', 'cities.id')
        ->where([
            ['stores.store_id', '=', $store_id], 
            ['web_status', '=', 1],
            ['stores.status', '=', 1],
            ['stores.is_deleted', '=', 0],
            ['is_store','=','Yes']
        ])->get(['stores.store_id','store_url','store_phone_number','email','store_address','cities.name as city_name','states.name as state_name','countries.name as country_name','store_logo','store_name'])->toArray();
    }

    public function get_store_id() {
        $store_url = CommonController::storeURL();
        $store = Store::where([
            'store_url' => $store_url,
            'web_status' => 1,
            'status' => 1,
            'is_deleted' => 0,
        ])->select('store_id')->first();
        if ($store) {
            return $store->store_id;
        } else {
            return 0;
        }
    }


}
