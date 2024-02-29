<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\Category;
use Exception;
use App\Http\Requests\StoreAdmin\CategoryRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use URL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\CommonController;
use Image;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class CategoryController extends Controller
{
    protected $store_url;
    protected $store_logo;
    public function __construct() {
        $this->store_url = CommonController::storeURL();
        $this->store_logo = CommonController::storeLogo();
    }

    /*public function index(Request $request)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $prefix_url = config('app.module_prefix_url');
        if($request->type != "") {
            $export_type = $request->export_type;
            if($export_type != "pdf" && $export_type != "excel") {
                $final_data=array();
                // $columns = array( 
                //     0 =>'id',
                //     1=> 'category_name',
                //     2=> 'category_number',
                //     3=> 'category_image',
                //     4=> 'banner',
                //     5=> 'icon',
                //     6=> 'created_at',
                //     7=> 'status',
                //     8=> 'action',
                // );
                $columns = array( 
                    0 =>'id',
                    1=> 'category_name',
                    2=> 'category_number',
                    3=> 'category_image',
                    4=> 'icon',
                    // 5=> 'order_number',
                    5=> 'created_at',
                    6=> 'status',
                    7=> 'action'
                );
                $limit = $request->length;
                $start = $request->start; 
                $dir = $request->order[0]['dir'];
                $order = ($columns[$request->order[0]['column']] == "id") ? 'category_id' : $columns[$request->order[0]['column']];
            }
            $where_cond = 'where is_deleted = 0 AND store_id = '.Auth::user()->store_id;
            if(!empty($request->search['value'])) {
                $search = $request->search['value']; 
                // $where_cond .= " AND (category_name LIKE '%".$search."%' or category_number LIKE '%".$search."%' or order_number LIKE '%".$search."%' or DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE '%".$search."%')";
                $where_cond .= " AND (category_name LIKE '%".$search."%' or category_number LIKE '%".$search."%' or DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE '%".$search."%')";
            }
            $category_details = DB::select('SELECT category_name, category_number, order_number, banner, icon, featured, status,category_id,category_image,DATE_FORMAT(created_at, "%d-%m-%Y %H:%i") as created_at FROM store_category '.$where_cond.' ORDER BY '.$order.' '.$dir.' LIMIT '.$start.','.$limit);
            $filtered_category_details = DB::select('SELECT category_id FROM store_category '.$where_cond);
            $count_query = Category::where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0],
            ]);
            $totalCount = $count_query->get()->count();
            if(!empty($category_details)) {
                if($export_type != "pdf" && $export_type != "excel") {
                    $i=0;$j=0;
                    foreach($category_details as $category) {
                        $status_checked = $category->status == 1 ? 'checked' : '';
                        $featured_checked = $category->featured == 1 ? 'checked' : '';
                        $final_data[$i]=array(
                            'id'=>++$j,
                            'category_name'=>$category->category_name, 
                            'category_number'=> $category->category_number, 
                            'category_image'=> ($category->category_image != "") ? '<img src="'.$category->category_image.'" class="img-sm img-thumbnail" alt="Item">' : '', 
                            // 'banner'=> ($category->banner != "") ? '<img src="'.$category->banner.'" class="img-sm img-thumbnail" alt="Item">' : '', 
                            'icon'=> ($category->icon != "") ? '<img src="'.$category->icon.'" class="img-sm img-thumbnail" alt="Item">' : '', 
                            // 'order_number'=> '<p data-order="'.$category->order_number.'"><a href="javascript:;" class="editable-category-order" data-type="text" data-pk="1" data-url="" data-mode="popup" data-title="Age:">'.$category->order_number.'</a></p>', 
                            // 'order_number'=> '<span class="editable-category-order">'.$category->order_number.'</span> <input class="category-order-number dnone" style="width: 20em"/>', 
                            // 'order_number'=> '<input type="text" class="category-order-number form-control" style="width: 6em;" data-order-number="'.$category->order_number.'" value="'.$category->order_number.'">', 
                            'created_at'=> $category->created_at, 
                            'status'=>
                                "<div class='custom-control custom-switch'>
                                    <input type='checkbox' data-type='status' class='custom-control-input category-status' name='status' value='1' $status_checked id='status-customSwitch".$i."'>
                                    <label class='custom-control-label' for='status-customSwitch".$i."'></label>
                                </div>",
                            'action'=>
                                "<a class='btn btn-circle btn-danger btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.category.create', Crypt::encrypt($category->category_id))."'><i class='fa fa-edit'></i></a>
                                <a class='btn btn-circle btn-primary btn-xs delete-category' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.category.destroy', Crypt::encrypt($category->category_id))."'><i class='fa fa-trash'></i></a>
                                <input type='hidden' class='category-id' value='".Crypt::encrypt($category->category_id)."'>
                                <input type='hidden' class='status-url' value='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.category.update')."'>" 
                        );
                        $i++;
                    }
                } else {

                }
            }
            $totalFiltered = count($filtered_category_details);
            $json_data = array(
                "draw"            => intval($request->draw),  
                "recordsTotal"    => intval($totalCount),  
                "recordsFiltered" => intval($totalFiltered), 
                "data"            => $final_data   
            );
            echo json_encode($json_data); 
        } else
            return view('store_admin.category.list',compact('store_url','store_logo'));
    }*/

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
                    1=> 'category_name',
                    2=> 'category_number',
                    3=> 'category_image',
                    4=> 'icon',
                    // 5=> 'order_number',
                    5=> 'created_at',
                    6=> 'status',
                    7=> 'action'
                );
                $limit = $request->length;
                $start = $request->start; 
                $dir = $request->order[0]['dir'];
                $order = ($columns[$request->order[0]['column']] == "id") ? 'category_id' : $columns[$request->order[0]['column']];
            }
            $whereCond = 'is_deleted = 0 AND store_id = ' . Auth::user()->store_id;
            if (!empty($request->search['value'])) {
                $search = $request->search['value'];
                $whereCond .= " AND (category_name LIKE '%" . $search . "%' OR category_number LIKE '%" . $search . "%' OR DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE '%" . $search . "%')";
            }
            $category_details_query = Category::select('category_name', 'category_number', 'order_number', 'banner', 'icon', 'featured', 'status', 'category_id', 'category_image',DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as category_created_at"))
            ->whereRaw($whereCond);
            $totalCount = $category_details_query->count();
            if($export_type != "pdf" && $export_type != "excel") {
                $category_details_query->orderBy($order, $dir);
                $category_details = $category_details_query->skip($start)
                    ->take($limit)
                    ->get();
            }
            $filtered_category_details = $category_details_query->get();
            if($export_type != "pdf" && $export_type != "excel") {
                if(!empty($category_details)) {
                    $i=0;$j=0;
                    foreach($category_details as $category) {
                        $status_checked = $category->status == 1 ? 'checked' : '';
                        $featured_checked = $category->featured == 1 ? 'checked' : '';
                        $final_data[$i]=array(
                            'id'=>++$j,
                            'category_name'=>$category->category_name, 
                            'category_number'=> $category->category_number, 
                            'category_image'=> ($category->category_image != "") ? '<img src="'.$category->category_image.'" class="img-sm img-thumbnail" alt="Item">' : '', 
                            // 'banner'=> ($category->banner != "") ? '<img src="'.$category->banner.'" class="img-sm img-thumbnail" alt="Item">' : '', 
                            'icon'=> ($category->icon != "") ? '<img src="'.$category->icon.'" class="img-sm img-thumbnail" alt="Item">' : '', 
                            // 'order_number'=> '<p data-order="'.$category->order_number.'"><a href="javascript:;" class="editable-category-order" data-type="text" data-pk="1" data-url="" data-mode="popup" data-title="Age:">'.$category->order_number.'</a></p>', 
                            // 'order_number'=> '<span class="editable-category-order">'.$category->order_number.'</span> <input class="category-order-number dnone" style="width: 20em"/>', 
                            // 'order_number'=> '<input type="text" class="category-order-number form-control" style="width: 6em;" data-order-number="'.$category->order_number.'" value="'.$category->order_number.'">', 
                            'created_at'=> $category->category_created_at, 
                            'status'=>
                                "<div class='custom-control custom-switch'>
                                    <input type='checkbox' data-type='status' class='custom-control-input category-status' name='status' value='1' $status_checked id='status-customSwitch".$i."'>
                                    <label class='custom-control-label' for='status-customSwitch".$i."'></label>
                                </div>",
                            'action'=>
                                "<a class='btn btn-circle btn-danger btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.category.create', Crypt::encrypt($category->category_id))."'><i class='fa fa-edit'></i></a>
                                <a class='btn btn-circle btn-primary btn-xs delete-category' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.category.destroy', Crypt::encrypt($category->category_id))."'><i class='fa fa-trash'></i></a>
                                <input type='hidden' class='category-id' value='".Crypt::encrypt($category->category_id)."'>
                                <input type='hidden' class='status-url' value='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.category.update')."'>" 
                        );
                        $i++;
                    }
                }
                $totalFiltered = count($filtered_category_details);
                $json_data = array(
                    "draw"            => intval($request->draw),  
                    "recordsTotal"    => intval($totalCount),  
                    "recordsFiltered" => intval($totalCount), 
                    "data"            => $final_data
                );
                echo json_encode($json_data);
            } else {
                $get_category_details = $filtered_category_details->toArray();
                if($export_type == "pdf") {
                    $columns = ['Category Name','Category ID','Created At'];
                    $column_field_name = ['category_name','category_number','category_created_at'];
                    $data = [
                        'export_columns' => $columns,
                        'export_data' => $get_category_details,
                        'column_field_name' => $column_field_name,
                        'type' => 'single_header',
                        'title' => 'Category Details'
                    ];
                    $pdf = PDF::loadView('pdf.template', $data);
                    $pdf->setPaper('A4', 'portrait');
                    return $pdf->download('category-details.pdf'); 
                } 
                else if ($export_type == "excel") {
                    $csvData = '';
                    $export_columns[] = ['#', 'Category Name','Category ID','Created At'];
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
                    $sheet->setTitle('Category Details');
                    $title = 'Category Details';
                    $sheet->setCellValue('A1', $title);
                    $sheet->mergeCells('A1:D1');
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                    if (!empty($export_columns)) {
                        foreach ($export_columns as $columns) {
                            $csvData .= implode(',', $columns) . "\n";
                        }
                    }
                    if (!empty($get_category_details)) {
                        $i = 0;
                        foreach ($get_category_details as $category) {
                            $row = [
                                ++$i,
                                $category['category_name'],
                                $category['category_number'],
                                $category['category_created_at']
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
                    $sheet->getColumnDimension('B')->setWidth(30);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(20);
                    $sheet->getStyle('1:1')->getFont()->setBold(true); 
                    $sheet->getStyle('A2:D2')->getFont()->setBold(true); 
                    $sheet->getStyle('A2:D2')->getAlignment()->setHorizontal('center');
                    $filename = 'category-details.xlsx';
                    $filePath = public_path($filename);
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
            return view('store_admin.category.list',compact('store_url','store_logo'));
    }

    public function create($id=null)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $mode = !empty($id) ? 'edit' : 'add';
        $category_details = [];
        if(!empty($id))
            $category_details = Category::where('category_id',Crypt::decrypt($id))->get(['category_number','category_name','order_number','banner','icon','category_id','store_id','meta_title','meta_description','slug','category_image']);
        $user_role_id = Auth::user()->is_admin;
        return view('store_admin.category.create',compact('store_url','mode','category_details','user_role_id','store_logo'));
    }

    
    public function store(CategoryRequest $request)
    {
        try {
            $input = $request->all();
            $mode = $input['mode'];
            $store_id = Auth::user()->store_id;
            $category_id = ($mode == "edit") ? $input['category_id'] : 0;
            $url = URL::to("/");
            $destinationPath = base_path().'/images/';
            if (!file_exists($destinationPath)) 
                mkdir($destinationPath, 0777, true);
            $destinationPath = base_path().'/images/'.$store_id;
            if (!file_exists($destinationPath)) 
                mkdir($destinationPath, 0777, true);
            if ($request->hasFile('category_image')) {
                $image = $request->file('category_image');
                $categoryImage = date('YmdHis') . "." . $image->extension();
                $destinationPath = base_path().'/images/'.$store_id.'/category';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $input['category_image'] = $url.'/images/'.$store_id.'/category'.'/'.$categoryImage;
                $img = Image::make($image->path());
                $img->resize(329, 263, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/'.$categoryImage);
            } else if(($request->remove_category_image == 1) || ($mode == "add"))
                $input['category_image'] = $url.'/assets/placeholder.jpg';

            if ($request->hasFile('banner_image')) {
                $image = $request->file('banner_image');
                $bannerImage = date('YmdHis') . "." . $image->extension();
                $destinationPath = base_path().'/images/'.$store_id.'/banner';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $input['banner'] = $url.'/images/'.$store_id.'/banner'.'/'.$bannerImage;
                $img = Image::make($image->path());
                $img->resize(200, 200, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/'.$bannerImage);
            } else if(($request->remove_banner_image == 1)  || ($mode == "add"))
                $input['banner'] = $url.'/assets/placeholder.jpg';

            if ($request->hasFile('icon_image')) {
                $image = $request->file('icon_image');
                $iconImage = date('YmdHis') . "." . $image->extension();
                $destinationPath = base_path().'/images/'.$store_id.'/icon';
                if (!file_exists($destinationPath)) 
                    mkdir($destinationPath, 0777, true);
                $input['icon'] = $url.'/images/'.$store_id.'/icon'.'/'.$iconImage;
                $img = Image::make($image->path());
                $img->resize(32, 32, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/'.$iconImage);
            } else if(($request->remove_icon_image == 1) || ($mode == "add"))
                $input['icon'] = $url.'/assets/placeholder_icon.jpg';

            //Reset the input values
            $remove_array_values = array('_token','mode','banner_image','icon_image','remove_icon_image','remove_banner_image','remove_category_image');
            foreach($remove_array_values as $value) {
                unset($input[$value]);
            }
            //Start DB Transaction
            DB::beginTransaction();
            if($mode == "add") {
                $input['created_by'] = Auth::user()->id;
                $input['store_id'] = Auth::user()->store_id;
                $category_id = Category::create($input)->category_id;
                $update_data = array();
                $update_data['category_number'] = "CAT".sprintf("%03d",$category_id);
                Category::where('category_id',$category_id)->update($update_data);
            } else {
                $input['updated_by'] = Auth::user()->id;
                Category::where('category_id',$category_id)->update($input);
            }
            //Commit Transaction to Save Data to Database
            DB::commit();
            $success_message = ($mode == "add") ? "Category added successfully" : "Category updated successfully.";
            $prefix_url = config('app.module_prefix_url');
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.category.index')->with('message',$success_message);
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
        $category_id = Crypt::decrypt($request->category_id);
        $column_name = $request->type;
        $update_status = array();
        $update_status[$column_name] = $request->value;
        $update_status['updated_by'] = Auth::user()->id;
        Category::where('category_id',$category_id)->update($update_status);
        return response()->json(['message'=>ucfirst($column_name).' updated successfully.']);
    }

    public function destroy($id)
    {
        $category_id = Crypt::decrypt($id);
        $delete_category = array();
        $delete_category['is_deleted'] = 1;  
        $delete_category['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_category['updated_by'] = Auth::user()->id;
        Category::where('category_id',$category_id)->update($delete_category);
        $prefix_url = config('app.module_prefix_url');
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.category.index')->with('message',"Category deleted successfully.");
    }

    public function updateOrderNumber(Request $request) {
        $old_order_number = $request->old_order_number;
        $order_number = $request->order_number;
        $category_id = Crypt::decrypt($request->category_id);
        $update_order_no = array();
        if($old_order_number < $order_number) {
            // $update_order_no['order_number'] = DB::raw('order_number' - 1); 
            // $where_condition = [
            //     ['order_number', '>', $old_order_number],
            //     ['order_number', '<=', $order_number],
            // ];
            $set = "order_number = order_number - 1";
            $where = "order_number > $old_order_number and order_number <= $order_number and store_id = '".Auth::user()->store_id."' and is_deleted = 0";
        } else {
            // $update_order_no['order_number'] = DB::raw('order_number'+ 1); 
            // $where_condition = [
            //     ['order_number', '<', $old_order_number],
            //     ['order_number', '>=', $order_number],
            // ];
            $set = "order_number = order_number + 1";
            $where = "order_number < $old_order_number and order_number >= $order_number and store_id = '".Auth::user()->store_id."' and is_deleted = 0";
        }
        DB::statement("UPDATE store_category SET $set where  $where");
        // Category::where([
        //     ['store_id', '=', Auth::user()->store_id],
        //     ['is_deleted', '=', 0]
        // ])->where($where_condition)->update($update_order_no);
        $update_order_no['order_number'] = $order_number; 
        Category::where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
            ['category_id', '=', $category_id]
        ])->update($update_order_no);
        return response()->json(['message'=>'Order number updated successfully.']);
        /*$update_order_no = array();
        $update_order_no['order_number'] = $order_number; 
        Category::where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
            ['category_id', '=', $category_id]
        ])->update($update_order_no);
        $max_order_number = Category::where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
        ])->max('order_number');
        for($i=$order_number;$order_number<=$max_order_number;$i++) {
            $update_category_order = array();
            $update_category_order['order_number'] = $i + 1;  
            Category::where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0],
                ['order_number', '=', $i]
            ])->whereNotIn('category_id',[$category_id])->update($update_category_order);
        }
        return response()->json(['message'=>'Order number updated successfully.']);*/
    }

}
