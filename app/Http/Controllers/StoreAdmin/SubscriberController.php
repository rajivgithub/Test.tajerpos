<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\Subscriber;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CommonController;

class SubscriberController extends Controller
{
    protected $store_url;
    public function __construct() {
        $this->store_url = CommonController::storeURL();
    }

    public function index(Request $request)
    {
        $store_url = $this->store_url; 
        if($request->type != "") {
            $final_data=array();
            $columns = array( 
              0 =>'id',
              1=> 'subscriber_email',
              2=> 'subscription_date',
              3=> 'status',
              4=> 'action'
            );
            $limit = $request->length;
            $start = $request->start; 
            $order = ($columns[$request->order[0]['column']] == "id") ? 'subscriber_id ' : $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $where_cond = 'where store_id = '.Auth::user()->store_id.' AND is_deleted = 0';
            if(!empty($request->search['value'])) {
                $search = $request->search['value']; 
                $where_cond .= " AND (subscriber_email LIKE '%".$search."%' or subscription_date LIKE '%".$search."%')";
            }
            $subscription_details = DB::select('SELECT subscriber_email, subscription_date, status, subscriber_id FROM store_subscribers '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $totalFiltered = DB::select('SELECT subscriber_id FROM store_subscribers '.$where_cond.' ORDER BY '.$order.' '.$dir);
            $count_query = Subscriber::where('store_id',Auth::user()->store_id)->where('is_deleted',0);
            $totalCount = $count_query->get()->count();
            if(!empty($subscription_details)) {
                $i=0;$j=0;
                foreach($subscription_details as $subscribe) {
                    $status_checked = $subscribe->status == 1 ? 'checked' : '';
                    $final_data[$i]=array(
                        'id'=>++$j,
                        'subscriber_email'=>$subscribe->subscriber_email,
                        'subscription_date'=>date("Y-m-d", strtotime(trim($subscribe->subscription_date))),
                        'status'=>
                            "<div class='form-check form-switch ps-0'>
                                <input class='form-check-input ms-0 subscriber-status' type='checkbox' name='status' value='1' $status_checked role='switch' id='flexSwitchCheckDefault'>
                            </div>",
                        'action'=>
                            "<form action='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.subscriber.destroy', Crypt::encrypt($subscribe->subscriber_id))."' class='delete-subscriber-form'>
                                <button class='btn btn-danger rounded font-sm subscriber-delete'><i class='fa fa-trash'></i></button>
                            </form>  
                            <input type='hidden' class='subscriber_id' value='".Crypt::encrypt($subscribe->subscriber_id)."'>
                            <input type='hidden' class='status_url' value='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.subscriber.update')."'>" 
                    );
                    $i++;
                }
            }
            $totalFiltered = count($totalFiltered);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data   
            );
            echo json_encode($json_data); 
        } else
            return view('store_admin.subscriber.list',compact('store_url'));
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
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request)
    {
        $subscriber_id = Crypt::decrypt($request->subscriber_id);
        $update_status = array();
        $update_status['status'] = $request->status_value;
        $update_status['updated_by'] = Auth::user()->id;
        Subscriber::where('subscriber_id',$subscriber_id)->update($update_status);
        return response()->json(['message'=>'Status updated successfully.']);
    }

    public function destroy($id)
    {
        $subscriber_id = Crypt::decrypt($id);
        $delete_subscriber = array();
        $delete_subscriber['is_deleted'] = 1;  
        $delete_subscriber['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_subscriber['updated_by'] = Auth::user()->id;
        Subscriber::where('subscriber_id',$subscriber_id)->update($delete_subscriber);
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.subscriber.index')->with('message',"Subscriber deleted successfully.");
    }
}
