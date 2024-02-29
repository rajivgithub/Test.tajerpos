<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLanguage
{
    
    public function handle(Request $request, Closure $next)
    {
        $current_locale = session('current_locale');
        if (!empty($current_locale)) {
            app()->setLocale($current_locale);
        }
        return $next($request);
    }
}
