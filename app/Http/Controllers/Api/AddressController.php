<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Exception;
use App\Models\Customers\Address;
use Validator;
use Carbon\Carbon;
use App\Http\Controllers\CommonController;

class AddressController extends ApiController
{
    public function addAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required',
            'customer_id'=> 'required', 
            'customer_name' => 'required|max:60',
            'mobile_number' => 'required|numeric|max:99999999999999999999',
            'email_address' => 'max:100',
            'street_name' => 'required|max:100',
            'building_name' => 'required|max:100',
            'country_id' => 'required|numeric|max:99999999999',
            'state_id' => 'required|numeric|max:99999999999',
            'city_id' => 'required|numeric|max:99999999999',
            'address_type' => 'required|max:100',
            'pincode' => 'required|numeric|max:99999999999',
            'address_id' => 'required_if:mode,edit,default_address|numeric|max:99999999999',
            'mode' => 'required|in:add,edit,default_address',
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            $mode = $request->mode;
            $input = $request->except('_token','mode','address_id');
            $address_id = ($mode == "edit" || $mode == "default_address") ? $request->address_id : 0;
            if($mode == "edit") {
                Address::where('address_id',$address_id)->update($input);
                $success_message = "Address updated successfully";
            }
            else if($mode == "add"){
                $input['is_default'] = 1;
                $address_id = Address::create($input)->address_id;
                $success_message = "Address added successfully";
            }
            if($mode == "add" || $mode == "default_address") {
                $update_default_address = array();
                $update_default_address['is_default'] = 0; 
                Address::where('customer_id',$request->customer_id)->where('store_id',$request->store_id)->whereNotIn('address_id',[$address_id])->update($update_default_address);
                if($mode == "default_address") {
                    $update_default_address['is_default'] = 1; 
                    Address::where('customer_id',$request->customer_id)->where('store_id',$request->store_id)->where('address_id',[$address_id])->update($update_default_address);
                    $success_message = "Address set as the default address successfully";
                }
            }
            return $this->createResponse($success_message, self::HTTP_OK, $request->store_id, $request->customer_id);
        } else {
            return $checkStoreId;
        }
    }

    public function viewAddress(Request $request) 
    {
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            $address_details = Address::leftJoin('countries', 'customer_address.country_id', '=', 'countries.id')->leftJoin('states', 'customer_address.state_id', '=', 'states.id')->leftJoin('cities', 'customer_address.city_id', '=', 'cities.id')->where([
                ['store_id', '=', $request->store_id],
                ['customer_id', '=', $request->customer_id],
                ['is_deleted', '=', 0]
            ])->get(['address_id','customer_name','mobile_number','street_name','building_name','customer_address.country_id','customer_address.state_id','customer_address.city_id','pincode','address_type','landmark','countries.name as country_name','states.name as state_name','cities.name as city_name','is_default']);
            return $this->createResponse("List of addresses according to customer", self::HTTP_OK, $request->store_id, $request->customer_id,$address_details);
        } else {
            return $checkStoreId;
        }
    }

    public function removeAddress(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required',
            'customer_id'=> 'required',
            'address_id'=> 'required',
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            $remove_address = array();
            $remove_address['is_deleted'] = 1;  
            $remove_address['deleted_at'] = Carbon::now()->toDateTimeString();
            Address::where([
                ['address_id', '=', $request->address_id],
                ['customer_id', '=', $request->customer_id],
                ['store_id','=',$request->store_id]
            ])->update($remove_address);
            return $this->createResponse("Address was removed successfully", self::HTTP_OK, $request->store_id, $request->customer_id);
        } else {
            return $checkStoreId;
        }
    }
}
