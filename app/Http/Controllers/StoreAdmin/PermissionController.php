<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\StoreAdmin\Permission;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAdmin\PermissionRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\CommonController;

class PermissionController extends Controller
{
    protected $store_url,$store_logo;
    public function __construct() {
        $this->store_url = CommonController::storeURL();
        $this->store_logo = CommonController::storeLogo();
    }

    public function index(Request $request)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        if($request->type != "") {
            $final_data=array();
            $columns = array( 
              0 =>'id',
              1=> 'permission_name',
              2=> 'description',
              3=> 'status',
              4=> 'action',
            );
            $limit = $request->length;
            $start = $request->start; 
            $dir = $request->order[0]['dir'];
            $order = ($columns[$request->order[0]['column']] == "id") ? 'permission_id' : $columns[$request->order[0]['column']];
            $where_cond = 'where created_by = '.Auth::user()->id.' AND is_deleted = 0 AND store_id = '.Auth::user()->store_id;
            if(!empty($request->search['value'])) {
                $search = $request->search['value']; 
                $where_cond .= " AND (permission_name LIKE '%".$search."%' or description LIKE '%".$search."%')";
            }
            $permission_details = DB::select('SELECT permission_id, permission_name, description, status FROM store_user_permissions '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $filtered_permission_details = DB::select('SELECT permission_id, permission_name, description, status FROM store_user_permissions '.$where_cond);
            $count_query = Permission::where([
                ['store_id', '=', Auth::user()->store_id],
                ['created_by', '=', Auth::user()->id],
                ['is_deleted', '=', 0],
            ]);
            $totalCount = $count_query->get()->count();
            if(!empty($permission_details)) {
                $i=0;$j=0;
                foreach($permission_details as $permission) {
                    $status_checked = $permission->status == 1 ? 'checked' : '';
                    $final_data[$i]=array(
                        'id'=>++$j,
                        'permission_name'=>$permission->permission_name, 
                        'description'=> $permission->description, 
                        'status'=>
                            "<div class='custom-control custom-switch'>
                                <input class='custom-control-input permission-status' type='checkbox' name='status' value='1' $status_checked id='status-customSwitch".$i."'>
                                <label class='custom-control-label' for='status-customSwitch".$i."'></label>
                            </div>",
                        'action'=>
                            "<a class='btn btn-circle btn-danger btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.permission.create', Crypt::encrypt($permission->permission_id))."'><i class='fa fa-edit'></i></a>
                            <a class='btn btn-circle btn-primary btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.permission.destroy', Crypt::encrypt($permission->permission_id))."'><i class='fa fa-trash'></i></a>  
                            <input type='hidden' class='permission_id' value='".Crypt::encrypt($permission->permission_id)."'>
                            <input type='hidden' class='status_url' value='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.permission.update')."'>" 
                    );
                    $i++;
                }
            }
            $totalFiltered = count($filtered_permission_details);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data   
            );
            echo json_encode($json_data); 
        } else
            return view('store_admin.permission.list',compact('store_url','store_logo'));
    }

    public function create($id=null)
    {
        $permission_details = [];
        $store_url = $this->store_url;
        $store_logo = $this->store_logo;
        if(!empty($id))
            $permission_details = Permission::where('permission_id',Crypt::decrypt($id))->get(['permission_id','permission_name','description']);
        $mode = !empty($id) ? 'edit' : 'add';
        return view('store_admin.permission.create',compact('permission_details','mode','store_url','id','store_logo'));
    }

    public function store(PermissionRequest $request)
    {
        try {
            $input = $request->all();
            $mode = $input['mode'];
            $permission_id = ($mode == "edit") ? $input['permission_id'] : 0;
            //Reset the input values
            $remove_array_values = array('_token','mode','permission_id');
            foreach($remove_array_values as $value) {
                unset($input[$value]);
            }
            $input['store_user_id'] = Auth::user()->id;
            if($mode == "add") {
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                Permission::create($input);
            } else {
                $input['updated_by'] = Auth::user()->id;
                Permission::where('permission_id',$permission_id)->update($input);
            }
            $success_message = ($mode == "add") ? "Permission added successfully" : "Permission updated successfully.";
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.permission.index')->with('message',$success_message);
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

    public function update(Request $request)
    {
        $permission_id = Crypt::decrypt($request->permission_id);
        $update_access = array();
        $update_access['status'] = $request->status_value;
        $update_access['updated_by'] = Auth::user()->id;
        Permission::where('permission_id',$permission_id)->update($update_access);
        return response()->json(['message'=>'Status updated successfully.']);
    }

    public function destroy($id)
    {
        $permission_id = Crypt::decrypt($id);
        $delete_permission = array();
        $delete_permission['is_deleted'] = 1;  
        $delete_permission['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_permission['updated_by'] = Auth::user()->id;
        Permission::where('permission_id',$permission_id)->update($delete_permission);
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.permission.index')->with('message',"Permission deleted successfully.");
    }
}
