<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\Customers\ShoppingCart;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\CashierAdmin\OnlineStoreOrder;
use App\Models\CashierAdmin\OnlineStoreOrderItems;
use App\Models\CashierAdmin\OnlineOrderStatus;
use App\Models\CashierAdmin\OnlinePayment;
use App\Models\StoreAdmin\Product;
use App\Models\StoreAdmin\VariantsOptionCombination;
use App\Models\StoreAdmin\Tax;
use Exception;

class CartController extends ApiController
{
    public function addToCart(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required',
            'customer_id'=> 'required|numeric',
            'product_id'=> 'required|numeric',
            'quantity'=> 'required|numeric',
            'mode'=> 'required|in:add,remove',
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            if($request->mode == "add") {
                $cart_data = ShoppingCart::where([
                    ['product_id', '=', $request->product_id],
                    ['customer_id', '=', $request->customer_id],
                    ['store_id','=',$request->store_id],
                    ['is_deleted','=',0]
                ])
                ->when($request->variants_id, function ($query) use ($request) {
                    $query->where('variants_id',$request->variants_id);
                })
                ->first();
                if ($cart_data) {
                    if($request->type == "cart") 
                        $cart_data->quantity = $request->quantity;
                    else 
                        $cart_data->quantity += $request->quantity;
                    $cart_data->save();
                } else {
                    ShoppingCart::create($request->except('mode'));
                }
                $message = "Product added to cart successfully";
            } elseif($request->mode == "remove") {
                $remove_cart = array();
                $remove_cart['is_deleted'] = 1;  
                $remove_cart['deleted_at'] = Carbon::now()->toDateTimeString();
                ShoppingCart::where([
                    ['product_id', '=', $request->product_id],
                    ['customer_id', '=', $request->customer_id],
                    ['store_id','=',$request->store_id]
                ])
                ->when($request->variants_id, function ($query) use ($request) {
                    $query->where('variants_id',$request->variants_id);
                })
                ->update($remove_cart);
                $message = "Product removed from cart successfully";
            }
            return $this->createResponse($message, self::HTTP_OK, $request->store_id, $request->customer_id);
        } else {
            return $checkStoreId;
        }
    }

    public function cartlist(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required',
            'customer_id'=> 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            $product_details = ShoppingCart::leftJoin('store_products', 'store_products.product_id', '=', 'shopping_cart.product_id')
                ->leftJoin('store_category', 'store_products.category_id', '=', 'store_category.category_id')
                ->leftJoin('store_sub_category', 'store_products.sub_category_id', '=', 'store_sub_category.sub_category_id')
                ->leftJoin('store_product_tax', 'store_products.product_id', '=', 'store_product_tax.product_id')
                ->leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')
                ->leftJoin('store_product_variants_combination', 'store_product_variants_combination.variants_combination_id', '=', 'shopping_cart.variants_id')
                ->where([
                    ['store_products.store_id', '=', $request->store_id],
                    ['store_products.is_deleted', '=', 0],
                    ['store_products.status_type', '=', 'publish'],
                    ['store_products.status', '=', 1],
                    ['store_category.is_deleted', '=', 0],
                    ['store_category.status', '=', 1],
                    ['shopping_cart.is_deleted', '=', 0],
                    ['shopping_cart.customer_id', '=', $request->customer_id],
                ])
                ->where(function ($query) {
                    $query->whereRaw('CASE WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END')
                        ->orWhereNull('store_products.sub_category_id');
                })
                ->whereRaw(('case WHEN (shopping_cart.variants_id > 0) THEN store_product_variants_combination.is_deleted = 0 ELSE TRUE END'))
                ->select('product_name', 'store_products.category_id', 'category_name', 'price', 'store_products.product_id', 'store_products.category_image', 'tax_type', 'tax_amount', 'taxable', 'type_of_product', 'unit', 'trackable', 'variants_combination_name', 'variants_combination_id', 'variant_price', 'on_hand','shopping_cart.quantity as cart_quantity')
                ->selectRaw('CASE WHEN (on_hand <= 0 AND on_hand IS NOT NULL AND on_hand != "") THEN "out-of-stock" ELSE "" END as product_available')
                ->get();
            $tax_details = Tax::where('store_id',$request->store_id)->get(['tax_percentage','tax_id'])->toArray();
            $result = array(
                'tax_details' => $tax_details,
                'product_details' => $product_details
            );
            return $this->createResponse('Cart list according to the customer', self::HTTP_OK, $request->store_id, $request->customer_id,$result);
        } else {
            return $checkStoreId;
        }
    }

    public function checkoutProduct(Request $request) {
        $checkoutData = $request->json('checkout_data');
        $address_id = $checkoutData['address_id'];
        $cartData = $checkoutData['cart_data'];
        $customer_id = $checkoutData['customer_id'];
        $store_id = $checkoutData['store_id'];
        $validator = Validator::make($checkoutData, [ 
            'store_id'=> 'required',
            'customer_id'=> 'required|numeric',
            'address_id' => 'required|numeric',
            'cart_data' => 'required|array',
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($store_id);
        if ($checkStoreId === true) {
            if(!empty($cartData)) {
                $store_order_status = OnlineOrderStatus::select('order_status_id')->where([
                    ['store_id', '=', $store_id],
                    ['is_deleted', '=', 0]
                ])->orderBy('order_number','asc')->limit(1)->get()->toArray();
                $status_id = !empty($store_order_status) && count($store_order_status) > 0 ? $store_order_status[0]['order_status_id'] : 0;
                $subtotal = 0; $totalAmount = 0; $totalTaxAmount = 0; $no_of_products = 0;
                $online_order = [];
                $online_order['customer_id'] = $customer_id;
                $online_order['store_id'] = $store_id;
                $online_order['address_id'] = $address_id;
                foreach ($cartData as $variant) {
                    $quantity = $variant['quantity'];
                    $productPrice = $variant['product_price'];
                    $taxAmount = $variant['tax_amount']; 
                    $subtotal += ($quantity * $productPrice) - $taxAmount;
                    $totalAmount += ($quantity * $productPrice);
                    $totalTaxAmount += $taxAmount;
                }
                $online_order['sub_total_amount'] = $subtotal;
                $online_order['total_amount'] = $totalAmount;
                $online_order['tax_amount'] = $totalTaxAmount;
                $online_order['discount_amount'] = $request->discount_amount;
                $online_order['discount_id'] = $request->discount_id;
                $online_order['no_of_products'] = count($cartData);
                $online_order['online_order_status'] = $status_id;
                $online_order['created_by'] = $address_id;
                $online_order['updated_by'] = $address_id;
                $order_id = OnlineStoreOrder::create($online_order)->online_order_id;
                $insert_payment = [];
                $insert_payment['order_id'] = $order_id;
                // $insert_payment['payment_method'] = !empty($request->payment_method) ? $request->payment_method : 'cash';
                $insert_payment['amount'] = $totalAmount;
                $insert_payment['created_by'] = $customer_id;
                $insert_payment['customer_id'] = $customer_id;
                $insert_payment['store_id'] = $store_id;
                $payment_id = OnlinePayment::create($insert_payment)->online_payment_id;
                $update_order = array();
                $update_order['order_number'] = "ORDER".sprintf("%03d",$order_id);
                $update_order['payment_id'] = $payment_id;
                OnlineStoreOrder::where('online_order_id',$order_id)->update($update_order);
                foreach ($cartData as $variant) {
                    // foreach ($item as $variant) {
                        $product_variants = [];
                        $product_variants['store_id'] = $store_id;
                        $product_variants['customer_id'] = $customer_id;
                        $product_variants['order_id'] = $order_id;
                        $product_variants['product_id'] = $variant['product_id'];
                        $product_variants['variants_id'] = ($variant['variants_id'] != "") ? $variant['variants_id'] : 0;
                        $product_variants['product_variants'] = ($variant['variant_combination_name'] != "-" && $variant['variant_combination_name'] != "") ? $variant['variant_combination_name'] : "";
                        $product_variants['quantity'] = $variant['quantity'];
                        $product_variants['sub_total'] = $variant['quantity'] * $variant['product_price'];
                        $product_variants['tax_amount'] = $variant['tax_amount'];
                        $product_variants['created_by'] = $customer_id;
                        $product_variants['updated_by'] = $customer_id;
                        $online_order_items_id = OnlineStoreOrderItems::create($product_variants)->online_order_items_id;
                        if(!empty($variant['variants_id'])) {
                            $product_details = VariantsOptionCombination::where([
                                ['store_id', '=', $store_id],
                                ['variants_combination_id', '=', $variant['variants_id']]
                            ])->get(['on_hand']);
                            $get_unit = (!empty($product_details) && count($product_details) > 0 && !empty($product_details[0]['on_hand'])) ? $product_details[0]['on_hand'] : 0;
                            $unit = $get_unit - $variant['quantity'];
                            $update_product = array();
                            $update_product['on_hand'] = ($unit > 0) ? $unit : 0;
                            VariantsOptionCombination::where('variants_combination_id',$variant['variants_id'])->update($update_product);
                        } else {
                            $product_details = Product::where([
                                ['store_id', '=', $store_id],
                                ['product_id', '=', $variant['product_id']]
                            ])->get(['unit']);
                            $get_unit = (!empty($product_details) && count($product_details) > 0 && !empty($product_details[0]['on_hand'])) ? $product_details[0]['unit'] : 0;
                            $unit = $get_unit - $variant['quantity'];
                            $update_product = array();
                            $update_product['unit'] = ($unit > 0) ? $unit : 0;
                            Product::where('product_id',$variant['product_id'])->update($update_product);
                        }
                        
                    // }
                }
            } else {
                return $this->createResponse('The cart_data must be a valid JSON structure.', self::HTTP_UNPROCESSABLE_ENTITY);
            }
            $remove_cart = array();
            $remove_cart['is_deleted'] = 1;  
            $remove_cart['deleted_at'] = Carbon::now()->toDateTimeString();
            ShoppingCart::where([
                ['customer_id', '=', $customer_id],
                ['store_id','=',$store_id]
            ])
            ->update($remove_cart);
            return $this->createResponse('Product ordered successfully', self::HTTP_OK);
        } else {
            return $checkStoreId;
        }
    }
}
