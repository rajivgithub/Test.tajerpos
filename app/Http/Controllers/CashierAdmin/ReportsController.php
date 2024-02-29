<?php

namespace App\Http\Controllers\CashierAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashierAdmin\Order;
use App\Models\CashierAdmin\OrderItems;
use App\Models\CashierAdmin\InStoreCustomer;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ReportsController extends Controller
{
    protected $store_url;
    protected $store_logo;
    public function __construct() {
        $this->store_url = CommonController::storeURL();
        $this->store_logo = CommonController::storeLogo();
    }

    public function transactionReport(Request $request)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $prefix_url = config('app.module_prefix_url');
        if($request->startDate != "") {
            $export_type = $request->export_type;
            if($export_type != "pdf" && $export_type != "excel") {
                $final_data=array();
                $columns = array( 
                0 =>'id',
                1=> 'order_number',
                2=> 'created_at', 
                3=> 'order_type',
                4=> 'product_name',
                5=> 'product_variants',
                6 => 'quantity',
                7=> 'status_name',
                8=> 'product_price',
                9=> 'product_tax',
                10=> 'sub_total_amount', 
                11=> 'total_tax_amount', 
                12=> 'total_amount'
                );
                $limit = $request->length;
                $start = $request->start; 
                $order = ($columns[$request->order[0]['column']] == "id") ? 'store_order_details.order_id' : ($columns[$request->order[0]['column']] == "created_at") ? 'store_order_details.created_at' : ($columns[$request->order[0]['column']] == "total_tax_amount") ? 'store_order_details.tax_amount' : $columns[$request->order[0]['column']];
                $dir = $request->order[0]['dir'];
            }
            $where_cond = 'store_order_details.is_deleted = 0 AND store_order_details.store_id = "' . Auth::user()->store_id . '"';
            $startDate = trim($request->startDate);
            $endDate = trim($request->endDate);
            if ($startDate != $endDate) {
                $report_date = Carbon::parse($startDate)->format('d-M-Y') . " to " . Carbon::parse($endDate)->format('d-M-Y');
                if (!empty($startDate)) {
                    $where_cond .= ' AND DATE(store_order_details.created_at) >= "' . $startDate . '"';
                }
            
                if (!empty($endDate)) {
                    $where_cond .= ' AND DATE(store_order_details.created_at) <= "' . $endDate . '"';
                }
            } else {
                $report_date = Carbon::parse($startDate)->format('d-M-Y');
                $where_cond .= ' AND DATE(store_order_details.created_at) = "' . $startDate . '"';
            }
            if (!empty($request->search['value'])) {
                $search_value = $request->search['value'];
                $search = (strpos($search_value, "SAR") !== false) ? trim($search_value, "SAR") : $search_value;
                $search = ((strpos($search, ".00") !== false) || (strpos($search, ".0")) !== false) ? round($search) : $search;
                $where_cond .= " AND (store_order_details.order_number LIKE '%" . $search . "%' or DATE_FORMAT(store_order_details.created_at, '%d-%m-%Y %H:%i') LIKE '%" . $search . "%' or store_place_order_prefer.status_name LIKE '%" . $search . "%' or product_name LIKE '%" . $search . "%' or product_variants LIKE '%" . $search . "%'  or store_order_items.sub_total LIKE '%" . $search . "%' or quantity LIKE '%" . $search . "%' or store_order_status.status_name LIKE '%" . $search . "%' or store_order_items.tax_amount LIKE '%" . $search . "%' or sub_total_amount LIKE '%" . $search . "%' or store_order_details.tax_amount LIKE '%" . $search . "%' or total_amount LIKE '%" . $search . "%')";
            }
            $orderDetailsQuery = Order::leftJoin('store_order_items', 'store_order_details.order_id', '=', 'store_order_items.order_id')
                ->leftJoin('store_products', 'store_products.product_id', '=', 'store_order_items.product_id')
                ->leftJoin('store_order_status', 'store_order_status.status_id', '=', 'store_order_details.store_order_status')
                ->leftJoin('store_place_order_prefer', 'store_order_details.order_type_id', '=', 'store_place_order_prefer.prefer_order_id')
                ->select(
                    'store_order_details.order_number',
                    DB::raw("DATE_FORMAT(store_order_details.created_at, '%d-%m-%Y %H:%i') as order_created_at"),
                    'product_name',
                    'product_variants',
                    'store_order_details.tax_amount as total_tax_amount',
                    'store_order_items.sub_total as product_price',
                    'quantity',
                    'store_order_items.tax_amount as product_tax',
                    'sub_total_amount',
                    'total_amount',
                    'store_order_status.status_name',
                    'store_place_order_prefer.status_name as order_type'
                )
                ->whereRaw($where_cond);
            if ($export_type != "pdf" && $export_type != "excel") {
                $orderDetailsQuery->orderBy($order, $dir)->limit($limit)->offset($start);
            }
            $order_details = $orderDetailsQuery->get();
            $filtered_order_details = Order::query()
                ->leftJoinSub(
                    function ($subquery) {
                        $subquery->select('order_id', DB::raw('SUM(total_amount) AS order_total_amount'))
                            ->from('store_order_details')
                            ->groupBy('order_id');
                    },
                    'order_totals',
                    'store_order_details.order_id',
                    '=',
                    'order_totals.order_id'
                )
                ->leftJoin('store_order_items', 'store_order_details.order_id', '=', 'store_order_items.order_id')
                ->leftJoin('store_products', 'store_products.product_id', '=', 'store_order_items.product_id')
                ->leftJoin('store_order_status', 'store_order_status.status_id', '=', 'store_order_details.store_order_status')
                ->selectRaw('store_order_details.order_id, COUNT(store_order_details.order_id) as count_order, MAX(order_totals.order_total_amount) as sum_total_amount')
                ->whereRaw($where_cond)
                ->groupBy('store_order_details.order_id')
                ->get()->toArray();
            $total_order_amount = 0; $total_order = 0;
            if(!empty($filtered_order_details)) {
                foreach ($filtered_order_details as $order) {
                    $total_order_amount += $order['sum_total_amount'];
                    $total_order += $order['count_order'];
                }
            }
            $totalCount = Order::query()
                ->leftJoin('store_order_items', 'store_order_details.order_id', '=', 'store_order_items.order_id')
                ->leftJoin('store_products', 'store_products.product_id', '=', 'store_order_items.product_id')
                ->where('store_order_details.is_deleted', 0)
                ->where('store_order_details.store_id', Auth::user()->store_id)
                ->count('store_order_details.order_id');

            $final_data = [];
            if($export_type != "pdf" && $export_type != "excel") {
                if (!empty($order_details)) {
                    $i = 0;
                    $j = 0;
                    foreach ($order_details as $order) {
                        $final_data[$i] = array(
                            'id' => ++$j,
                            'order_number' => $order->order_number,
                            'created_at' => $order->order_created_at,
                            'order_type' => $order->order_type,
                            'product_name' => $order->product_name,
                            'product_variants' => $order->product_variants,
                            'quantity' => $order->quantity,
                            'status_name' => $order->status_name,
                            'product_price' => $order->product_price,                        
                            'product_tax' => $order->product_tax,
                            'sub_total_amount' => $order->sub_total_amount,
                            'total_tax_amount' => $order->total_tax_amount,
                            'total_amount' => $order->total_amount,
                        );
                        $i++;
                    }
                }
                $json_data = array(
                    "draw" => intval($request->draw),  
                    "recordsTotal" => !empty($totalCount) ? $totalCount : 0,  
                    "recordsFiltered" => intval($total_order), 
                    "data" => $final_data,
                    "total_sum_amount" => $total_order_amount,  
                );
                echo json_encode($json_data); 
            }  else {
                if($export_type == "pdf") {
                    $data = [
                        'transaction_report_data' => $order_details,
                        'report_date' => $report_date,
                        'total_sum_amount' => $total_order_amount,  
                    ];
                    $pdf = PDF::loadView('cashier_admin.reports.transaction_report_pdf', $data);
                    $pdf->setPaper('A4', 'portrait');
                    return $pdf->download('transaction-report.pdf');     
                } else if ($export_type == "excel") {
                    $csvData = '';
                    $export_columns[] = ['#', 'Order ID', 'Ordered At', 'Order Type', 'Product Name', 'Variants', 'Quantity', 'Price', 'Tax', 'Sub Total', 'Total Tax', 'Total'];
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
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle('Transaction Report');
                    $title = 'Transaction Report';
                    $sheet->setCellValue('A1', $title);
                    $sheet->mergeCells('A1:L1');
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                    $sheet->setCellValue('A2', 'Report Date: ' . $report_date);
                    $sheet->mergeCells('A2:L2');
                    $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
                    if (!empty($export_columns)) {
                        foreach ($export_columns as $columns) {
                            $csvData .= implode(',', $columns) . "\n";
                        }
                    }
                    if (!empty($order_details)) {
                        $i = 0;
                        $previousValue = null; // Initialize previous value variable
                        // $rowIndex = 3; // Initialize rowIndex variable
                        $startRowIndex = 3; // Initialize startRowIndex variable
                        $endRowIndex = 3; 
                        foreach ($order_details as $order) {
                            $row = [
                                ++$i,
                                $order['order_number'],
                                $order['order_created_at'],
                                $order['order_type'],
                                $order['product_name'],
                                !empty($order['product_variants']) ? $order['product_variants'] : "-",
                                $order['product_price'],
                                $order['quantity'],
                                $order['product_tax'],
                                $order['sub_total_amount'],
                                $order['total_tax_amount'],
                                $order['total_amount']
                            ];
                            $currentValue = $order['order_number']; // Get the value from the second column
                            if ($previousValue !== null && $currentValue == $previousValue) {
                                $endRowIndex++; // Increment the end row index to include the current row
                            } else {
                                $columnsToMerge = [
                                    'B' => 'B',
                                    'C' => 'C',
                                    'D' => 'D',
                                    'J' => 'J',
                                    'K' => 'K',
                                    'L' => 'L',
                                ];
                                foreach ($columnsToMerge as $mergeStartColumn => $mergeEndColumn) {
                                    $mergeRange = $mergeStartColumn . $startRowIndex . ':' . $mergeEndColumn . $endRowIndex;
                                    $sheet->mergeCells($mergeRange);
                                    $alignment = $sheet->getStyle($mergeRange)->getAlignment();
                                    $alignment->setHorizontal('center');
                                    $alignment->setVertical('center');
                                }
                                $startRowIndex = $endRowIndex + 1; 
                                $endRowIndex = $startRowIndex; 
                            }
                            $csvData .= implode(',', $row) . "\n";
                            $previousValue = $currentValue;
                        }
                    }
                    $rows = explode("\n", $csvData);
                    $rowIndex = 3;
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
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(10);
                    $sheet->getColumnDimension('E')->setWidth(30);
                    $sheet->getColumnDimension('F')->setWidth(30);
                    $sheet->getColumnDimension('G')->setWidth(10);
                    $sheet->getColumnDimension('H')->setWidth(10);
                    $sheet->getColumnDimension('I')->setWidth(10);
                    $sheet->getColumnDimension('J')->setWidth(10);
                    $sheet->getColumnDimension('K')->setWidth(10);
                    $sheet->getColumnDimension('L')->setWidth(10);
                    $sheet->getStyle('1:1')->getFont()->setBold(true);
                    $sheet->getStyle('A2:L2')->getFont()->setBold(true);
                    $sheet->getStyle('A3:L3')->getFont()->setBold(true);
                    $sheet->getStyle('A3:L3')->getAlignment()->setHorizontal('center');
                    $filename = 'transaction-report.xlsx';
                    $filePath = base_path($filename);
                    $writer = new Xlsx($spreadsheet);
                    $writer->save($filePath);
                    $response = [
                        'success' => true,
                        'message' => trans('store-admin.excel_generated_msg'), 
                        'file_url' => asset($filename)
                    ];
                    echo json_encode($response);
                }
            }
        } else {
            return view('cashier_admin.reports.transaction_report',compact('store_url','store_logo'));
        }
    }

    public function customerReport(Request $request) {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $prefix_url = config('app.module_prefix_url');
        if($request->startDate != "") {
            $export_type = $request->export_type;
            if($export_type != "pdf" && $export_type != "excel") {
                $final_data=array();
                $columns = array( 
                    0 =>'customer_id',
                    1=> 'customer_name',
                    2=> 'phone_number', 
                    3=> 'email',
                    4=> 'created_at',
                );
                $limit = $request->length;
                $start = $request->start; 
                $order = $columns[$request->order[0]['column']];
                $dir = $request->order[0]['dir'];
            }
            
            $query = InstoreCustomer::select('customer_name', 'customer_id', 'phone_number', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as customer_created_at"),'email')
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->where('store_id', Auth::user()->store_id);

            if ($request->startDate != $request->endDate) {
                $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
            } else {
                $query->whereDate('created_at', 'LIKE', '%' . $request->startDate . '%');
            }
            if (!empty($request->search['value'])) {
                $search = trim($request->search['value']);
                $query->where(function ($query) use ($search) {
                    $query->where('customer_name', 'LIKE', '%' . $search . '%')
                        ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE '%" . $search . "%'")
                        ->orWhere('phone_number', 'LIKE', '%' . $search . '%');
                });
            }
            if($export_type != "pdf" && $export_type != "excel") {
                $query->orderBy($order, $dir)->offset($start)->limit($limit);
            }
            $customer_details = $query->get();
            $filtered_customer_details = $query->count();
            $totalCount = InstoreCustomer::where('is_deleted', 0)
                ->where('status', 1)
                ->where('store_id', Auth::user()->store_id)
                ->count();
            if($export_type != "pdf" && $export_type != "excel") {
                if(!empty($customer_details)) {
                    $i=0;$j=0;
                    foreach($customer_details as $customer) {
                        $final_data[$i]=array(
                            'id'=>++$j,
                            'customer_name'=> $customer->customer_name,
                            'phone_number'=> $customer->phone_number,
                            'email'=> $customer->email,
                            'created_at'=> $customer->customer_created_at,
                        );
                        $i++;
                    }
                }
                $totalFiltered = !empty($filtered_customer_details) ? $filtered_customer_details : 0;
                $json_data = array(
                    "draw"            => intval($request->draw),  
                    "recordsTotal"    => !empty($totalCount) ? $totalCount : 0,  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $final_data, 
                );
                echo json_encode($json_data); 
            }  else {
                if($export_type == "pdf") {
                    $columns = ['Customer Name','Phone Number','Created At'];
                    $column_field_name = ['customer_name','phone_number','customer_created_at'];  
                    $data = [
                        'export_columns' => $columns,
                        'export_data' => $customer_details,
                        'column_field_name' => $column_field_name,
                        'title' => 'Customer Details',
                        'type' => 'single_header'
                    ];
                    $pdf = PDF::loadView('pdf.template', $data);
                    $pdf->setPaper('A4', 'portrait');
                    return $pdf->download('customer-details.pdf');
                } else if ($export_type == "excel") {
                    $csvData = '';
                    $export_columns[] = ['#', 'Customer Name','Phone Number','Created At'];
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
                    $sheet->setTitle('Customer Details');
                    $title = 'Customer Details';
                    $sheet->setCellValue('A1', $title);
                    $sheet->mergeCells('A1:D1');
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                    if (!empty($export_columns)) {
                        foreach ($export_columns as $columns) {
                            $csvData .= implode(',', $columns) . "\n";
                        }
                    }
                    if (!empty($customer_details)) {
                        $i = 0;
                        foreach ($customer_details as $customer) {
                            $row = [
                                ++$i,
                                $customer['customer_name'],
                                $customer['phone_number'],
                                $customer['customer_created_at']
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
                    $filename = 'customer-details.xlsx';
                    $filePath = base_path($filename);
                    $writer = new Xlsx($spreadsheet);
                    $writer->save($filePath);
                    $response = [
                        'success' => true,
                        'message' => trans('store-admin.excel_generated_msg'), 
                        'file_url' => asset($filename)
                    ];
                    echo json_encode($response);
                } 
            }
        } else {
            return view('cashier_admin.reports.customer_report',compact('store_url','store_logo'));
        }
    }
}
