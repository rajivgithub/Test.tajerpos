<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdmin\GeneralSettingsRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\GeneralSettings;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Country;
use URL;
use App\Http\Controllers\CommonController;

class GeneralSettingsController extends Controller
{
    protected $store_url;
    public function __construct() {
        $this->store_url = CommonController::storeURL();
    }

    public function index()
    {
        //
    }

    public function create()
    {
        $store_url = $this->store_url; 
        $genral_settings_details = GeneralSettings::where([
            ['store_id', '=', Auth::user()->store_id],
            ['created_by', '=', Auth::user()->id]
        ])->get(['settings_id','system_name','system_white_logo','system_black_logo','email_logo','country_id','system_timezone','background_image']);
        $mode = (count($genral_settings_details) > 0) ? 'edit' : 'add';
        $countries = Country::get(['id','name']);
        return view('store_admin.general_settings.create',compact('store_url','genral_settings_details','mode','countries'));
    }

    public function store(GeneralSettingsRequest $request)
    {
        try {
            $mode = $request->mode;
            $input = $request->all();
            $settings_id = ($mode == "edit") ? Crypt::decrypt($request->settings_id) : 0;
            $store_id = Auth::user()->store_id;
            $url = URL::to("/");
            $destinationPath = base_path().'/images/';
            if (!file_exists($destinationPath)) 
                mkdir($destinationPath, 0777, true);
            $destinationPath = base_path().'/images/'.$store_id;
            if (!file_exists($destinationPath)) 
                mkdir($destinationPath, 0777, true);
            $destinationPath = base_path().'/images/'.$store_id.'/logo';
            if (!file_exists($destinationPath)) 
                mkdir($destinationPath, 0777, true);
            if ($request->hasFile('system_white_logo_image')) {
                $image = $request->file('system_white_logo_image');
                $rand_number = rand(0000,9999);
                $whiteLogoImage = $rand_number.date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $whiteLogoImage);
                $white_logo_path = $url.'/images/'.$store_id.'/logo'.'/'.$whiteLogoImage;
                $input['system_white_logo'] = $white_logo_path;
            }
            if ($request->hasFile('system_black_logo_image')) {
                $image = $request->file('system_black_logo_image');
                $rand_number = rand(0000,9999);
                $blackLogoImage = $rand_number.date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $blackLogoImage);
                $black_logo_path = $url.'/images/'.$store_id.'/logo'.'/'.$blackLogoImage;
                $input['system_black_logo'] = $black_logo_path;
            }
            if ($request->hasFile('email_logo_image')) {
                $image = $request->file('email_logo_image');
                $rand_number = rand(0000,9999);
                $emailLogoImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $emailLogoImage);
                $email_logo_path = $url.'/images/'.$store_id.'/logo'.'/'.$emailLogoImage;
                $input['email_logo'] = $email_logo_path;
            }
            if ($request->hasFile('admin_login_image')) {
                $image = $request->file('admin_login_image');
                $rand_number = rand(0000,9999);
                $adminLoginImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $adminLoginImage);
                $background_image_path = $url.'/images/'.$store_id.'/logo'.'/'.$adminLoginImage;
                $input['background_image'] = $background_image_path;
            }
            if($mode == "edit") {
                //Reset the input values
                $remove_array_values = array('_token','mode','system_white_logo_image','system_black_logo_image','email_logo_image','admin_login_image','settings_id');
                foreach($remove_array_values as $value) {
                    unset($input[$value]);
                }
                $input['updated_by'] = Auth::user()->id;
                GeneralSettings::where('settings_id',$settings_id)->update($input);
            } else {
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                GeneralSettings::create($input);
            }
            $success_message = ($mode == "edit") ? "General settings updated successfully" : "General settings added successfully.";
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.general-settings.create')->with('message',$success_message);
        } catch (Exception $e) {
            throw $e;
        }
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
