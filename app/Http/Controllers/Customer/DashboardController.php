<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\Category;
use App\Models\Admin\Store;
use App\Http\Requests\Customer\RegisterRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Controllers\CommonController;
use URL;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\Admin\ChangePassword;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;
use App\Models\StoreAdmin\Product;
use App\Models\StoreAdmin\SubCategory;
use App\Models\CashierAdmin\InStoreCustomer;
use App\Mail\Customer\RegistrationEmail;
use App\Mail\Customer\LoginEmail;
use App\Mail\Customer\ChangePasswordEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\StoreAdmin\CustomerBanners;
use Carbon\Carbon;
use App\Models\Customers\ShoppingCart;
use App\Http\Controllers\Customer\WishlistController;

class DashboardController extends Controller
{
    protected $store_url;
    protected $guard = 'customer';
    protected $store_id;

    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
        $this->store_id = CommonController::get_store_id();
    }

    public function home()
    {
        $store_url = $this->store_url;
        $store_id = $this->store_id;
        CustomerBanners::where([
            ['store_id', '=', $store_id],
            ['is_deleted', '=', 0],
            ['end_date', '<', Carbon::now()],
        ])->update(['status' => 'expired']);
        $bannersDetails = CustomerBanners::where([
            ['store_id','=', $store_id],
            ['is_deleted','=', 0],
            ['status','=', 'active'],
        ])->select('banner_image','banner_url')->get();
        return view('customer.home',compact('store_url','store_id','bannersDetails'));
    }

    public function dashboard()
    {
        $store_url = $this->store_url;
        $store_id = $this->store_id;
        $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
        $customer_details = InStoreCustomer::select('customer_id','customer_name','email','profile_image','phone_number')->where('customer_id',$customer_id)->get();
        $breadcrumbs = [];
        $breadcrumbs[] = ['name' => trans('customer.your_account'), 'url' => "#"];
        return view('customer.dashboard',compact('store_url','customer_details','breadcrumbs','store_id'));
    }

    public function updateProfile(Request $request) {
        try {
            $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
            $store_id = $this->store_id;
            $this->validate($request, [
                'customer_name' => 'required',
                'email' => ['required', 'string', 'email', 'max:191',Rule::unique('instore_customers')->where(function ($query) use ($customer_id) {
                    $query->whereNotIn('customer_id', [$customer_id]);
                    return $query->where('email', request()->email);
                })],
                'phone_number' => 'required',
            ], [], [
                'customer_name' => __('customer.name'),
                'email' => __('customer.email'),
                'phone_number' => __('customer.phone_number'),
            ]);
            $input = $request->all();
            unset($input['_token']);
            DB::beginTransaction();
            InStoreCustomer::where('customer_id',$customer_id)->update($input);
            DB::commit();
            return redirect()->route($this->store_url.'.customer.dashboard')->with('message',trans('customer.updated_msg',['name'=>trans('customer.profile')]));
        } catch (Exception $e) {
            //Rollback Database Entry
            DB::rollback();
            throw $e;
        }
    }

    public function showRegister($type='')
    {
        $type = ($type != "") ? Crypt::decrypt($type) : "";
        $store_url = $this->store_url;
        $store_id = $this->store_id;
        if($type == "placeorder")
            return view('customer.cart_register',compact('store_url','store_id'));
        else
            return view('customer.register',compact('store_url','store_id'));
    }

    public function register(RegisterRequest $request)
    {
        try {
            $input = $request->all();
            $_type = !empty($request->_type) ? Crypt::decrypt($request->_type) : "";
            $store_id = $this->store_id;
            $input['plain_password'] = $request->password;
            $input['password'] = Hash::make($request->password);  
            $input['store_id'] = CommonController::get_store_id();
            InStoreCustomer::create($input);
            $details = [];
            $details['store_details'] = $store_details = CommonController::get_store_details();
            $details['store_url'] = $this->store_url;
            // if(!empty($store_details)) { 
            //     Mail::to($request->email)->bcc('rajashree.vividinfotech@gmail.com')->send(new RegistrationEmail($details));
            // }
            if ($_type == "placeorder") {
                if (Auth::guard($this->guard)->attempt(array('email' => $input['email'], 'password' => $input['plain_password'], 'store_id' => $input['store_id']))) {
                    return redirect()->route($this->store_url . '.customer.checkout');
                } else {
                    return redirect()->route($this->store_url . '.customer-login')->with('message', trans('customer.login_failed'));
                }
            } else {
                return redirect()->route($this->store_url . '.customer-login')->with('message', trans('customer.added_msg',['name'=>trans('customer.customer')]));
            }
        } catch (Exception $e) {
            return redirect()->route($this->store_url . '.customer-login')->with('error', trans('customer.error_occurred'));
        }
    }

    public function showLogin($type='')
    {
        $type = ($type != "") ? Crypt::decrypt($type) : "";
        $store_url = $this->store_url;
        $store_id = $this->store_id;
        if($type == "placeorder")
            return view('customer.cart_login',compact('store_url','store_id'));
        else {
            $current_url = Session::get('current_url');
            Session::forget('current_url');
            return view('customer.login',compact('store_url','current_url','store_id'));
        }
    }

    public function login(Request $request)
    {   
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ], [], [
            'email' => __('customer.username'),
            'password' => __('customer.password'),
        ]);
        $input = $request->all();
        $_type = !empty($request->_type) ? Crypt::decrypt($request->_type) : "";
        $store_id = $this->store_id;
        if (Auth::guard($this->guard)->attempt(array('email' => $input['email'], 'password' => $input['password'], 'store_id' => $store_id))) {
            $user = Auth::guard($this->guard)->user();
            session(['authenticate_user' => $user]);
            $details = [];
            $details['store_details'] = $store_details = CommonController::get_store_details();
            $details['store_url'] = $this->store_url;
            $details['customer_name'] = Auth::guard($this->guard)->user()->customer_name;
            // if(!empty($store_details)) { 
            //     Mail::to($input['email'])->bcc('rajashree.vividinfotech@gmail.com')->send(new LoginEmail($details));
            // }
            $cart_data = session()->get('cart', []);
            $wishlistRequestURL = Session::get('wishlistRequestURL');
            $wishlistData = Session::get('wishlistData');
            if(!empty($cart_data)) {
                foreach($cart_data as $productID => $product) {
                    if(!empty($product)) {
                        foreach($product as $key => $val) {
                            $addToCart = [];
                            $addToCart['store_id'] = $store_id;
                            $addToCart['customer_id'] = auth()->guard('customer')->user()->customer_id;
                            $addToCart['product_id'] = $productID;
                            if(is_array($val)) {
                                $addToCart['variants_id'] = $val['variants_combination_id'];
                                $addToCart['quantity'] = $val['quantity'];
                            } else {
                                if($key == 'quantity') 
                                    $addToCart['quantity'] = $product['quantity'];
                            }
                            $existingCart = ShoppingCart::where('store_id', $store_id)
                                ->where('customer_id', auth()->guard('customer')->user()->customer_id)
                                ->where('product_id', $addToCart['product_id'])
                                ->where('is_deleted', 0)
                                ->when(isset($addToCart['variants_id']) && !empty($addToCart['variants_id']),function($query) use ($addToCart) {
                                    return $query->where('variants_id', $addToCart['variants_id']);
                                })
                                ->first();
                            if ($existingCart) {
                                if(isset($addToCart['quantity']))
                                    $existingCart->quantity += $addToCart['quantity'];
                                $existingCart->save();
                            } else {
                                if(isset($addToCart['quantity']))
                                    ShoppingCart::create($addToCart);
                            }
                        }
                    }
                }
                Session::forget('cart');
            }  
            if($_type == "placeorder")
                return redirect()->route($this->store_url . '.customer.checkout');
            else if(!empty($wishlistRequestURL) && !empty($wishlistData)) {
                $requestInstance = new Request($wishlistData);
                $response = WishlistController::store($requestInstance);
                $responseData = json_decode($response->getContent(), true);
                $message = $responseData['message'];
                $type = $responseData['type'];
                Session::forget('wishlistRequestURL');
                Session::forget('wishlistData');
                return redirect($wishlistRequestURL)->with('message', $message)->with('type', $type);
            }
            else if(isset($input['current_url']) && !empty($input['current_url'])) 
                return redirect($input['current_url']);
            else {
                return redirect()->route($this->store_url . '.customer.dashboard');
            }
        } else {
            return back()->withErrors(['email' => trans('customer.login_error_msg')]);
        }
    }

    public function updatePassword(ChangePassword $request) {
        if(session()->has('authenticate_user'))
            $user = session('authenticate_user');  
        else
            $user = Auth::guard('customer')->user();  

        if (!(Hash::check($request->current_password, $user->password))) 
            return redirect()->back()->with("error",trans('customer.current_pwd_err'));
        // Current password and new password same
        if(strcmp($request->current_password, $request->new_password) == 0)
            return redirect()->back()->with("error",trans('customer.current_pwd_err'));
        
        $user->plain_password = encrypt($request->new_password);
        $user->password = Hash::make($request->new_password);
        $user->save();
        $details = [];
        $details['store_details'] = $store_details = CommonController::get_store_details();
        $details['store_url'] = $this->store_url;
        $details['customer_name'] = $user->customer_name;
        // if(!empty($store_details)) { 
        //     Mail::to($user->email)->bcc('rajashree.vividinfotech@gmail.com')->send(new ChangePasswordEmail($details));
        // }
        return redirect()->back()->with("message",trans('customer.current_pwd_err'));
    }

    public function profileImage(Request $request)
    {
        try {
            $user_id = Crypt::decrypt($request->user_id);
            $store_id = Auth::user()->store_id;
            $url = URL::to("/");
            //Profile Image
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $destinationPath = base_path().'/images/'.$store_id.$user_id.'/profile';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
                $profile_image_path = $url.'/images/'.$store_id.$user_id.'/profile'.'/'.$profileImage;
                $input['profile_image'] = $profile_image_path;
            }
            DB::beginTransaction();
            User::where('id',$user_id)->update($input);
            DB::commit();
            return redirect()->route($this->store_url.'.customer.dashboard')->with('message',"Profile Updated Successfully..!");
        } catch (Exception $e) {
            //Rollback Database Entry
            DB::rollback();
            throw $e;
        }
    }

    public function removeProfileImage(Request $request)
    {
        try {
            $user_id = Crypt::decrypt($request->user_id);
            $url = URL::to("/");
            $input['profile_image'] = $url.'/images/default-profile-image.jpg';
            DB::beginTransaction();
            User::where('id',$user_id)->update($input);
            DB::commit();
            return response()->json(['message'=>'Profile image removed successfully.']);
        } catch (Exception $e) {
            //Rollback Database Entry
            DB::rollback();
            throw $e;
        }
    }

    public function logout(Request $request)
    {
        Session::flush();
        Auth::guard('customer')->logout();
        return redirect()->route($this->store_url.'.customer-login');
    }

    public function getStoreDetails() {
        $store_details = CommonController::get_store_details();
        return response()->json(['store_details'=>$store_details]);
    }

}
