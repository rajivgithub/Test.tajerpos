<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\User as StoreAdminUser;
use App\Models\User;
use App\Models\StoreAdmin\Roles;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\StoreAdmin\UserRequest;
use Carbon\Carbon;
use App\Http\Controllers\CommonController;

class UserController extends Controller
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
              1=> 'name',
              2=> 'email',
              3=> 'phone_number',
              4=> 'role_name',
              5=> 'status',
              6=> 'action',
            );
            $limit = $request->length;
            $start = $request->start; 
            $dir = $request->order[0]['dir'];
            $order = $columns[$request->order[0]['column']];
            $where_cond = 'where users.created_by = '.Auth::user()->id.' AND users.is_deleted = 0 AND users.store_id = '.Auth::user()->store_id;
            if(!empty($request->search['value'])) {
                $search = $request->search['value']; 
                // $where_cond .= " AND (name LIKE '%".$search."%' or email LIKE '%".$search."%' or role_name LIKE '%".$search."%' or phone_number LIKE '%".$search."%')";
                // $where_cond .= " AND (name LIKE '%".$search."%' or email LIKE '%".$search."%' or or phone_number LIKE '%".$search."%')";

                $where_cond .= " AND (name LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%' OR phone_number LIKE '%" . $search . "%'";
                if ((strtolower($search) == "store admin") || (strtolower($search) == "admin")) {
                    $where_cond .= " OR role_id = 2";
                } 
                if ((strtolower($search) == "cashier admin") || (strtolower($search) == "admin")) {
                    $where_cond .= " OR role_id = 3";
                }
                $where_cond .= ")";
            }
            $store_admin_details = DB::select('SELECT id, name, email, phone_number, users.status, role_id FROM users '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $filtered_admin_details = DB::select('SELECT id, name, email, phone_number, users.status FROM users '.$where_cond);
            // $store_admin_details = DB::select('SELECT id, name, email, phone_number, users.status, role_name FROM users LEFT JOIN store_user_roles on users.role_id = store_user_roles.role_id  '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            // $filtered_admin_details = DB::select('SELECT id, name, email, phone_number, users.status FROM users LEFT JOIN store_user_roles on users.role_id = store_user_roles.role_id  '.$where_cond);
            $count_query = User::where([
                ['users.created_by', '=', Auth::user()->id],
                ['users.store_id', '=', Auth::user()->store_id],
                ['users.is_deleted', '=', 0],
            ]);
            $totalCount = $count_query->get()->count();
            if(!empty($store_admin_details)) {
                $i=0;$j=0;
                foreach($store_admin_details as $users) {
                    $status_checked = $users->status == 1 ? 'checked' : '';
                    $final_data[$i]=array(
                        'id'=>++$j,
                        'name'=>$users->name, 
                        'email'=> $users->email, 
                        'phone_number'=> $users->phone_number, 
                        'role_name'=> ($users->role_id == 2) ? "Store Admin" : "Cashier Admin",
                        'status'=>
                            "<div class='custom-control custom-switch'>
                                <input type='checkbox' data-type='status' class='custom-control-input admin-user-status' name='status' value='1' $status_checked id='status-customSwitch".$i."'>
								<label class='custom-control-label' for='status-customSwitch".$i."'></label>
                            </div>",
                        'action'=>
                            "<a class='btn btn-circle btn-danger btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.users.create', Crypt::encrypt($users->id))."'><i class='fa fa-edit'></i></a>
                            <a class='btn btn-circle btn-primary btn-xs admin-user-delete' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.users.destroy', Crypt::encrypt($users->id))."'><i class='fa fa-trash'></i></a>  
                            <input type='hidden' class='user_id' value='".Crypt::encrypt($users->id)."'>
                            <input type='hidden' class='status_url' value='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.users.update')."'>" 
                    );
                    $i++;
                }
            }
            $totalFiltered = count($filtered_admin_details);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data   
            );
            echo json_encode($json_data); 
        } else {
            return view('store_admin.users.list',compact('store_url','store_logo'));
        }
            
    }

    public function create($id=null)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $mode = !empty($id) ? 'edit' : 'add';
        $admin_user_details = [];
        $role_details = Roles::where([
            ['store_id', '=', Auth::user()->store_id],
            ['store_user_id', '=', Auth::user()->id]
        ])->get(['role_id','role_name']);
        if(!empty($id)) 
            $admin_user_details = User::where('id',Crypt::decrypt($id))->get(['id','store_id','name','phone_number','email','role_id','plain_password','is_admin']);
        return view('store_admin.users.create',compact('store_url','mode','role_details','admin_user_details','store_logo'));
    }

    public function store(UserRequest $request)
    {
        try {
            $input = $request->all();
            $mode = $input['mode'];
            $store_id = ($mode == "edit") ? Crypt::decrypt($input['store_id']) : 0;
            $user_id = ($mode == "edit") ? Crypt::decrypt($input['user_id']) : 0;
            //Reset the input values
            $remove_array_values = array('_token','mode','store_id','user_id');
            foreach($remove_array_values as $value) {
                unset($input[$value]);
            }
            //Start DB Transaction
            DB::beginTransaction();
            $input['plain_password'] = encrypt($request->password);   
            $input['password'] = Hash::make($request->password); 
            // $input['is_admin'] = (!empty($input['is_admin'])) ? $input['is_admin'] : 2; 
            $input['is_admin'] = $input['role_id'];
            if($mode == "add") {
                $input['company_name'] = Auth::user()->company_name;
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                User::create($input);
            }
            else {
                $input['updated_by'] = Auth::user()->id;
                User::where('id',$user_id)->where('store_id',$store_id)->update($input);
            }
            //Commit Transaction to Save Data to Database
            DB::commit();
            $success_message = ($mode == "add") ? trans('store-admin.added_msg',['name'=>trans('store-admin.admin')]) : trans('store-admin.updated_msg',['name'=>trans('store-admin.admin')]);
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.users.index')->with('message',$success_message);
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
        $user_id = Crypt::decrypt($request->user_id);
        $update_access = array();
        $update_access['status'] = $request->status_value;
        $update_access['updated_by'] = Auth::user()->id;
        User::where('id',$user_id)->update($update_access); 
        return response()->json(['message'=>trans('store-admin.updated_msg',['name'=>trans('store-admin.status')])]);
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $delete_user = array();
        $delete_user['is_deleted'] = 1;  
        $delete_user['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_user['updated_by'] = Auth::user()->id;
        User::where('id',$id)->update($delete_user); 
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.users.index')->with('message',trans('store-admin.deleted_msg',['name'=>trans('store-admin.admin')]));
    }
}
