<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsCashierAdmin
{

    public function handle(Request $request, Closure $next)
    {
        if((auth()->check() && auth()->user()->is_admin == 3) || (auth()->check() && auth()->user()->is_admin == 2)) {
            return $next($request);
        } else {
            $current_url = request()->path();
            $split_url = explode("/",$current_url);
            $split_url_index = config('app.split_url_index');
            $store_url =  (!empty($split_url)) ?$split_url[$split_url_index] : '';
            return redirect()->route(config('app.prefix_url').'.'.$store_url);
        }
    }
}
