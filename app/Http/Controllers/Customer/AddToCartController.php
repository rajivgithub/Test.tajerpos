<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\StoreAdmin\Product;
use App\Http\Controllers\CommonController;
use Session;
use App\Models\StoreAdmin\Tax;
use App\Models\Customers\ShoppingCart;
use App\Models\StoreAdmin\Variants;
use Auth;
use DB;
use App\Models\StoreAdmin\VariantProductImage;
use App\Models\StoreAdmin\ProductImage;

class AddToCartController extends Controller
{
    protected $store_url;
    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
    }

    public function addToCart(Request $request) {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        $variants_combination_id = $request->input('product_variants_combination');
        $type = $request->input('type');
        $remove_variant_id = $request->input('remove_variant_id');
        $store_id = CommonController::get_store_id();
        if(auth()->guard('customer')->check()) {
            $customer_id = auth()->guard('customer')->user()->customer_id;
            if(!empty($remove_variant_id)) {
                ShoppingCart::where('store_id', $store_id)
                    ->where('customer_id', $customer_id)
                    ->when($type != 'delete' && !empty($productId),function($query) use ($productId) {
                        return $query->where('product_id', $productId);
                    })
                    ->when($type != 'delete' && !empty($remove_variant_id),function($query) use ($remove_variant_id) {
                        return $query->where('variants_id', $remove_variant_id);
                    })
                    ->when($type == 'delete' && !empty($remove_variant_id),function($query) use ($remove_variant_id) {
                        return $query->where('cart_id', $remove_variant_id);
                    })->update(['is_deleted' => 1]);
            }
            if($type != 'delete') {
                $add_to_cart = $request->except('_token');
                $add_to_cart['store_id'] = $store_id;
                $add_to_cart['customer_id'] = $customer_id;
                $add_to_cart['variants_id'] = $variants_combination_id;
                $existingCart = ShoppingCart::where('store_id', $store_id)
                    ->where('customer_id', $customer_id)
                    ->where('product_id', $productId)
                    ->where('is_deleted', 0)
                    ->when(!empty($variants_combination_id),function($query) use ($variants_combination_id) {
                        return $query->where('variants_id', $variants_combination_id);
                    })
                    ->first();
                if ($existingCart) {
                    if($type == "add_to_cart_page")
                        $existingCart->quantity = $quantity;
                    else
                        $existingCart->quantity += $quantity;
                    $existingCart->save();
                } else {
                    ShoppingCart::create($add_to_cart);
                }
            }
        } else {
            $isAuthenticated = $request->isAuthenticated;
            if($type != "" && $type == "add_to_cart_page" && !$isAuthenticated) {
                Session::forget('cart');
            } 
            if($type != "" && $type == "add_to_cart_page") {
                $cart_data = $request->input("cart_data");
                if(!empty($cart_data)) {
                    $cart = array_filter($cart_data);
                    /*if (!empty($remove_variant_id) && isset($cart[$productId]) && (!empty($remove_variant_id) && isset($cart[$productId][$remove_variant_id]))) {
                        unset($cart[$productId][$remove_variant_id]);
                        if (empty($cart[$productId])) {
                            unset($cart[$productId]);
                        }
                    }*/
                }
                    
            } else {
                $cart = session()->get('cart', []);
                if (isset($cart[$productId]) && ((!empty($variants_combination_id) && isset($cart[$productId][$variants_combination_id])) || ($variants_combination_id == ""))) {
                    if($variants_combination_id == "")
                        $cart[$productId]['quantity'] += $quantity;
                    else
                        $cart[$productId][$variants_combination_id]['quantity'] += $quantity;
                } else {
                    if($variants_combination_id == "") {
                        $cart[$productId] = [
                            'quantity' => $quantity,
                            'product_id' => $productId
                        ];
                    } else {
                        $cart[$productId][$variants_combination_id] = [
                            'quantity' => $quantity,
                            'variants_combination_id' => $variants_combination_id,
                            'product_id' => $productId
                        ];
                    }
                }
            }
            session()->put('cart', $cart);
            $cart_data = session()->get('cart', []);
            $total_quantity = 0;
            foreach ($cart_data as $key => $cart) {
                if (isset($cart['quantity'])) {
                    $total_quantity += $cart['quantity'];
                } else {
                    foreach ($cart as $variant) {
                        $total_quantity += $variant['quantity'];
                    }
                }
            }
            session()->put('cart_total_quantity', $total_quantity);
        }
        return response()->json(['success' => trans('customer.product_added_to_cart_success')]);
    }

    public function viewCart() {
        $store_url = $this->store_url;
        $store_id = CommonController::get_store_id();
        $all_variants = []; $get_quantity = []; $product_ids = []; $product_details = []; $cartData = [];
        if (auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $store_id) {
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
                    if (!isset($cartData[$productId])) 
                        $cartData[$productId] = [];
                    if (!empty($variantId)) {
                        if (!isset($cartData[$productId][$variantId])) 
                            $cartData[$productId][$variantId] = [];
                        $cartData[$productId][$variantId] = $cart;
                    } else 
                        $cartData[$productId] = $cart;
                    if (!in_array($productId, $product_ids)) 
                        $product_ids[] = $productId;
                    if (!empty($variantId) && !in_array($variantId, $all_variants)) 
                        $all_variants[] = $variantId;
                }
            }
        } else {
            $cart_data = session()->get('cart', []);
            if(!empty($cart_data)) {
                foreach($cart_data as $k => $product) {
                    $product_ids[] = $k;
                    if(!empty($product)) {
                        foreach($product as $key => $val) {
                            if(is_array($val)) {
                                $all_variants[] = $key;
                                $get_quantity[$k][$key] = $val['quantity'];
                            } else {
                                $get_quantity[$k] = $product['quantity'];
                            }
                        }
                    }
                }
            } 
        }
        if(!empty($product_ids)) {
            $product_details = Product::leftJoin('store_category', 'store_products.category_id', '=', 'store_category.category_id')
            ->leftJoin('store_sub_category', 'store_products.sub_category_id', '=', 'store_sub_category.sub_category_id')
            ->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')
            ->leftJoin('store_product_variants',function ($join) use ($product_ids) {
                $join->on('store_products.product_id','=','store_product_variants.product_id')
                ->where('store_product_variants.is_deleted',0)
                ->whereIn('store_products.product_id',$product_ids);
            })
            ->leftJoin('store_product_variants_combination', function ($join) use ($all_variants) {
                $join->on('store_products.product_id', '=', 'store_product_variants_combination.product_id')
                    ->whereIn('store_product_variants_combination.variants_id', $all_variants);
            })
            ->where([
                ['store_products.store_id', '=', $store_id],
                ['store_products.is_deleted', '=', 0],
                ['store_products.status_type', '=', 'publish'],
                ['store_products.status', '=', 1],
                ['store_category.is_deleted', '=', 0],
                ['store_category.status', '=', 1],
            ])
            ->whereRaw(('case WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END'))
            ->whereRaw(('case WHEN (store_products.type_of_product = "variant") THEN store_product_variants_combination.is_deleted = 0 ELSE TRUE END'))
            ->whereIn('store_products.product_id', $product_ids)
            ->orderBy('store_products.category_id', 'desc')
            ->leftJoin('variant_product_images', function ($join) use ($all_variants) {
                $join->on('store_product_variants_combination.variants_id', '=', 'variant_product_images.variant_ids')
                    ->whereIn('variant_product_images.variant_ids', $all_variants)
                    ->where('variant_product_images.is_deleted', '=', 0)
                    ->whereRaw('variant_product_images.product_image_id = (SELECT product_image_id FROM variant_product_images WHERE variant_ids = store_product_variants_combination.variants_id AND is_deleted = 0 LIMIT 1)');
            })
            ->leftJoin('product_images AS variant_images', function ($join) {
                $join->on('variant_product_images.product_image_id', '=', 'variant_images.image_id')
                    ->where('variant_images.is_deleted', '=', 0);
            })
            ->leftJoin('product_images AS single_images', function ($join) use ($product_ids) {
                $join->on('store_products.product_id', '=', 'single_images.product_id')
                    ->whereIn('single_images.product_id', $product_ids)
                    ->where('single_images.is_deleted', '=', 0)
                    ->whereRaw('single_images.image_id = (SELECT MIN(image_id) FROM product_images WHERE product_id = store_products.product_id)');
            })
            ->select([
                'product_name', 'price', 'store_products.product_id', 'taxable', 'category_name', 'sub_category_name','store_products.category_id',
                'type_of_product', 'unit', 'trackable', 'variants_combination_name', 'variants_combination_id', 'variant_price', 'on_hand', 'store_product_variants_combination.variants_id', 'product_description',
                \DB::raw('CASE WHEN (on_hand <= 0 AND on_hand IS NOT NULL AND on_hand != "") THEN "out-of-stock" ELSE "" END as product_available'),
                \DB::raw('CASE WHEN type_of_product = "variant" THEN variant_images.image_path ELSE single_images.image_path END AS image_path'),
                \DB::raw('CASE WHEN type_of_product = "variant" THEN variant_images.image_id ELSE single_images.image_id END AS image_id'),
                \DB::raw('CASE WHEN type_of_product = "variant" THEN GROUP_CONCAT(variants_name SEPARATOR "***") ELSE NULL END as variants_name'),
            ])
            ->groupBy(['store_products.product_id', 'product_name', 'price', 'taxable', 'type_of_product', 'unit', 'trackable', 'variants_combination_name', 'variants_combination_id', 'variant_price', 'on_hand', 'store_product_variants_combination.variants_id', 'image_path', 'image_id', 'category_name', 'sub_category_name', 'product_description','category_id'])
            ->get();
        }
        $tax_details = Tax::where('store_id',$store_id)->get(['tax_percentage','tax_id'])->toArray();
        return view('customer.view_cart', compact('store_url','product_details','get_quantity','tax_details','store_id','cartData'));
    }

    public function quantityBySession(Request $request) {
        $variantsCombination = [];
        $store_id = CommonController::get_store_id();
        $product_id = $request->product_id;
        $variant_combination_id = $request->variant_combination_id;
        $quantity = 0;
        if (auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $store_id) {
            $quantity = ShoppingCart::where([
                ['product_id', $product_id],
                ['store_id', $store_id],
                ['customer_id', auth()->guard('customer')->user()->customer_id],
                ['is_deleted', 0],
            ])
            ->when(!empty($variant_combination_id), function($query) use ($variant_combination_id) {
                return $query->where('variants_id', $variant_combination_id);
            })
            ->value('quantity') ?? 0;
        } else {
            $cart_data = session()->get('cart', []);
            if(!empty($cart_data) && isset($cart_data[$product_id])) {
                $product_type = $request->product_type;
                if($product_type == "variant" && isset($cart_data[$product_id][$variant_combination_id])) {
                    $quantity = $cart_data[$product_id][$variant_combination_id]['quantity'];
                } else if($product_type == "single") {
                    $quantity = $cart_data[$product_id]['quantity'];
                }
            }
        }
        return response()->json(['quantity'=>$quantity]);
    }
    public function getProductCount() {
        $store_id = CommonController::get_store_id();
        if (auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $store_id) {
            $cart_total_quantity = DB::table('shopping_cart')->where([
                ['store_id','=',$store_id],
                ['customer_id','=',auth()->guard('customer')->user()->customer_id],
                ['is_deleted','=',0],
            ])
            ->sum('quantity');  
        }
        else 
            $cart_total_quantity = session()->get('cart_total_quantity');
        return response()->json(['cart_total_quantity'=>$cart_total_quantity]);
    }
}
