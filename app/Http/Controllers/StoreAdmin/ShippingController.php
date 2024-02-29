<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdmin\ShippingRequest;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\Shipping;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\CommonController;

class ShippingController extends Controller
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
        $shipping_details = Shipping::where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0]
        ])->get(['api_account', 'private_key','api_credential_id','account_number','account_password'])->toArray();
        $mode = (count($shipping_details) > 0) ? 'edit' : 'add';
        return view('store_admin.shipping.create',compact('store_url','shipping_details','mode'));
    }

    public function store(Request $request)
    {
        try {
            $mode = $request->mode;
            $input = $request->except('_token','mode','api_credential_id');
            $api_credential_id = ($mode == "edit") ? Crypt::decrypt($request->api_credential_id) : 0;
            if($mode == "edit") {
                $input['updated_by'] = Auth::user()->id;
                Shipping::where('api_credential_id',$api_credential_id)->update($input);
            } else {
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                Shipping::create($input);
            }
            $success_message = ($mode == "add") ? trans('store-admin.added_msg',['name'=>trans('store-admin.shipping_api_credentials')]) : trans('store-admin.updated_msg',['name'=>trans('store-admin.shipping_api_credentials')]);
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.shipping.create')->with('message',$success_message);
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
