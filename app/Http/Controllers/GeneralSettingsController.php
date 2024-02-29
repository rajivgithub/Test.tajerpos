<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModuleLogos;
use Image;
use URL;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class GeneralSettingsController extends Controller
{
    public function adminGeneralSettings() {
        $moduleLogos = ModuleLogos::where([
            ['module_name', '=', 'admin'],
            ['is_deleted','=',0]
        ])->select('sidebar_logo','company_logo','logo_id','favicon')->first();
        return view('admin.general_settings',compact('moduleLogos'));
    }

    public function generalSettings(Request $request) {
        try {
            $input = $request->except('_token', 'company_logo', 'sidebar_logo', 'logo_id', 'remove_login_logo', 'remove_sidebar_logo','remove_favicon');
            $logo_id = !empty($request->logo_id) ? Crypt::decrypt($request->logo_id) : '';
            $url = URL::to("/");
            DB::beginTransaction();
            if ($request->hasFile('company_logo')) {
                $input['company_logo'] = $this->handleImage($request, 'company_logo', 250, 125);
            } elseif ($request->remove_login_logo == 1) {
                $input['company_logo'] = NULL;
            }
            
            if ($request->hasFile('sidebar_logo')) {
                $input['sidebar_logo'] = $this->handleImage($request, 'sidebar_logo', 120, 70);
            } elseif ($request->remove_sidebar_logo == 1) {
                $input['sidebar_logo'] = NULL;
            }

            if ($request->hasFile('favicon')) {
                $input['favicon'] = $this->handleImage($request, 'favicon', 48, 67);
            } elseif ($request->remove_favicon == 1) {
                $input['favicon'] = NULL;
            }

            if (empty($logo_id)) {
                $input['created_by'] = Auth::user()->id;
                ModuleLogos::create($input);
            } else {
                $input['updated_by'] = Auth::user()->id;
                ModuleLogos::where('logo_id', $logo_id)->update($input);
            }
            DB::commit();
            if ($request->module_name == "admin") {
                return redirect()->route(config('app.prefix_url') . '.admin.general-settings')->with('message', "Logo Updated Successfully");
            }
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getLogoImage(Request $request) {
        $moduleLogos = ModuleLogos::where([
            ['module_name', '=', 'admin'],
            ['is_deleted','=',0]
        ])->select('company_logo','logo_id','favicon')->first();
        return response()->json(['moduleLogos'=>$moduleLogos]);
    }

    public function handleImage(Request $request, $fieldName, $width, $height) {
        $image = $request->file($fieldName);
    
        if ($image) {
            $imageName = date('YmdHis') . $fieldName . "." . $image->extension();
    
            $baseDestinationPath = base_path().'/images/'.$request->module_name;
            $destinationPath = $baseDestinationPath . '/saved-logo';
    
            if ($request->has('store_id')) {
                $destinationPath = $baseDestinationPath . '/' . $request->store_id . '/saved-logo';
            }
    
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
    
            $imageUrl = URL::to("/") . '/images/' . $request->module_name . '/saved-logo/' . $imageName;
    
            $img = Image::make($image->path());
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . '/' . $imageName, 90);
    
            return $imageUrl;
        }
    }

}
