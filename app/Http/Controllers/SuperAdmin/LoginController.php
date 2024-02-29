<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ModuleLogos;
use App;
use App\Services\LocaleService;

class LoginController extends Controller
{
    protected $localeService;

    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    public function showLogin()
    {
        $moduleLogos = ModuleLogos::where([
            ['module_name', '=', 'admin'],
            ['is_deleted', '=', 0]
        ])
        ->select('company_logo','logo_id')->first();
        return view('auth.login',compact('moduleLogos'));
    }

    public function login(Request $request)
    {   
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ], [], trans('admin'));
        $input = $request->all();
        if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'], 'is_admin' => 1)))      
            return redirect()->route(config('app.prefix_url').'.admin.home');
        else
            return redirect()->route(config('app.prefix_url').'.super-admin')->withErrors(['email' => trans('admin.login_error_msg')]); 
    }

}
