<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\SubCategory;
use Exception;
use App\Http\Requests\StoreAdmin\SubCategoryRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StoreAdmin\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use URL;
use App\Http\Controllers\CommonController;
use Image;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class SubCategoryController extends Controller
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
        $prefix_url = config('app.module_prefix_url');
        if($request->type != "") {
            $export_type = $request->export_type;
            if($export_type != "pdf" && $export_type != "excel") {
                $final_data=array();
                $columns = array( 
                    0 =>'id',
                    1=> 'category_number',
                    2=> 'category_name',
                    3=> 'sub_category_number',
                    4=> 'sub_category_name', 
                    5=> 'created_at',
                    6=> 'status',
                    7=> 'action',
                    8 => 'checkbox',
                    9 => 'updated_at',
                );
                $limit = $request->length;
                $start = $request->start; 
                $dir = $request->order[0]['dir'];
                $order = ($columns[$request->order[0]['column']] == "id") ? 'sub_category_id' : $columns[$request->order[0]['column']];
            }
            $where_cond = 'store_sub_category.is_deleted = 0 AND store_sub_category.store_id = '.Auth::user()->store_id.' AND store_category.is_deleted = 0 AND store_category.status = 1';
            if(!empty($request->search['value'])) {
                $search = $request->search['value']; 
                $where_cond .= " AND (category_number LIKE '%".$search."%' or category_name LIKE '%".$search."%' or sub_category_number LIKE '%".$search."%' or sub_category_name LIKE '%".$search."%' or store_sub_category.order_number LIKE '%".$search."%' or DATE_FORMAT(store_sub_category.created_at, '%d-%m-%Y %H:%i') LIKE '%".$search."%') or DATE_FORMAT(updated_at, '%d-%m-%Y %H:%i') LIKE '%" . $search . "%'";
            }
            if($request->filterByStatus != "")
                $where_cond .= " AND store_sub_category.status = $request->filterByStatus ";
            if(!empty($request->filterByCategory))
                $where_cond .= " AND store_sub_category.category_id = $request->filterByCategory ";
            $sub_category_details_query = SubCategory::select('sub_category_name', 'sub_category_number', 'store_sub_category.order_number', 'sub_category_image', 'store_sub_category.banner', 'store_sub_category.icon', 'store_sub_category.featured', 'store_sub_category.status', 'sub_category_id', 'category_name', 'category_number',DB::raw("DATE_FORMAT(store_sub_category.created_at, '%d-%m-%Y %H:%i') as sub_category_created_at"),DB::raw("DATE_FORMAT(store_sub_category.updated_at, '%d-%m-%Y %H:%i') as sub_category_updated_at"))
                ->join('store_category', 'store_category.category_id', '=', 'store_sub_category.category_id')
                ->whereRaw($where_cond);
            $totalCount = $sub_category_details_query->count();
            if($export_type != "pdf" && $export_type != "excel") {
                $sub_category_details_query->orderBy($order, $dir);
                $sub_category_details = $sub_category_details_query->skip($start)
                    ->take($limit)
                    ->get();
            }
            $filtered_sub_category_details = $sub_category_details_query->get();
            if($export_type != "pdf" && $export_type != "excel") {
                if(!empty($sub_category_details)) {
                    $i=0;$j=0;
                    foreach($sub_category_details as $sub_category) {
                        $status_checked = $sub_category->status == 1 ? 'checked' : '';
                        $featured_checked = $sub_category->featured == 1 ? 'checked' : '';
                        $final_data[$i]=array(
                            'checkbox' => '<div class="form-check"><input type="checkbox" name="sub_category_checkbox" class="form-check-input sub-category-checkbox" value="' . $sub_category->sub_category_id . '"></div>',
                            'id'=>++$j,
                            'category_number'=> $sub_category->category_number, 
                            'category_name'=>$sub_category->category_name, 
                            'sub_category_number'=>$sub_category->sub_category_number, 
                            'sub_category_name'=> $sub_category->sub_category_name, 
                            'created_at' => $sub_category->sub_category_created_at, 
                            'updated_at' => $sub_category->sub_category_updated_at,
                            'status'=>
                                "<div class='custom-control custom-switch'>
                                    <input type='checkbox' data-type='status' class='custom-control-input sub-category-status' name='status' value='1' $status_checked id='status-customSwitch".$i."'>
                                    <label class='custom-control-label' for='status-customSwitch".$i."'></label>
                                </div>",
                            'action'=>
                                "<a class='btn btn-circle btn-danger btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.sub-category.create', Crypt::encrypt($sub_category->sub_category_id))."'><i class='fa fa-edit'></i></a>
                                <a class='btn btn-circle btn-primary btn-xs delete-sub-category' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.sub-category.destroy', Crypt::encrypt($sub_category->sub_category_id))."'><i class='fa fa-trash'></i></a>
                                <input type='hidden' class='sub-category-id' value='".$sub_category->sub_category_id."'>" 
                        );
                        $i++;
                    }
                }
                $totalFiltered = count($filtered_sub_category_details);
                $json_data = array(
                    "draw"            => intval($request->draw),  
                    "recordsTotal"    => intval($totalCount),  
                    "recordsFiltered" => intval($totalCount), 
                    "data"            => $final_data   
                );
                echo json_encode($json_data); 
            } else {
                $get_sub_category_details = $filtered_sub_category_details->toArray();
                if($export_type == "pdf") {
                    $columns = ['Category ID','Category Name','Sub Category ID','Sub Category Name','Created At'];
                    $column_field_name = ['category_number','category_name','sub_category_number','sub_category_name','sub_category_created_at'];
                    $data = [
                        'export_columns' => $columns,
                        'export_data' => $get_sub_category_details,
                        'column_field_name' => $column_field_name,
                        'type' => 'single_header',
                        'title' => 'Sub Category Details'
                    ];
                    $pdf = PDF::loadView('pdf.template', $data);
                    $pdf->setPaper('A4', 'portrait');
                    return $pdf->download('sub-category-details.pdf');   
                } else if ($export_type == "excel") {
                    $csvData = '';
                    $export_columns[] = ['#', 'Category ID','Category Name','Sub Category ID','Sub Category Name','Created At'];
                    $spreadsheet = new Spreadsheet();
                    $defaultBorderStyle = [
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ];
                    $spreadsheet->getDefaultStyle()->applyFromArray($defaultBorderStyle);
                    // Add a new worksheet
                    $sheet = $spreadsheet->getActiveSheet();
                    // Set the title of the worksheet
                    $sheet->setTitle('Sub Category Details');
                    $title = 'Sub Category Details';
                    $sheet->setCellValue('A1', $title);
                    $sheet->mergeCells('A1:F1');
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                    if (!empty($export_columns)) {
                        foreach ($export_columns as $columns) {
                            $csvData .= implode(',', $columns) . "\n";
                        }
                    }
                    if (!empty($get_sub_category_details)) {
                        $i = 0;
                        foreach ($get_sub_category_details as $sub_category) {
                            $row = [
                                ++$i,
                                $sub_category['category_number'],
                                $sub_category['category_name'],
                                $sub_category['sub_category_number'],
                                $sub_category['sub_category_name'],
                                $sub_category['sub_category_created_at']
                            ];
                            $csvData .= implode(',', $row) . "\n";
                        }
                    }
                    $rows = explode("\n", $csvData);
                    $rowIndex = 2;
                    foreach ($rows as $row) {
                        $columns = explode(",", $row);
                        $columnIndex = 1;
                        foreach ($columns as $column) {
                            $cell = $sheet->getCellByColumnAndRow($columnIndex, $rowIndex);
                            $cell->setValue($column);
                            $columnIndex++;
                        }
                        $rowIndex++;
                    }
                    $sheet->getColumnDimension('A')->setWidth(5);
                    $sheet->getColumnDimension('B')->setWidth(20);
                    $sheet->getColumnDimension('C')->setWidth(30);
                    $sheet->getColumnDimension('D')->setWidth(20);
                    $sheet->getColumnDimension('E')->setWidth(30);
                    $sheet->getColumnDimension('F')->setWidth(20);
                    $sheet->getStyle('1:1')->getFont()->setBold(true); 
                    $sheet->getStyle('A2:F2')->getFont()->setBold(true); 
                    $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('center');
                    $filename = 'sub-category-details.xlsx';
                    $filePath = base_path($filename);
                    $writer = new Xlsx($spreadsheet);
                    $writer->save($filePath);
                    $response = [
                        'success' => true,
                        'message' => 'Excel file generated successfully.',
                        'file_url' => asset($filename)
                    ];
                    echo json_encode($response);
                }  
            }
        } else
            return view('store_admin.sub_category.list',compact('store_url','store_logo'));
    }

    public function create($id=null)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $mode = !empty($id) ? 'edit' : 'add';
        $category_details = Category::where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
            ['status', '=', 1]
        ])->get(['category_name','category_id']);
        $sub_category_details = [];
        if(!empty($id))
            $sub_category_details = SubCategory::where('sub_category_id',Crypt::decrypt($id))->get(['sub_category_number','order_number','banner','icon','category_id','store_id','meta_title','meta_description','slug','sub_category_id','sub_category_name','sub_category_image','sub_category_url']);
        $user_role_id = Auth::user()->is_admin;
        return view('store_admin.sub_category.create',compact('store_url','mode','category_details','sub_category_details','user_role_id','store_logo'));
    }

    public function store(SubCategoryRequest $request)
    {
        try {
            $input = $request->except('_token','mode','banner_image','icon_image','remove_subcategory_image','remove_banner_image','remove_icon_image');
            $mode = $request->mode;
            $store_id = Auth::user()->store_id;
            $sub_category_id = ($mode == "edit") ? $input['sub_category_id'] : 0;
            //Start DB Transaction
            DB::beginTransaction();
            if($mode == "add") {
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                $sub_category_id = SubCategory::create($input)->sub_category_id;
                $subCategoryCount = SubCategory::where([
                    ['is_deleted', '=', 0],
                    ['store_id','=',Auth::user()->store_id]
                ])->count();
                $update_data = array();
                $update_data['sub_category_number'] = "SUBCAT".sprintf("%03d",$subCategoryCount);
                SubCategory::where('sub_category_id',$sub_category_id)->update($update_data);
            } else {
                $input['updated_by'] = Auth::user()->id;
                SubCategory::where('sub_category_id',$sub_category_id)->update($input);
            }
            //Commit Transaction to Save Data to Database
            DB::commit();
            $success_message = ($mode == "add") ? trans('store-admin.added_msg',['name'=>trans('store-admin.sub_category')]) : trans('store-admin.updated_msg',['name'=>trans('store-admin.sub_category')]);
            $prefix_url = config('app.module_prefix_url');
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.sub-category.index')->with('message',$success_message);
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
        $subCategoryIDs = $request->subCategoryIDs;
        $column_name = $request->_type;
        $update_status = array();
        $update_status[$column_name] = $request->updateValue;
        $update_status['updated_by'] = Auth::user()->id;
        SubCategory::whereIn('sub_category_id',$subCategoryIDs)->update($update_status);
        return response()->json(['message'=>trans('store-admin.updated_msg',['name'=>trans('store-admin.status')])]);
    }

    public function destroy($id)
    {
        $sub_category_id = Crypt::decrypt($id);
        $delete_sub_category = array();
        $delete_sub_category['is_deleted'] = 1;  
        $delete_sub_category['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_sub_category['updated_by'] = Auth::user()->id;
        SubCategory::where('sub_category_id',$sub_category_id)->update($delete_sub_category);
        $prefix_url = config('app.module_prefix_url');
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.sub-category.index')->with('message',trans('store-admin.deleted_msg',['name'=>trans('store-admin.sub_category')]));
    }

    public function subCategoryList(Request $request) {
        $sub_category_details = SubCategory::where([
            ['category_id', '=', $request->category_id],
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
            ['status', '=', 1],
        ])->get(['sub_category_id','sub_category_name']);
        return response()->json(['sub_category_details'=> $sub_category_details]);
    }
}
