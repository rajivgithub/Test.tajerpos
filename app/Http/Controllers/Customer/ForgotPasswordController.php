<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB; 
use Carbon\Carbon; 
use App\Models\User; 
use Mail; 
use Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\CommonController;
use App\Models\CashierAdmin\InStoreCustomer;

class ForgotPasswordController extends Controller
{
    protected $store_url;
    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
        $this->middleware('throttle:3,1')->only('forgetPassword');
    }

    public function showForgetPassword()
    {
        $store_url = $this->store_url;
        $store_id = CommonController::get_store_id();
        return view('customer.forget_password',compact('store_url','store_id'));
    }

    public function forgetPassword(Request $request)
    { 
        $store_id = CommonController::get_store_id();
        $request->validate([
            'email' => 'required|email|exists:instore_customers,email,store_id,' . $store_id,
        ], [], [
            'email' => __('customer.email'),
        ]);   
        $existingToken = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('store_id', $store_id)
        ->first();
        if ($existingToken && !$this->tokenExpired($existingToken->created_at)) {
            return back()->with('warning', trans('customer.password_reset_sent'));
        } else {
            DB::table('password_resets')->where(['email'=> $request->email,'store_id'=> $store_id])->delete();
        }
        $token = Str::random(64);
        DB::table('password_resets')->insert([
            'store_id' => $store_id,
            'email' => $request->email, 
            'token' => $token, 
            'created_at' => Carbon::now()
        ]);
        // $data['store_details'] = CommonController::get_store_details();
        // $data['store_url'] = $this->store_url;
        // $data['token'] = $token;
        // $headers = [
        //     'Content-Disposition' => 'inline',
        // ];
        // Mail::send('customer.forget_password_email', ['data' => $data], function($message) use ($headers, $request) {
        //     $message->to($request->email)
        //             ->subject('Reset Password')
        //             ->getHeaders()
        //             ->addTextHeader('Content-Disposition', $headers['Content-Disposition']);
        // });
        return back()->with('message', trans('customer.password_reset_link_sent'));
    }

    private function tokenExpired($createdAt)
    {
        $expirationPeriod = config('auth.passwords.instore_customers.expire'); 
        $createdAt = Carbon::parse($createdAt);
        $differenceInMinutes = now()->diffInMinutes($createdAt);
        return $differenceInMinutes > $expirationPeriod;
    }

    public function showResetPassword($token) { 
        $store_url = $this->store_url;
        $request = new Request();
        $category_details = CommonController::get_category_details($request);
        $store_id = CommonController::get_store_id();
        return view('customer.password_reset',compact('token','store_url','category_details','store_id'));
    }

    public function resetPassword(Request $request)
    {
        $store_id = CommonController::get_store_id();
        $request->validate([
            'email' => 'required|email|exists:instore_customers,email,store_id,' . $store_id,
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);
        $updatePassword = DB::table('password_resets')
        ->where([
            'email' => $request->email, 
            'token' => $request->token,
            'store_id' => $store_id,
        ])
        ->where('created_at', '>=', now()->subMinutes(config('auth.passwords.instore_customers.expire')))->first();
        if(!$updatePassword)
            return back()->withInput()->with('error', trans('customer.invalid_expired_token'));
        InStoreCustomer::where('email', $request->email) 
            ->where('store_id', $store_id)
            ->update([
                'password' => Hash::make($request->password),
                'plain_password' => $request->password,
            ]);
        DB::table('password_resets')->where(['email'=> $request->email,'store_id'=> $store_id])->delete();
        return redirect()->route($this->store_url.'.customer-login')->with('message', trans('customer.pwd_update_message'));
    }
}

