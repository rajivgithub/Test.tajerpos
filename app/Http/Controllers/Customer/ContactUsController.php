<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customers\ContactUs;
use App\Mail\Customer\ContactUsMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\CommonController;

class ContactUsController extends Controller
{
    protected $store_url;
    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
    }

    public function showContactUs() {
        $store_url = $this->store_url;
        $store_id = CommonController::get_store_id();
        return view("customer.contact_us",compact('store_url','store_id'));
    }

    public function saveQueries(Request $request) {
        try {
            $request->validate([
                'contactor_name' => 'required|max:100',
                'contactor_email' => 'required|email|max:150',
                'contactor_phone_no' => 'max:20',
                'contactor_message' => 'required'
            ], [], [
                'contactor_name' => __('customer.name'),
                'contactor_email' => __('customer.email'),
                'contactor_phone_no' => __('customer.phone_number'),
                'contactor_message' => __('customer.comment'),
            ]);            
            $store_details = CommonController::get_store_details();
            $input = $request->all();
            $input['store_id'] = CommonController::get_store_id();
            unset($input['_token']);
            ContactUs::create($input);
            // $details = [];
            // $details['store_details'] = $store_details;
            // $details['queries'] = $input;
            // if(!empty($store_details)) {
            //     $store_email = $store_details[0]['email'];
            //     Mail::to($store_email)->bcc('rajashree.vividinfotech@gmail.com')->send(new ContactUsMail($details));
            // }
            return redirect()->back()->with('message', trans('customer.inquiry_received'));
        } catch (Exception $e) {
            throw $e;
        }
    }
}
