<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\Category;
use App\Models\StoreAdmin\SubCategory;
use App\Models\StoreAdmin\Product;
use App\Models\StoreAdmin\ProductDiscount;
use App\Models\StoreAdmin\ProductFlashDeals;
use App\Models\StoreAdmin\ProductStock;
use App\Models\StoreAdmin\ProductTax;
use App\Models\StoreAdmin\Price;
use App\Models\StoreAdmin\Variants;
use App\Models\StoreAdmin\VariantsOption;
use App\Models\StoreAdmin\VariantsOptionCombination;
use App\Models\StoreAdmin\VariantProductImage;
use App\Models\StoreAdmin\ProductImage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAdmin\ProductRequest;
use Illuminate\Support\Facades\DB;
use URL;
use Exception;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\FlashDeal;
use Carbon\Carbon;
use App\Http\Controllers\CommonController;
use Image;
use PDF;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Session;
use App\Models\StoreAdmin\Tax;


class ProductController extends Controller
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
        if ($request->type != "") {
            $export_type = $request->export_type;
            if($export_type != "pdf" && $export_type != "excel") {
                $final_data = array();
                $columns = array(
                    0 => 'checkbox',
                    1 => 'product_id',
                    2 => 'category_image',
                    3 => 'product_name',
                    4 => '',
                    // 4 => 'order_number',
                    5 => 'type_of_product',
                    6 => 'category_name',
                    7 => 'sub_category_name',
                    8 => 'price',
                    9 => 'status',
                    10 => 'created_at',
                    11 => 'action',
                    12 => 'updated_at',
                );
                $limit = $request->length;
                $start = $request->start;
                $order = ($columns[$request->order[0]['column']] == "checkbox") ? 'store_products.product_id' : 
                ($columns[$request->order[0]['column']] == "created_at") ? 'store_products.created_at' : $columns[$request->order[0]['column']];
                /*if ($order == 'price') {
                    $order = DB::raw("CASE
                        WHEN type_of_product = 'variant' THEN (
                            SELECT CASE
                                WHEN taxable = 0 AND tax_type = 'incl_of_tax' THEN variant_price + (variant_price * tax_amount / 100)
                                ELSE variant_price
                            END
                            FROM store_product_variants_combination
                            WHERE store_product_variants_combination.product_id = store_products.product_id
                                AND store_product_variants_combination.is_deleted = 0
                            ORDER BY store_product_variants_combination.variants_combination_id ASC
                            LIMIT 1
                        )
                        ELSE CASE
                            WHEN taxable = 0 AND tax_type = 'incl_of_tax' THEN price + (price * tax_amount / 100)
                            ELSE price
                        END
                    END");
                }*/
                if ($order == 'price') {
                    $order = DB::raw("CASE
                        WHEN type_of_product = 'variant' THEN (
                            SELECT variant_price
                            FROM store_product_variants_combination
                            WHERE store_product_variants_combination.product_id = store_products.product_id
                                AND store_product_variants_combination.is_deleted = 0
                            ORDER BY store_product_variants_combination.variants_combination_id ASC
                            LIMIT 1
                        )
                        ELSE price
                    END");
                }
                $dir = $request->order[0]['dir'];
            }
            $product_details_query = Product::select(
                'store_products.product_id',
                'store_products.category_image',
                'product_name',
                'category_name',
                'sub_category_name',
                'store_products.status',
                'status_type',
                'type_of_product','store_products.order_number',
                DB::raw("DATE_FORMAT(store_products.created_at, '%d-%m-%Y %H:%i') as product_created_at"),
                DB::raw("DaTE_FORMAT(store_products.updated_at, '%d-%m-%Y %H:%i') as product_updated_at"),
                /*DB::raw("CASE
                    WHEN type_of_product = 'variant' THEN (
                        SELECT CASE
                            WHEN taxable = 0 AND tax_type = 'incl_of_tax' THEN variant_price + (variant_price * tax_amount / 100)
                            ELSE variant_price
                        END
                        FROM store_product_variants_combination
                        WHERE store_product_variants_combination.product_id = store_products.product_id
                            AND store_product_variants_combination.is_deleted = 0
                        ORDER BY store_product_variants_combination.variants_combination_id ASC
                        LIMIT 1
                    )
                    ELSE CASE
                        WHEN taxable = 0 AND tax_type = 'incl_of_tax' THEN price + (price * tax_amount / 100)
                        ELSE price
                    END
                END AS price"),*/
                DB::raw("CASE
                    WHEN type_of_product = 'variant' THEN (
                        SELECT variant_price
                        FROM store_product_variants_combination
                        WHERE store_product_variants_combination.product_id = store_products.product_id
                            AND store_product_variants_combination.is_deleted = 0
                        ORDER BY store_product_variants_combination.variants_combination_id ASC
                        LIMIT 1
                    )
                    ELSE price
                END AS price"),
                DB::raw("CASE
                    WHEN type_of_product = 'variant' THEN (
                        SELECT image_path
                        FROM product_images
                        INNER JOIN variant_product_images ON variant_product_images.product_image_id = product_images.image_id
                        INNER JOIN store_product_variants_combination ON variant_product_images.variant_combination_id = store_product_variants_combination.variants_combination_id
                        WHERE product_images.product_id = variant_product_images.product_id
                            AND product_images.is_deleted = 0 AND variant_product_images.is_deleted = 0
                        ORDER BY store_product_variants_combination.variants_combination_id ASC
                        LIMIT 1
                    )
                    ELSE (
                        SELECT image_path
                        FROM product_images
                        WHERE store_products.product_id = product_images.product_id
                            AND product_images.is_deleted = 0
                        ORDER BY product_images.image_id ASC
                        LIMIT 1
                    )
                END AS product_image")
            )->join('store_category', 'store_products.category_id', '=', 'store_category.category_id')
                ->leftJoin('store_sub_category', 'store_products.sub_category_id', '=', 'store_sub_category.sub_category_id')
                ->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')
                ->leftJoin('store_product_tax', 'store_products.product_id', '=', 'store_product_tax.product_id')
                ->where('store_products.store_id', Auth::user()->store_id)
                ->where('store_products.is_deleted', 0)
                ->where('store_category.status', 1)
                ->where('store_category.is_deleted', 0);
            $product_details_query->where(function ($query) {
                $query->whereRaw('CASE WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END');
            });
            if(!empty($request->search['value'])) {
                $search_value = $request->search['value'];
                $product_details_query->where(function ($query) use ($search_value) {
                    if(strpos($search_value, "SAR ") !== false) {
                        $search = (strpos($search_value, "SAR ") !== false) ? trim($search_value, "SAR") : $search_value;
                        $search = ((strpos($search, ".00") !== false) || (strpos($search, ".0")) !== false) ? round(trim($search)) : trim($search);
                        $query->whereRaw("CASE
                            WHEN type_of_product = 'variant' THEN (
                                SELECT variant_price
                                FROM store_product_variants_combination
                                WHERE store_product_variants_combination.product_id = store_products.product_id
                                    AND store_product_variants_combination.is_deleted = 0
                                ORDER BY store_product_variants_combination.variants_combination_id ASC
                                LIMIT 1
                            )
                            ELSE price
                        END LIKE '%" . $search . "%'");
                    } else {
                        $search = trim($search_value);
                        $query->where('product_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('status_type', 'LIKE', '%' . $search . '%')
                        ->orWhere('type_of_product', 'LIKE', '%' . $search . '%')
                        ->orWhere('category_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('sub_category_name', 'LIKE', '%' . $search . '%')
                        ->orWhereRaw("CASE
                            WHEN type_of_product = 'variant' THEN (
                                SELECT variant_price
                                FROM store_product_variants_combination
                                WHERE store_product_variants_combination.product_id = store_products.product_id
                                    AND store_product_variants_combination.is_deleted = 0
                                ORDER BY store_product_variants_combination.variants_combination_id ASC
                                LIMIT 1
                            )
                            ELSE price
                        END LIKE '%" . $search . "%'")
                        ->orWhereRaw("DATE_FORMAT(store_products.created_at, '%d-%m-%Y %H:%i') LIKE '%" . $search . "%'")
                        ->orWhereRaw("DATE_FORMAT(store_products.updated_at, '%d-%m-%Y %H:%i') LIKE '%" . $search . "%'");
                    }
                });
            }
            if(!empty($request->filterByCategory))
                $product_details_query->where('store_products.category_id', $request->filterByCategory);
            if(!empty($request->filterBySubCategory))
                $product_details_query->where('store_products.sub_category_id', $request->filterBySubCategory);
            if(!empty($request->filterByProductType))
                $product_details_query->where('store_products.type_of_product', $request->filterByProductType);
            if(!empty($request->filterByStatus))
                $product_details_query->where('store_products.status_type', $request->filterByStatus);
            $totalCount = $product_details_query->count();
            if($export_type != "pdf" && $export_type != "excel") {
                $product_details_query->orderBy($order, $dir);
                $product_details = $product_details_query->skip($start)
                    ->take($limit)
                    ->get();
            }
            $filtered_product_details = $product_details_query->get();
            if($export_type != "pdf" && $export_type != "excel") {
                if (!empty($product_details)) {
                    $i = 0;
                    $j = 0;
                    foreach ($product_details as $product) {
                        $status_checked = $product->status == 1 ? 'checked' : '';
                        $final_data[$i] = array(
                            'checkbox' => '<div class="form-check"><input type="checkbox" name="product_checkbox" class="form-check-input product-checkbox" value="' . $product->product_id . '"></div>',
                            'product_id' => ++$j,
                            'category_image' => '<img src="' . $product->product_image . '" class="img-sm img-thumbnail" alt="Item">',
                            'product_name' => "<a href='" . route(config('app.prefix_url') . '.' . $this->store_url . '.' . $prefix_url . '.product.create', Crypt::encrypt($product->product_id)) . "'>".$product->product_name."</a>",
                            // 'order_number'=> '<input type="text" class="category-order-number form-control" style="width: 6em;" data-order-number="'.$product->order_number.'" value="'.$product->order_number.'">', 
                            'type_of_product' => $product->type_of_product,
                            'category_name' => $product->category_name,
                            'sub_category_name' => $product->sub_category_name,
                            'price' => "SAR " . number_format((float)($product->price), 2, '.', ''),
                            'status' => '<span class="badge '.($product->status_type == 'publish' ? 'badge-success' : 'badge-secondary').'">'.ucfirst($product->status_type).'</span>',
                            'created_at' => $product->product_created_at,
                            'updated_at' => $product->product_updated_at,
                            'action' => "
                                <a class='btn btn-circle btn-warning btn-xs' href='" . route(config('app.prefix_url') . '.' . $this->store_url . '.' . $prefix_url . '.product.show', Crypt::encrypt($product->product_id)) . "'><i class='fa fa-eye'></i></a>
                                <a class='btn btn-circle btn-danger btn-xs' href='" . route(config('app.prefix_url') . '.' . $this->store_url . '.' . $prefix_url . '.product.create', Crypt::encrypt($product->product_id)) . "'><i class='fa fa-edit'></i></a>
                                <a class='btn btn-circle btn-primary btn-xs product-delete' href='" . route(config('app.prefix_url') . '.' . $this->store_url . '.' . $prefix_url . '.product.destroy', Crypt::encrypt($product->product_id)) . "'><i class='fa fa-trash'></i></a>
                                <input type='hidden' class='encrypted_product_id' value='" . Crypt::encrypt($product->product_id) . "'><input type='hidden' class='product_id' value='" . $product->product_id . "'>"
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
                $product_details = $filtered_product_details->toArray();
                if(!empty($filtered_product_details)) {
                    if(!empty($product_details)) {
                        $product_ids = array_map(function ($product_details) {
                            return $product_details['product_id'];
                        }, $product_details);
                        if(!empty($product_ids)) {
                            $variant_option_details = VariantsOptionCombination::select(DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as variant_created_at"),'variants_combination_name','variant_price','on_hand','available','sku','barcode','product_id')
                            ->where([
                                ['store_id', '=', Auth::user()->store_id],
                                ['is_deleted', '=', 0],
                            ])->whereIn('product_id',$product_ids)->get()->toArray();
                            if(!empty($variant_option_details)) {
                                $product_variant_details = [];
                                foreach($variant_option_details as $variants) {
                                    $product_variant_details[$variants['product_id']][] = $variants;
                                }
                            }
                        }
                    }
                }
                $product_variant_details = (isset($product_variant_details) && !empty($product_variant_details)) ? $product_variant_details : [];
                $columns = ['Product Name','Product Type','Category','Sub Category','Price','Status Type','Created At'];
                $column_field_name = ['product_name','type_of_product','category_name','sub_category_name','price','status_type','product_created_at'];
                $nested_columns = ['Variant','Price','Onhand','SKU','Created At'];
                $nested_columns_field_name = ['variants_combination_name','variant_price','on_hand','sku','variant_created_at'];
                if($export_type == "pdf") {
                    $data = [
                        'export_columns' => $columns,
                        'export_data' => $product_details,
                        'nested_export_data' => $product_variant_details,
                        'column_field_name' => $column_field_name,
                        'nested_columns' => $nested_columns,
                        'nested_columns_field_name' => $nested_columns_field_name,
                        'title' => 'Product Details',
                        'type' => 'multi_header',
                    ];
                    $pdf = PDF::loadView('pdf.template', $data);
                    $pdf->setPaper('A4', 'portrait');
                    return $pdf->download('product-details.pdf');
                } else if ($export_type == "excel") {
                    $csvData = '';
                    $export_columns[] = ['#', 'Product Name', 'Product Type', 'Category', 'Sub Category', 'Price', 'Status Type', 'Created At'];
                    $export_nested_columns[] = ['#', 'Variant', 'Price', 'Onhand', 'SKU', 'Created At'];
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
                    $sheet->setTitle('Product Details');
                    $title = 'Product Details';
                    $product_variant_title = "Product Variant Details";
                    $sheet->setCellValue('A1', $title);
                    $sheet->mergeCells('A1:H1');
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
                    if (!empty($product_details)) {
                        $i = 0;
                        foreach ($product_details as $product) {
                            if (!empty($export_columns)) {
                                foreach ($export_columns as $columns) {
                                    $csvData .= implode(',', $columns) . "\n";
                                }
                            }
                            $row = [
                                ++$i,
                                $product['product_name'],
                                $product['type_of_product'],
                                $product['category_name'],
                                $product['sub_category_name'],
                                $product['price'],
                                $product['status_type'],
                                $product['product_created_at']
                            ];
                            $csvData .= implode(',', $row) . "\n";
                            if (isset($product_variant_details) && !empty($product_variant_details) && $product['type_of_product'] == "variant" && array_key_exists($product['product_id'], $product_variant_details) && !empty($product_variant_details[$product['product_id']])) {
                                $csvData .= $product_variant_title."\n";
                
                                if (!empty($export_nested_columns)) {
                                    foreach ($export_nested_columns as $nested_columns) {
                                        $csvData .= implode(',', $nested_columns) . "\n";
                                    }
                                    $j = 0;
                                    foreach ($product_variant_details[$product['product_id']] as $value) {
                                        $row = [
                                            ++$j,
                                            $value['variants_combination_name'],
                                            $value['variant_price'],
                                            $value['on_hand'],
                                            $value['sku'],
                                            $value['variant_created_at']
                                        ];
                                        $csvData .= implode(',', $row) . "\n";
                                    }
                                }
                            }
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
                    
                            if (($columns == $export_columns[0]) || ($columns == $export_nested_columns[0]) || ($column == $product_variant_title)) {
                                $cell->getStyle()->getFont()->setBold(true);
                                $cell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            }
                            if($column == $product_variant_title) {
                                $mergeRange = 'A' . $rowIndex . ':F' . $rowIndex;
                                $sheet->mergeCells($mergeRange);
                                $sheet->getStyle($mergeRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            }
                            $columnIndex++;
                        }
                        $rowIndex++;
                    }
                    $sheet->getColumnDimension('A')->setWidth(5);
                    $sheet->getColumnDimension('B')->setWidth(50);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(15);
                    $sheet->getColumnDimension('E')->setWidth(15);
                    $sheet->getColumnDimension('F')->setWidth(20);
                    $sheet->getColumnDimension('G')->setWidth(15);
                    $sheet->getColumnDimension('H')->setWidth(20);
                    $sheet->getStyle('1:1')->getFont()->setBold(true); 
                    $filename = 'product-details.xlsx';
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
        } else {
            $category_details = Category::where([
                ['store_id', '=', Auth::user()->store_id],
                ['is_deleted', '=', 0],
                ['status', '=', 1]
            ])->get(['category_name', 'category_id']);
            $user_role_id = Auth::user()->is_admin;
            return view('store_admin.products.list', compact('store_url', 'category_details', 'user_role_id', 'store_logo'));
        }
    }

    public function create($id=null)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $mode = !empty($id) ? 'edit' : 'add';
        $category_details = Category::where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
            ['status', '=', 1],
        ])->get(['category_name','category_id']);
        $product_details = $variants = $variants_options_array = $variant_combination_name = $variant_combinations = $product_image = $variantImages = [];
        if(!empty($id)) {
            $product_details = Product::leftJoin('store_product_tax', 'store_products.product_id', '=', 'store_product_tax.product_id')->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')->where('store_products.product_id',Crypt::decrypt($id))->get(['product_name','category_id','sub_category_id','unit','tags','product_description','store_products.category_image','sku','meta_title','meta_description','tax_amount','tax_type','store_products.product_id','product_tax_id','price','compare_price','cost_per_item','profit','margin','taxable','trackable','sell_out_of_stock','is_sku_barcode','status_type','price_id','type_of_product','barcode','product_type','product_url']);
            $product_image = ProductImage::where([
                ['store_id', '=', Auth::user()->store_id],
                ['product_id', '=', Crypt::decrypt($id)],
                ['is_deleted', '=', 0],
            ])->get(['image_id','image_path'])->toArray();
            $product_variant_image = VariantProductImage::leftJoin('product_images', 'variant_product_images.product_image_id', '=', 'product_images.image_id')->where([
                ['variant_product_images.store_id', '=', Auth::user()->store_id],
                ['variant_product_images.product_id', '=', Crypt::decrypt($id)],
                ['variant_product_images.is_deleted', '=', 0],
                ['product_images.is_deleted', '=', 0],
            ])->get(['variant_img_id','variant_ids','variant_combination_id','product_image_id','image_path'])->toArray();
            if(!empty($product_variant_image)) {
                foreach($product_variant_image as $image) {
                    $variantImages[$image['variant_combination_id']][] = $image;
                }
            }
            $variants = Variants::where([
                ['store_id', '=', Auth::user()->store_id],
                ['product_id', '=', Crypt::decrypt($id)],
                ['is_deleted', '=', 0],
            ])->get(['variants_id','variants_name']);
            $variants_options = VariantsOption::where([
                ['store_id', '=', Auth::user()->store_id],
                ['product_id', '=', Crypt::decrypt($id)],
                ['is_deleted', '=', 0],
            ])->get(['variants_id','variant_options_name','variant_options_id','variants_option_image'])->toArray();
            $variant_combination_name = VariantsOptionCombination::where([
                ['store_id', '=', Auth::user()->store_id],
                ['product_id', '=', Crypt::decrypt($id)],
                ['is_deleted', '=', 0],
            ])->get(['variants_combination_name','on_hand','variant_price','available','sku','barcode','quantity','variants_combination_id'])->toArray();
            if(!empty($variants_options)) {
                foreach($variants_options as $variant) {
                    $variants_options_array[$variant['variants_id']][] = $variant;
                }
            }
            if(!empty($variant_combination_name)) {
                foreach($variant_combination_name as $combinations) {
                    $variant_combinations[$combinations['variants_combination_name']] = $combinations;
                }
            }
        }
        $user_role_id = Auth::user()->is_admin;
        $tax_details = Tax::where('store_id',Auth::user()->store_id)->get(['tax_percentage','tax_id']);
        return view('store_admin.products.create',compact('store_url','store_logo','mode','category_details','product_details','variants','user_role_id','variants_options_array','variant_combinations','tax_details','product_image','variantImages'));
    }

    public function store(ProductRequest $request)
    {
        try { 
            $input = $request->all();
            $product_data = $request->products;
            $variants_details = (array) json_decode(json_decode($input['variants_details']));
            $variants_combination_details = (array) json_decode(json_decode($input['variants_combination_details']));
            $variants_option_details = (array) json_decode(json_decode($input['variants_option_details']));
            $variantsOptionsDetails = $input['option_fields_value'];
            $variantOptionImages = $request->file('variant_option_image');
            $variantsOptionsFieldsID =  $input['option_fields_id'];
            $variantOptionNames = $input['option_names'];
            if(!empty($product_data)) {
                $product_data['sell_out_of_stock'] = !empty($product_data['sell_out_of_stock']) ? 1 : 0;
                $product_data['trackable'] = !empty($product_data['trackable']) ? 1 : 0;
                $product_data['is_sku_barcode'] = !empty($product_data['is_sku_barcode']) ? 1 : 0;
            }
            $saved_variants_details = []; 
            $price_data = $request->price_details;
            $tax_data = $request->tax;
            if(isset($product_data['taxable'])) {
                $tax_data['tax_type'] = ($product_data['taxable'] == 0) ? "incl_of_tax" : $tax_data['tax_type'];
                $tax_data['tax_amount'] = ($product_data['taxable'] == 0) ? $request->tax_percentage : $request->tax_amount;
            }
            $mode = $input['mode'];
            $store_id = Auth::user()->store_id;
            $product_id = ($mode == "edit" && !empty($input['product_id'])) ? Crypt::decrypt($input['product_id']) : 0;
            $product_tax_id = ($mode == "edit" && !empty($input['product_tax_id'])) ? Crypt::decrypt($input['product_tax_id']) : 0;
            $price_id = ($mode == "edit" && !empty($input['price_id'])) ? Crypt::decrypt($input['price_id']) : 0;
            $url = URL::to("/");
            if($mode == "edit") {
                $delete_data = array();
                $delete_data['is_deleted'] = 1;
                VariantProductImage::where('product_id',$product_id)->update($delete_data);
            }                       
            //Start DB Transaction
            DB::beginTransaction();
            if($mode == "add") {
                $product_data['created_by'] = $price_data['created_by'] = Auth::user()->id;
                $product_data['store_id'] = $price_data['store_id'] = Auth::user()->store_id;
                $product_id = Product::create($product_data)->product_id;
                $update_data = array();
                $data_order = Product::where('store_id',Auth::user()->store_id)->orderBy('order_number', 'desc')->limit(1)->first();
                $ordering = $data_order->order_number + 1 ; 
                $update_data['order_number'] = $ordering;
                $update_data['product_number'] = "PRO".sprintf("%03d",$product_id);
                Product::where('product_id',$product_id)->update($update_data);
                $price_data['product_id'] = $product_id;
            } else {
                $product_data['updated_by'] = Auth::user()->id;
                Product::where('product_id',$product_id)->update($product_data);
            }
            if(!empty($price_id))
                Price::where('price_id',$price_id)->update($price_data);
            else
                Price::create($price_data);

            if(!empty($product_tax_id))
                ProductTax::where('product_tax_id',$product_tax_id)->update($tax_data);
            else {
                $tax_data['created_by'] = Auth::user()->id;
                $tax_data['store_id'] = Auth::user()->store_id;
                $tax_data['product_id'] = $product_id;
                ProductTax::create($tax_data);
            }
            $imageIDByFileName = [];
            if(!empty($input['remove_image_ids']) && $mode == "edit") {
                $imageIdsArray = str_replace(['[', ']', ' '], '', $input['remove_image_ids']);
                $removeImageIds = !empty($imageIdsArray) ? explode(",",$imageIdsArray) : [];
                if(!empty($removeImageIds)) {
                    ProductImage::where([
                        ['product_id', '=', $product_id],
                        ['store_id','=',Auth::user()->store_id]
                    ])->whereIn('image_id',$removeImageIds)->update($delete_data);
                    VariantProductImage::where([
                        ['product_id', '=', $product_id],
                        ['store_id','=',Auth::user()->store_id]
                    ])->whereIn('product_image_id',$removeImageIds)->update($delete_data);
                }
            } 
            if ($request->category_image){
                foreach($request->category_image as $image) {
                    $fileName = $image->getClientOriginalName();
                    $category_image = CommonController::uploadImage($image, '/images/' . $store_id . '/meta', $url, $store_id);
                    $product_images = [];
                    $product_images['product_id'] = $product_id;
                    $product_images['image_path'] = $category_image;
                    $product_images['store_id'] = Auth::user()->store_id;
                    $product_images['created_by'] = Auth::user()->id;
                    $image_id = ProductImage::create($product_images)->image_id;
                    $imageIDByFileName[$fileName] = $image_id;
                }
            }
            if(!empty($variants_details)) {
                if(isset($product_data['type_of_product']) && $product_data['type_of_product'] == "variant" && $mode == "edit") {
                    Variants::where([
                        ['product_id', '=', $product_id],
                        ['store_id','=',Auth::user()->store_id]
                    ])->update($delete_data);
                }
                foreach($variants_details as $key=>$val) {
                    $variants = []; 
                    $variants["product_id"] = $product_id;
                    $variants["variants_name"] = $val->variants_name;
                    if(isset($val->variants_id)) {
                        $variants["is_deleted"] = 0;
                        $variants_id = $val->variants_id;
                        Variants::where([
                            ['product_id', '=', $product_id],
                            ['store_id','=',Auth::user()->store_id],
                            ['variants_id','=',$val->variants_id],
                        ])->update($variants);
                    } else {
                        $variants["store_id"] = Auth::user()->store_id;
                        $variants["created_by"] = Auth::user()->id;
                        $variants_id = Variants::create($variants)->variants_id;
                    }
                    $saved_variants_details[$val->variants_name] = $variants_id;
                } 
            }
            $variantOptionsData = [];
            if(!empty($variantsOptionsDetails)) { 
                if(isset($product_data['type_of_product']) && $product_data['type_of_product'] == "variant" && $mode == "edit") {
                    VariantsOption::where([
                        ['product_id', '=', $product_id],
                        ['store_id','=',Auth::user()->store_id]
                    ])->update($delete_data);
                }
                foreach($variantsOptionsDetails as $key=>$val) { 
                    if(!empty($val) || (!empty($variantOptionImages) && isset($variantOptionImages[$key]))) {
                        $variants_options = [];
                        $variants_options["variants_id"] = (!empty($variantOptionNames) && isset($variantOptionNames[$key])) ? $saved_variants_details[$variantOptionNames[$key]] : 0;
                        $variants_options["variant_options_name"] = $val; 
                        if(!empty($variantOptionImages) && isset($variantOptionImages[$key])) {
                            $uploadedFile = $variantOptionImages[$key];
                            $variants_options['variants_option_image'] = CommonController::uploadImage($uploadedFile, '/images/' . $store_id . '/variants-options-image', $url, $store_id);
                        }
                        $variants_options["product_id"] = $product_id;
                        $variants_options["store_id"] = Auth::user()->store_id;
                        if(!empty($variantsOptionsFieldsID) && isset($variantsOptionsFieldsID[$key])) { 
                            $variants_options["is_deleted"] = 0;
                            $variants_options["updated_by"] = Auth::user()->id;
                            $variants_options["variant_options_id"] = $variant_options_id = $variantsOptionsFieldsID[$key];
                            VariantsOption::where([
                                ['product_id', '=', $product_id],
                                ['store_id','=',Auth::user()->store_id],
                                ['variant_options_id','=',$variantsOptionsFieldsID[$key]],
                            ])->update($variants_options);
                        } else {
                            $variants_options["created_by"] = Auth::user()->id;
                            $variant_options_id = VariantsOption::create($variants_options)->variant_options_id;
                        }
                        $variantOptionsData[$val] = $variant_options_id;
                    }
                }
            } 
            if(!empty($variants_combination_details)) {
                if(isset($product_data['type_of_product']) && $product_data['type_of_product'] == "variant" && $mode == "edit") {
                    VariantsOptionCombination::where([
                        ['product_id', '=', $product_id],
                        ['store_id','=',Auth::user()->store_id]
                    ])->update($delete_data);
                }
                foreach($variants_combination_details as $key=>$val) {
                    $variants_combination = []; 
                    $variants_name = $val->variants_name;
                    $variants_combination_id = $val->variants_combination_id;
                    $variants_combination["variants_id"] = 0;
                    if(!empty($variants_name)) {
                        $variants = explode('/', $variants_name);
                        $variants = array_map('trim', $variants);
                        if(!empty($variantOptionsData)) {
                            $variantIds = array_map(function ($variant) use ($variantOptionsData) {
                                return $variantOptionsData[$variant];
                            }, $variants);
                            $variants_combination["variants_id"] = implode(' / ', $variantIds);
                        }
                    }
                    $variants_combination["variants_combination_name"] = $variants_name;
                    $variants_combination["variant_price"] = $val->price;
                    $variants_combination["on_hand"] = $val->onhand;
                    $variants_combination["sku"] = $val->sku;
                    $variants_combination["barcode"] = $val->barcode;
                    if(!empty($val->variants_combination_id) && $val->variants_combination_id != 'undefined') {
                        $variants_combination["is_deleted"] = 0;
                        VariantsOptionCombination::where([
                            ['product_id', '=', $product_id],
                            ['store_id','=',Auth::user()->store_id],
                            ['variants_combination_id','=',$val->variants_combination_id],
                        ])->update($variants_combination);
                        $variants_combination_id = $val->variants_combination_id;
                    } else {
                        $variants_combination["created_by"] = Auth::user()->id;
                        $variants_combination["store_id"] = Auth::user()->store_id;
                        $variants_combination["product_id"] = $product_id;
                        $variants_combination_id = VariantsOptionCombination::create($variants_combination)->variants_combination_id;
                    }
                    if(!empty($val->variant_img)) {
                        $variantImages = json_decode($val->variant_img);
                        if ($variantImages !== null) {
                            foreach ($variantImages as $image) {
                                $product_variant_image = [];
                                $product_variant_image['variant_ids'] = $variants_combination["variants_id"];
                                $product_variant_image['is_deleted'] = 0;
                                $product_variant_image['store_id'] = Auth::user()->store_id;
                                $product_variant_image['product_id'] = $product_id;
                                $product_variant_image['variant_combination_id'] = $variants_combination_id;
                                if(!empty($image->productImageID)) {
                                    $product_variant_image['product_image_id'] = $image->productImageID;
                                } else if(!empty($imageIDByFileName) && isset($imageIDByFileName[$image->fileName])) {
                                    $product_variant_image['product_image_id'] = $imageIDByFileName[$image->fileName];
                                }
                                if(!empty($image->variantImageID)) {
                                    VariantProductImage::where('variant_img_id',$image->variantImageID)->update($product_variant_image);
                                } else {
                                    $product_variant_image['created_by'] = Auth::user()->id;
                                    VariantProductImage::create($product_variant_image);
                                }
                            }
                        } 
                    }
                    
                }
            } 
            //Commit Transaction to Save Data to Database
            DB::commit();
            $success_message = ($mode == "add") ? trans('store-admin.added_msg',['name'=>trans('store-admin.product')]) : trans('store-admin.updated_msg',['name'=>trans('store-admin.product')]);
            $prefix_url = config('app.module_prefix_url');
            return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.product.index')->with('message',$success_message);
        } catch (Exception $e) {
            //Rollback Database Entry
            DB::rollback();
            throw $e;
        }
    }

    public function show($id)
    {
        $store_url = $this->store_url; 
        $store_logo = $this->store_logo;
        $variantsOptions = $variantsCombination = $variantImages = [];
        $product_details = Product::leftJoin('store_category', 'store_products.category_id', '=', 'store_category.category_id')->leftJoin('store_sub_category', 'store_products.sub_category_id', '=', 'store_sub_category.sub_category_id')->leftJoin('store_product_tax', 'store_products.product_id', '=', 'store_product_tax.product_id')->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')->where('store_products.product_id',Crypt::decrypt($id))->get(['product_name','store_products.category_id','store_products.sub_category_id','unit','tags','product_description','store_products.category_image','sku','store_products.meta_title','store_products.meta_description','tax_amount','tax_type','store_products.product_id','product_tax_id','category_name','sub_category_name','price','compare_price','cost_per_item','profit','margin','barcode','type_of_product','status_type','product_type','is_sku_barcode','trackable']);
        $product_image = ProductImage::where([
            ['store_id', '=', Auth::user()->store_id],
            ['product_id', '=', Crypt::decrypt($id)],
            ['is_deleted', '=', 0],
        ])->get(['image_id','image_path'])->toArray();
        $product_variant_image = VariantProductImage::leftJoin('product_images', 'variant_product_images.product_image_id', '=', 'product_images.image_id')->where([
            ['variant_product_images.store_id', '=', Auth::user()->store_id],
            ['variant_product_images.product_id', '=', Crypt::decrypt($id)],
            ['variant_product_images.is_deleted', '=', 0],
            ['product_images.is_deleted', '=', 0],
        ])->get(['variant_img_id','variant_ids','variant_combination_id','product_image_id','image_path'])->toArray();
        if(!empty($product_variant_image)) {
            foreach($product_variant_image as $image) {
                $variantImages[$image['variant_combination_id']][] = $image;
            }
        }
        $variants = Variants::where([
            ['store_id', '=', Auth::user()->store_id],
            ['product_id', '=', Crypt::decrypt($id)],
            ['is_deleted', '=', 0],
        ])->get(['variants_id','variants_name'])->toArray();
        $variants_options_data = VariantsOption::where([
            ['store_id', '=', Auth::user()->store_id],
            ['product_id', '=', Crypt::decrypt($id)],
            ['is_deleted', '=', 0],
        ])->get(['variants_id','variant_options_name','variant_options_id','variants_option_image'])->toArray();
        if(!empty($variants_options_data)) {
            $variantsOptions = array_reduce($variants_options_data, function ($acc, $item) {
                $variantsId = $item['variants_id'];
                $acc[$variantsId][] = $item;
                return $acc;
            }, []);
        }
        $variant_combination_details = VariantsOptionCombination::where([
            ['product_id', '=', Crypt::decrypt($id)], 
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
        ])->get(['variants_combination_name','variant_price','on_hand','available','sku','barcode','variants_id','variants_combination_id'])->toArray();
        if(!empty($variant_combination_details)) {
            $variantsCombination = array_reduce($variant_combination_details,function($acc,$item) {
                $variants_id = $item['variants_id'];
                if(!isset($acc[$variants_id])) {
                    $acc[$variants_id] = [];
                }
                $acc[$variants_id] = $item;
                return $acc;
            },[]);
        }
        $user_role_id = Auth::user()->is_admin;
        return view('store_admin.products.view',compact('store_url','product_details','user_role_id','store_logo','product_image','product_variant_image','variantImages','variants','variantsOptions','variantsCombination'));
    }
    public function edit($id)
    {
        //
    }

    public function update(Request $request)
    {
        $product_id = $request->product_id;
        $update_access = array();
        $update_access[$request->_type] = $request->status_value;
        $update_access['updated_by'] = Auth::user()->id;
        Product::whereIn('product_id',$product_id)->update($update_access); 
        $message = $request->action == "delete" ? trans('store-admin.deleted_msg',['name'=>trans('store-admin.product')]) : trans('store-admin.updated_msg',['name'=>trans('store-admin.status')]);
        return response()->json(['message'=>$message]);
    }

    public function destroy($id)
    {
        $product_id = Crypt::decrypt($id);
        $get_cart_data = Session::get('cart_data');
        $get_product_ids = Session::get('product_ids');
        $get_variant_ids = Session::get('variant_ids');
        $total_cart_quantity = Session::get('total_cart_quantity');
        if(!empty($get_cart_data)) {
            $filtered_cart_data = array_filter($get_cart_data[0]);
            if(!empty($filtered_cart_data)) {
                if (array_key_exists($product_id, $filtered_cart_data)) {
                    if (!empty($total_cart_quantity)) {
                        $totalQuantity = 0;
                        $cartArray = $filtered_cart_data[$product_id];
                        foreach ($cartArray as $cartItem) {
                            if (is_array($cartItem)) {
                                foreach ($cartItem as $variant) {
                                    if (isset($variant['quantity'])) {
                                        $totalQuantity += $variant['quantity'];
                                    }
                                }
                            } else {
                                $totalQuantity += $cartItem;
                            }
                        }
                        $total_cart_quantity[0] -= $totalQuantity;
                        Session::forget('total_cart_quantity');
                        Session::push('total_cart_quantity', $total_cart_quantity[0]);
                    }
                    unset($filtered_cart_data[$product_id]);
                    $get_cart_data[0] = $filtered_cart_data;
                    Session::forget('cart_data');
                    Session::push('cart_data', $get_cart_data[0]);
                }
            }
        }
        if(!empty($get_product_ids)) {
            foreach ($get_product_ids as $key => $subArray) {
                if (in_array($product_id, $get_product_ids[0])) {
                    unset($get_product_ids[0][$key]);
                }
            }
            Session::forget('product_ids');
            Session::push('product_ids', $get_product_ids[0]);
        } 
        $delete_product = array();
        $delete_product['is_deleted'] = 1;  
        $delete_product['deleted_at'] = Carbon::now()->toDateTimeString();
        $delete_product['updated_by'] = Auth::user()->id;
        Product::where('product_id',$product_id)->update($delete_product);
        ProductTax::where('product_id',$product_id)->update($delete_product);
        $prefix_url = config('app.module_prefix_url');
        return redirect()->route(config('app.prefix_url').'.'.$this->store_url.'.'.$prefix_url.'.category.index')->with('message',trans('store-admin.deleted_msg',['name'=>trans('store-admin.product')]));
    }

    public function import() {
        $store_url = $this->store_url;
        $store_logo = $this->store_logo;
        $user_role_id = Auth::user()->is_admin;
        return view('store_admin.products.import',compact('store_url','user_role_id','store_logo'));
    } 

    public function reviews() {
        $store_url = $this->store_url;
        $store_logo = $this->store_logo;
        $user_role_id = Auth::user()->is_admin;
        return view('store_admin.products.reviews',compact('store_url','user_role_id','store_logo'));
    }

    public function get_product_details(Request $request) {
        $category_id = $request->category_id;
        $product_name = $request->product_name;
        $query = Product::leftJoin('store_category', function ($join) {
            $join->on('store_category.category_id', '=', 'store_products.category_id');
        })
        ->leftJoin('store_sub_category', 'store_products.sub_category_id', '=', 'store_sub_category.sub_category_id')
        ->where([
            ['store_products.store_id', '=', Auth::user()->store_id],
            ['store_products.is_deleted', '=', 0],
            ['store_products.status_type', '=', 'publish'],
            ['store_products.status', '=', 1],
            ['store_category.is_deleted', '=', 0],
            ['store_category.status', '=', 1]
        ])
        ->whereIn('store_products.product_type', ['instore', 'both'])
        ->whereRaw('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END');
        if($product_name != "")
            $query->where('product_name','like','%' . $product_name . '%');
        if($category_id != "all")
            $query->where('store_category.category_id',$category_id);
        $product_details = $query->select('product_name')->get()->toArray();
        $indexed_product_details = array_map(function ($product_details) {
            return $product_details['product_name'];
        }, $product_details);
        return $indexed_product_details;
    }

    public function checkUniqueBarcode(Request $request) {
        $barcode = $request->barcode;
        $existsInTable1 = DB::table('store_products')
            ->where('barcode', $barcode)
            ->exists();
        $existsInTable2 = DB::table('store_product_variants_combination')
            ->where('barcode', $barcode)
            ->exists();
        if ($existsInTable1 || $existsInTable2) 
            return response()->json(['message'=>1]);
        else
            return response()->json(['message'=>0]);
    }

    public function updateOrderNumber(Request $request) {
        $old_order_number = $request->old_order_number;
        $order_number = $request->order_number;
        $category_id = Crypt::decrypt($request->category_id);
        $update_order_no = array();
        if($old_order_number < $order_number) {
            $set = "order_number = order_number - 1";
            $where = "order_number > $old_order_number and order_number <= $order_number and store_id = '".Auth::user()->store_id."' and is_deleted = 0";
        } else {
            $set = "order_number = order_number + 1";
            $where = "order_number < $old_order_number and order_number >= $order_number and store_id = '".Auth::user()->store_id."' and is_deleted = 0";
        }
        DB::statement("UPDATE store_products SET $set where  $where");
        $update_order_no['order_number'] = $order_number; 
        Product::where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
            ['product_id', '=', $category_id]
        ])->update($update_order_no);
        return response()->json(['message'=>'Order number updated successfully.']);
    }

    public function removeImage(Request $request) {
        $product_id = Crypt::decrypt($request->product_id);
        $store_id = Auth::user()->store_id;
        $remove_img_path = $request->remove_img_path;
        $product_image = Product::where([
            ['store_id', '=', Auth::user()->store_id],
            ['is_deleted', '=', 0],
            ['product_id', '=', $product_id]
        ])->select('category_image')->get()->toArray();
        if(!empty($product_image)) {
            $update_product_image = array();
            $product_image_path = $product_image[0]['category_image'];
            if (strpos($product_image_path, $remove_img_path."***") !== false) {
                $update_product_image['category_image'] = str_replace($remove_img_path."***", '', $product_image_path); 
            } else if(strpos($product_image_path, "***".$remove_img_path) !== false){
                $update_product_image['category_image'] = str_replace("***".$remove_img_path, '', $product_image_path); 
            } else if(strpos($product_image_path, $remove_img_path) !== false) {
                $update_product_image['category_image'] = str_replace($remove_img_path, '', $product_image_path); 
            }
            Product::where([
                ['product_id', '=', $product_id]
            ])->update($update_product_image);
            return response()->json(['message'=>trans('store-admin.deleted_msg',['name'=>trans('store-admin.product_image')])]);
        }
    }

}
