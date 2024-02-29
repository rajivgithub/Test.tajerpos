<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customers\ContactUs;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class CustomerInquiriesController extends Controller
{
    protected $store_url,$prefix_url,$store_logo;
    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
        $this->prefix_url = config('app.module_prefix_url');
        $this->store_logo = CommonController::storeLogo();
    }

    public function index(Request $request)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $prefix_url = config('app.module_prefix_url');
        if($request->_type != "") {
            $final_data=array();
            $columns = array( 
                0 =>'contactor_id',
                1=> 'contactor_name',
                2=> 'contactor_email', 
                3=> 'contactor_phone_no',
                4=> 'contactor_message',
                5=> 'created_at',
                6=> 'action'
            );
            $limit = $request->length;
            $start = $request->start; 
            $order = $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $query = ContactUs::select('contactor_id', 'contactor_name', 'contactor_email', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as contactor_created_at"),'contactor_phone_no','contactor_message')
                ->where('is_deleted', 0)
                ->where('store_id', Auth::user()->store_id);
            if (!empty($request->search['value'])) {
                $search = trim($request->search['value']);
                $query->where(function ($query) use ($search) {
                    $query->where('contactor_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('contactor_email', 'LIKE', '%' . $search . '%')
                        ->orWhere('contactor_phone_no', 'LIKE', '%' . $search . '%')
                        ->orWhere('contactor_message', 'LIKE', '%' . $search . '%')
                        ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE '%" . $search . "%'");
                });
            }
            $query->orderBy($order, $dir)->offset($start)->limit($limit);
            $customer_inquiries = $query->get();
            $filtered_inquiries_details = $query->count();
            $totalCount = ContactUs::where('is_deleted', 0)
                ->where('store_id', Auth::user()->store_id)
                ->count();
            if(!empty($customer_inquiries)) {
                $i=0;$j=0;
                foreach($customer_inquiries as $inquiries) {
                    $final_data[$i]=array(
                        'contactor_id'=>++$j,
                        'contactor_name'=> $inquiries->contactor_name,
                        'contactor_email'=> $inquiries->contactor_email,
                        'contactor_phone_no'=> $inquiries->contactor_phone_no,
                        'contactor_message'=> '<p class="message-column" data-bs-toggle="tooltip" title="'.$inquiries->contactor_message.'">'.$inquiries->contactor_message.'</p>',
                        'created_at'=> $inquiries->contactor_created_at,
                        'action' => "<a class='btn btn-circle btn-warning btn-xs' href='" . route(config('app.prefix_url') . '.' . $this->store_url . '.' . $prefix_url . '.customer-inquiries.show', Crypt::encrypt($inquiries->contactor_id)) . "'><i class='fa fa-eye'></i></a>
                            <a class='btn btn-circle btn-primary btn-xs inquiries-delete' href='" . route(config('app.prefix_url') . '.' . $this->store_url . '.' . $prefix_url . '.customer-inquiries.destroy', Crypt::encrypt($inquiries->contactor_id)) . "'><i class='fa fa-trash'></i></a>
                            <input type='hidden' class='encrypted_contactor_id' value='" . Crypt::encrypt($inquiries->contactor_id) . "'><input type='hidden' class='contactor_id' value='" . $inquiries->contactor_id . "'>"
                    );
                    $i++;
                }
            }
            $totalFiltered = !empty($filtered_inquiries_details) ? $filtered_inquiries_details : 0;
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => !empty($totalCount) ? $totalCount : 0,  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data, 
            );
            echo json_encode($json_data); 
        } else {
            return view('store_admin.customer_inquiries.list',compact('store_url','store_logo'));
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $customer_inquiries = ContactUs::select('contactor_id', 'contactor_name', 'contactor_email', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as contactor_created_at"),'contactor_phone_no','contactor_message')
                ->where('is_deleted', 0)
                ->where('store_id', Auth::user()->store_id)
                ->where('contactor_id', Crypt::decrypt($id))
                ->get();
        return view('store_admin.customer_inquiries.view',compact('store_url','customer_inquiries','store_logo'));
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
        $inquiries_id = Crypt::decrypt($id);
        $delete_inquiries = array();
        $delete_inquiries['is_deleted'] = 1;  
        $delete_inquiries['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_inquiries['updated_by'] = Auth::user()->id;
        ContactUs::where('contactor_id',$inquiries_id)->update($delete_inquiries);
        $prefix_url = config('app.module_prefix_url');
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.customer-inquiries.index')->with('message',"Inquiry deleted successfully.");
    }
}
