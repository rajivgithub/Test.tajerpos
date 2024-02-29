<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashierAdmin\OnlineStoreOrderItems;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\CommonController;
use DB;

class OrderController extends Controller
{
    protected $store_url;
    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
    }
    
    public function index()
    {
        $store_url = $this->store_url;
        $store_id = CommonController::get_store_id();
        $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
        $order_details = OnlineStoreOrderItems::leftJoin('online_store_order_details', 'online_store_order_items.order_id', '=', 'online_store_order_details.online_order_id')
        ->leftJoin('online_order_status', 'online_store_order_details.online_order_status', '=', 'online_order_status.order_status_id')
        ->leftJoin('store_products', 'online_store_order_items.product_id', '=', 'store_products.product_id')
        ->where([
            ['online_store_order_items.store_id', '=', $store_id],
            ['online_store_order_items.customer_id', '=', $customer_id]
        ])->select('product_name','category_image','product_variants','quantity','sub_total','online_store_order_items.tax_amount','status_name','online_order_id','online_store_order_details.order_number','discount_amount','total_amount')
        ->selectRaw("DATE_FORMAT(online_store_order_details.created_at, '%d-%m-%Y %H:%i') as ordered_at")
        ->orderBy("online_order_id","desc")
        ->get();
        $breadcrumbs = [
            ['name' => trans('customer.your_account'), 'url' => route($store_url.'.customer.dashboard')],
            ['name' => trans('customer.your_orders'), 'url' => "#"]
        ];
        return view("customer.orders",compact('store_url','store_id','order_details','breadcrumbs'));
    }

    public function getOrdersProducts(Request $request) {
        $perPage = $request->perPage;
        $store_url = $this->store_url;
        $search_text = $request->search_text;
        $page = !empty($request->input('page')) ? $request->input('page') : 1;
        $store_id = CommonController::get_store_id();
        $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
        $order_details_query = OnlineStoreOrderItems::leftJoin('online_store_order_details', 'online_store_order_items.order_id', '=', 'online_store_order_details.online_order_id')
            ->leftJoin('online_order_status', 'online_store_order_details.online_order_status', '=', 'online_order_status.order_status_id')
            ->leftJoin('customer_address', 'online_store_order_details.address_id', '=', 'customer_address.address_id')
            ->leftJoin('store_products', 'online_store_order_items.product_id', '=', 'store_products.product_id')
            ->leftJoin('store_category', 'store_category.category_id', '=', 'store_products.category_id')
            ->leftJoin('variant_product_images', function ($join) {
                $join->on('online_store_order_items.variants_id', '=', 'variant_product_images.variant_ids')
                    ->where('variant_product_images.is_deleted', '=', 0)
                    ->whereRaw('variant_product_images.product_image_id = (SELECT product_image_id FROM variant_product_images WHERE variant_ids = online_store_order_items.variants_id AND is_deleted = 0 LIMIT 1)');
            })
            ->leftJoin('product_images AS variant_images', function ($join) {
                $join->on('variant_product_images.product_image_id', '=', 'variant_images.image_id')
                    ->where('variant_images.is_deleted', '=', 0);
            })
            ->leftJoin('product_images AS single_images', function ($join) {
                $join->on('online_store_order_items.product_id', '=', 'single_images.product_id')
                    ->where('single_images.is_deleted', '=', 0)
                    ->whereRaw('single_images.image_id = (SELECT MIN(image_id) FROM product_images WHERE product_id = online_store_order_items.product_id)');
            })
            ->leftJoin('countries', 'customer_address.country_id', '=', 'countries.id')
            ->leftJoin('states', 'customer_address.state_id', '=', 'states.id')
            ->leftJoin('cities', 'customer_address.city_id', '=', 'cities.id')
            ->when($search_text, function ($query) use ($search_text) {
                $query->where('store_products.product_name', 'LIKE', '%' . $search_text . '%');
                $query->orWhere('online_store_order_details.order_number', 'LIKE', '%' . $search_text . '%');
                $query->orWhere('customer_name', 'LIKE', '%' . $search_text . '%');
                $query->orWhere('status_name', 'LIKE', '%' . $search_text . '%');
            })
            ->where([
                ['online_store_order_items.store_id', '=', $store_id],
                ['online_store_order_items.customer_id', '=', $customer_id]
            ])->select('product_name','product_variants','quantity','sub_total','online_store_order_items.tax_amount','status_name','online_order_id','online_store_order_details.order_number','discount_amount','total_amount','customer_name','online_store_order_items.product_id','street_name','building_name','pincode','countries.name as country_name','states.name as state_name','cities.name as city_name','store_products.category_id','category_name',
                \DB::raw('CASE WHEN type_of_product = "variant" THEN variant_images.image_path ELSE single_images.image_path END AS image_path'),
            )
            ->selectRaw("DATE_FORMAT(online_store_order_details.created_at, '%d-%m-%Y %H:%i') as ordered_at")
            ->selectRaw("(SELECT GROUP_CONCAT(variants_name) FROM store_product_variants WHERE product_id = store_products.product_id AND is_deleted = 0) as variants_name")
            ->orderBy("online_order_id","desc");
        $order_details = $order_details_query->paginate($perPage);
        $all_order_details = $order_details->total();   
        $totalPages = ceil($all_order_details / $perPage);  
        $order_details_array = $order_details->toArray();
        $order_details = $order_details_array['data'];
        $groupedOrders = [];
        if (isset($order_details) && !empty($order_details)) {
            foreach ($order_details as $order) {
                $orderNumber = $order['order_number'];
                if (!isset($groupedOrders[$orderNumber])) {
                    $groupedOrders[$orderNumber] = [];
                }
                $groupedOrders[$orderNumber][] = $order;
            }
        }
        $orders_list = ""; 
        if(isset($groupedOrders) && !empty($groupedOrders)) {
            foreach($groupedOrders as $orders_data) {
                foreach($orders_data as $key => $order) {
                    $variants_name = "";
                    if(!empty($order['product_variants'])) {
                        $variants = explode(',', $order['variants_name']);
                        if(count($variants) > 1) {
                            $lastVariant = array_pop($variants);
                            $variants_name = implode(', ', $variants) . ' and ' . $lastVariant;
                        } else {
                            $variants_name = implode(', ', $variants);
                        }
                    }
                    if($key == 0) {
                        $ordered_date = Carbon::createFromFormat('d-m-Y H:i', $order['ordered_at']);
                        $shipping_Address = '';
                        if(!empty($order['building_name']))
                            $shipping_Address .= $order['building_name'].",<br/>";
                        if(!empty($order['street_name']))
                            $shipping_Address .= $order['street_name'].",<br/>";
                        if(!empty($order['city_name']))
                            $shipping_Address .= $order['city_name'].", ";
                        if(!empty($order['state_name']))
                            $shipping_Address .= $order['state_name'].",<br/>";
                        if(!empty($order['pincode']))
                            $shipping_Address .= $order['pincode'].",<br/>";
                        if(!empty($order['country_name']))
                            $shipping_Address .= $order['country_name'].".";

                        $orders_list .= '<div class="order-one mb-3"><div class="card order-review">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-12 d-flex">
                                                <div class="col-sm-4 col-md-4 col-lg-4">
                                                    <span>'.trans('customer.order_placed').'</span><br/>
                                                    <span>'.$ordered_date->format('d F Y').'</span>
                                                </div>
                                                <div class="col-sm-4 col-md-4 col-lg-4">
                                                    <span>'.trans('customer.total').'</span><br/>
                                                    <span>SAR '.number_format($order['total_amount'], 2, '.', '').'</span>
                                                </div>
                                                <div class="col-sm-4 col-md-4 col-lg-4 user-select-none" title="'.$order['customer_name'].'" data-bs-container="body" data-bs-toggle="popover"  data-bs-placement="bottom" data-bs-content="'.$shipping_Address.'" data-bs-trigger="hover"  data-bs-html="true">
                                                    <span>'.trans('customer.ship_to').'</span><br/>
                                                    <span>'.$order['customer_name'].' <i class="fa fa-angle-down"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <span>'.trans('customer.order').' # '.$order['order_number'].'</span><br/> 
                                        <button type="button" class="btn btn-link-custom"><a href="' . route($this->store_url .'.customer.orders.show',$order['online_order_id']).'">'.trans('customer.view_order_details').'</a></button>
                                    </div>
                                </div>
                            </div>'; 
                    }
                    $orders_list .= '<ul class="list-group list-group-flush">';
                        if($key == 0) {
                            $orders_list .= '<li class="list-group-item">
                                <h6 style="margin-bottom:0px;">'.$order['status_name'].'</h6>
                            </li>';
                        }
                        $singleProductURL = url($store_url . '/customer/single-product/category/' . $order['category_id'] . '/' . strtolower(str_replace(' ', '-', $order['category_name'])) . '/product/' . $order['product_id'] . '/' . strtolower(str_replace(' ', '-', $order['product_name'])));
                        $orders_list .= '<li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="order-img"> 
                                    <img src="'.$order['image_path'].'" alt="" class="rounded">
                                </div>
                                <div class="order-des">
                                    <a href="' . $singleProductURL.'" target="_blank">'.$order['product_name'].'</a>
                                    <p class="mb-2 fw-bold">SAR '.number_format(($order['sub_total'] / $order['quantity']), 2, '.', '').'</p>';
                                    if(!empty($order['product_variants']))
                                        $orders_list .= '<p class="mb-2">'.$variants_name." : " .'<span class="fw-bold">'.$order['product_variants'].'</span></p>';
                                $orders_list .= '</div>
                            </div>
                        </li>
                    </ul> ';
                    if($key == (count($orders_data) - 1)) {
                        $orders_list .= '</div></div>';
                    }
                    // <div class="">
                    //     <button class="btn btn-light btn-sm"><a href="' . route($store_url . '.customer.single-product', ['id' => Crypt::encrypt($order['product_id'])]).'" target="_blank">View your item</a></button>
                    // </div>
                }
            }
        } else {
            $orders_list .= '<h6 class="text-center">No orders placed</h6>';
        }
        return response()->json(['orders_list'=>$orders_list,'totalPages' => $totalPages,'currentPage' => $page]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    
    public function show($id)
    {
        $store_url = $this->store_url;
        $store_id = CommonController::get_store_id();
        $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
        $order_details = OnlineStoreOrderItems::leftJoin('online_store_order_details', 'online_store_order_items.order_id', '=', 'online_store_order_details.online_order_id')
            ->leftJoin('online_order_status', 'online_store_order_details.online_order_status', '=', 'online_order_status.order_status_id')
            ->leftJoin('customer_address', 'online_store_order_details.address_id', '=', 'customer_address.address_id')
            ->leftJoin('store_products', 'online_store_order_items.product_id', '=', 'store_products.product_id')
            ->leftJoin('store_category', 'store_category.category_id', '=', 'store_products.category_id')
            ->leftJoin('variant_product_images', function ($join) {
                $join->on('online_store_order_items.variants_id', '=', 'variant_product_images.variant_ids')
                    ->where('variant_product_images.is_deleted', '=', 0)
                    ->whereRaw('variant_product_images.product_image_id = (SELECT product_image_id FROM variant_product_images WHERE variant_ids = online_store_order_items.variants_id AND is_deleted = 0 LIMIT 1)');
            })
            ->leftJoin('product_images AS variant_images', function ($join) {
                $join->on('variant_product_images.product_image_id', '=', 'variant_images.image_id')
                    ->where('variant_images.is_deleted', '=', 0);
            })
            ->leftJoin('product_images AS single_images', function ($join) {
                $join->on('online_store_order_items.product_id', '=', 'single_images.product_id')
                    ->where('single_images.is_deleted', '=', 0)
                    ->whereRaw('single_images.image_id = (SELECT MIN(image_id) FROM product_images WHERE product_id = online_store_order_items.product_id)');
            })
            ->leftJoin('countries', 'customer_address.country_id', '=', 'countries.id')
            ->leftJoin('states', 'customer_address.state_id', '=', 'states.id')
            ->leftJoin('cities', 'customer_address.city_id', '=', 'cities.id')
            ->where([
                ['online_store_order_items.store_id', '=', $store_id],
                ['online_store_order_items.customer_id', '=', $customer_id],
                ['online_order_id', '=', $id],
            ])->select('product_name','product_variants','quantity','sub_total','online_store_order_items.tax_amount','status_name','online_order_id','online_store_order_details.order_number','discount_amount','total_amount','customer_name','online_store_order_items.product_id','street_name','building_name','pincode','countries.name as country_name','states.name as state_name','cities.name as city_name','sub_total_amount','online_store_order_details.tax_amount as total_tax_amount','store_products.category_id','category_name',
                \DB::raw('CASE WHEN type_of_product = "variant" THEN variant_images.image_path ELSE single_images.image_path END AS image_path'))
            ->selectRaw("DATE_FORMAT(online_store_order_details.created_at, '%d-%m-%Y %H:%i') as ordered_at")
            ->selectRaw("(SELECT GROUP_CONCAT(variants_name) FROM store_product_variants WHERE product_id = store_products.product_id AND is_deleted = 0) as variants_name")
            ->get()
            ->map(function($item) {
                $variants = explode(',', $item->variants_name);
                if(count($variants) > 1) {
                    $lastVariant = array_pop($variants);
                    $variantsString = implode(', ', $variants) . ' and ' . $lastVariant;
                } else {
                    $variantsString = implode(', ', $variants);
                }
                $item->variants_name = $variantsString;
                return $item;
            })
            ->toArray();
        $breadcrumbs = [
            ['name' => trans('customer.your_account'), 'url' => route($store_url.'.customer.dashboard')],
            ['name' => trans('customer.your_orders'), 'url' => route($store_url.'.customer.orders.index')],
            ['name' => trans('customer.order_details'), 'url' => "#"]
        ];
        $payment_details = DB::table('store_payment_responses')->where('order_id',$id)->first();
        return view("customer.orders_details",compact('store_url','store_id','order_details','breadcrumbs','payment_details'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

   
    public function destroy($id)
    {
        //
    }
}
