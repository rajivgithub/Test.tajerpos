<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\CustomerBanners;
use App\Http\Controllers\CommonController;
use Auth;
use Exception;
use DB;
use URL;
use Image;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class CustomerBannersController extends Controller
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
            $final_data = array();
            $columns = array(
                0 => 'checkbox',
                1 => 'banner_id',
                2 => 'banner_image',
                // 3 => 'banner_url',
                4 => 'start_date',
                5 => 'end_date',
                6 => 'banner_type',
                7 => 'status',
                8 => 'created_at',
                9 => 'action',
            );
            $limit = $request->length;
            $start = $request->start;
            $order = ($columns[$request->order[0]['column']] == "checkbox") ? 'banner_id' : $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $expiredBanners = CustomerBanners::where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0],
                ['end_date', '<', Carbon::now()],
            ])->get();
            if(!empty($expiredBanners)) {
                foreach ($expiredBanners as $banner) {
                    $banner->status = 'expired';
                    $banner->save();
                }
            }
            $banner_list_query = CustomerBanners::where([
                ['store_id','=', Auth::user()->store_id],
                ['is_deleted','=', 0],
            ])->select('banner_id','banner_image','banner_url',DB::raw("DATE_FORMAT(start_date, '%d-%m-%Y %H:%i') as start_date"),DB::raw("DATE_FORMAT(end_date, '%d-%m-%Y %H:%i') as end_date"),'banner_type','status',DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as banner_created_at"));
            if(!empty($request->search['value'])) {
                $search = trim($request->search['value']);
                $banner_list_query->where(function ($query) use ($search) {
                    $query->where('banner_url', 'LIKE', '%' . $search . '%')
                        ->orWhere('start_date', 'LIKE', '%' . $search . '%')
                        ->orWhere('end_date', 'LIKE', '%' . $search . '%')
                        ->orWhere('banner_type', 'LIKE', '%' . $search . '%');
                });
            }
            $banner_list_query->when($request->_type != 'all', function ($query) use ($request) {
                $query->where('status',$request->_type);
            });
            $totalCount = $banner_list_query->count();
            $banner_list_query->orderBy($order, $dir);
            $banner_list = $banner_list_query->skip($start)
                ->take($limit)
                ->get();
            if (!empty($banner_list)) {
                $i = 0;
                $j = 0;
                foreach ($banner_list as $banner) {
                    $status_checked = $banner->status == "active" ? 'checked' : '';
                    $final_data[$i] = array(
                        'checkbox' => '<div class="form-check"><input type="checkbox" name="banner_checkbox" class="form-check-input banner-checkbox" value="' . $banner->banner_id . '"></div>',
                        'banner_id' => ++$j,
                        'banner_image' => '<img src="' . $banner->banner_image . '" class="img-sm img-thumbnail" alt="Item">',
                        // 'banner_url' => $banner->banner_url,
                        'start_date' => $banner->start_date,
                        'end_date' => $banner->end_date,
                        'banner_type' => ucfirst($banner->banner_type),
                        'status' => ($banner->status == 'expired') ? '<span class="badge badge-secondary">Expired</span>' : "<div class='custom-control custom-switch'>
                                <input class='custom-control-input banner-status' data-type='status' type='checkbox' name='status' value='1' $status_checked id='feature-customSwitch" . $i . "'>
                                <label class='custom-control-label' for='feature-customSwitch" . $i . "'></label>
                            </div>",
                        'created_at' => $banner->banner_created_at,
                        'action' => "<a class='btn btn-circle btn-danger btn-xs' href='" . route(config('app.prefix_url') . '.' . $this->store_url . '.' . $prefix_url . '.customer-banners.create', Crypt::encrypt($banner->banner_id)) . "'><i class='fa fa-edit'></i></a>
                            <a class='btn btn-circle btn-primary btn-xs banner-status banner-delete' data-type='delete' href='#'><i class='fa fa-trash'></i></a>
                            <input type='hidden' class='encrypted_banner_id' value='" . Crypt::encrypt($banner->banner_id) . "'><input type='hidden' class='banner_id' value='" . $banner->banner_id . "'>"
                    );
                    $i++;
                }
            }
            $json_data = array(
                "draw" => intval($request->draw),
                "recordsTotal" => intval($totalCount),
                "recordsFiltered" => intval($totalCount),
                "data" => $final_data
            );
            return response()->json($json_data);
        } else {
            return view('store_admin.customer_banners.list',compact('store_url','store_logo'));
        }
    }

    public function create($id=null)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $mode = !empty($id) ? 'edit' : 'add';
        $banner_details = [];
        if(!empty($id)) {
            $banner_details = CustomerBanners::where([
                ['store_id','=', Auth::user()->store_id],
                ['is_deleted','=', 0],
                ['banner_id','=', Crypt::decrypt($id)], 
            ])->select('banner_id','banner_image','banner_url',DB::raw("DATE_FORMAT(start_date, '%d-%m-%Y %H:%i') as start_date"),DB::raw("DATE_FORMAT(end_date, '%d-%m-%Y %H:%i') as end_date"),'banner_type','status',DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as banner_created_at"))->get();
        } 
        return view('store_admin.customer_banners.create',compact('store_url','store_logo','mode','banner_details'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'banner_image' => 'required_if:mode,add|image|mimes:jpeg,png,jpg',
            // 'banner_image' => 'required_if:mode,add|image|mimes:jpeg,png,jpg|dimensions:width=1440,height=470',
            // 'banner_url' => 'required',
            'banner_type' => 'required',
        ]);
        try {
            $input = $request->except('_token','mode','remove_banner_image');
            $store_id = $input['store_id'] = Auth::user()->store_id;
            $banner_id = $input['banner_id'] = ($request->mode == "edit") ? Crypt::decrypt($input['banner_id']) : 0;
            $url = URL::to("/");
            DB::beginTransaction();
            $destinationPath = base_path().'/images/';
            if (!file_exists($destinationPath)) 
                mkdir($destinationPath, 0777, true);
            $destinationPath = base_path().'/images/'.$store_id;
            if (!file_exists($destinationPath)) 
                mkdir($destinationPath, 0777, true);
            if ($request->hasFile('banner_image')) {
                $image = $request->file('banner_image');
                $bannerImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $destinationPath = base_path().'/images/'.$store_id.'/dashboard-banner';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $input['banner_image'] = $url.'/images/'.$store_id.'/dashboard-banner'.'/'.$bannerImage;
                $image->move($destinationPath, $bannerImage);
            }
            if(!empty($input['start_date']))
                $input['start_date'] = Carbon::createFromFormat('d-m-Y H:i', $input['start_date'])->format('Y-m-d H:i:s');
            if(!empty($input['end_date']))
                $input['end_date'] = Carbon::createFromFormat('d-m-Y H:i', $input['end_date'])->format('Y-m-d H:i:s');
            $input['status'] = ($input['status'] == "inactive") ? $input['status'] : (!empty($input['end_date']) && $input['end_date'] < Carbon::now()) ? 'expired' : 'active';
            if($request->mode == "add") {
                $input['created_by'] = Auth::user()->id;
                CustomerBanners::create($input);
            } else { 
                $input['updated_by'] = Auth::user()->id;
                CustomerBanners::where('banner_id',$banner_id)->update($input);
            }
            DB::commit();
            $success_message = ($request->mode == "add") ? trans('store-admin.added_msg',['name'=>trans('store-admin.banner')]) : trans('store-admin.updated_msg',['name'=>trans('store-admin.banner')]);
            $prefix_url = config('app.module_prefix_url');
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.customer-banners.index')->with('message',$success_message);
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
        $banner_id = $request->banner_id;
        $update_access = array();
        if($request->status_value == "delete") {
            $update_access['is_deleted'] = 1;  
            $update_access['deleted_at'] = Carbon::now()->toDateTimeString();
            $message = trans('store-admin.deleted_msg',['name'=>trans('store-admin.banner')]); 
        } else {
            $update_access['status'] = $request->status_value;
            $message = trans('store-admin.updated_msg',['name'=>trans('store-admin.status')]);
        }
        $update_access['updated_by'] = Auth::user()->id;
        CustomerBanners::whereIn('banner_id',$banner_id)->update($update_access);
        return response()->json(['message'=>$message]);
    }
}
