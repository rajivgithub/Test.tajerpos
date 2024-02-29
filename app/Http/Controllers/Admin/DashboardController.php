<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Store;
use App\Models\User;
use App\Models\State;
use App\Models\Cities;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\Profile;
use App\Http\Requests\Admin\ChangePassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use URL;
use Image;
use App\Models\Admin\Payment;
use App\Services\LocaleService;
use App\Models\ModuleLogos;
use App\Http\Controllers\GeneralSettingsController;

class DashboardController extends Controller
{
    protected $localeService;

    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
        $this->middleware('auth');
    }

    public function index()
    {
        Session::flush();
        Auth::logout();
        return redirect()->route(config('app.prefix_url').'.super-admin');
    } 

    public function dashboard()
    {
        $data['total_store_count'] = Store::select('store_id')->get()->count();
        $data['active_store_count'] = Store::select('store_id')->where('status',1)->get()->count();
        $data['inactive_store_count'] = Store::select('store_id')->where('status',0)->get()->count();
        $data['total_revenue'] = Payment::get()->sum('paid_amount');
        $month_based_revenue = Payment::select(
            DB::raw("(SUM(paid_amount)) as revenue"),
            DB::raw("MONTH(created_at) as month")
        )->whereYear('created_at', date('Y'))->groupBy('month')->get()->toArray();
        $month_based_revenue = collect($month_based_revenue);
        $data['month_based_revenue'] = collect(range(1, 12))->map(
            function ($month) use ($month_based_revenue) {
              $match = $month_based_revenue->firstWhere('month', $month);
              return $match ? $match['revenue'] : 0;
            }
        );
        return view('admin.dashboard',compact('data'));
    }

    public function editProfile() {
        $admin_details = User::select('id','name','email','company_name','phone_number','address','city_id','state_id','country_id','postal_code','message','profile_image','street_name','building_name',DB::raw('(SELECT company_logo FROM module_logos WHERE module_name = "admin" AND is_deleted = 0) as company_logo'))->where('id',Auth::user()->id)->get();
        $countries = Country::get(['id','name']);
        return view('admin.profile',compact('admin_details','countries'));
    }
    public function updateProfile(Profile $request) {
        try {
            $user_id = Crypt::decrypt($request->user_id);
            $input = $request->except('_token','user_id','is_admin','remove_profile_image','remove_company_logo');
            $logo_settings = [];
            $url = URL::to("/");
            //Profile Image
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $profileImage = date('YmdHis') . "." . $image->extension();
                $destinationPath = base_path().'/images';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $destinationPath = base_path().'/images/super-admin';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $destinationPath = base_path().'/images/super-admin/profile';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $input['profile_image'] = $url.'/images/super-admin/profile'.'/'.$profileImage;
                $img = Image::make($image->path());
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/'.$profileImage);
            } else {
                if($request->remove_profile_image == 1)
                    $input['profile_image'] = '';
            }
            //Company Logo
            // if ($request->hasFile('company_logo')) {
            //     $logo_settings['company_logo'] = GeneralSettingsController::handleImage($request, 'company_logo', 250, 125);
            // } else {
            //     if($request->remove_company_logo == 1)
            //         $logo_settings['company_logo'] = '';
            // }
            DB::beginTransaction();
            $email = Auth::user()->email;
            User::where('id',$user_id)->update($input);
            if(!empty($logo_settings)) {
                $logo_settings['updated_by'] = Auth::user()->id;
                ModuleLogos::where('module_name','admin')->update($logo_settings);
            }
            DB::commit();
            // $details = [
            //     'title' => 'Your profile details updated',
            //     'body' => 'This is for testing email for profile update'
            // ];
            // $ccEmails = ["rajashree.vividinfotech@gmail.com"];
            // $bccEmails = ["deva.vivid@gmail.com"];
            // \Mail::to($email)->cc($ccEmails)->bcc($bccEmails)->send(new \App\Mail\SuperAdmin\ProfileUpdate($details));
            return redirect()->route(config('app.prefix_url').'.admin.home')->with('message',trans('admin.profile_update_message'));
        } catch (Exception $e) {
            //Rollback Database Entry
            DB::rollback();
            throw $e;
        }
    }

    public function isEmailExist(Request $request) {
        $user_id = !empty($request->user_id) ? Crypt::decrypt($request->user_id) : '';
        $store_id = !empty($request->store_id) ? Crypt::decrypt($request->store_id) : '';
        $query = User::select('id')->where([
            ['email', '=', $request->email],
            ['is_deleted', '=', 0]
        ]);
        if(!empty($user_id))
            $query->whereNotIn('id',[$user_id]);
        if($request->type == "admin")
            $query->where('is_admin',[$request->is_admin]);
        if(!empty($store_id))
            $query->where('store_id',$store_id);
        $email_exist = $query->get()->count();
        return response()->json(['email_exist'=>$email_exist]);
    }

    public function changePassword() {
        return view('admin.change_password');
    }

    public function updatePassword(ChangePassword $request) {
        if (!(Hash::check($request->current_password, Auth::user()->password))) 
            return redirect()->back()->with("error",trans('admin.current_pwd_err'));
        // Current password and new password same
        if(strcmp($request->current_password, $request->new_password) == 0)
            return redirect()->back()->with("error",trans('admin.new_pwd_err'));
        $user = Auth::user();  
        $user->plain_password = encrypt($request->new_password);
        $user->password = Hash::make($request->new_password);
        $user->save();
        $details = [
            'title' => 'Your Password Updated',
            'body' => 'This is for testing email for updated the password'
        ];
        $ccEmails = ["rajashree.vividinfotech@gmail.com"];
        $bccEmails = ["deva.vivid@gmail.com"];
        // \Mail::to(Auth::user()->email)->cc($ccEmails)->bcc($bccEmails)->send(new \App\Mail\SuperAdmin\PasswordUpdate($details));
        return redirect()->back()->with("message",trans('admin.pwd_update_message'));
    }

    public function logout(Request $request)
    {
        Session::flush();
        Auth::logout();
        return redirect()->route(config('app.prefix_url').'.'."super-admin");
    }
}
