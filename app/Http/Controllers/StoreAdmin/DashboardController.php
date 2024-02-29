<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\StoreAdmin\Product;
use App\Models\Customers\User;
use App\Models\User as StoreAdminUser;
use App\Models\Country;
use App\Http\Requests\Admin\Profile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\ChangePassword;
use Illuminate\Support\Facades\Hash;
use URL;
use App\Http\Controllers\CommonController;
use Image;

class DashboardController extends Controller
{
    protected $store_url;
    public function __construct() {
        $this->store_url = CommonController::storeURL();
    }

    public function showLogin()
    {
        $store_url = $this->store_url;
        return view('store_admin.login',compact('store_url'));
    }

    public function dashboard()
    {
        $store_url = $this->store_url;
        $data['total_product_count'] = Product::select('product_id')->where([
            ['store_id', '=', Auth::user()->store_id],
            ['status', '=', '1'],
            ['is_deleted','=','0']
        ])->get()->count();
        $data['total_customer_count'] = User::select('customer_id')->where([
            ['store_id', '=', Auth::user()->store_id],
            ['status', '=', '1']
        ])->get()->count();
        return view('store_admin.dashboard',compact('store_url','data'));
    }

    
    public function login(Request $request)
    {   
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ], 
        [
            'email.required' => 'The user name field is required.',
            'email.email' => 'The user name field is invalid.'
        ]);
        $input = $request->all();
        $store_details = Store::where('store_url',$this->store_url)->get('store_id');
        $store_id = (!empty($store_details)) ? $store_details[0]['store_id'] : 0;
        if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'], 'store_id' => $store_id)))
        {
            if (auth()->user()->is_admin == 2) 
                return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.home');
            else
                return redirect()->route('home');
        }else
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.store-login')->withErrors(['email' => 'Email-Address And Password Are Wrong.']);
    }

    public function logout(Request $request)
    {
        Session::flush();
        Auth::logout();
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url);
    }

    public function editProfile() {
        $store_url = $this->store_url;
        $admin_details = StoreAdminUser::select('id','name','email','company_name','phone_number','address','city_id','state_id','country_id','postal_code','message','profile_image','store_id','street_name','building_name')->where('id',Auth::user()->id)->get();
        $countries = Country::get(['id','name']);
        return view('store_admin.profile',compact('admin_details','countries','store_url'));
    }

    public function updateProfile(Profile $request) {
        try {
            $input = $request->all();
            $user_id = Crypt::decrypt($input['user_id']);
            $store_id = Auth::user()->store_id;
            //Reset the input values
            $remove_array_values = array('_token','user_id','is_admin','store_id','remove_image');
            foreach($remove_array_values as $value) {
                unset($input[$value]);
            }
            $url = URL::to("/");

            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $profileImage = date('YmdHis') . "." . $image->extension();
                $destinationPath = base_path().'/images/';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $destinationPath = base_path().'/images/'.$store_id;
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $destinationPath = base_path().'/images/'.$store_id.'/profile';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $input['profile_image'] = $url.'/images/'.$store_id.'/profile'.'/'.$profileImage;
                $img = Image::make($image->path());
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/'.$profileImage);
            } else {
                if($request->remove_image == 1)
                    $input['profile_image'] = '';
            }
            DB::beginTransaction();
            StoreAdminUser::where('id',$user_id)->update($input);
            DB::commit();
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.home')->with('message',"Profile Updated Successfully..!");
        } catch (Exception $e) {
            //Rollback Database Entry
            DB::rollback();
            throw $e;
        }
    }

    public function changePassword() {
        $store_url = $this->store_url;
        return view('store_admin.change_password',compact('store_url'));
    }

    public function updatePassword(ChangePassword $request) {
        if (!(Hash::check($request->current_password, Auth::user()->password))) 
            return redirect()->back()->with("error","Your current password does not matches with the password.");
        // Current password and new password same
        if(strcmp($request->current_password, $request->new_password) == 0)
            return redirect()->back()->with("error","New Password cannot be same as your current password.");
        $user = Auth::user();  
        $user->plain_password = encrypt($request->new_password);
        $user->password = Hash::make($request->new_password);
        $user->save();
        return redirect()->back()->with("message","Password successfully changed!");
    }
    
    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
