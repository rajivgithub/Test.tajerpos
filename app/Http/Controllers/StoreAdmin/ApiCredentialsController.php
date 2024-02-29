<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdmin\ApiCredentialRequest;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\ApiCredentials;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\CommonController;

class ApiCredentialsController extends Controller
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
        $api_credentials = ApiCredentials::where([
            ['store_id', '=', Auth::user()->store_id],
            ['created_by', '=', Auth::user()->id]
        ])->get(['google_recaptcha', 'site_key','api_credential_id']);
        $mode = (count($api_credentials) > 0) ? 'edit' : 'add';
        return view('store_admin.api_credentials.create',compact('store_url','api_credentials','mode'));
    }

    public function store(ApiCredentialRequest $request)
    {
        try {
            $mode = $request->mode;
            $input = $request->all();
            $input['google_recaptcha'] = !empty($input['google_recaptcha']) ? $input['google_recaptcha'] : 'no';
            $api_credential_id = ($mode == "edit") ? Crypt::decrypt($request->api_credential_id) : 0;
            if($mode == "edit") {
                //Reset the input values
                $remove_array_values = array('_token','mode','api_credential_id');
                foreach($remove_array_values as $value) {
                    unset($input[$value]);
                }
                $input['updated_by'] = Auth::user()->id;
                ApiCredentials::where('api_credential_id',$api_credential_id)->update($input);
            } else {
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                ApiCredentials::create($input);
            }
            $success_message = ($mode == "edit") ? "API credentials details updated successfully" : "API credentials details added successfully.";
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.api-credentials.create')->with('message',$success_message);
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
