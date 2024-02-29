<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdmin\PaymentGatewayRequest;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\CommonController;

class PaymentGatewayController extends Controller
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
        $stripe_details = PaymentGateway::where([
            ['store_id', '=', Auth::user()->store_id],
            ['created_by', '=', Auth::user()->id],
            ['payment_type', '=', 'stripe']
        ])->get(['payment_credential_id','webhook_key','client_secret','client_id']);
        $paypal_details = PaymentGateway::where([
            ['store_id', '=', Auth::user()->store_id],
            ['created_by', '=', Auth::user()->id],
            ['payment_type', '=', 'paypal']
        ])->get(['payment_credential_id','webhook_key','client_secret','client_id','sandbox_mode']);
        $stripe_mode = (count($stripe_details) > 0) ? 'edit' : 'add';
        $paypal_mode = (count($paypal_details) > 0) ? 'edit' : 'add';
        return view('store_admin.payment_gateway.create',compact('store_url','stripe_details','stripe_mode','paypal_mode','paypal_details'));
    }

    public function store(PaymentGatewayRequest $request)
    {
        try {
            $mode = $request->mode;
            $input = $request->all();
            $payment_credential_id = ($mode == "edit") ? Crypt::decrypt($request->payment_credential_id) : 0;
            $store_id = Auth::user()->store_id;
            $input['sandbox_mode'] = !empty($input['sandbox_mode']) ? $input['sandbox_mode'] : 'no';
            if($mode == "edit") {
                //Reset the input values
                $remove_array_values = array('_token','mode','payment_credential_id');
                foreach($remove_array_values as $value) {
                    unset($input[$value]);
                }
                $input['updated_by'] = Auth::user()->id;
                PaymentGateway::where('payment_credential_id',$payment_credential_id)->update($input);
            } else {
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                PaymentGateway::create($input);
            }
            $success_message = ($mode == "edit") ? "Payment gateway updated successfully" : "Payment gateway added successfully.";
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.payment-gateway.create')->with('message',$success_message);
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
