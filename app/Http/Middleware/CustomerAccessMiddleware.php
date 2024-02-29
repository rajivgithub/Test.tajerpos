<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin\Store;

class CustomerAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
        $store_details = Store::where('store_url',$this->store_url)->get(['store_id','customer_access']);
        if(!empty($store_details) && isset($store_details[0]) && $store_details[0]->customer_access == 1) {
            return $next($request);
        } else {
            // return view('errors.forbidden');
            abort(403);
        }
        
    }
}
