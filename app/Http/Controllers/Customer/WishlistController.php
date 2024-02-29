<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customers\Wishlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CommonController;
use App\Models\StoreAdmin\Product;
use DB;
use Illuminate\Support\Facades\Crypt;
use Session;

class WishlistController extends Controller
{
    protected $store_url;
    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
    }
    
    public function index(Request $request)
    {
        $store_url = $this->store_url;
        $store_id = CommonController::get_store_id();
        if ($request->type != "") {
            $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
            $page = !empty($request->input('page')) ? $request->input('page') : 1;
            $perPage = 12;
            $product_details = Wishlist::select(
                'product_name',
                'store_products.product_id',
                'store_products.category_id',
                'type_of_product',
                'wishlist_id',
                'trackable',
                'unit',
                'product_description',
                'category_name',
                'sub_category_name', 
                DB::raw('(SELECT variants_id FROM store_product_variants_combination WHERE is_deleted = 0 AND product_id = store_products.product_id LIMIT 1) AS variants_id'),
                DB::raw('CASE WHEN (type_of_product = "variant") THEN (SELECT variant_price FROM store_product_variants_combination WHERE is_deleted = 0 AND product_id = store_products.product_id LIMIT 1) ELSE sp.price END AS price'),
                DB::raw('CASE WHEN type_of_product = "variant" THEN GROUP_CONCAT(variant_images.image_path SEPARATOR "***") ELSE GROUP_CONCAT(single_images.image_path SEPARATOR "***") END AS image_path'),
                DB::raw('CASE WHEN type_of_product = "variant" THEN GROUP_CONCAT(variant_images.image_id SEPARATOR "***") ELSE GROUP_CONCAT(single_images.image_id SEPARATOR "***") END AS image_id')
            )
            ->leftJoin('store_products', 'store_products.product_id', '=', 'wishlist.product_id')
            ->leftJoin('store_category', 'store_category.category_id', '=', 'store_products.category_id')
            ->leftJoin('store_sub_category', function($join) {
                $join->on('store_sub_category.sub_category_id', '=', 'store_products.sub_category_id');
            })
            ->leftJoin('store_price as sp', 'store_products.product_id', '=', 'sp.product_id')
            ->leftJoin('variant_product_images', function ($join) {
                $join->on('wishlist.product_id', '=', 'variant_product_images.product_id')
                    ->where('variant_product_images.is_deleted', '=', 0)
                    ->whereRaw('variant_product_images.variant_ids = (SELECT variants_id FROM store_product_variants_combination WHERE product_id = wishlist.product_id AND is_deleted = 0 ORDER BY variants_combination_id LIMIT 1)')
                    ->whereRaw('variant_product_images.product_image_id = (SELECT product_image_id FROM variant_product_images WHERE variant_ids = (SELECT variants_id FROM store_product_variants_combination WHERE product_id = wishlist.product_id AND is_deleted = 0 ORDER BY variants_combination_id LIMIT 1) AND is_deleted = 0 LIMIT 1)');
            })
            ->leftJoin('product_images AS variant_images', function ($join) {
                $join->on('variant_product_images.product_image_id', '=', 'variant_images.image_id')
                    ->where('variant_images.is_deleted', '=', 0);
            })
            ->leftJoin('product_images AS single_images', function ($join) {
                $join->on('wishlist.product_id', '=', 'single_images.product_id')
                    ->where('single_images.is_deleted', '=', 0);
            })
            ->where('wishlist.store_id', $store_id)
            ->where('wishlist.customer_id', $customer_id)
            ->where('wishlist.is_deleted', 0)
            ->where('store_products.is_deleted', 0)
            ->where('store_products.status_type', 'publish')
            ->where('store_products.status', 1)
            ->where('store_category.is_deleted', 0)
            ->where('store_category.status', 1)
            ->whereRaw('CASE WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END')
            ->distinct('store_products.product_id')
            ->groupBy('product_name','store_products.product_id', 'store_products.category_id','type_of_product','wishlist_id','trackable','unit','product_description','category_name','sub_category_name','price')
            ->orderByDesc('wishlist_id')
            ->paginate($perPage);
            $all_product_data = $product_details->total();
            $totalPages = ceil($all_product_data / $perPage);
            $productDetailsArray = $product_details->toArray();
            $product_details = $productDetailsArray['data'];
            $wishlist_product_list = "";
            if(!empty($product_details) && count($product_details) > 0) {
                $cart_data = session()->get('cart', []);
                $variantProductIds = collect($product_details)->where('type_of_product', 'variant')->pluck('product_id')->toArray();
                $productVariants = [];
                if(!empty($variantProductIds)) {
                    $product_variants_combinations = Product::leftJoin('store_product_variants_combination', 'store_products.product_id', '=', 'store_product_variants_combination.product_id')
                    ->where([
                        ['store_products.store_id', '=', $store_id], 
                        ['store_products.is_deleted', '=', 0],
                        ['status_type', '=', 'publish'],
                        ['store_product_variants_combination.is_deleted', '=', 0]
                    ])
                    ->whereIn('store_products.product_id',$variantProductIds)
                    ->select('variants_combination_id','variants_combination_name','store_products.product_id','variant_price','on_hand')->get()->toArray();
                    if(!empty($product_variants_combinations)) {
                        foreach ($product_variants_combinations as $product_item) {
                            $productId = $product_item['product_id'];
                            if (!isset($productVariants[$productId])) {
                                $productVariants[$productId] = [];
                            }
                            $available_product = $product_item['on_hand'];
                            if(isset($cart_data[$product_item['product_id']][$product_item['variants_combination_id']])) {
                                $quantity = $cart_data[$product_item['product_id']][$product_item['variants_combination_id']]['quantity'];
                                if(!empty($available_product) && is_numeric($available_product) && $available_product >= 0) {
                                    $available_product = ($available_product - $quantity);
                                }
                            }
                            $productVariants[$productId][] = [
                                'variants_combination_id' => $product_item['variants_combination_id'],
                                'variants_combination_name' => $product_item['variants_combination_name'],
                                'variant_price' => $product_item['variant_price'],
                                'on_hand' => $product_item['on_hand'],
                                'product_id' => $product_item['product_id'],
                                'available_product' => $available_product,
                            ];
                        }
                    }
                }
                foreach ($product_details as $product) {
                    $product_images = !empty($product) && !empty($product['image_path']) ? explode("***", $product['image_path']) : [];
                    $allProductImages = !empty($product_images) ? htmlspecialchars(json_encode($product_images, JSON_HEX_QUOT | JSON_HEX_TAG), ENT_QUOTES, 'UTF-8') : "";
                    $product_unit = $available_quantity = ($product['type_of_product'] == "single") ? $product['unit'] : "";
                    if(!empty($cart_data) && isset($cart_data[$product['product_id']]) && ($product['type_of_product'] == "single")) {
                        $quantity = $cart_data[$product['product_id']]['quantity'];
                        $product_unit = $available_quantity = $product['unit'] - $quantity;
                    }
                    if($product['type_of_product'] == "variant") {
                        $available_quantity = 0;
                        if(isset($productVariants[$product['product_id']])) {
                            foreach($productVariants[$product['product_id']] as $variant) {
                                if($variant['on_hand'] == "" || ($variant['on_hand'] != "" && $variant['available_product'] > 0))
                                    $available_quantity++;
                            }
                        }
                    }
                    $wishlist_product_list .= '<div class="col-lg-3 col-md-3 col-sm-6 single-product-details">
                        <article class="single_product">
                            <figure>
                                <input type="hidden" class="wishlist-id" value="'.$product['wishlist_id'].'"> 
                                <input type="hidden" class="single-product-id" value="'.$product['product_id'] .'">
                                <input type="hidden" class="single-product-type" value="'.$product['type_of_product'] .'">
                                <input type="hidden" class="single-product-variants-combination" value="'.$product['variants_id'] .'"> 
                                <input type="hidden" class="add-product-quantity quantity" value="1">
                                <input type="hidden" class="variant-on-hand" value="">
                                <input type="hidden" class="modal-variant-on-hand" value="">
                                <input type="hidden" class="variant-combination-data" value="">
                                <input type="hidden" class="modal-product-unit" value="'.$product_unit.'">
                                <input type="hidden" class="product-unit" value="' . $product['unit'] . '">
                                <input type="hidden" class="single-product-trackable" value="'.$product['trackable'].'">
                                <input type="hidden" class="product-category-name" value="'.$product['category_name'].'">
                                <input type="hidden" class="product-subcategory-name" value="'.$product['sub_category_name'].'">
                                <input type="hidden" class="product-category-images" value="'.$allProductImages.'">
                                <input type="hidden" class="variant-combinations variant-combinations-' . $product['product_id'] . '" value="">
                                <input type="hidden" class="variant-combinations-images variant-combinations-images-'.$product['product_id'].'" value=""> 
                                <input type="hidden" class="single-product-description" value="' . htmlentities($product['product_description'], ENT_QUOTES, 'UTF-8') . '">
                                <div class="product_thumb">';
                                if((($product['type_of_product'] == "single" && $product['trackable'] == 1) || ($product['type_of_product'] == "variant")) && ($available_quantity <= 0)) {
                                    $wishlist_product_list .= '<div class="out-of-stock-overlay">
                                        <p>'.trans('customer.product_out_of_stock').'</p>
                                    </div>';
                                }
                                $singleProductURL = url($store_url . '/customer/single-product/category/' . $product['category_id'] . '/' . strtolower(str_replace(' ', '-', $product['category_name'])) . '/product/' . $product['product_id'] . '/' . strtolower(str_replace(' ', '-', $product['product_name'])));
                                $wishlist_product_list .= '<div class="action_links2">
                                        <ul class="d-flex ">
                                            <li class="add_to_cart remove-wishlist"><a href="#"><span class="pe-7s-trash"></span></a></li>
                                        </ul>
                                    </div>
                                    <a class="single-product-url" href="' . $singleProductURL . '"><img class="product-image-path" style="height:275px;" src="' . (!empty($product_images) && count($product_images) > 0 ? $product_images[0] : "") . '" alt=""></a>';
                                    if((($product['type_of_product'] == "single" && (($product['trackable'] == 1 && $available_quantity > 0) || $product['trackable'] == 0)) || ($product['type_of_product'] == "variant" && $available_quantity > 0))) {
                                        $wishlist_product_list .= '<div class="action_links1">
                                            <ul class="d-flex ">
                                                <li class="product-quick-view" data-page-type="wishlist"><a href="#add-to-cart" title="'.trans('customer.add_to_cart').'"><span class="pe-7s-shopbag"></span></a></li>
                                            </ul>
                                        </div>';
                                    }
                                    $wishlist_product_list .= '</div>
                                <figcaption class="product_content text-center">
                                    <h4><a href="' . $singleProductURL . '" class="product-name truncate-text ">'.$product['product_name'].'</a></h4>
                                    <div class="price_box">
                                        <span class="current_price product-price">SAR '.$product['price'].'</span>
                                    </div>
                                </figcaption>
                            </figure>
                        </article>
                    </div>';
                }
                // <li class="add_to_cart product-add-to-cart add-to-cart" data-type="wishlist"><a href="#add-to-cart" title="Add to cart"><span class="pe-7s-shopbag"></span></a></li>
            } else {
                $wishlist_product_list .= '<div class="col-lg-12 col-md-12 col-sm-12 text-center">'.trans('customer.empty_wishlist').'</div>';
            }
            return response()->json(['wishlist_product_list'=>$wishlist_product_list,'totalPages' => $totalPages,'currentPage' => $page,'status'=>200]);
        } else {
            return view('customer.wishlist', compact('store_url','store_id'));
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $store_id = CommonController::get_store_id();
        $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
        $wishlist_exist_query = Wishlist::where([
            ['product_id', '=', $request->product_id],
            ['customer_id', '=', $customer_id],
            ['store_id','=',$store_id],
            ['is_deleted','=',0],
        ]);
        if(!empty($request->wishlist_id))
            $wishlist_exist_query->where('wishlist_id',$request->wishlist_id);
        $wishlistItem = $wishlist_exist_query->first();
        if($request->_type == "add") {
            if (!$wishlistItem) {
                $input = $request->all();
                $input['customer_id'] = $customer_id;
                $input['store_id'] = $store_id;
                Wishlist::create($input);
            }
            $type="success";
            $message = trans('customer.add_to_wishlist_msg');
        } else {
            if ($wishlistItem) {
                $remove_wishlist = array();
                $remove_wishlist['is_deleted'] = 1;  
                $remove_wishlist['deleted_at'] = Carbon::now()->toDateTimeString();
                $wishlistItem->update($remove_wishlist);
            }
            $type="danger";
            $message = trans('customer.remove_from_wishlist_msg');
        }
        return response()->json(['message' =>$message, 'type'=>$type]);
    }

    public function show(Request $request)
    {
        $store_id = CommonController::get_store_id();
        $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
        $wishlist_exist_query = Wishlist::where([
            ['product_id', '=', $request->product_id],
            ['customer_id', '=', $customer_id],
            ['store_id','=',$store_id],
            ['is_deleted','=',0]
        ]);
        // if($request->product_type == "variant")
        //     $wishlist_exist_query->where('variants_id',$request->variants_id);
        $wishlist = $wishlist_exist_query->get()->count();
        return response()->json(['wishlist' =>$wishlist]);
    }

    public function saveWishlistInSession(Request $request) {
        $wishlistData = $request->input('wishlistData');
        $currentURL = $request->input('currentURL');
        if(Session::has('wishlistData'))    
            Session::forget('wishlistData');
        if(Session::has('wishlistRequestURL'))
            Session::forget('wishlistRequestURL');
        Session::put('wishlistData', $wishlistData);
        Session::put('wishlistRequestURL', $currentURL);
        return response()->json(['redirectURL' =>route($this->store_url.'.customer-login')]);
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
