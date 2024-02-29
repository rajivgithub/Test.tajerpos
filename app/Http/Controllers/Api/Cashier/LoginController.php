<?php

namespace App\Http\Controllers\Api\Cashier;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Models\Admin\Store;

class LoginController extends ApiController
{
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
                /*$store_details = Store::where([
                    ['store_id', '=', $store_id],
                    ['is_deleted', '=', 0],
                ])->get(['store_id','cashier_app']);
                if(!empty($store_details)) {
                    $cashier_app_status = $store_details[0]['cashier_app'];
                } else {
                    $cashier_app_status = 0;
                }*/
                if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'], 'store_id' => $store_id)))
                {
                    // if ($cashier_app_status == 1 && (auth()->user()->is_admin == 3)) {
                        $user = Auth::user()->only(['id','name', 'email','phone_number']);
                        return $this->createResponse("Login Successful", self::HTTP_OK, $store_id,'',$user);
                    // }
                    // else {
                    //     return $this->createResponse("You do not have access.", self::HTTP_UNAUTHORIZED);
                    // }
                }else
                    return $this->createResponse("Unauthorized", self::HTTP_UNAUTHORIZED);
            } else {
                return $checkStoreId;
            }
        }
    }
}
