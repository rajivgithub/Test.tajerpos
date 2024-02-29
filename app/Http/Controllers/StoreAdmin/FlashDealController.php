<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdmin\FlashDealRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\FlashDeal;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use URL;
use App\Http\Controllers\CommonController;

class FlashDealController extends Controller
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
              1=> 'deal_title',
              2=> 'banner_image',
              3=> 'start_date',
              4=> 'end_date',
              5=> 'featured',
              6=> 'status',
              7=> 'action',
            );
            $limit = $request->length;
            $start = $request->start; 
            $order = ($columns[$request->order[0]['column']] == "id") ? 'flash_deals_id ' : $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $where_cond = 'where created_by = '.Auth::user()->id.' AND is_deleted = 0';
            if(!empty($request->search['value'])) {
                $search = $request->search['value']; 
                $where_cond .= " AND (deal_title LIKE '%".$search."%' or start_date LIKE '%".$search."%'  or end_date LIKE '%".$search."%')";
            }
            $flash_details = DB::select('SELECT deal_title, banner_image, start_date, end_date, status, flash_deals_id,featured FROM store_flash_deals '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $count_query = FlashDeal::where('created_by',Auth::user()->id)->where('is_deleted',0);
            $totalCount = $count_query->get()->count();
            if(!empty($flash_details)) {
                $i=0;$j=0;
                foreach($flash_details as $flash) {
                    $status_checked = $flash->status == 1 ? 'checked' : ''; 
                    $featured_checked = $flash->featured == 1 ? 'checked' : '';
                    $final_data[$i]=array(
                        'id'=>++$j,
                        'deal_title'=>$flash->deal_title,
                        'banner_image'=> '<img src="'.$flash->banner_image.'" class="img-sm img-thumbnail" alt="Item">', 
                        'start_date'=>date("Y-m-d", strtotime(trim($flash->start_date))),
                        'end_date'=>date("Y-m-d", strtotime(trim($flash->end_date))),
                        'featured'=>
                            "<div class='form-check form-switch ps-0'>
                                <input class='form-check-input ms-0 flash-deal-status' data-type='featured' type='checkbox' name='status' value='1' $featured_checked role='switch' id='flexSwitchCheckDefault'>
                            </div>",
                        'status'=>
                            "<div class='form-check form-switch ps-0'>
                                <input class='form-check-input ms-0 flash-deal-status' data-type='status' type='checkbox' name='status' value='1' $status_checked role='switch' id='flexSwitchCheckDefault'>
                            </div>",
                        'action'=>
                            "<a class='btn btn-success text-white rounded font-sm' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.flash-deals.create', Crypt::encrypt($flash->flash_deals_id))."'><i class='fa fa-edit'></i></a>
                            <form action='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.flash-deals.destroy', Crypt::encrypt($flash->flash_deals_id))."' class='delete-deal-form'>
                                <button class='btn btn-danger rounded font-sm flash-deal-delete'><i class='fa fa-trash'></i></button>
                            </form>  
                            <input type='hidden' class='flash_deals_id' value='".Crypt::encrypt($flash->flash_deals_id)."'>
                            <input type='hidden' class='status_url' value='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.flash-deals.update')."'>" 
                    );
                    $i++;
                }
            }
            $totalFiltered = count($flash_details);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data   
            );
            echo json_encode($json_data); 
        } else
            return view('store_admin.flash_deals.list',compact('store_url'));
    }

    public function create($id=null)
    {
        $store_url = $this->store_url; 
        $mode = !empty($id) ? 'edit' : 'add';
        $flash_deals_details = [];
        if(!empty($id)) {
            $flash_deals_id = Crypt::decrypt($id);
            $flash_deals_details = FlashDeal::where('flash_deals_id',$flash_deals_id)->get(['deal_title','background_color','text_color','banner_image','start_date','end_date','flash_deals_id']);
        }
        return view('store_admin.flash_deals.create',compact('store_url','mode','flash_deals_details'));
    }

    public function store(FlashDealRequest $request)
    {
        try {
            $input = $request->all();
            $mode = $input['mode'];
            $store_id = Auth::user()->store_id;
            $flash_deals_id = $input['flash_deals_id'] = ($mode == "edit") ? Crypt::decrypt($input['flash_deals_id']) : 0;
            $url = URL::to("/");
            if ($request->hasFile('banner_image')) {
                $image = $request->file('banner_image');
                $destinationPath = base_path().'/images/';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $destinationPath = base_path().'/images/'.$store_id;
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $destinationPath = base_path().'/images/'.$store_id.'/banner';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $bannerImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $bannerImage);
                $banner_image_path = $url.'/images/'.$store_id.'/banner'.'/'.$bannerImage;
                $input['banner_image'] = $banner_image_path;
            }
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
                FlashDeal::create($input);
            } else {
                $input['updated_by'] = Auth::user()->id;
                FlashDeal::where('flash_deals_id',$flash_deals_id)->update($input);
            }
            //Commit Transaction to Save Data to Database
            DB::commit();
            $success_message = ($mode == "add") ? "Flash deals added successfully" : "Flash deals updated successfully.";
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.flash-deals.index')->with('message',$success_message);
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
        $flash_deals_id = Crypt::decrypt($request->flash_deals_id);
        $column_name = $request->type;
        $update_status = array();
        $update_status[$column_name] = $request->value;
        $update_access['updated_by'] = Auth::user()->id;
        FlashDeal::where('flash_deals_id',$flash_deals_id)->update($update_access);
        return response()->json(['message'=>ucfirst($column_name).' updated successfully.']);
    }

    public function destroy($id)
    {
        $flash_deals_id = Crypt::decrypt($id);
        $delete_deal = array();
        $delete_deal['is_deleted'] = 1;  
        $delete_deal['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_deal['updated_by'] = Auth::user()->id;
        FlashDeal::where('flash_deals_id',$flash_deals_id)->update($delete_deal);
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.flash-deals.index')->with('message',"Flash deals deleted successfully.");
    }
}
