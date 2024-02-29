<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAdmin\ThemeRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\Theme;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\CommonController;

class ThemeController extends Controller
{
    protected $store_url;
    public function __construct() {
        $this->store_url = CommonController::storeURL();
    }

    public function index(Request $request)
    {
        $store_url = $this->store_url; 
        $theme_details = Theme::where('created_by',Auth::user()->id)->where('is_deleted',0)->get(['theme_id','color_name','color_code']);
        $mode = (count($theme_details) > 0) ? 'edit' : 'add';
        return view('store_admin.themes.list',compact('store_url','theme_details','mode'));
        /*f($request->type != "") {
            $final_data=array();
            $columns = array( 
              0 =>'id',
              1=> 'color_name',
              2=> 'status',
              3=> 'action',
            );
            $limit = $request->length;
            $start = $request->start;
            $order = ($columns[$request->order[0]['column']] == "id") ? 'theme_id ' : $columns[$request->order[0]['column']];
            $dir = $request->order[0]['dir'];
            $where_cond = 'where created_by = '.Auth::user()->id.' AND is_deleted = 0';
            if(!empty($request->search['value'])) {
                $search = $request->search['value']; 
                $where_cond .= " AND (color_name LIKE '%".$search."%')";
            }
            $theme_details = DB::select('SELECT color_name, theme_id, status FROM store_themes '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $totalCount = $count_query->get()->count();
            if(!empty($theme_details)) {
                $i=0;$j=0;
                foreach($theme_details as $theme) {
                    $status_checked = $theme->status == 1 ? 'checked' : '';
                    $final_data[$i]=array(
                        'id'=>++$j,
                        'color_name'=>$theme->color_name,
                        'status'=>
                            "<div class='form-check form-switch ps-0'>
                                <input class='form-check-input ms-0 theme-status' type='checkbox' name='status' value='1' $status_checked role='switch' id='flexSwitchCheckDefault'>
                            </div>",
                        'action'=>
                            "<a class='btn btn-success text-white rounded font-sm' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.themes.create', Crypt::encrypt($theme->theme_id))."'><i class='fa fa-edit'></i></a>
                            <form action='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.themes.destroy', Crypt::encrypt($theme->theme_id))."' class='delete-theme-form'>
                                <button class='btn btn-danger rounded font-sm theme-color-delete'><i class='fa fa-trash'></i></button>
                            </form>  
                            <input type='hidden' class='theme_id' value='".Crypt::encrypt($theme->theme_id)."'>
                            <input type='hidden' class='status_url' value='".route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.themes.update')."'>" 
                    );
                    $i++;
                }
            }
            $totalFiltered = count($theme_details);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data   
            );
            echo json_encode($json_data); 
        } else {
            return view('store_admin.themes.list',compact('store_url'));
        }*/
    }

    public function create($id=null)
    {
        $store_url = $this->store_url; 
        $mode = 'edit';
        $theme_id = Crypt::decrypt($id);
        $theme_details = Theme::where('theme_id',$theme_id)->get(['theme_id','color_name','color_code']);
        return view('store_admin.themes.create',compact('store_url','mode','theme_details'));
    }

    public function store(ThemeRequest $request)
    {
        try {
            $mode = $request->mode;
            $input = $request->all();
            $theme_id = ($mode == "edit") ? Crypt::decrypt($request->theme_id) : 0;
            if($mode == "edit") {
                //Reset the input values
                $remove_array_values = array('_token','mode');
                foreach($remove_array_values as $value) {
                    unset($input[$value]);
                }
                $input['updated_by'] = Auth::user()->id;
                $input['theme_id'] = $theme_id;
                Theme::where('theme_id',$theme_id)->update($input);
            } else {
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                $theme_id = Theme::create($input)->theme_id;
            }
            $success_message = ($mode == "edit") ? "Theme color updated successfully" : "Theme color added successfully.";
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.themes.index')->with('message',$success_message);
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
        $theme_id = Crypt::decrypt($request->theme_id);
        $update_access = array();
        $update_access['status'] = $request->status_value;
        $update_access['updated_by'] = Auth::user()->id;
        Theme::where('theme_id',$theme_id)->update($update_access);
        return response()->json(['message'=>'Status updated successfully.']);
    }

    public function destroy($id)
    {
        $theme_id = Crypt::decrypt($id);
        $delete_theme = array();
        $delete_theme['is_deleted'] = 1;  
        $delete_theme['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_theme['updated_by'] = Auth::user()->id;
        Theme::where('theme_id',$theme_id)->update($delete_theme);
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.config('app.module_prefix_url').'.themes.index')->with('message',"Theme color deleted successfully.");
    }
}
