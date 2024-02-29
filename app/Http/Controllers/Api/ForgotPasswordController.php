<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Validator;
use App\Models\Customers\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\CommonController;
use Mail; 
use Illuminate\Support\Str;
use App\Models\CashierAdmin\InStoreCustomer;

class ForgotPasswordController extends ApiController
{
    public function forgetPassword(Request $request)
    { 
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required',
            'email' => 'required|email|exists:instore_customers,email,store_id,' . $request->store_id,
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            try {
                DB::beginTransaction();
                $existingToken = DB::table('password_resets')
                ->where('email', $request->email)
                ->where('store_id', $request->store_id)
                ->first();
                if ($existingToken && !$this->tokenExpired($existingToken->created_at)) {
                    return $this->createResponse("A password reset email has already been sent. Please check your email.", self::HTTP_OK, $request->store_id);
                } else {
                    DB::table('password_resets')->where(['email'=> $request->email,'store_id'=> $request->store_id])->delete();
                }
                $token = mt_rand(100000, 999999);
                DB::table('password_resets')->insert([
                    'store_id' => $request->store_id,
                    'email' => $request->email, 
                    'token' => $token, 
                    'created_at' => Carbon::now()
                ]);
                $data['store_details'] = $store_details = CommonController::get_store_details($request->store_id);
                $data['store_url'] = (!empty($store_details) && count($store_details) > 0 && isset($store_details[0]['store_url'])) ? $store_details[0]['store_url'] : "";
                $data['token'] = $token;
                $headers = [
                    'Content-Disposition' => 'inline',
                ];
                Mail::send('customer.emails.app.forget_password', ['data' => $data], function($message) use ($headers, $request) {
                    $message->to($request->email)
                            ->subject('Password Reset Request')
                            ->getHeaders()
                            ->addTextHeader('Content-Disposition', $headers['Content-Disposition']);
                });
                DB::commit();
                return $this->createResponse("We have e-mailed your password reset link!", self::HTTP_OK, $request->store_id);
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->createResponse($e->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
                // return $this->createResponse("Error occurred. Password reset email could not be sent.", self::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return $checkStoreId;
        }
    }

    private function tokenExpired($createdAt)
    {
        $expirationPeriod = config('auth.passwords.instore_customers.expire'); 
        $createdAt = Carbon::parse($createdAt);
        $differenceInMinutes = now()->diffInMinutes($createdAt);
        return $differenceInMinutes > $expirationPeriod;
    }

    public function verifyOTP(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required',
            'email' => 'required|email|exists:password_resets,email,store_id,' . $request->store_id,
            'otp'=> 'required'
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email, 
                'token' => $request->otp,
                'store_id' => $request->store_id,
            ])
            ->where('created_at', '>=', now()->subMinutes(config('auth.passwords.instore_customers.expire')))->first();
            if(!empty($updatePassword)) {
                return $this->createResponse("The OTP has been verified successfully.", self::HTTP_OK,$request->store_id);
            }
            else {
                return $this->createResponse("The OTP is invalid or has expired.", self::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            return $checkStoreId;
        }
    } 

    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required',
            'email' => 'required|email|exists:instore_customers,email,store_id,' . $request->store_id,
            'new_password'=> 'required|min:8',
            'confirm_password'=> 'required|min:8|same:new_password',
            'otp'=> 'required'
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email, 
                'token' => $request->otp,
                'store_id' => $request->store_id,
            ])
            ->where('created_at', '>=', now()->subMinutes(config('auth.passwords.instore_customers.expire')))->first();
            if(!empty($updatePassword)) {
                InStoreCustomer::where('email', $request->email) 
                    ->where('store_id', $request->store_id)
                    ->update([
                        'password' => Hash::make($request->new_password),
                        'plain_password' => $request->new_password,
                    ]);
                DB::table('password_resets')->where(['email'=> $request->email,'store_id'=> $request->store_id])->delete();
                return $this->createResponse("Your password was successfully changed.", self::HTTP_OK,$request->store_id);
            }
            else {
                return $this->createResponse("The OTP is invalid or has expired.", self::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            return $checkStoreId;
        }
        
    } 
}
