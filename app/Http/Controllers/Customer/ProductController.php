<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\Product;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\Category;
use DB;
use App\Models\StoreAdmin\SubCategory;
use App\Models\Customers\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\StoreAdmin\Variants;
use App\Models\StoreAdmin\VariantProductImage;
use App\Models\StoreAdmin\ProductImage;
use App\Models\Customers\ShoppingCart;
use App\Models\StoreAdmin\VariantsOption;

class ProductController extends Controller
{
    protected $store_url;
    protected $store_id;
    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
        $this->store_id = CommonController::get_store_id();
    }

    public function singleProduct($category_id,$category_name,$product_id,$product_name,$variant_option_id= '',$type = '')
    {
        $store_url = $this->store_url; 
        $store_id = $this->store_id;
        $_page = !empty($type) ? $type : "";
        $productVariants = $variantImages = $variantsOptions = $product_details = $product_variants_combinations = $breadcrumbs = $cart_data = [];
        $product_details = Product::leftJoin('store_category', 'store_products.category_id', '=', 'store_category.category_id')
            ->leftJoin('store_sub_category', 'store_products.sub_category_id', '=', 'store_sub_category.sub_category_id')
            ->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')
            ->leftJoin('product_images AS single_images', function ($join) use ($product_id) {
                $join->on('store_products.product_id', '=', 'single_images.product_id')
                    ->where('single_images.product_id', $product_id)
                    ->where('single_images.is_deleted', '=', 0);
            })
            ->where([
                ['store_products.store_id', '=', $store_id],
                ['store_products.is_deleted', '=', 0],
                ['status_type', '=', 'publish'],
                ['store_category.is_deleted', '=', 0],
                ['store_products.product_id', '=', $product_id]
            ])
            ->whereIn('store_products.product_type', ['online', 'both'])
            ->whereRaw('
                (CASE WHEN (store_products.sub_category_id > 0) 
                    THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 
                    ELSE TRUE 
                END)
            ')
            ->select('store_products.product_id', 'store_products.category_id', 'product_name', 'type_of_product', 'price', 'unit', 'trackable', 'product_description', 'category_name', 'sub_category_name','store_products.sub_category_id',
                \DB::raw('CASE WHEN type_of_product = "single" THEN GROUP_CONCAT(single_images.image_path SEPARATOR "***") ELSE TRUE END AS image_path')
            )
            ->groupBy('product_id','category_id','product_name', 'type_of_product', 'price','unit', 'trackable', 'product_description', 'category_name', 'sub_category_name','store_products.sub_category_id')
            ->orderBy('store_products.created_at', 'DESC')
            ->first();
        if ($product_details) 
            $product_details = $product_details->toArray();
        if(!empty($product_details) && count($product_details) > 0 && $product_details['type_of_product'] == "variant" && !empty($product_details['product_id'])) {
            $get_product_variants_combinations = Product::leftJoin('store_product_variants_combination', 'store_products.product_id', '=', 'store_product_variants_combination.product_id')
                ->where([
                    ['store_products.store_id', '=', $store_id], 
                    ['store_products.is_deleted', '=', 0],
                    ['status_type', '=', 'publish'],
                    ['store_product_variants_combination.is_deleted', '=', 0]
                ])
                ->whereIn('store_products.product_type', ['online', 'both'])
                ->where('store_products.product_id',$product_details['product_id'])
                ->select('variants_combination_id','variants_combination_name','store_products.product_id','variant_price','on_hand','variants_id')->get()->toArray();
            if(!empty($get_product_variants_combinations)) {
                $product_variants_combinations = $this->checkVariantOutOfStock($get_product_variants_combinations,$product_details['product_id']);
            }
            $variantImages = VariantProductImage::leftJoin('product_images', 'variant_product_images.product_image_id', '=', 'product_images.image_id')
                ->where([
                    ['variant_product_images.store_id', '=', $store_id],
                    ['variant_product_images.product_id', '=', $product_details['product_id']],
                    ['variant_product_images.is_deleted', '=', 0],
                    ['product_images.is_deleted', '=', 0],
                ])
                ->get(['variant_img_id','variant_ids','variant_combination_id','product_image_id','image_path'])
                ->groupBy(['variant_ids'])
                ->toArray();
            $productVariants = Variants::where([
                    ['store_id', '=', $store_id], 
                    ['product_id', '=', $product_id],
                    ['is_deleted', '=', 0],
                ])->get(['variants_id','variants_name'])->toArray();
            $variantsOptions = VariantsOption::where([
                    ['store_id', '=', $store_id],
                    ['product_id', '=', $product_id],
                    ['is_deleted', '=', 0],
                ])->get(['variants_id','variant_options_name','variant_options_id','variants_option_image'])
                ->groupBy(['variants_id'])
                ->toArray();
        }
        $wishlistData = [];
        if(auth()->guard('customer')->check()) {
            if(session()->has('authenticate_user'))
                $user = session('authenticate_user');
            else 
                $user = Auth::guard('customer')->user();
            $wishlistData = Wishlist::where([
                ['customer_id', '=',$user->customer_id],
                ['store_id','=',$user->store_id],
                ['is_deleted','=',0],
                ['product_id','=',$product_id]
            ])
            ->select('wishlist_id','product_id','variants_id')->get()->toArray();
        }
        $breadcrumbs[] = ['name' => trans('customer.home'), 'url' => route($store_url.'.customer.home')];
        if($_page == "product_page" || $_page == "related_products")
            $breadcrumbs[] = ['name' => trans('customer.products'), 'url' => route($store_url.'.customer.category')];            
        if(!empty($product_details) && isset($product_details['product_name'])) 
            $breadcrumbs[] = ['name' => $product_details['product_name'], 'url' => "#"];
        if (auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $this->store_id) {
            $cartDetails = ShoppingCart::where([
                ['store_id', $store_id],
                ['customer_id', auth()->guard('customer')->user()->customer_id],
                ['is_deleted', 0],
            ])
            ->get(['cart_id','product_id','variants_id','quantity'])->toArray();
            if(!empty($cartDetails)) {
                foreach ($cartDetails as $cart) {
                    $productId = $cart['product_id'];
                    $variantId = $cart['variants_id'];
                    if (!isset($cart_data[$productId])) 
                        $cart_data[$productId] = [];
                    if (!empty($variantId)) {
                        if (!isset($cart_data[$productId][$variantId])) 
                            $cart_data[$productId][$variantId] = [];
                        $cart_data[$productId][$variantId] = $cart;
                    } else 
                        $cart_data[$productId] = $cart;
                }
            }
        }
        else
            $cart_data = session()->get('cart', []);
        return view('customer.single_product',compact('cart_data','store_url','product_variants_combinations','product_details','breadcrumbs','wishlistData','store_id','productVariants','variantImages','variantsOptions','variant_option_id'));
    }

    public function categoryProduct($category_name = '',$sub_category_name = '')
    {
        $store_url = $this->store_url; 
        $store_id = $this->store_id;
        $breadcrumbs = [];
        $breadcrumbs[] = ['name' => trans('customer.home'), 'url' => route($store_url.'.customer.home')];
        $breadcrumbs[] = ['name' => trans('customer.products'), 'url' => "#"];
        return view('customer.category_product',compact('store_url','breadcrumbs','store_id','category_name','sub_category_name'));
    }

    public function getCategoryProduct(Request $request) {
        $store_id = $this->store_id;
        $category_id = $request->category_id;
        $category_details = Category::where([
            ['store_category.store_id', '=', $store_id], 
            ['store_category.status', '=', 1],
            ['store_category.is_deleted', '=', 0], 
        ])
        ->select(
            'store_category.category_id',
            'store_category.category_name',
            DB::raw('(SELECT COUNT(*) FROM store_products AS sp LEFT JOIN store_sub_category on sp.sub_category_id = store_sub_category.sub_category_id WHERE sp.category_id = store_category.category_id AND sp.is_deleted = 0 AND status_type = "publish" AND (CASE WHEN sp.sub_category_id > 0 THEN store_sub_category.status = 1 AND store_sub_category.is_deleted = 0 ELSE TRUE END)) AS product_count')
        )
        ->orderBy('store_category.created_at', 'DESC')
        ->get();
        $total_product_count = $category_details->sum('product_count');
        $category_details = $category_details->toArray();
        $all_product_query = Product::leftJoin('store_category', 'store_products.category_id', '=', 'store_category.category_id')
        ->leftJoin('store_sub_category', 'store_products.sub_category_id', '=', 'store_sub_category.sub_category_id')
        ->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')->where([
            ['store_products.store_id', '=', $store_id], 
            ['store_products.is_deleted', '=', 0],
            ['status_type', '=', 'publish'],
            ['store_category.status', '=', 1], 
            ['store_category.is_deleted', '=', 0], 
        ])
        ->whereIn('store_products.product_type', ['online', 'both']);
        if($category_id != 'all')
            $all_product_query->where('store_products.category_id',$category_id);
        $all_product_details = $all_product_query->whereRaw(('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END'))->select('store_products.product_id','store_products.category_id','product_name','type_of_product','price')->orderBy('store_products.created_at','DESC')->get();
        return response()->json(['category_details'=>$category_details,'total_product_count'=>$total_product_count,'all_product_details'=>$all_product_details]);
    }

    public function productsByCategory(Request $request) {
        $store_id = $this->store_id;
        $store_url = $this->store_url; 
        $page = !empty($request->input('page')) ? $request->input('page') : 1;
        $category_id = !empty($request->category_id) ? $request->category_id : "all";
        $sub_category_id = $request->sub_category_id;
        $product_id = $request->product_id;
        $category_url = $request->category_url;
        $sub_category_url = $request->sub_category_url;
        $sorting_column = $request->sorting_column;
        $sorting_order = $request->sorting_order;
        $search_text = $request->search_text;
        $page_type = $request->_type;
        $perPage = $request->perPage;
        if($page_type != "related_products") {
            $category_details = Category::select(
                'category_name',
                'category_url',
                'category_id',
                DB::raw('(SELECT COUNT(DISTINCT sp.product_id) 
                        FROM store_products AS sp 
                        LEFT JOIN store_sub_category ON sp.sub_category_id = store_sub_category.sub_category_id 
                        WHERE sp.category_id = store_category.category_id 
                        AND sp.is_deleted = 0 
                        AND status_type = "publish" 
                        AND product_type IN ("online","both")
                        AND (CASE WHEN sp.sub_category_id > 0 THEN store_sub_category.status = 1 AND store_sub_category.is_deleted = 0 ELSE TRUE END)) AS product_count'
            ))
            ->where('store_id', $store_id)
            ->where('is_deleted', 0)
            ->where('status', 1)
            ->orderByDesc('category_id')
            ->get();
            $totalProductCount = $category_details->sum('product_count');
        }
        $product_details_query = Product::select(
            'store_products.product_id',
            'type_of_product',
            'product_name',
            'store_products.category_id',
            'category_name',
            'unit_price',
            'store_products.sub_category_id',
            'unit',
            'trackable',
            'price',
            'product_description',
            'sub_category_name',
            \DB::raw('CASE WHEN type_of_product = "variant" THEN variant_images.image_path ELSE GROUP_CONCAT(single_images.image_path SEPARATOR "***") END AS image_path'),
        );
        if ($sorting_column == "low_to_high" || $sorting_column == "high_to_low") {
            $product_details_query->selectRaw('
                CASE 
                    WHEN store_products.type_of_product = "variant" THEN 
                        (SELECT variants_id FROM store_product_variants_combination 
                         WHERE product_id = store_products.product_id and store_product_variants_combination.is_deleted = 0
                         ORDER BY CAST(variant_price AS DECIMAL(10,2)) '.$sorting_order.' LIMIT 1)
                    ELSE TRUE
                END as variants_id
            ');
        } 
        $product_details_query->leftJoin('store_category', 'store_category.category_id', '=', 'store_products.category_id')
        ->leftJoin('store_sub_category', 'store_sub_category.sub_category_id', '=', 'store_products.sub_category_id')
        ->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')
        ->leftJoin('variant_product_images', function ($join) {
            $join->on('store_products.product_id', '=', 'variant_product_images.product_id')
                ->where('variant_product_images.is_deleted', '=', 0)
                ->whereRaw('variant_product_images.variant_ids = (SELECT variants_id FROM store_product_variants_combination WHERE product_id = store_products.product_id AND is_deleted = 0 ORDER BY variants_combination_id LIMIT 1)')
                ->whereRaw('variant_product_images.product_image_id = (SELECT product_image_id FROM variant_product_images WHERE variant_ids = (SELECT variants_id FROM store_product_variants_combination WHERE product_id = store_products.product_id AND is_deleted = 0 ORDER BY variants_combination_id LIMIT 1) AND is_deleted = 0 LIMIT 1)');
        })
        ->leftJoin('product_images AS variant_images', 'variant_product_images.product_image_id', '=', 'variant_images.image_id')
        ->leftJoin('product_images AS single_images', 'store_products.product_id', '=', 'single_images.product_id')
        ->where('store_products.store_id', $store_id)
        ->where('store_products.is_deleted', 0)
        ->where('store_products.status_type', 'publish')
        ->where('store_products.status', 1)
        ->whereIn('store_products.product_type', ['online', 'both'])
        ->where('store_category.is_deleted', 0)
        ->where('store_category.status', 1)
        ->when($search_text, function ($query) use ($search_text) {
            $query->where('store_products.product_name', 'LIKE', '%' . $search_text . '%');
        })
        ->when($category_id != "all", function ($query) use ($category_id) {
            $query->where('store_products.category_id', $category_id);
        }) 
        ->when($category_url != "", function ($query) use ($category_url) {
            $query->where('store_category.category_url', $category_url);
        }) 
        ->when($sub_category_id, function ($query) use ($sub_category_id) {
            $query->where('store_products.sub_category_id', $sub_category_id);
        })
        ->when($page_type == "related_products", function ($query) use ($product_id) {
            $query->whereNotIn('store_products.product_id', [$product_id]);
        }) 
        ->whereRaw('CASE WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END')
        ->whereRaw('CASE WHEN ? != "" THEN store_sub_category.sub_category_url = ? ELSE TRUE END', [$sub_category_url, $sub_category_url])
        ->groupBy(
            'store_products.product_id',
            'type_of_product',
            'product_name',
            'store_products.category_id',
            'category_name',
            'unit_price',
            'store_products.sub_category_id',
            'unit',
            'trackable',
            'price',
            'product_description',
            'sub_category_name',
            'variant_images.image_path',
        );
        if (!empty($sorting_column) && !empty($sorting_order)) {
            if ($sorting_column == "low_to_high" || $sorting_column == "high_to_low") {
                $product_details_query->orderByRaw('
                    CASE 
                        WHEN store_products.type_of_product = "variant" THEN 
                            (SELECT CAST(variant_price AS DECIMAL(10,2)) FROM store_product_variants_combination 
                            WHERE product_id = store_products.product_id and store_product_variants_combination.is_deleted = 0
                            ORDER BY CAST(variant_price AS DECIMAL(10,2)) '.$sorting_order.' LIMIT 1)
                        ELSE CAST(price AS DECIMAL(10,2))
                    END '.$sorting_order.'
                ');
            } else {
                $product_details_query->orderBy($sorting_column, $sorting_order);
            }
        } else {
            $product_details_query->orderByDesc('store_products.product_id');
        }
        $product_details = $product_details_query->paginate($perPage);
        $all_product_data = $product_details->total();   
        $totalPages = ceil($all_product_data / $perPage);  
        $productIds = $product_details->pluck('product_id');

        //To Display Variant Option Images
        $product_variants_collection = Product::leftJoin('store_product_variants', 'store_products.product_id', '=', 'store_product_variants.product_id')
            ->leftJoin('store_product_variants_options', 'store_product_variants.variants_id', '=', 'store_product_variants_options.variants_id')   
            ->select('variants_name', 'store_product_variants.product_id', 'store_product_variants_options.variants_id','variants_option_image','variant_options_name','variant_options_id')
            ->where('store_products.store_id', $store_id)
            ->where('store_products.is_deleted', 0)
            ->whereIn('store_products.product_type', ['online', 'both'])
            ->where('status_type', 'publish')
            ->where('type_of_product', 'variant')
            ->where('store_product_variants.is_deleted', 0)
            ->where('store_product_variants_options.is_deleted', 0)
            ->when(!empty($productIds), function ($query) use ($productIds) {
                $query->whereIn('store_products.product_id', $productIds);
            })
            ->when(true, function ($query) {
                $query->whereNotNull('store_product_variants_options.variants_option_image')
                      ->where('store_product_variants_options.variants_option_image', '<>', '');
            })
            ->get()
            ->groupBy(['product_id','variants_id'])
            ->toArray();

        $product_variants_combinations_query = Product::leftJoin('store_product_variants_combination', 'store_products.product_id', '=', 'store_product_variants_combination.product_id')
            ->where('store_products.store_id', $store_id)
            ->where('store_products.is_deleted', 0)
            ->whereIn('store_products.product_type', ['online', 'both'])
            ->where('status_type', 'publish')
            ->where('type_of_product', 'variant')
            ->where('store_product_variants_combination.is_deleted', 0)
            ->when(!empty($productIds), function ($query) use ($productIds) {
                $query->whereIn('store_products.product_id', $productIds);
            });

        $get_product_variants_combinations = $product_variants_combinations_query->select('variants_combination_id', 'variants_combination_name', 'store_products.product_id', 'variant_price', 'on_hand', 'variants_id')->get()->toArray();
        $cart_data = [];
        if (auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $this->store_id) {
            $cartDetails = ShoppingCart::where([
                ['store_id', $store_id],
                ['customer_id', auth()->guard('customer')->user()->customer_id],
                ['is_deleted', 0],
            ])
            ->get(['cart_id','product_id','variants_id','quantity'])->toArray();
            if(!empty($cartDetails)) {
                foreach ($cartDetails as $cart) {
                    $productId = $cart['product_id'];
                    $variantId = $cart['variants_id'];
                    if (!isset($cart_data[$productId])) 
                        $cart_data[$productId] = [];
                    if (!empty($variantId)) {
                        if (!isset($cart_data[$productId][$variantId])) 
                            $cart_data[$productId][$variantId] = [];
                        $cart_data[$productId][$variantId] = $cart;
                    } else 
                        $cart_data[$productId] = $cart;
                }
            }
        }
        else
            $cart_data = session()->get('cart', []);
        $product_variants_combinations = []; 
        if(!empty($get_product_variants_combinations)) {
            foreach($get_product_variants_combinations as $key => $variants) {
                $variant_product_unit = $available_variants_quantity = $variants['on_hand'];
                if(!empty($cart_data) && isset($cart_data[$variants['product_id']]) && isset($cart_data[$variants['product_id']][$variants['variants_id']])) {
                    $quantity = $cart_data[$variants['product_id']][$variants['variants_id']]['quantity'];
                    if(!empty($variant_product_unit) && is_numeric($variant_product_unit) && $variant_product_unit >= 0)
                        $available_variants_quantity = ($variant_product_unit - $quantity);
                }
                $variants['product_available'] = (is_numeric($available_variants_quantity) && ($available_variants_quantity <= 0)) ? "out-of-stock" : "";
                $product_variants_combinations[$variants['product_id']][$variants['variants_id']] = $variants;
            }
        }
        $wishlistData = [];
        if(!empty($productIds) && auth()->guard('customer')->check()) {
            $user = session()->has('authenticate_user') ? session('authenticate_user') : Auth::guard('customer')->user();
            $wishlistData = Wishlist::where([
                ['customer_id', '=', $user->customer_id],
                ['store_id','=',$user->store_id],
                ['is_deleted','=',0]
            ])
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id')
            ->selectRaw('MAX(wishlist_id) as wishlist_id, product_id')
            ->get()->keyBy('product_id')->toArray();
        } 
        if($category_id != "all" || $page_type == "product_page")  {
            $sub_category_details_query = SubCategory::leftJoin('store_products as sp', function ($join) use ($category_id,$page_type) {
                $join->on('sp.sub_category_id', '=', 'store_sub_category.sub_category_id')
                    ->when($category_id != "all" && $page_type != "product_page", function ($query) use ($category_id) {
                        $query->where('store_sub_category.category_id', $category_id);
                    })
                    ->where('sp.is_deleted', '=', 0)
                    ->where('sp.status_type', '=', 'publish')
                    ->whereIn('sp.product_type', ['online', 'both'])
                    ->where('store_sub_category.status', '=', 1)
                    ->where('store_sub_category.is_deleted', '=', 0);
            })
            ->leftJoin('store_category', 'sp.category_id', '=', 'store_category.category_id')
            ->where('store_sub_category.store_id', '=', $store_id)
            ->where('store_sub_category.is_deleted', '=', 0)
            ->where('store_sub_category.status', '=', 1);
            $sub_category_details = $sub_category_details_query
                ->groupBy('store_sub_category.category_id', 'sub_category_url', 'sub_category_name', 'sub_category_id')
                ->select('store_sub_category.category_id', 'sub_category_url', 'sub_category_name', 'store_sub_category.sub_category_id', DB::raw('COUNT(DISTINCT sp.product_id) as product_count'))
                ->orderByDesc('store_sub_category.category_id')
                ->get()
                ->toArray();
        }
        $category_list_html = "";
        if(isset($category_details) && !empty($category_details) && ($totalProductCount > 0))  {
            $active_class =  ($category_id == "all") ? "active" : "";
            if($page_type == "product_page") {
                $organizedSubcategories = array();
                if(!empty($sub_category_details)) {
                    foreach ($sub_category_details as $subcategory) {
                        $organizedSubcategories[$subcategory["category_id"]][] = $subcategory;
                    }
                }
                $active_class = (($category_id == "all" && $category_url == "")) ? "active" : "";
                $category_list_html .= '<ul><li class="category-details '.$active_class.'"><input type="hidden" class="category-id" value="all" tabindex="0"><a href="'.route($this->store_url.'.customer.category').'" class="category-name">'.trans("customer.all").' <span>('.($totalProductCount).')</span></a></li>';
                foreach ($category_details as $category) {
                    $isDisplayCategory = 0;
                    if($category->product_count > 0) {
                        $active_class = (($category_url == $category->category_url)|| ($category_id == $category->category_id)) ? "active" : "";
                        $subCategoryDetails = (!empty($organizedSubcategories) && array_key_exists($category->category_id,$organizedSubcategories)) ? $organizedSubcategories[$category->category_id] : [];
                        if(!empty($subCategoryDetails)) {
                            foreach($subCategoryDetails as $key=> $sub_category) {
                                $sc_active_class = (($sub_category_url == $sub_category['sub_category_url']) || ($sub_category['sub_category_id'] == $sub_category_id)) ? "active" : ""; 
                                if($sub_category['product_count'] > 0) {
                                    if($isDisplayCategory == 0) {
                                        $category_list_html .= '<li class="category-details nested '.$active_class.'"><input type="hidden" class="category-id" value="'.$category->category_id.'"><a href="'.url($this->store_url.'/customer/category/'.strtolower(str_replace(' ', '-', $category->category_name))).'" class="toggle category-name">'.$category->category_name.' <span>('.$category->product_count.')</span></a><ul class="nested-list">';
                                        $isDisplayCategory++;
                                    }
                                    $category_list_html.= '<li class="nested-sub-category-list sub-category-list '.$sc_active_class.' sub-category-list-'.$sub_category['sub_category_id'].'" data-sub-category-id="'.$sub_category['sub_category_id'].'">
                                                                <a href="'.url($this->store_url.'/customer/category/'.strtolower(str_replace(' ', '-', $category->category_name)).'/sub-category/'.strtolower(str_replace(' ', '-', $sub_category['sub_category_name']))).'" class="sub-category-li nested-sub-category-name">'.$sub_category['sub_category_name'].' <span class="product-count-sub-category">('.$sub_category['product_count'].')</span></a>
                                                            </li>';
                                    if($key == (count($subCategoryDetails) - 1))
                                        $category_list_html.= '</ul></li>';
                                } 
                            }
                            if($isDisplayCategory == 0) {
                                $category_list_html .= '<li class="category-details '.$active_class.'"><input type="hidden" class="category-id" value="'.$category->category_id.'"><a href="'.url($this->store_url.'/customer/category/'.strtolower(str_replace(' ', '-', $category->category_name))).'" class="category-name">'.$category->category_name.' <span>('.$category->product_count.')</span></a></li>';
                                $isDisplayCategory++;
                            }
                        }
                        else 
                            $category_list_html .= '<li class="category-details '.$active_class.'"><input type="hidden" class="category-id" value="'.$category->category_id.'"><a href="'.url($this->store_url.'/customer/category/'.strtolower(str_replace(' ', '-', $category->category_name))).'" class="category-name">'.$category->category_name.' <span>('.$category->product_count.')</span></a></li>';
                    }
                }
                $category_list_html .= '</ul>';
            } else {
                $category_list_html .= 
                '<div class="col-lg-4 category-details">
                    <div class="single_featured_banner wow fadeInUp '.$active_class.'" data-wow-delay="0.1s" data-wow-duration="1.1s">
                        <div class="featured_banner_text d-flex justify-content-between align-items-center">
                            <input type="hidden" class="category-id" value="all">
                            <h3><a href="#0">'.trans("customer.all").'</a></h3>
                            <span class="all-product-count">('.($totalProductCount).')</span> 
                        </div>
                    </div>
                </div>'; 
                foreach ($category_details as $category) {
                    if($category->product_count > 0) {
                        $active_class = ($category_id == $category->category_id) ? "active" : "";
                        $category_list_html .= 
                        '<div class="col-lg-4 category-details">
                            <div class="single_featured_banner wow fadeInUp '.$active_class.'" data-wow-delay="0.1s" data-wow-duration="1.1s">
                                <div class="featured_banner_text d-flex justify-content-between align-items-center">
                                    <input type="hidden" class="category-id" value="'.$category->category_id.'">
                                    <h3><a href="#0">'.$category->category_name.'</a></h3>
                                    <span class="category-product-count">('.$category->product_count.')</span>
                                </div>
                            </div>
                        </div>'; 
                    }
                }
            }
        }
        $product_list_by_category = "";
        if($category_id != "all" && $page_type != "product_page" && $page_type != "related_products") {
            $product_list_by_category .= 
                '<div class="product_header">
                    <div class="product_tab_button">
                        <ul class="nav featured_banner_inner sub-category-list-details">';
                            if(isset($sub_category_details)) {
                                $product_list_by_category.= '<li class="sub-category-list" data-sub-category-id="all">
                                                                <a class="active sub-category-li" href="#01">'.trans("customer.all").'<span class="all-sub-category-li"></span></a>
                                                            </li>';
                            }
                            if(!empty($sub_category_details)) {
                                foreach($sub_category_details as $sub_category) {
                                    if($sub_category['product_count'] > 0) {
                                        $product_list_by_category.= '<li class="sub-category-list sub-category-list-'.$sub_category['sub_category_id'].'" data-sub-category-id="'.$sub_category['sub_category_id'].'">
                                                                    <a href="#02" class="sub-category-li">'.$sub_category['sub_category_name'].' <span class="product-count-sub-category">('.$sub_category['product_count'].')</span></a>
                                                                </li>';
                                    }
                                }
                            }
            $product_list_by_category .= '</ul></div></div>';
        }
        $productDetailsArray = $product_details->toArray();
        $product_details = $productDetailsArray['data'];
        if (isset($product_details) && !empty($product_details)) {
            $product_list_by_category .= '<div class="tab-content product_container">';
            if (auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $store_id) 
                $isAuthenticated = 1;
            else 
                $isAuthenticated = 0;
            $product_list_by_category .= '<div class="product_gallery"><input type="hidden" class="is_authenticated" value="' . $isAuthenticated . '"><div class="row">';
            foreach ($product_details as $product) {
                $productImages = !empty($product) && !empty($product['image_path']) ? explode("***", $product['image_path']) : [];
                $product_list_by_category .=
                '<div class="col-lg-4 col-md-4 col-sm-6 sub-category-product-list sub-category-product-' . $product['sub_category_id'] . '">
                    <article class="single_product single-product-details single-product-details-'.$product['product_id'].'">';
                        if (!empty($product_variants_combinations) && isset($product_variants_combinations[$product['product_id']])) {
                            $product_list_by_category .= '<input type="hidden" class="variant-combinations variant-combinations-' . $product['product_id'] . '" value="' . htmlspecialchars(json_encode($product_variants_combinations[$product['product_id']]), ENT_QUOTES, 'UTF-8') . '">';
                        } else {
                            $product_list_by_category .= '<input type="hidden" class="variant-combinations variant-combinations-' . $product['product_id'] . '" value="">';
                        }
                        $product_list_by_category .= '<input type="hidden" class="variant-combinations-images variant-combinations-images-' . $product['product_id'] . '" value="">';
                        $product_id = $product["product_id"];
                        if(($sorting_column == "low_to_high" || $sorting_column == "high_to_low") && $product['type_of_product'] == "variant" && $page_type == "product_page")
                            $variants_id = $variants_name = $product['variants_id'];
                        else 
                            $variants_id = $variants_name = (!empty($product_variants_combinations) && isset($product_variants_combinations[$product['product_id']])) ? key($product_variants_combinations[$product['product_id']]) : "";                     
                        $variants_on_hand = ""; $product_quantity = 1; 
                        $product_unit = $available_quantity = ($product['type_of_product'] == "variant" && !empty($product_variants_combinations) && isset($product_variants_combinations[$product['product_id']])) ? $product_variants_combinations[$product_id][$variants_name]['on_hand'] : $product['unit'];
                        if(!empty($cart_data) && isset($cart_data[$product_id])) {
                            if($product['type_of_product'] == "variant" && isset($cart_data[$product_id][$variants_id])) {
                                $quantity = $cart_data[$product_id][$variants_id]['quantity'];
                                if(!empty($product_unit) && is_numeric($product_unit) && $product_unit >= 0) {
                                    $variants_on_hand = $available_quantity = ($product_unit - $quantity);
                                }
                            } 
                            else if($product['type_of_product'] == "single") {
                                $quantity = $cart_data[$product_id]['quantity'];
                                $product_unit = $available_quantity = $product['unit'] - $quantity;
                            }
                        }
                        $variantsInStock = false;
                        if($product['type_of_product'] == "variant" && !empty($product_variants_combinations) && isset($product_variants_combinations[$product['product_id']])) {
                            foreach ($product_variants_combinations[$product['product_id']] as $variant) {
                                if (((!empty($variant['on_hand']) && $variant['on_hand'] > 0) || ($variant['on_hand'] == "")) && $variant['product_available'] != "out-of-stock") {
                                    $variantsInStock = true;
                                    break;
                                }
                            }
                        }   
                        $singleProductURL = url($store_url . '/customer/single-product/category/' . $product['category_id'] . '/' . strtolower(str_replace(' ', '-', $product['category_name'])) . '/product/' . $product['product_id'] . '/' . strtolower(str_replace(' ', '-', $product['product_name'])));
                        $product_list_by_category .= '<figure>
                            <div class="product_thumb">';
                                if((($product['type_of_product'] == "single" && $product['trackable'] == 1 && $available_quantity <= 0) || ($product['type_of_product'] == "variant" && !$variantsInStock))) {
                                    $product_list_by_category .= '<div class="outofstock_button"><a href="#" title="">'.trans("customer.out_of_stock").'</a></div>';
                                }
                                $product_list_by_category .= '<a class="single-product-url" href="' . $singleProductURL . '" target="_blank"><img class="product-image-path" src="' . (!empty($productImages) ? $productImages[0] : "") . '" alt=""></a>
                                <div class="quickview_button"><a href="#quick-view" title="'.trans("customer.quick_view").'" data-product-type="' . $product['type_of_product'] . '" class="product-quick-view"><span class="pe-7s-shopbag"></span> '.trans("customer.quick_view").'</a></div>
                                <div class="action_links2">
                                    <ul class="d-flex justify-content-center">';
                                        $wishlist_class = "far"; $wishlist_type = "add"; $title = trans("customer.add_to_wishlist");
                                        if (!empty($wishlistData) && isset($wishlistData[$product['product_id']])) { 
                                            $wishlist_class = "fas"; $wishlist_type = "remove"; $title = trans("customer.remove_from_wishlist");
                                        }
                                        $product_list_by_category .=  '<li class="wishlist"><a href="#01" title="'.$title.'" class="product-wishlist"><span data-type="products-in-home" data-wishlist-type="'.$wishlist_type.'" class="wishlist-icon fa-heart '.$wishlist_class.'"></span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <figcaption class="product_content text-center">
                                <div class="justify-content-between"> 
                                    <h4 class="me-2 mb-2 fw-bold text-center"><a href="' . $singleProductURL . '" class="product-name truncate-text" target="_blank">' . $product['product_name'] . '</a></h4>';
                                    if($product['type_of_product'] == "variant" && !empty($product_variants_collection) && isset($product_variants_collection[$product['product_id']])) {
                                        $product_list_by_category .= '<div class="mb-3">';
                                        if(!empty($product_variants_collection) && isset($product_variants_collection[$product['product_id']])) {
                                            $getVariantsOption = $product_variants_collection[$product['product_id']];
                                            foreach($getVariantsOption as $variantsOptions) {
                                                foreach($variantsOptions as $key => $variant) {
                                                    $variantProductURL = url($store_url . '/customer/single-product/category/' . $product['category_id'] . '/' . strtolower(str_replace(' ', '-', $product['category_name'])) . '/product/' . $product['product_id'] . '/' . strtolower(str_replace(' ', '-', $product['product_name'])) .'/'.$variant['variant_options_id']);
                                                    if($key == 0)
                                                        $product_list_by_category .= '<p class="fs-6 mb-1">'.$variant['variants_name'].'</p><div class="d-flex flex-wrap gap-2">';
                                                    $product_list_by_category .= '<a href="' . $variantProductURL . '" target="_blank"><img src="'.$variant['variants_option_image'].'" style="height: 30px;" class="rounded-circle variant-option" title="'.$variant['variant_options_name'].'" alt=""></a>';
                                                    if($key+1 == count($variantsOptions))
                                                        $product_list_by_category .= '</div>';
                                                }
                                            }
                                        }
                                        $product_list_by_category .= '</div>';
                                    }
                                    $allProductImages = !empty($productImages) ? htmlspecialchars(json_encode($productImages, JSON_HEX_QUOT | JSON_HEX_TAG), ENT_QUOTES, 'UTF-8') : "";
                                    $product_list_by_category .= '<input type="hidden" class="product-id single-product-id" value="' . $product['product_id'] . '">
                                    <input type="hidden" class="product-trackable single-product-trackable" value="' . $product['trackable'] . '">
                                    <input type="hidden" class="product-category-name" value="'.$product['category_name'].'">
                                    <input type="hidden" class="product-subcategory-name" value="'.$product['sub_category_name'].'">
                                    <input type="hidden" class="product-unit" value="' . $product['unit'] . '">
                                    <input type="hidden" class="single-product-variants-combination" value="'.($product["type_of_product"] == "variant" ? $variants_id : "").'">
                                    <input type="hidden" class="variant-on-hand" value="'.$product_unit.'">
                                    <input type="hidden" class="modal-variant-on-hand" value="'.$variants_on_hand.'">
                                    <input type="hidden" class="modal-product-unit" value="'.$product_unit.'">
                                    <input type="hidden" class="single-product-type" value="' . $product['type_of_product'] . '">
                                    <input type="hidden" class="single-product-description" value="' . htmlentities($product['product_description'], ENT_QUOTES, 'UTF-8') . '">
                                    <input type="hidden" class="add-product-quantity quantity" value="1"> 
                                    <input type="hidden" class="product-category-images" value="'.$allProductImages.'">
                                    <div class="price_box mb-2">
                                        <span class="current_price fw-bold product-price modal-product-price">';
                                        if ($product["type_of_product"] == "variant") {
                                            $product_list_by_category .=  (!empty($product_variants_combinations) && isset($product_variants_combinations[$product['product_id']]) && isset($product_variants_combinations[$product_id][$variants_name])) ? 'SAR '.number_format($product_variants_combinations[$product_id][$variants_name]['variant_price'], 2, '.', '') : "";
                                        } else {
                                            $product_list_by_category .= ($product['type_of_product'] == "single") ? "SAR " . number_format($product['price'], 2, '.', '') : "";
                                        }
                                        $product_list_by_category .= '</span>
                                    </div>
                                </div>
                            </figcaption>
                        </figure>
                    </article>
                </div>';
            }
            $product_list_by_category .= '</div></div></div>';
        }  
        if(!empty(trim($search_text)) && $product_list_by_category == "") {
            $product_list_by_category = "<h6>No results for ".$search_text."</h6><p>Search instead for ".$search_text."</p>";
        } else {
            if($product_list_by_category == "" && $page_type != "related_products") {
                $product_list_by_category = "<p class='text-center'>Sorry, no products available right now. Check back later for updates!</p>";
                $product_available = 0;
            }
            if($product_list_by_category == "" && $page_type == "related_products")
                $product_list_by_category = "<p style='margin-bottom:30px;' class='text-center'>We're sorry, but this product currently doesn't have any related products.</p>";
        }
        return response()->json(['product_list_by_category'=>$product_list_by_category,'category_list_html'=>$category_list_html,'totalPages' => $totalPages,'currentPage' => $page,'status'=>200]);
    }

    public function variantsByProduct(Request $request) {
        $store_id = $this->store_id;
        $product_id = $request->product_id;
        $type = $request->_type;
        $variantsCombination = Product::leftJoin('store_product_variants_combination', 'store_products.product_id', '=', 'store_product_variants_combination.product_id')
            ->where([
                ['store_products.store_id', '=', $store_id], 
                ['store_products.is_deleted', '=', 0],
                ['status_type', '=', 'publish'],
                ['store_product_variants_combination.is_deleted', '=', 0],
                ['store_products.product_id', '=', $product_id]
            ])
            ->whereIn('store_products.product_type', ['online', 'both'])
            ->get(['variants_combination_id','variants_combination_name','store_products.product_id','variant_price','on_hand','variants_id',\DB::raw('CASE WHEN (on_hand <= 0 AND on_hand IS NOT NULL AND on_hand != "") THEN "out-of-stock" ELSE "" END as product_available')])
            ->groupBy('variants_id')
            ->map(function($group) {
                return $group->first();
            })
            ->toArray();
        if(!empty($variantsCombination)) 
            $variantsCombination = $this->checkVariantOutOfStock($variantsCombination,$product_id);
        if($type != "single-product") {
            $productVariants = Variants::select('variants_name','variants_id')
                ->where([
                    ['store_id', '=', $store_id], 
                    ['is_deleted', '=', 0], 
                    ['product_id', '=', $product_id]
                ])->get()->toArray();
            $variantsOptions = VariantsOption::where([
                    ['store_id', '=', $store_id],
                    ['product_id', '=', $product_id],
                    ['is_deleted', '=', 0],
                ])
                ->get(['variants_id','variant_options_name','variant_options_id','variants_option_image','product_id'])
                ->groupBy(['variants_id'])
                ->toArray();
            $variantImages = VariantProductImage::leftJoin('product_images', 'variant_product_images.product_image_id', '=', 'product_images.image_id')->where([
                    ['variant_product_images.store_id', '=', $store_id],
                    ['variant_product_images.product_id', '=', $product_id],
                    ['variant_product_images.is_deleted', '=', 0],
                    ['product_images.is_deleted', '=', 0],
                ])->get(['image_path','variant_ids'])
                ->groupBy('variant_ids')
                ->toArray();
            return response()->json(['product_variants'=>$productVariants,'variants_options'=>$variantsOptions,'product_variants_combinations'=>$variantsCombination,'variantImages'=>$variantImages]);
        } else 
            return response()->json(['product_variants_combinations'=>$variantsCombination]);
    }

    function checkVariantOutOfStock($variantsCombination,$productIds) { 
        if(!empty($variantsCombination) && !empty($productIds)) {
            $cartData = []; $product_variants_combinations = [];
            if (auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $this->store_id) {
                $cartDetails = ShoppingCart::where([
                    ['store_id', $this->store_id],
                    ['customer_id', auth()->guard('customer')->user()->customer_id],
                    ['is_deleted', 0],
                ])
                ->get(['cart_id','product_id','variants_id','quantity'])->toArray();
                if(!empty($cartDetails)) {
                    foreach ($cartDetails as $cart) {
                        $productId = $cart['product_id'];
                        $variantId = $cart['variants_id'];
                        if (!isset($cartData[$productId])) 
                            $cartData[$productId] = [];
                        if (!empty($variantId)) {
                            if (!isset($cartData[$productId][$variantId])) 
                                $cartData[$productId][$variantId] = [];
                            $cartData[$productId][$variantId] = $cart;
                        } else 
                            $cartData[$productId] = $cart;
                    }
                }
            }
            else
                $cartData = session()->get('cart', []);
            foreach($variantsCombination as $key => $variants) {
                $product_unit = $available_quantity = $variants['on_hand'];
                if(!empty($cartData) && isset($cartData[$variants['product_id']]) && isset($cartData[$variants['product_id']][$variants['variants_id']])) {
                    $quantity = $cartData[$variants['product_id']][$variants['variants_id']]['quantity'];
                    if(!empty($product_unit) && is_numeric($product_unit) && $product_unit >= 0)
                        $available_quantity = ($product_unit - $quantity);
                }
                $variants['product_available']  = (is_numeric($available_quantity) && ($available_quantity <= 0)) ? "out-of-stock" : "";
                $product_variants_combinations[$variants['variants_id']] = $variants;
            }
            return $product_variants_combinations;
        }
    }
}
