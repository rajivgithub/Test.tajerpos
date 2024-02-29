<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\Category;
use Exception;
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
use App\Rules\ValidateCsvContent;

class PaymentController extends Controller
{
    protected $store_url;
    protected $store_logo;
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
                    1=> 'payment_credential_id',
                    2=> 'payment_type',
                    3=> 'client_id',
                    4=> 'created_at',
                    5=> 'status',
                    6=> 'action'
                );
                $limit = $request->length;
                $start = $request->start; 
                $dir = $request->order[0]['dir'];
                $order = ($columns[$request->order[0]['column']] == "id") ? 'id' : $columns[$request->order[0]['column']];
            }
            $whereCond = 'is_deleted = 0 AND store_id = ' . Auth::user()->store_id;
            if (!empty($request->search['value'])) {
                $search = $request->search['value'];
                $whereCond .= " AND (payment_type LIKE '%" . $search . "%' OR payment_credential_id LIKE '%" . $search . "%' OR DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE '%" . $search . "%')";
            }
            $gateway_details_query = DB::table('store_payment_credentials')->select('id','payment_credential_id', 'store_id', 'payment_type', 'client_id', 'client_secret', 'client_url', 'status',DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as gateway_created_at"))
            ->whereRaw($whereCond);
            $totalCount = $gateway_details_query->count();
            if($export_type != "pdf" && $export_type != "excel") {
                $gateway_details_query->orderBy($order, $dir);
                $gateway_details = $gateway_details_query->skip($start)
                    ->take($limit)
                    ->get();
            }
            $filtered_gateway_details = $gateway_details_query->get();
            if($export_type != "pdf" && $export_type != "excel") {
                if(!empty($gateway_details)) {
                    $i=0;$j=0;
                    foreach($gateway_details as $gateway) {
                        $status_checked = $gateway->status == 1 ? 'checked' : '';
                        // $featured_checked = $gateway->featured == 1 ? 'checked' : '';
                        $final_data[$i]=array(
                            'id'=>++$j,
                            'payment_credential_id'=>$gateway->payment_credential_id, 
                            'payment_type'=> $gateway->payment_type, 
                            'client_id'=> $gateway->client_id, 
                            'created_at'=> $gateway->gateway_created_at, 
                            'status'=>
                                "<div class='custom-control '>
                                    <input type='radio' data-type='status' class='custom-control-input gateway-status' name='status' value='1' $status_checked id='status-customSwitch".$i."'>
                                    <label class='custom-control-label' for='status-customSwitch".$i."'></label>
                                </div>",
                            'action'=>
                                "<a class='btn btn-circle btn-danger btn-xs' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.managepayment.create', Crypt::encrypt($gateway->id))."'><i class='fa fa-edit'></i></a>
                                <a class='btn btn-circle btn-primary btn-xs delete-gateway' href='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.managepayment.destroy', Crypt::encrypt($gateway->id))."'><i class='fa fa-trash'></i></a>
                                <input type='hidden' class='gateway-id' value='".Crypt::encrypt($gateway->id)."'>
                                <input type='hidden' class='status-url' value='".route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.managepayment.update')."'>" 
                        );
                        $i++;
                    }
                }
                $totalFiltered = count($filtered_gateway_details);
                $json_data = array(
                    "draw"            => intval($request->draw),  
                    "recordsTotal"    => intval($totalCount),  
                    "recordsFiltered" => intval($totalCount), 
                    "data"            => $final_data
                );
                echo json_encode($json_data);
            } else {
                // $get_category_details = $filtered_gateway_details->toArray();
                // if($export_type == "pdf") {
                //     $columns = [trans('store-admin.category_name'),trans('store-admin.category_id'),trans('store-admin.created_at')];
                //     $column_field_name = ['category_name','category_number','category_created_at'];
                //     $data = [
                //         'export_columns' => $columns,
                //         'export_data' => $get_category_details,
                //         'column_field_name' => $column_field_name,
                //         'type' => 'single_header',
                //         'title' => trans('store-admin.category_details')
                //     ];
                //     $pdf = PDF::loadView('pdf.template', $data);
                //     $pdf->setPaper('A4', 'portrait');
                //     return $pdf->download('category-details.pdf'); 
                // } 
                // else if ($export_type == "excel") {
                //     $csvData = '';
                //     $export_columns[] = ['#', trans('store-admin.category_name'),trans('store-admin.category_id'),trans('store-admin.created_at')];
                //     $spreadsheet = new Spreadsheet();
                //     $defaultBorderStyle = [
                //         'borders' => [
                //             'outline' => [
                //                 'borderStyle' => Border::BORDER_THIN,
                //                 'color' => ['argb' => '000000'],
                //             ],
                //         ],
                //     ];
                //     $spreadsheet->getDefaultStyle()->applyFromArray($defaultBorderStyle);
                //     // Add a new worksheet
                //     $sheet = $spreadsheet->getActiveSheet();
                //     // Set the title of the worksheet
                //     $sheet->setTitle(trans('store-admin.category_details'));
                //     $title = trans('store-admin.category_details');
                //     $sheet->setCellValue('A1', $title);
                //     $sheet->mergeCells('A1:D1');
                //     $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                //     if (!empty($export_columns)) {
                //         foreach ($export_columns as $columns) {
                //             $csvData .= implode(',', $columns) . "\n";
                //         }
                //     }
                //     if (!empty($get_category_details)) {
                //         $i = 0;
                //         foreach ($get_category_details as $category) {
                //             $row = [
                //                 ++$i,
                //                 $category['category_name'],
                //                 $category['category_number'],
                //                 $category['category_created_at']
                //             ];
                //             $csvData .= implode(',', $row) . "\n";
                //         }
                //     }
                //     $rows = explode("\n", $csvData);
                //     $rowIndex = 2;
                //     foreach ($rows as $row) {
                //         $columns = explode(",", $row);
                //         $columnIndex = 1;
                //         foreach ($columns as $column) {
                //             $cell = $sheet->getCellByColumnAndRow($columnIndex, $rowIndex);
                //             $cell->setValue($column);
                //             $columnIndex++;
                //         }
                //         $rowIndex++;
                //     }
                //     $sheet->getColumnDimension('A')->setWidth(5);
                //     $sheet->getColumnDimension('B')->setWidth(30);
                //     $sheet->getColumnDimension('C')->setWidth(20);
                //     $sheet->getColumnDimension('D')->setWidth(20);
                //     $sheet->getStyle('1:1')->getFont()->setBold(true); 
                //     $sheet->getStyle('A2:D2')->getFont()->setBold(true); 
                //     $sheet->getStyle('A2:D2')->getAlignment()->setHorizontal('center');
                //     $filename = 'category-details.xlsx';
                //     $filePath = base_path($filename);
                //     $writer = new Xlsx($spreadsheet);
                //     $writer->save($filePath);
                //     $response = [
                //         'success' => true,
                //         'message' => trans('store-admin.excel_generated_msg'),
                //         'file_url' => asset($filename)
                //     ];
                //     echo json_encode($response);
                // }
            } 
        } else
            return view('store_admin.paymentgateway.list',compact('store_url','store_logo'));
    }

    public function create($id=null)
    {

        // echo Crypt::decrypt($id); exit;

        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $mode = !empty($id) ? 'edit' : 'add';
        $gateway_details = [];
        if(!empty($id))
            $gateway_details = DB::table('store_payment_credentials')->where('id',Crypt::decrypt($id))->get(['id','payment_credential_id','store_id','payment_type','client_id','client_secret','client_url','client_currency','status','is_deleted']);
        $user_role_id = Auth::user()->is_admin;
        return view('store_admin.paymentgateway.create',compact('store_url','mode','gateway_details','user_role_id','store_logo'));
    }

    
    public function store(Request $request)
    {
        try {
       
            $mode = $request->mode;
            $store_id = Auth::user()->store_id;
            $category_id = ($mode == "edit") ? $request->gateway_id : 0;            
            //Start DB Transaction
            DB::beginTransaction();
            if($mode == "add") {

                $data = DB::table('store_payment_credentials')->get()->count();
                if($data == 0){
                    $data_count = 1;
                    $status = 1;
                }else{
                    $data_count = $data+1;
                    $status = 0;
                }
                $gateway = "PGWAY".sprintf("%03d",$data_count);

                DB::table('store_payment_credentials')->insert([
                    'payment_credential_id' => $gateway,
                    'store_id' => Auth::user()->store_id,
                    'payment_type' => 'PayTabs',
                    'client_id' => $request->profile_id,
                    'client_secret' => $request->auth,
                    'client_url' => $request->payment_url,
                    'client_currency' => 'SAR',
                    'status' => $status,
                    'created_by' =>  Auth::user()->id
                ]);

            } else {

                DB::table('store_payment_credentials')->where('id',$request->gateway_id)->update([
                    
                    'client_id' => $request->profile_id,
                    'client_secret' => $request->auth,
                    'client_url' => $request->payment_url,
                    'updated_by' =>  Auth::user()->id
                ]);
            }
            //Commit Transaction to Save Data to Database
            DB::commit();
            $success_message = ($mode == "add") ? trans('store-admin.added_msg',['name'=>trans('store-admin.payment_gateway')]) : trans('store-admin.updated_msg',['name'=>trans('store-admin.payment_gateway')]);
            $prefix_url = config('app.module_prefix_url');
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.managepayment.index')->with('message',$success_message);
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
        $gateway_id = Crypt::decrypt($request->gateway_id);
        $store_id = Auth::user()->store_id;

            DB::table('store_payment_credentials')->where('store_id',$store_id)->where('id',$gateway_id)->update([                    
                'status' => 1,
                'updated_by' =>  Auth::user()->id
            ]);
            DB::table('store_payment_credentials')->where('store_id',$store_id)->where('id','!=',$gateway_id)->update([                    
                'status' => 0,
                'updated_by' =>  Auth::user()->id
            ]);

        return response()->json(['message'=>trans('store-admin.updated_msg',['name'=>trans('store-admin.status')])]);
    }

    public function destroy($id)
    {
        $gateway_id = Crypt::decrypt($id);
        DB::table('store_payment_credentials')->where('id',$gateway_id)->update([
                    
            'is_deleted' =>1,
            'deleted_at' => Carbon::now()->toDateTimeString(),
            'updated_by' =>  Auth::user()->id
        ]);
        $prefix_url = config('app.module_prefix_url');
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.managepayment.index')->with('message',trans('store-admin.deleted_msg',['name'=>trans('store-admin.payment_gateway')]));
    }

   
}
