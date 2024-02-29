<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdmin\NewsLettersRequest;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\NewsLetters;
use App\Models\StoreAdmin\Subscriber;
use App\Models\Customers\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\CommonController;

class NewsLettersController extends Controller
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
        $newsletters_details = NewsLetters::where([
            ['store_id', '=', Auth::user()->store_id],
            ['created_by', '=', Auth::user()->id]
        ])->get(['user_id', 'subscriber_id','newsletter_id','subject','content']);
        $customer_details = User::where([
            ['store_id', '=', Auth::user()->store_id]
        ])->get(['customer_id', 'customer_email']);
        $customer_details = User::where([
            ['store_id', '=', Auth::user()->store_id]
        ])->get(['customer_id', 'customer_email']);
        $subscriber_details = Subscriber::where([
            ['store_id', '=', Auth::user()->store_id]
        ])->get(['subscriber_id', 'subscriber_email']);
        $mode = (count($newsletters_details) > 0) ? 'edit' : 'add';
        return view('store_admin.newsletters.create',compact('store_url','newsletters_details','mode','customer_details','subscriber_details'));
    }

    public function store(NewsLettersRequest $request)
    {
        try {
            $mode = $request->mode;
            $input = $request->all();
            $newsletter_id = ($mode == "edit") ? Crypt::decrypt($request->newsletter_id) : 0;
            if($mode == "edit") {
                //Reset the input values
                $remove_array_values = array('_token','mode','newsletter_id');
                foreach($remove_array_values as $value) {
                    unset($input[$value]);
                }
                $input['updated_by'] = Auth::user()->id;
                NewsLetters::where('newsletter_id',$newsletter_id)->update($input);
            } else {
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                NewsLetters::create($input);
            }
            $success_message = ($mode == "edit") ? "NewsLetters details updated successfully" : "NewsLetters details added successfully.";
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.newsletters.create')->with('message',$success_message);
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
