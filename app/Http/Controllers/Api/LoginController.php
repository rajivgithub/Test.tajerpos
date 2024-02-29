<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CustomerUser;
use App\Models\Admin\Store;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;
use App\Models\StoreAdmin\Category;
use App\Models\CashierAdmin\InStoreCustomer;

class LoginController extends ApiController
{
    protected $guard = 'customer';
    // Registration for Customer
    public function register(Request $request) {
        $store_id = $request->store_id;
        $validator = Validator::make($request->all(), [ 
            'store_id'=>'required',
            'customer_name'=> 'required|max:150',
            'phone_number'=> 'nullable|max:20',
            'email'=> 'required|email|unique:instore_customers,email,NULL,customer_id,store_id,' . $store_id.'|max:100',
            'password'=> 'required|max:255'
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY, $store_id);
        }
        else{
            $checkStoreId = $this->checkStoreId($store_id);
            if ($checkStoreId === true) {
                $input = $request->all();
                $input['plain_password'] = $request->password;
                $input['password'] = Hash::make($request->password);  
                $customer_id = InStoreCustomer::create($input)->customer_id;
                return $this->createResponse('Customer Registerd Successfully', self::HTTP_OK, $store_id, $customer_id);
            } else {
                return $checkStoreId;
            }
        }
    }   

    public function login(Request $request) {
        $store_id = $request->store_id;
        $validator = Validator::make($request->all(), [ 
            'store_id'=>'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY, $store_id);
        }
        else {
            $checkStoreId = $this->checkStoreId($store_id);
            if ($checkStoreId === true) {
                $input = $request->all();
                if (Auth::guard($this->guard)->attempt(array('email' => $input['email'], 'password' => $input['password'], 'store_id' => $store_id))) {
                    $user = Auth::guard($this->guard)->user()->makeHidden(['password', 'plain_password']);
                    return $this->createResponse("Login Successful", self::HTTP_OK, $store_id,'',$user);
                } else {
                    return $this->createResponse("Unauthorized", self::HTTP_UNAUTHORIZED);
                }
            } else {
                return $checkStoreId;
            }
        }
    }
}

