<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StoreAdmin\Permission;
use App\Models\StoreAdmin\Roles;
use App\Models\StoreAdmin\RolePermission;
use App\Models\StoreAdmin\Modules;
use App\Models\StoreAdmin\ModulesPermission;
use Exception;
use App\Http\Requests\StoreAdmin\RolesRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\CommonController;

class RolesController extends Controller
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
              1=> 'role_name',
              2=> 'description',
              3=> 'status',
              4=> 'action',
            );
            $limit = $request->length;
            $start = $request->start; 
            $dir = $request->order[0]['dir'];
            $order = ($columns[$request->order[0]['column']] == "id") ? 'role_id' : $columns[$request->order[0]['column']];
            $where_cond = 'where created_by = '.Auth::user()->id.' AND is_deleted = 0 AND store_id = '.Auth::user()->store_id;
            if(!empty($request->search['value'])) {
                $search = $request->search['value']; 
                $where_cond .= " AND (role_name LIKE '%".$search."%' or description LIKE '%".$search."%')";
            }
            $role_details = DB::select('SELECT role_id, role_name, description, status FROM store_user_roles '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $filtered_role_details = DB::select('SELECT role_id, role_name, description, status FROM store_user_roles '.$where_cond);
            $count_query = Roles::where([
                ['store_id', '=', Auth::user()->store_id],
                ['created_by', '=', Auth::user()->id],
                ['is_deleted', '=', 0],
            ]);
            $totalCount = $count_query->get()->count();
            if(!empty($role_details)) {
                $i=0;$j=0;
                foreach($role_details as $role) {
                    $status_checked = $role->status == 1 ? 'checked' : '';
                    $final_data[$i]=array(
                        'id'=>++$j,
                        'role_name'=>$role->role_name, 
                        'description'=> $role->description, 
                        'status'=>
                            "<div class='custom-control custom-switch'>
                                <input type='checkbox' data-type='status' class='custom-control-input role-status' name='status' value='1' $status_checked id='status-customSwitch".$i."'>
								<label class='custom-control-label' for='status-customSwitch".$i."'></label>
                            </div>",
                        'action'=>
                            "<a class='btn btn-circle btn-danger btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.roles.create', Crypt::encrypt($role->role_id))."'><i class='fa fa-edit'></i></a>
                            <a class='btn btn-circle btn-primary btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.roles.destroy', Crypt::encrypt($role->role_id))."'><i class='fa fa-trash'></i></a>
                            <input type='hidden' class='role_id' value='".Crypt::encrypt($role->role_id)."'>
                            <input type='hidden' class='status_url' value='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.roles.update')."'>" 
                    );
                    $i++;
                }
            }
            $totalFiltered = count($filtered_role_details);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data   
            );
            echo json_encode($json_data); 
        } else
            return view('store_admin.roles.list',compact('store_url','store_logo'));
    }

    public function create($id=null)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $role_details = $role_permission_data = [];
        if(!empty($id)) {
            $where_condition = [
                ['store_id', '=', Auth::user()->store_id],
                ['store_user_id', '=', Auth::user()->id],
                ['role_id', '=', Crypt::decrypt($id)],
            ];
            $role_details = Roles::where($where_condition)->get(['role_id','role_name','description']);
            $role_permission_details = ModulesPermission::where([
                ['store_id', '=', Auth::user()->store_id],
                ['roles_id', '=', Crypt::decrypt($id)],
            ])->get(['modules_id','add','view','edit','delete'])->toArray();
            $role_permission_data = array_column($role_permission_details, null, 'modules_id');
        }
        $modules_details = Modules::where('status',1)->get();
        $mode = !empty($id) ? 'edit' : 'add';
        return view('store_admin.roles.create',compact('store_url','role_details','mode','id','store_logo','modules_details','role_permission_data'));
    }

    public function store(Request $request)
    {
        try {
            $mode = $request->mode;
            $role_id = ($mode == "edit") ? $request->role_id : 0;
            $input['role_name'] = $request->role_name;
            $input['description'] = $request->description;
            $input['store_user_id'] = Auth::user()->id;
            if($mode == "add") {
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                $role_id = Roles::create($input)->role_id;
            } else {
                $input['updated_by'] = Auth::user()->id;
                Roles::where('role_id',$role_id)->update($input);
                ModulesPermission::where('roles_id',$role_id)->delete();
            }
            $permissions = $request->permissions;
            $modules_id = $request->modules_id; 
            if(!empty($permissions) && !empty($modules_id)) {
                $permission_module = [];
                foreach ($modules_id as $moduleId) {
                    $moduleExists = false;
                    foreach ($permissions as $permission) {
                        $permissionParts = explode('.', $permission);
                        $permissionModuleId = $permissionParts[0];
                        $permissionAction = $permissionParts[1];
                        if ($permissionModuleId == $moduleId) {
                            $moduleExists = true;
                            $permission_module[] = [
                                'store_id' => Auth::user()->store_id,
                                'roles_id' => $role_id,
                                'modules_id' => $moduleId,
                                'add' => ($permissionAction == 'add') ? 1 : 0,
                                'view' => ($permissionAction == 'view') ? 1 : 0,
                                'edit' => ($permissionAction == 'edit') ? 1 : 0,
                                'delete' => ($permissionAction == 'delete') ? 1 : 0,
                                'created_by' => Auth::user()->id,
                                'updated_by' => Auth::user()->id,
                            ];
                        }
                    }
                    if (!$moduleExists) {
                        $permission_module[] = [
                            'store_id' => Auth::user()->store_id,
                            'roles_id' => $role_id,
                            'modules_id' => $moduleId,
                            'add' => 0,
                            'view' => 0,
                            'edit' => 0,
                            'delete' => 0,
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                        ];
                    }
                }
                $save_permission_module = [];
                if(!empty($permission_module)) {
                    foreach ($permission_module as $item) {
                        $moduleId = $item['modules_id'];
                        if (!isset($save_permission_module[$moduleId])) {
                            $save_permission_module[$moduleId] = $item;
                        } else {
                            $save_permission_module[$moduleId]['add'] = max($save_permission_module[$moduleId]['add'], $item['add']);
                            $save_permission_module[$moduleId]['view'] = max($save_permission_module[$moduleId]['view'], $item['view']);
                            $save_permission_module[$moduleId]['edit'] = max($save_permission_module[$moduleId]['edit'], $item['edit']);
                            $save_permission_module[$moduleId]['delete'] = max($save_permission_module[$moduleId]['delete'], $item['delete']);
                        }
                    }
                    $save_permission_module = array_values($save_permission_module);
                }
                ModulesPermission::insert($save_permission_module);
            }
            $success_message = ($mode == "add") ? "Role added successfully" : "Role updated successfully.";
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.roles.index')->with('message',$success_message);
        } catch (Exception $e) {
            throw $e;
        }
    }

    function array_contains_key( array $input_array, $search_value, $case_sensitive = false)
    {
        if($case_sensitive)
            $preg_match = '/'.$search_value.'/';
        else
            $preg_match = '/'.$search_value.'/i';

        $return_array = array();
        $keys = array_keys( $input_array );
        foreach ( $keys as $k ) {
            if ( preg_match($preg_match, $k) )
                $return_array[$k] = $input_array[$k];
        }
        return $return_array;
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
        $role_id = Crypt::decrypt($request->role_id);
        $update_access = array();
        $update_access['status'] = $request->status_value;
        $update_access['updated_by'] = Auth::user()->id;
        Roles::where('role_id',$role_id)->update($update_access);
        return response()->json(['message'=>'Status updated successfully.']);
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $delete_role = array();
        $delete_role['is_deleted'] = 1;  
        $delete_role['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_role['updated_by'] = Auth::user()->id;
        Roles::where('role_id',$id)->update($delete_role);
        RolePermission::where('role_id',$id)->update($delete_role);
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.roles.index')->with('message',"Roles deleted successfully.");
    }

}
