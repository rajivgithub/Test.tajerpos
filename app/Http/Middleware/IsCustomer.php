<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;
use App\Http\Controllers\CommonController;
use Auth;

class IsCustomer
{
    public function handle(Request $request, Closure $next)
    {
        $store_id = CommonController::get_store_id();
        if (auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $store_id) {
            return $next($request);
        } else {
            $current_url = request()->path();
            $split_url = explode("/",$current_url);
            $store_url = (!empty($split_url)) ?$split_url[0] : '';
            if(empty($split_url) || (!empty($split_url) && isset($split_url[1]) && $split_url[1] != "customer-login" && $split_url[1] != "customer-register")) {
                Session::forget('current_url');
                Session::put('current_url', url()->current());
            }
            return redirect()->route($store_url.'.customer-login');
        }
    }
}
