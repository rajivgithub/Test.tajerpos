<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\Coupon;
use Exception;
use App\Http\Requests\StoreAdmin\CouponRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\StoreAdmin\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Http\Controllers\CommonController;

class CouponController extends Controller
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
              1=> 'coupon_code',
              2=> 'coupon_type',
              3=> 'start_up_date',
              4=> 'expiration_date',
              5=> 'status',
              6=> 'action',
            );
            $limit = $request->length;
            $start = $request->start; 
            $order = ($columns[$request->order[0]['column']] == "id") ? 'coupon_id ' : $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $where_cond = 'where created_by = '.Auth::user()->id.' AND is_deleted = 0';
            if(!empty($request->search['value'])) {
                $search = $request->search['value']; 
                $where_cond .= " AND (coupon_code LIKE '%".$search."%' or coupon_type LIKE '%".$search."%' or start_up_date LIKE '%".$search."%'  or expiration_date LIKE '%".$search."%')";
            }
            $coupon_details = DB::select('SELECT coupon_code, coupon_type, start_up_date, expiration_date, status, coupon_id FROM store_coupons '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $count_query = Coupon::where('created_by',Auth::user()->id)->where('is_deleted',0);
            $totalCount = $count_query->get()->count();
            if(!empty($coupon_details)) {
                $i=0;$j=0;
                foreach($coupon_details as $coupon) {
                    $status_checked = $coupon->status == 1 ? 'checked' : '';
                    $final_data[$i]=array(
                        'id'=>++$j,
                        'coupon_code'=>$coupon->coupon_code,
                        'coupon_type'=> $coupon->coupon_type,
                        'start_up_date'=>date("Y-m-d", strtotime(trim($coupon->start_up_date))),
                        'expiration_date'=>date("Y-m-d", strtotime(trim($coupon->expiration_date))),
                        'status'=>
                            "<div class='form-check form-switch ps-0'>
                                <input class='form-check-input ms-0 coupon-status' type='checkbox' name='status' value='1' $status_checked role='switch' id='flexSwitchCheckDefault'>
                            </div>",
                        'action'=>
                            "<a class='btn btn-success text-white rounded font-sm' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.coupon.create', Crypt::encrypt($coupon->coupon_id))."'><i class='fa fa-edit'></i></a>
                            <form action='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.coupon.destroy', Crypt::encrypt($coupon->coupon_id))."' class='delete-coupon-form'>
                                <button class='btn btn-danger rounded font-sm coupon-delete'><i class='fa fa-trash'></i></button>
                            </form>  
                            <input type='hidden' class='coupon_id' value='".Crypt::encrypt($coupon->coupon_id)."'>
                            <input type='hidden' class='status_url' value='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.coupon.update')."'>" 
                    );
                    $i++;
                }
            }
            $totalFiltered = count($coupon_details);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data   
            );
            echo json_encode($json_data); 
        } else
            return view('store_admin.coupon.list',compact('store_url'));
    }

    public function create($id=null)
    {
        $store_url = $this->store_url; 
        $mode = !empty($id) ? 'edit' : 'add';
        $product_details = Product::select('product_id','product_name')->where([
            ['store_id', '=', Auth::user()->store_id],
            ['created_by', '=', Auth::user()->id],
            ['status', '=', '1'],
            ['is_deleted','=','0']
        ])->get();
        $coupon_details = [];
        if(!empty($id)) {
            $coupon_id = Crypt::decrypt($id);
            $coupon_details = Coupon::where('coupon_id',$coupon_id)->get(['coupon_code','product_id','coupon_type','discount','discount_type','start_up_date','expiration_date','coupon_id']);
        }
        return view('store_admin.coupon.create',compact('store_url','mode','coupon_details','product_details'));
    }

    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $mode = $input['mode'];
            $coupon_id = $input['coupon_id'] = ($mode == "edit") ? Crypt::decrypt($input['coupon_id']) : 0;
            //Reset the input values
            $remove_array_values = array('_token','mode');
            foreach($remove_array_values as $value) {
                unset($input[$value]);
            }
            //Start DB Transaction
            DB::beginTransaction();
            if($mode == "add") {
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                Coupon::create($input);
            } else {
                $input['updated_by'] = Auth::user()->id;
                Coupon::where('coupon_id',$coupon_id)->update($input);
            }
            //Commit Transaction to Save Data to Database
            DB::commit();
            $success_message = ($mode == "add") ? "Coupon added successfully" : "Coupon updated successfully.";
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.coupon.index')->with('message',$success_message);
        } catch (Exception $e) {
            //Rollback Database Entry
            DB::rollback();
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

    public function update(Request $request)
    {
        $coupon_id = Crypt::decrypt($request->coupon_id);
        $update_access = array();
        $update_access['status'] = $request->status_value;
        $update_access['updated_by'] = Auth::user()->id;
        Coupon::where('coupon_id',$coupon_id)->update($update_access);
        return response()->json(['message'=>'Status updated successfully.']);
    }

    public function destroy($coupon_id)
    {
        $coupon_id = Crypt::decrypt($coupon_id);
        $delete_coupon = array();
        $delete_coupon['is_deleted'] = 1;  
        $delete_coupon['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_coupon['updated_by'] = Auth::user()->id;
        Coupon::where('coupon_id',$coupon_id)->update($delete_coupon);
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.coupon.index')->with('message',"Coupon deleted successfully.");
    }
}
