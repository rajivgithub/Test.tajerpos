<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\Customers\User;
use Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\CashierAdmin\InStoreCustomer;
use DB;
use Illuminate\Validation\Rule;

class CustomerController extends ApiController
{
    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'current_password'=> 'required',
            'new_password'=> 'required|min:8|max:255',
            'confirm_password'=> 'required|min:8|same:new_password|max:255',
            'store_id'=> 'required',
            'customer_id'=> 'required',
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $checkStoreId = $this->checkStoreId($request->store_id);
            if ($checkStoreId === true) {
                $customer_details = InStoreCustomer::select('password')->where([
                    ['store_id', '=', $request->store_id],
                    ['customer_id', '=', $request->customer_id],
                    ['status', '=', 1],
                    ['is_deleted', '=', 0],
                ])->get()->toArray();
                if(!empty($customer_details) && count($customer_details) > 0) {
                    if(Hash::check($request->current_password, $customer_details[0]['password'])) {
                        $update_data = array();
                        $update_data['password'] = Hash::make($request->new_password);
                        InStoreCustomer::where([
                            ['store_id', '=', $request->store_id],
                            ['customer_id', '=', $request->customer_id],
                            ['status', '=', 1],
                            ['is_deleted', '=', 0],
                        ])->update($update_data);
                        return $this->createResponse('Password has been changed successfully', self::HTTP_OK, $request->store_id, $request->customer_id);
                    } else {
                        return $this->createResponse('Password is incorrect', self::HTTP_UNAUTHORIZED);
                    }
                } else {
                    return $this->createResponse('Customer does not exist', self::HTTP_NOT_FOUND);
                }
            } else {
                return $checkStoreId;
            }
        }
    } 

    public function updateProfile(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required|numeric|max:99999999999',
            'customer_id'=> 'required|numeric|max:99999999999',
            'first_name'=> 'required|string|max:150',
            'last_name'=> 'required|string|max:150',
            'screen_name'=> 'required|max:150',
            'email' => ['required', 'string', 'email', 'max:100',Rule::unique('instore_customers')->where(function ($query) {
                $query->whereNotIn('customer_id', [request()->customer_id]);
                $query->where('store_id', [request()->store_id]);
                return $query->where('email', request()->email);
            })],
            'date_of_birth'=> 'required|max:10',
            'gender'=> 'required|in:male,female',
            'phone_number'=> 'required|numeric|digits_between:10,15',
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $checkStoreId = $this->checkStoreId($request->store_id);
            if ($checkStoreId === true) {
                $input = $request->all();
                DB::beginTransaction();
                InStoreCustomer::where('customer_id',$request->customer_id)->update($input);
                DB::commit();
                return $this->createResponse("Profile Updated Successfully!", self::HTTP_OK, $request->store_id, $request->customer_id);
            } else {
                return $checkStoreId;
            }
        }
    } 

    public function getProfile(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required|numeric|max:99999999999',
            'customer_id'=> 'required|numeric|max:99999999999'
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $checkStoreId = $this->checkStoreId($request->store_id);
            if ($checkStoreId === true) { 
                $customer_details = InStoreCustomer::where([
                    ['customer_id','=',$request->customer_id],
                    ['store_id','=',$request->store_id],
                    ['status','=',1],
                    ['is_deleted','=',0],
                ])->get(['first_name','last_name','screen_name','email','phone_number','date_of_birth','gender']);
                return $this->createResponse("Profile Details", self::HTTP_OK, $request->store_id, $request->customer_id,$customer_details);
            } else {
                return $checkStoreId;
            }
        }
    } 
}
