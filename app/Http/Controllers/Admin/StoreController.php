<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Store;
use App\Models\User;
use App\Models\Country;
use App\Models\Admin\Payment;
use App\Models\Admin\PaymentHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\CreateStore;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Image;
use URL;
use App\Mail\SuperAdmin\StoreCreate;
use PDF;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        if($request->type != "") {
            $final_data=array();
            $columns = array( 
                0 =>'id',
                1=> 'store_number',
                2=> 'store_name',
                3=> 'web_status',
                4=> 'app_status',
                5=> 'cashier_status',
                6=> 'validity',
                7=> 'action'
            );
            $limit = $request->length;
            $start = $request->start; 
            $order = $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $order = ($columns[$request->order[0]['column']] == "id") ? 'stores.store_id' : $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $search = $request->search['value']; 
            $where_cond = 'where stores.created_by = "'.Auth::user()->id.'" AND stores.is_deleted = "0" AND users.is_store = "Yes"';
            if(($request->type == '0') || ($request->type == '1'))
                $where_cond .= 'AND stores.status = "'.$request->type.'"';
            else if($request->type == 'recent') {
                $date = \Carbon\Carbon::today()->subDays(7);
                $where_cond .= 'AND stores.created_at >= "'.$date.'"';
            }
            if(!empty($request->search['value'])) 
                $where_cond .= "AND (store_number LIKE '%".$search."%' or store_name LIKE '%".$search."%' or store_phone_number LIKE '%".$search."%' or email LIKE '%".$search."%'  or CONCAT(DATEDIFF(store_validity_date, NOW()),' days') LIKE '%".$search."%')";
            $store_details = DB::select('SELECT app_status, web_status,cashier_status,customer_access, store_number, store_name, store_phone_number, email, store_validity_date, stores.store_id,store_logo, stores.status,CONCAT(DATEDIFF(store_validity_date, NOW())," days") as validity FROM users LEFT JOIN stores on stores.store_id = users.store_id '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $filtered_store_details = DB::select('SELECT stores.store_id FROM users LEFT JOIN stores on stores.store_id = users.store_id '.$where_cond);
            $count_query = Store::join("users",function($join){
                $join->on("users.store_id","=","stores.store_id");
            })->where([
                ['stores.is_deleted', '=', 0],
                ['stores.created_by', '=', Auth::user()->id],
                ['users.is_store', '=', 'Yes'],
            ]);
            $totalCount = $count_query->get()->count();
            if(!empty($store_details)) {
                $i=0;$j=0;
                foreach($store_details as $store) {
                    $app_access_checked = $store->app_status == 1 ? 'checked' : '';
                    $web_access_checked = $store->web_status == 1 ? 'checked' : '';
                    $cashier_status_checked = $store->cashier_status == 1 ? 'checked' : '';
                    $customer_access = $store->customer_access == 1 ? 'checked' : '';
                    $final_data[$i]=array('id'=>++$j,
                        'store_number'=>$store->store_number,
                        'store_name'=>$store->store_name,
                        'store_logo' => '<img src="' . $store->store_logo . '" class="img-sm img-thumbnail" alt="Item">',
                        'email'=>$store->email,
                        'store_phone_number'=>$store->store_phone_number,
                        'web_status'=>
                            "<div class='form-check form-switch ps-0'>
                                <input class='form-check-input ms-0 web-access application-access' type='checkbox' name='web_status' value='1' $web_access_checked role='switch' id='flexSwitchCheckDefault'>
                            </div>",
                        'app_status'=>
                            "<div class='form-check form-switch ps-0'>
                                <input class='form-check-input ms-0 app-access application-access' type='checkbox' name='app_status' value='1' $app_access_checked role='switch' id='flexSwitchCheckDefault'>
                            </div>",
                        'cashier_status'=>
                            "<div class='form-check form-switch ps-0'>
                                <input class='form-check-input ms-0 cashier-access application-access' type='checkbox' name='cashier_status' value='1' $cashier_status_checked role='switch' id='flexSwitchCheckDefault'>
                            </div>",
                        'customer_access'=>
                            "<div class='form-check form-switch ps-0'>
                                <input class='form-check-input ms-0 customer-access application-access' type='checkbox' name='customer_access' value='1' $customer_access role='switch' id='flexSwitchCheckDefault'>
                            </div>",
                        'validity'=>$store->validity,
                        // 'action'=>
                        //     "<a class='btn btn-warning text-white rounded-circle font-sm p-2' title='View' href='".route(config('app.prefix_url').'.admin.store.show', Crypt::encrypt($store->store_id))."'><i class='fa fa-eye'></i></a>
                        //     <a class='btn btn-success text-white  rounded-circle font-sm p-2' title='Edit' href='".route(config('app.prefix_url').'.admin.store.create', Crypt::encrypt($store->store_id))."'><i class='fa fa-edit'></i></a>
                        //     <a class='btn btn-danger text-white rounded-circle font-sm p-2' title='Delete' href='".route(config('app.prefix_url').'.admin.store.destroy', Crypt::encrypt($store->store_id))."'><i class='fa fa-trash'></i></a>
                        //     <a href='".route(config('app.prefix_url').'.admin.store.payment', Crypt::encrypt($store->store_id))."' class='btn btn-dark text-white rounded-circle font-sm p-2' title='Payment'><i class='fa fa-history'></i></a> 
                        //     <a href='#' class='btn btn-info text-white rounded-circle font-sm p-2 send-reminder' data-bs-toggle='modal' data-bs-target='#reminderModal' title='Reminder'><i class='fa fa-envelope'></i></a>
                        //     <a href='".route(config('app.prefix_url').'.admin.store.invoice', Crypt::encrypt($store->store_id))."' target='_blank' class='btn btn-secondary text-white rounded-circle font-sm p-2' style='padding: 8px 13px;' title='Invoice'><i class='fa fa-print'></i></a>
                        //     <input type='hidden' class='store_id' value='".Crypt::encrypt($store->store_id)."'>" 
                        'action'=>
                            "<a class='btn btn-warning text-white rounded-circle font-sm p-2' title='View' href='".route(config('app.prefix_url').'.admin.store.show', Crypt::encrypt($store->store_id))."'><i class='fa fa-eye'></i></a>
                            <a class='btn btn-success text-white  rounded-circle font-sm p-2' title='Edit' href='".route(config('app.prefix_url').'.admin.store.create', Crypt::encrypt($store->store_id))."'><i class='fa fa-edit'></i></a>
                            <a class='btn btn-danger text-white rounded-circle font-sm p-2 store-delete' title='Delete' href='".route(config('app.prefix_url').'.admin.store.destroy', Crypt::encrypt($store->store_id))."'><i class='fa fa-trash'></i></a>
                            <input type='hidden' class='store_id' value='".Crypt::encrypt($store->store_id)."'>" 
                    );
                    $i++;
                }
            }
            $totalFiltered = count($filtered_store_details);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data   
            );
            echo json_encode($json_data); 
        }
        else 
            return view('admin.store.list');
    }

    public function create($id=null)
    {
        $store_details = [];
        if(!empty($id)) {
            $store_id = Crypt::decrypt($id);
            $store_details = Store::join('users', 'stores.store_id', '=', 'users.store_id')->leftJoin('countries', 'users.country_id', '=', 'countries.id')->leftJoin('states', 'users.state_id', '=', 'states.id')->leftJoin('cities', 'users.city_id', '=', 'cities.id')->where('stores.store_id', $store_id)->get(['stores.store_id','store_user_name','store_validity_date','store_name','store_phone_number','users.country_id','users.city_id','users.state_id','postal_code','store_logo','street_name','building_name','email','users.id as user_id','store_url','plain_password','cities.name','states.name','countries.name','store_background_image']);
        }
        $countries = Country::get(['id','name']);
        $mode = !empty($id) ? 'edit' : 'add';
        return view('admin.store.create',compact('store_details','mode','countries'));
    }

    public function store(CreateStore $request)
    {
        try {
            $input = $request->except('_token','mode','store_id','user_id','email','store_password','store_logo_image','store_background_image','add_payment_details','building_name','street_name');
            $mode = $request->mode;
            $user_id = ($mode == "edit" && !empty($request->user_id)) ? Crypt::decrypt($request->user_id) : 0;
            $store_id = ($mode == "edit" && !empty($request->store_id)) ? Crypt::decrypt($request->store_id) : 0;
            //Start DB Transaction
            DB::beginTransaction();
            if($mode == "add") {
                $input['created_by'] = Auth::user()->id;
                $store_id = Store::create($input)->store_id;
                $update_data = array();
                $update_data['store_number'] = "SHOP".sprintf("%03d",$store_id);
                Store::where('store_id',$store_id)->update($update_data);
            } else {
                $input['updated_by'] = Auth::user()->id;
                Store::where('store_id',$store_id)->update($input);
            }
            $url = URL::to("/");
            $upload_image = array(); $store_user = array();
            //Upload the logo according to store id
            if ($request->hasFile('store_logo_image')) {
                $image = $request->file('store_logo_image');
                $logoImage = date('YmdHis') . "." . $image->extension();
                $destinationPath = base_path().'/images/';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $destinationPath = base_path().'/images/'.$store_id;
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $destinationPath = base_path().'/images/'.$store_id.'/logo';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $upload_image['store_logo'] = $store_user['company_logo'] = $url.'/images/'.$store_id.'/logo/'.$logoImage;
                $img = Image::make($image->path());
                $img->resize(250, 150, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/'.$logoImage);
            }
            if ($request->hasFile('store_background_image')) {
                $image = $request->file('store_background_image');
                $backgroundImage = date('YmdHis') . "." . $image->extension();
                $destinationPath = base_path().'/images/';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $destinationPath = base_path().'/images/'.$store_id;
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $destinationPath = base_path().'/images/'.$store_id.'/background-image';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $upload_image['store_background_image'] = $url.'/images/'.$store_id.'/background-image/'.$backgroundImage;
                $img = Image::make($image->path());
                $img->resize(1600, 1067, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/'.$backgroundImage);
            }
            if(!empty($upload_image))
                Store::where('store_id',$store_id)->update($upload_image);
            //Create the Store User
            $store_user['name'] = $request->store_user_name;        
            $store_user['email'] = $request->email; 
            $store_user['store_id'] = $store_id;
            $store_user['is_admin'] = 2;
            $store_user['is_store'] = "Yes";
            $store_user['company_name'] = $request->store_name;
            $store_user['phone_number'] = $request->store_phone_number;
            $store_user['street_name'] = $request->street_name;
            $store_user['building_name'] = $request->building_name;
            $store_user['country_id'] = $request->store_country;
            $store_user['state_id'] = $request->store_state;
            $store_user['city_id'] = $request->store_city;
            $store_user['postal_code'] = $request->store_postal_code;
            $store_user['plain_password'] = encrypt($request->store_password);   
            $store_user['password'] = Hash::make($request->store_password); 
            if($mode == "add") {
                $store_user['created_by'] = Auth::user()->id;
                User::create($store_user);
            } else {
                $store_user['updated_by'] = Auth::user()->id;
                User::where('id',$user_id)->update($store_user);
            }
            //Commit Transaction to Save Data to Database
            DB::commit();
            /*if($mode == "add") {
                $details = [
                    'title' => 'Your store is created successfully',
                    'body' => 'Congratulations on starting your journey with eMonta',
                    'password' => $store_password,
                ];
                $ccEmails = ["rajashree.vividinfotech@gmail.com"];
                $bccEmails = ["deva.vivid@gmail.com","rajashree.vividinfotech@gmail.com"];
                \Mail::to($request->email)
                ->cc($ccEmails)
                ->bcc($bccEmails)
                ->send(new StoreCreate($details));
            }*/
            $success_message = ($mode == "add") ? trans('admin.store_success_msg') : trans('admin.store_update_msg');
            // if($mode == "add" || $request->add_payment_details == "1") {
            //     $type = ($request->add_payment_details == "1") ? 'add-payment' : "";
            //     return redirect(config('app.prefix_url').'/admin/store/add-payment/'.Crypt::encrypt($store_id).'/'.$type)->with('message',$success_message);
            // }
            // else
                return redirect()->route(config('app.prefix_url').'.admin.store.index')->with('message',$success_message);
        } catch (Exception $e) {
            //Rollback Database Entry
            DB::rollback();
            throw $e;
        }
    }

    public function show($id)
    {
        $store_id = Crypt::decrypt($id);
        $store_details = Store::join('users', 'stores.store_id', '=', 'users.store_id')->leftJoin('countries', 'users.country_id', '=', 'countries.id')->leftJoin('states', 'users.state_id', '=', 'states.id')->leftJoin('cities', 'users.city_id', '=', 'cities.id')->where([
            ['stores.store_id','=', $store_id],
            ['stores.is_deleted','=', 0]
        ])->get(['stores.store_id','store_user_name','store_validity_date','store_name','store_phone_number','users.country_id','users.city_id','users.state_id','postal_code','store_logo','street_name','building_name','email','users.id as user_id','store_url','plain_password','cities.name as city_name','states.name as state_name','countries.name as country_name','store_background_image']);
        return view('admin.store.show',compact('store_details'));
    }

    public function update(Request $request)
    {
        $store_id = Crypt::decrypt($request->store_id);
        $update_access = array();
        $update_access[$request->type] = $request->access;
        $update_access['updated_by'] = Auth::user()->id;
        Store::where('store_id',$store_id)->update($update_access);
        return response()->json(['message'=>trans('admin.status_update_msg')]);
    }

    public function updateStatus(Request $request)
    {
        $store_id = $request->store_id;
        $update_access = array();
        $update_access['status'] = $request->store_status;
        $update_access['updated_by'] = Auth::user()->id;
        Store::where('store_id',$store_id)->update($update_access);
        return response()->json(['message'=>trans('admin.status_update_msg')]);
    }

    public function destroy($id)
    {
        $store_id = Crypt::decrypt($id);
        $delete_store = array();
        $delete_store['is_deleted'] = 1;  
        $delete_store['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_store['updated_by'] = Auth::user()->id;
        Store::where('store_id',$store_id)->update($delete_store);
        User::where('store_id',$store_id)->update($delete_store);
        return redirect()->route(config('app.prefix_url').'.admin.store.index')->with('message',trans('admin.store_delete_msg'));
    }

    public function paymentHistory(Request $request,$store_id)
    {
        $data = [];
        $store_id = Crypt::decrypt($store_id);
        $final_data=array();
        $columns = array( 
            0 =>'id',
            1=> 'payment_method',
            2=> 'package_amount',
            3=> 'tax_percentage',
            4=> 'tax_amount',
            5=> 'discount',
            6=> 'discount_type',
            7=> 'discount_amount',
            8=> 'paid_amount',
            9=> 'created_at',
            10=> 'total_amount',
            11=> 'amount_payable',
            12=> 'balance_amount',
        );
        if(!empty($request->type)) {
            $limit = $request->length;
            $start = $request->start; 
            $order = ($columns[$request->order[0]['column']] == "id") ? 'payment_id' : $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $where_cond = 'where payment_history.store_id = "'.$store_id.'"';
            if(!empty($request->type) && $request->type != "all") 
                $where_cond .= 'AND payment_method = "'.$request->type.'"';
            if(!empty($request->search['value'])) {
                $search = $request->search['value']; 
                $where_cond .= "AND (payment_method LIKE '%".$search."%' or package_amount LIKE '%".$search."%'  or paid_amount LIKE '%".$search."%' or tax_percentage LIKE '%".$search."%'  or tax_amount LIKE '%".$search."%' or total_amount LIKE '%".$search."%' or amount_payable LIKE '%".$search."%' or discount LIKE '%".$search."%'  or discount_type LIKE '%".$search."%' or discount_amount LIKE '%".$search."%'  or balance_amount LIKE '%".$search."%' or DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE '%".$search."%')";
            }
            $payment_details = DB::select('SELECT payment_method, package_amount, paid_amount, tax_percentage, tax_amount, discount, discount_type, discount_amount, balance_amount,total_amount,amount_payable,'.DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as created_at").' FROM payment_history '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $total_payment_details =  Payment::where('store_id', $store_id)->orderBy('payment_id', 'desc')->limit(1)->get(['amount_payable','paid_amount','balance_amount']);
            $count_query = PaymentHistory::where('store_id', $store_id);
            $totalCount = $count_query->get()->count();
            if(!empty($payment_details)) {
                $i=0;$j=0;
                foreach($payment_details as $payment) {
                    $final_data[$i]=array('id'=>++$j,
                        'payment_method'=>$payment->payment_method,
                        'package_amount'=>$payment->package_amount,
                        'tax_percentage'=>$payment->tax_percentage,
                        'tax_amount'=>$payment->tax_amount,
                        'discount'=>$payment->discount,
                        'discount_type'=>$payment->discount_type,
                        'discount_amount'=>$payment->discount_amount,
                        'paid_amount'=>$payment->paid_amount,
                        'created_at'=>$payment->created_at,
                        'total_amount'=>$payment->total_amount,
                        'amount_payable'=>$payment->amount_payable,
                        'balance_amount'=>$payment->balance_amount,
                    );
                    $i++;
                }
            }
            $totalFiltered = count($payment_details);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data,
                "total_payment_details" => $total_payment_details
            );
            echo json_encode($json_data); 
        } else {
            $store_details = Store::where('store_id', $store_id)->get(['store_name','store_id']);
            return view('admin.store.payment_history',compact('store_id','store_details'));
        }
    }  

    public function isUrlExist(Request $request) {
        $is_admin = ($request->type == "admin") ? 1 : 2;
        $store_id = !empty($request->store_id) ? Crypt::decrypt($request->store_id) : '';
        $query = Store::select('store_id')->where([
            ['store_url', '=', $request->store_url],
            ['is_deleted', '=', 0]
        ]);
        if(!empty($store_id))
            $query->whereNotIn('store_id',[$store_id]);
        $url_exist = $query->get()->count();
        return response()->json(['url_exist'=>$url_exist]);
    }

    public function createPayment($store_id=null,$type=null) {
        if(!empty($store_id)) {
            $payment_details = [];
            if($type == "pending" || $type == "add-payment") {
                $decrypt_store_id = Crypt::decrypt($store_id);
                $payment_details = Payment::select('package_amount','paid_amount','tax_percentage','tax_amount','discount','discount_type','discount_amount','balance_amount','payment_id')->where('store_id',$decrypt_store_id)->orderBy('payment_id', 'desc')->limit(1)->get();
            }
            $mode = !empty($payment_details) ? 'edit' : 'add';
            return view('admin.store.add_payment',compact('store_id','payment_details','mode'));
        }
        else
            return redirect()->route(config('app.prefix_url').'.admin.store.index');
    }

    public function storePayment(Request $request)
    {
        $this->validate($request, [
            'payment_method' => 'required_unless:payment_method,free',
            'package_amount' => 'required_unless:payment_method,free',
            'paid_amount' => 'required_unless:payment_method,free',
        ]);
        $input = $request->all();
        $remove_array_values = array('_token','store_id','mode','total_paid_amount','balance_exist');
        foreach($remove_array_values as $value) {
            unset($input[$value]);
        }
        $store_id = !empty($request->store_id) ? Crypt::decrypt($request->store_id) : '';
        $input['store_id'] = $store_id;
        $input['created_by'] = Auth::user()->id;
        $input['ip_address'] = \Request::ip();
        $payment_id = !empty($request->payment_id) ? Crypt::decrypt($request->payment_id) : '';
        if($request->mode == "edit" && $request->balance_exist > 0) {
            $update_data = array();
            $update_data['paid_amount'] = $request->total_paid_amount; 
            $update_data['balance_amount'] = $request->balance_amount; 
            Payment::where([
                ['store_id', '=', $store_id], 
                ['payment_id', '=', $payment_id]
            ])->update($update_data);
        } else 
            $payment_id = Payment::create($input)->payment_id;
        $input['payment_id'] = $payment_id;
        PaymentHistory::create($input);
        return redirect()->route(config('app.prefix_url').'.admin.store.index')->with('message','Payment added successfully.');
    } 

    public function sendReminder(Request $request) {
        $store_id = !empty($request->store_id) ? Crypt::decrypt($request->store_id) : '';
        $store_details = User::where([
            ['store_id', '=', $store_id], 
            ['is_store', '=', "Yes"],
            ['is_deleted', '=', "0"],
        ])->get(['email']);
        $ccEmails = ["rajashree.vividinfotech@gmail.com"];
        $bccEmails = ["deva.vivid@gmail.com"];
        // if($request->type == "expire") {
        //     $details = [
        //         'title' => 'Your package going to expire soon',
        //         'body' => 'This is for testing email for your package going to expire soon'
        //     ];
        //     if(!empty($store_details) && !empty($store_details[0]->email))
        //         \Mail::to($store_details[0]->email)->cc($ccEmails)->bcc($bccEmails)->send(new \App\Mail\SuperAdmin\ExpireReminder($details));
        // } else {
        //     $details = [
        //         'title' => 'Pay the due on time',
        //         'body' => 'This is for testing email for pay the due on time'
        //     ];
        //     if(!empty($store_details) && !empty($store_details[0]->email))
        //         \Mail::to($store_details[0]->email)->cc($ccEmails)->bcc($bccEmails)->send(new \App\Mail\SuperAdmin\BalanceDueReminder($details));
        // }
        return redirect()->route(config('app.prefix_url').'.admin.store.index')->with('message','Mail sent successfully.');
    }

    public function invoice($id,$type = null) {
        $store_id = Crypt::decrypt($id);
        $invoice_details =  Payment::join('stores', 'payment.store_id', '=', 'stores.store_id')->join('users', 'payment.store_id', '=', 'users.store_id')->where([
            ['payment.store_id', '=', $store_id], 
            ['is_store', '=', "Yes"]
        ])->orderBy('payment_id', 'desc')->limit(1)->get(['package_amount','paid_amount','tax_percentage','tax_amount','discount','discount_type','discount_amount','balance_amount','total_amount','amount_payable','invoice_number','payment.created_by','payment.created_at','store_name','email','store_phone_number','store_validity_date']);
        $store_admin_details = User::leftJoin('countries', 'users.country_id', '=', 'countries.id')->leftJoin('states', 'users.state_id', '=', 'states.id')->leftJoin('cities', 'users.city_id', '=', 'cities.id')->where([
            ['users.id', '=', Auth::user()->id]
        ])->get(['email','company_name','phone_number','postal_code','street_name','building_name','company_logo','countries.name as country_name','states.name as state_name','cities.name as city_name','store_name','email','store_phone_number','store_validity_date']);
        if($type == "download") {
            $data = [
                'invoice_details' => $invoice_details,
                'store_admin_details' => $store_admin_details
            ];
            $pdf = PDF::loadView('admin.store.download_invoice', $data);
            return $pdf->download('download-invoice.pdf');
        }
        else
            return view('admin.store.invoice',compact('invoice_details','store_admin_details','store_id'));
    }
}
