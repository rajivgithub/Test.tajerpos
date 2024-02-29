<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\Product;
use App\Http\Controllers\CommonController;
use Session;
use App\Models\Country;
use App\Models\Customers\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\CashierAdmin\OnlineStoreOrder;
use App\Models\CashierAdmin\OnlineStoreOrderItems;
use App\Models\CashierAdmin\OnlinePayment;
use App\Models\StoreAdmin\VariantsOptionCombination;
use App\Models\CashierAdmin\OnlineOrderStatus;
use DB;
use Carbon\Carbon;
use App\Models\StoreAdmin\Tax;
use App\Models\Customers\ShoppingCart;
use Illuminate\Support\Facades\Http;
use App\Models\StoreAdmin\Shipping;
use App\Models\Admin\Store;


class CheckoutController extends Controller
{

    protected $store_url;
    protected $redirect_url;
    public function __construct() {
        $current_url = request()->path();
        $split_url = explode("/",$current_url);
        $split_url_index = config('app.split_url_index');
        $this->store_url = (!empty($split_url)) ?$split_url[$split_url_index] : '';
    }

    //To generate a business parameter signature
    private function getContentDigest($accountNumber,$accountPassword,$privateKey){
        $hashPassword = strtoupper($accountNumber.md5($accountPassword.'jadada236t2')).$privateKey;
        return base64_encode(pack('H*', strtoupper(md5($hashPassword))));
    }

    //To generate a Headers signature
    private function getHeaderDigest($post,$privateKey){
        $digest = base64_encode(pack('H*',strtoupper(md5(json_encode($post,JSON_UNESCAPED_UNICODE).$privateKey))));
        return $digest;
    }

    public function productCheckout() {
        $store_url = $this->store_url;
        $store_id = CommonController::get_store_id();
        $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
        $all_variants = []; $product_ids = []; $product_details = []; 
        $cartDetailsQuery = ShoppingCart::where([
            ['store_id', $store_id],
            ['customer_id', auth()->guard('customer')->user()->customer_id],
            ['is_deleted', 0],
        ])
        ->get(['cart_id','product_id','variants_id','quantity']);
        $product_ids = $cartDetailsQuery->pluck('product_id')->toArray();
        $all_variants = $cartDetailsQuery->pluck('variants_id')->toArray();
        $cartData = $cartDetailsQuery->groupBy(['product_id','variants_id'])->toArray();
        if (!empty($cartData)) {
            $cartData = array_map(function ($products) {
                $resetProducts = array_map('reset', $products);
                if(array_key_exists("",$resetProducts)) {
                    $resetProducts[0] = $resetProducts[""];
                    unset($resetProducts[""]);
                }
                return $resetProducts;
            }, $cartData);
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
                ->leftJoin('product_images AS variant_images', function ($join) use ($product_ids) {
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
                    'product_name', 'price', 'store_products.product_id', 'taxable', 'category_name', 'sub_category_name',
                    'type_of_product', 'unit', 'trackable', 'variants_combination_name', 'variants_combination_id', 'variant_price', 'on_hand', 'store_product_variants_combination.variants_id', 'product_description',
                    \DB::raw('CASE WHEN (on_hand <= 0 AND on_hand IS NOT NULL AND on_hand != "") THEN "out-of-stock" ELSE "" END as product_available'),
                    \DB::raw('CASE WHEN type_of_product = "variant" THEN variant_images.image_path ELSE single_images.image_path END AS image_path'),
                    \DB::raw('CASE WHEN type_of_product = "variant" THEN GROUP_CONCAT(variants_name SEPARATOR "***") ELSE NULL END as variants_name'),
                ])
                ->groupBy(['store_products.product_id', 'product_name', 'price', 'taxable', 'type_of_product', 'unit', 'trackable', 'variants_combination_name', 'variants_combination_id', 'variant_price', 'on_hand', 'store_product_variants_combination.variants_id', 'image_path', 'category_name', 'sub_category_name', 'product_description'])
                ->get()->toArray();
        }
        $address_details = Address::leftJoin('countries', 'customer_address.country_id', '=', 'countries.id')->leftJoin('states', 'customer_address.state_id', '=', 'states.id')->leftJoin('cities', 'customer_address.city_id', '=', 'cities.id')->where([
            ['store_id', '=', $store_id],
            ['customer_id', '=', $customer_id]
        ])->get(['address_id','customer_name','mobile_number','street_name','building_name','customer_address.country_id','customer_address.state_id','customer_address.city_id','pincode','address_type','landmark','countries.name as country_name','states.name as state_name','cities.name as city_name','email_address']);
        $countries = Country::get(['id','name']);
        $tax_details = Tax::where('store_id',$store_id)->get(['tax_percentage','tax_id'])->toArray();
        return view('customer.checkout', compact('store_url','store_id','product_details','countries','address_details','tax_details','cartData'));
    }

    public function paytabs(Request $request){
        $address_id = (!empty($request->address_id)) ? $request->address_id : 0;
        $store_id = CommonController::get_store_id();
        // $cart_data = $request->cart_data;
        // $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
        // if(!empty($cart_data)) {
        //     $store_order_status = OnlineOrderStatus::select('order_status_id')->where([
        //         ['store_id', '=', $store_id],
        //         ['is_deleted', '=', 0]
        //     ])->orderBy('order_number','asc')->limit(1)->get()->toArray();
        //     $status_id = !empty($store_order_status) ? $store_order_status[0]['order_status_id'] : 0;
        //     $placeorder_data = array_filter(json_decode($cart_data));
        //     $subtotal = 0; $totalAmount = 0; $totalTaxAmount = 0; $no_of_products = 0;
        //     if(!empty($placeorder_data)) {
        //         $online_order = [];
        //         $online_order['customer_id'] = $customer_id;
        //         $online_order['store_id'] = $store_id;
        //         $online_order['address_id'] = $address_id;
        //         foreach ($placeorder_data as $item) {
        //             $no_of_products += count((array)$item);
        //             foreach ($item as $variant) {
        //                 $quantity = $variant->quantity;
        //                 $productPrice = $variant->product_price;
        //                 $taxAmount = $variant->tax_amount; 
        //                 $subtotal += ($quantity * $productPrice) - $taxAmount;
        //                 $totalAmount += ($quantity * $productPrice);
        //                 $totalTaxAmount += $taxAmount;
        //             }
        //         }
        //         $online_order['sub_total_amount'] = $subtotal;
        //         $online_order['total_amount'] = $totalAmount;
        //         $online_order['tax_amount'] = $totalTaxAmount;
        //         $online_order['discount_amount'] = $request->discount_amount;
        //         $online_order['discount_id'] = $request->discount_id;
        //         $online_order['no_of_products'] = $no_of_products;
        //         $online_order['online_order_status'] = $status_id;
        //         $online_order['created_by'] = $customer_id;
        //         $online_order['updated_by'] = $customer_id;


        $amount = $request->id;

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://secure.paytabs.sa/payment/request',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>'{
                    "profile_id": 106571,
                    "tran_type": "sale",
                    "tran_class": "ecom",
                    "cart_id": "cart_11111",
                    "cart_currency": "SAR",
                    "cart_amount": "'.$amount.'",
                    "cart_description": "Description of the items/services",
                    "paypage_lang": "en",
                    "customer_details": {
                        "name": "first last",
                        "email": "email@domain.com",
                        "phone": "0522222222",
                        "street1": "address street",
                        "city": "dubai",
                        "state": "du",
                        "country": "AE",
                        "zip": "12345"
                    },
                    "shipping_details": {
                        "name": "name1 last1",
                        "email": "email1@domain.com",
                        "phone": "971555555555",
                        "street1": "street2",
                        "city": "dubai",
                        "state": "dubai",
                        "country": "AE",
                        "zip": "54321"
                    },
                    "callback": "https://dev.tajerpos.com/TajerPOS/customer/checkout",
                    "return": ""
                }',
                  CURLOPT_HTTPHEADER => array(
                    'Authorization: SRJNHD96GB-JHJMMTKJ6B-TK22TNRRKJ'
                  ),
                ));
                
                $response = curl_exec($curl);
                curl_close($curl);
                $response = json_encode($response,TRUE);


                return $response;






        //     } 



        // }

    }

    public function placeorder(Request $request) {
        $store_id = CommonController::get_store_id();
        $address_id = (!empty($request->address_id)) ? $request->address_id : 0;
        $cart_data = $request->cart_data;
        $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
        $shipping_details = Shipping::where([
            ['store_id', '=', $store_id],
            ['is_deleted', '=', 0]
        ])->get(['api_account', 'private_key','account_number','account_password'])->toArray();
        if(!empty($shipping_details) && isset($shipping_details[0])) {
            $productsID = $variantsID = [];
            $apiAccount = $shipping_details[0]['api_account'];
            $privateKey = $shipping_details[0]['private_key'];
            $accountNumber = $shipping_details[0]['account_number'];
            $accountPassword = $shipping_details[0]['account_password'];
            $businessParameterSignature = $this->getContentDigest($accountNumber,$accountPassword,$privateKey);
            $store_details = Store::join('users', 'stores.store_id', '=', 'users.store_id')->leftJoin('countries', 'users.country_id', '=', 'countries.id')->leftJoin('states', 'users.state_id', '=', 'states.id')->leftJoin('cities', 'users.city_id', '=', 'cities.id')->where([
                ['stores.store_id','=', $store_id],
                ['stores.is_deleted','=', 0],
                ['is_store','=','Yes'],
                ['is_admin','=',2],
            ])->get(['stores.store_id','store_user_name','store_name','store_phone_number','postal_code','street_name','building_name','email','users.id as user_id','cities.name as city_name','states.name as state_name','countries.name as country_name'])->toArray();
            $address_details = Address::leftJoin('countries', 'customer_address.country_id', '=', 'countries.id')->leftJoin('states', 'customer_address.state_id', '=', 'states.id')->leftJoin('cities', 'customer_address.city_id', '=', 'cities.id')->where([
                ['store_id', '=', $store_id],
                ['customer_id', '=', $customer_id],
                ['address_id','=',$request->address_id]
            ])->get(['address_id','customer_name','mobile_number','street_name','building_name','customer_address.country_id','customer_address.state_id','customer_address.city_id','pincode','address_type','landmark','countries.name as country_name','states.name as state_name','cities.name as city_name','email_address','area'])->toArray();
            $post = array();
            $post['customerCode'] = $accountNumber;
            $post['digest'] = $businessParameterSignature;
            $post['expressType'] = 'EZKSA';
            $post['orderType'] = '1';
            $post['serviceType'] = '02';
            $post['deliveryType'] = '04';
            //sender details 
            if(!empty($store_details)) {
                $sender = array();
                $sender['town'] = '';
                $sender['street'] = $store_details[0]['street_name'];
                $sender['city'] = $store_details[0]['city_name'];
                $sender['mobile'] = $store_details[0]['store_phone_number'];
                $sender['phone'] = $store_details[0]['store_phone_number'];
                $sender['countryCode'] = 'KSA';
                $sender['name'] = $store_details[0]['store_name'];
                $sender['postCode'] = $store_details[0]['postal_code'];
                $sender['prov'] = $store_details[0]['state_name'];
                $sender['address'] = '';
                if (!empty($sender['area'])) {
                    $sender['address'] .= trim($sender['area']) . ', ';
                }
                if (!empty($sender['street'])) {
                    $sender['address'] .= trim($sender['street']) . ', ';
                }
                if (!empty($sender['city'])) {
                    $sender['address'] .= trim($sender['city']) . ', ';
                }
                if (!empty($sender['prov'])) {
                    $sender['address'] .= trim($sender['prov']) . ', ';
                }
                if (!empty($sender['countryCode'])) {
                    $sender['address'] .= trim($sender['countryCode']);
                }
                $post['sender'] = $sender;
            }
            //receiver details
            if(!empty($address_details) && isset($address_details[0])) {
                $receiver = array();
                $receiver['area'] = $address_details[0]['area'];
                $receiver['town'] = '';
                $receiver['street'] = $address_details[0]['street_name'];
                $receiver['city'] = $address_details[0]['city_name'];
                $receiver['mobile'] = $address_details[0]['mobile_number'];
                $receiver['phone'] = $address_details[0]['mobile_number'];
                $receiver['countryCode'] = 'KSA';
                $receiver['name'] = $address_details[0]['customer_name'];
                $receiver['postCode'] = $address_details[0]['pincode'];
                $receiver['prov'] = $address_details[0]['state_name'];
                $receiver['address'] = '';
                if (!empty($receiver['area'])) {
                    $receiver['address'] .= trim($receiver['area']) . ', ';
                }
                if (!empty($receiver['street'])) {
                    $receiver['address'] .= trim($receiver['street']) . ', ';
                }
                if (!empty($receiver['city'])) {
                    $receiver['address'] .= trim($receiver['city']) . ', ';
                }
                if (!empty($receiver['prov'])) {
                    $receiver['address'] .= trim($receiver['prov']) . ', ';
                }
                if (!empty($receiver['countryCode'])) {
                    $receiver['address'] .= trim($receiver['countryCode']);
                }
                $post['receiver'] = $receiver;
            }
        }
        if(!empty($cart_data)) {
            $store_order_status = OnlineOrderStatus::select('order_status_id')->where([
                ['store_id', '=', $store_id],
                ['is_deleted', '=', 0]
            ])->orderBy('order_number','asc')->limit(1)->get()->toArray();
            $status_id = !empty($store_order_status) ? $store_order_status[0]['order_status_id'] : 0;
            $placeorder_data = array_filter(json_decode($cart_data));
            $subtotal = 0; $totalAmount = 0; $totalTaxAmount = 0; $no_of_products = 0;
            if(!empty($placeorder_data)) {
                $online_order = [];
                $online_order['customer_id'] = $customer_id;
                $online_order['store_id'] = $store_id;
                $online_order['address_id'] = $address_id;
                foreach ($placeorder_data as $product_id => $item) {
                    if(!empty($shipping_details) && isset($shipping_details[0]))
                        $productsID[] = $product_id;
                    $no_of_products += count((array)$item);
                    foreach ($item as $variants_id => $variant) {
                        if(!empty($variants_id) && !empty($shipping_details) && isset($shipping_details[0]))
                            $variantsID[] = $variants_id;
                        $quantity = $variant->quantity;
                        $productPrice = $variant->product_price;
                        $taxAmount = $variant->tax_amount; 
                        $subtotal += ($quantity * $productPrice) - $taxAmount;
                        $totalAmount += ($quantity * $productPrice);
                        $totalTaxAmount += $taxAmount;
                    }
                }
                $online_order['sub_total_amount'] = $subtotal;
                $online_order['total_amount'] = $totalAmount;
                $online_order['tax_amount'] = $totalTaxAmount;
                $online_order['discount_amount'] = $request->discount_amount;
                $online_order['discount_id'] = $request->discount_id;
                $online_order['no_of_products'] = $no_of_products;
                $online_order['online_order_status'] = $status_id;
                $online_order['created_by'] = $customer_id;
                $online_order['updated_by'] = $customer_id;
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
                $update_order['order_number'] = $order_number = "ORDER".sprintf("%03d",$order_id);
                $update_order['payment_id'] = $payment_id;
                OnlineStoreOrder::where('online_order_id',$order_id)->update($update_order);
                foreach ($placeorder_data as $product_id => $item) {
                    foreach ($item as $variant) {
                        $product_variants = [];
                        $product_variants['store_id'] = $store_id;
                        $product_variants['customer_id'] = $customer_id;
                        $product_variants['order_id'] = $order_id;
                        $product_variants['product_id'] = $product_id;
                        $product_variants['variants_id'] = ($variant->variants_id != "") ? $variant->variants_id : 0;
                        $product_variants['product_variants'] = ($variant->variant_combination_name != "-" && $variant->variant_combination_name != "") ? $variant->variant_combination_name : "";
                        $product_variants['quantity'] = $variant->quantity;
                        $product_variants['sub_total'] = $variant->quantity * $variant->product_price;
                        $product_variants['tax_amount'] = $variant->tax_amount;
                        $product_variants['created_by'] = $customer_id;
                        $product_variants['updated_by'] = $customer_id;
                        $online_order_items_id = OnlineStoreOrderItems::create($product_variants)->online_order_items_id;
                        if(!empty($variant->variants_id)) {
                            $product_details = VariantsOptionCombination::where([
                                ['store_id', '=', $store_id],
                                ['variants_id', '=', $variant->variants_id]
                            ])->get(['on_hand']);
                            $get_unit = (!empty($product_details) && !empty($product_details[0]['on_hand'])) ? $product_details[0]['on_hand'] : 0;
                            $unit = $get_unit - $variant->quantity;
                            $update_product = array();
                            $update_product['on_hand'] = ($unit > 0) ? $unit : 0;
                            VariantsOptionCombination::where('variants_id',$variant->variants_id)->update($update_product);
                        } else {
                            $product_details = Product::where([
                                ['store_id', '=', $store_id],
                                ['product_id', '=', $product_id]
                            ])->get(['unit']);
                            $get_unit = !empty($product_details) ? $product_details[0]['unit'] : 0;
                            $unit = $get_unit - $variant->quantity;
                            $update_product = array();
                            $update_product['unit'] = ($unit > 0) ? $unit : 0;
                            Product::where('product_id',$product_id)->update($update_product);
                        }
                        
                    }
                }
            }
            if(!empty($shipping_details) && isset($shipping_details[0]) && isset($productsID) && !empty($productsID)) {
                $product_details = Product::leftJoin('store_price', 'store_products.product_id', '=', 'store_price.product_id')
                ->leftJoin('store_product_variants_combination', function ($join) use ($variantsID) {
                    $join->on('store_products.product_id', '=', 'store_product_variants_combination.product_id')
                        ->whereIn('store_product_variants_combination.variants_id', $variantsID);
                })
                ->where([
                    ['store_products.store_id', '=', $store_id],
                    ['store_products.is_deleted', '=', 0],
                    ['store_products.status_type', '=', 'publish'],
                    ['store_products.status', '=', 1],
                ])
                ->whereRaw(('case WHEN (store_products.type_of_product = "variant") THEN store_product_variants_combination.is_deleted = 0 ELSE TRUE END'))
                ->whereIn('store_products.product_id', $productsID)
                ->select(['type_of_product', 'product_name', 'price', 'store_products.product_id', 'variants_combination_name', 'variants_id', 'variant_price', 'on_hand'])
                ->get()->toArray();             
                $items = [];
                if(!empty($product_details)) {
                    foreach ($product_details as $product) {
                        $item = [
                            'number' => 1,
                            'itemType' => 'ITN1',
                            'priceCurrency' => 'SAR',
                        ];
                        if ($product['type_of_product'] === 'single') {
                            $item['itemValue'] = $product['price'];
                            $item['itemName'] = $product['product_name'];
                        } elseif ($product['type_of_product'] === 'variant') {
                            $item['itemValue'] = $product['variant_price'];
                            $item['itemName'] = $product['product_name'].' '.$product['variants_combination_name'];
                        }
                        $items[] = $item;
                        $post['items'] = $items;
                    }   
                    $post['txlogisticId'] = $order_number;
                    $post['totalQuantity'] = $no_of_products;
                    $post['itemsValue'] = ($totalAmount * 0.98);
                    $post['priceCurrency'] = 'AED';   
                    $post['goodsType'] = 'ITN1';
                    $post['weight'] = '0.02';
                    $post['operateType'] = 1;
                    $post['isUnpackEnabled'] = 1; 
                    $headerDigest = $this->getHeaderDigest($post, $privateKey);
                    $jsonPayload = json_encode($post,JSON_UNESCAPED_UNICODE);
                    $saudiTime = Carbon::now('Asia/Riyadh');
                    $formattedSaudiTime = $saudiTime->format('Y-m-d H:i:s');
                    $httpHeader = [
                        'apiAccount: ' . $apiAccount,
                        'digest: ' . $headerDigest,
                        'timestamp: ' . strtotime($formattedSaudiTime) * 1000, // Convert to milliseconds
                        'Content-Type: application/x-www-form-urlencoded',
                    ];
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://demoopenapi.jtjms-sa.com/webopenplatformapi/api/order/addOrder',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => 'bizContent=' . $jsonPayload, 
                        CURLOPT_HTTPHEADER => $httpHeader,
                    ));
                    $response = curl_exec($curl);
                    $shippingResponse = json_decode($response, true);
                    // Check if decoding was successful
                    if ($shippingResponse && isset($shippingResponse['data']['billCode'])) {
                        $update_order = array();
                        $update_order['shipping_order_id'] = $shippingResponse['data']['billCode'];
                        OnlineStoreOrder::where('online_order_id',$order_id)->update($update_order);
                    }
                    curl_close($curl);
                }
            }
            ShoppingCart::where([
                ['store_id', $store_id],
                ['customer_id', $customer_id],
                ['is_deleted', 0],
            ])->update(['is_deleted' => 1]);
        }
        Session::forget('cart');
        Session::forget('cart_total_quantity');
        //Payment Gateway

        $cus_name = Auth::guard('customer')->user()->customer_name;
        $cus_phone = Auth::guard('customer')->user()->customer_phone_number; 
        $cus_email = Auth::guard('customer')->user()->customer_email; 
        $cus_details = DB::table('instore_customers')
            ->select('instore_customers.*')
            ->where('customer_id',$customer_id)
            ->first();
        $cus_address = DB::table('customer_address')
            ->select('customer_address.*','countries.name as country_name','countries.currency','countries.iso2','states.name as state_name','cities.name as city_name')
            ->join('countries','countries.id','=','customer_address.country_id')
            ->join('states','states.id','=','customer_address.state_id')
            ->join('cities','cities.id','=','customer_address.city_id')
            ->where('store_id',$store_id)
            ->where('customer_id',$customer_id)
            ->where('address_id',$address_id)
            ->first();
        $gateway = DB::table('store_payment_credentials')
            ->select('*')
            ->where('store_id',$store_id) //as of now we are hide for payment using all Stores
            ->where('status',1) 
            ->where('is_deleted',0) 
            ->first();
        $cart_id = "CART_".$order_id;   
        $ord_id = Crypt::encrypt($order_id);
        $redirect = url($store_url . '/customer/payment/response', ['id' =>$ord_id]);
        $redirect  = str_replace("https","http",$redirect);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://secure.paytabs.sa/payment/request',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "profile_id": "'.$gateway->client_id.'",
                "tran_type": "sale",
                "tran_class": "ecom",
                "cart_id": "'.$cart_id.'",
                "cart_currency": "SAR",
                "cart_amount": "'.$totalAmount.'",
                "cart_description": "Online Order Payment",
                "paypage_lang": "en",
                "customer_details": {
                    "name": "'.$cus_name.'",
                    "email": "'.$cus_details->email.'",
                    "phone": "'.$cus_phone.'",
                    "street1": "'.$cus_address->street_name.'",
                    "city": "'.$cus_address->state_name.'",
                    "state": "'.$cus_address->state_name.'",
                    "country": "'.$cus_address->iso2.'",
                    "zip": "'.$cus_address->pincode.'"
                },
                "shipping_details": {
                    "name": "'.$cus_name.'",
                    "email": "'.$cus_details->email.'",
                    "phone": "'.$cus_phone.'",
                    "street1": "'.$cus_address->street_name.'",
                    "city": "'.$cus_address->state_name.'",
                    "state": "'.$cus_address->state_name.'",
                    "country": "'.$cus_address->iso2.'",
                    "zip": "'.$cus_address->pincode.'"
                },
                "callback": "'.$redirect.'",
                "return": "'.$redirect.'"
            }',
            CURLOPT_HTTPHEADER => array(
              'Authorization: SRJNHD96GB-JHJMMTKJ6B-TK22TNRRKJ'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);  
        $result = json_decode($response,TRUE);
        DB::table('store_payment_transactions')->insert([
            "store_id" => $store_id,
            "order_id" => $order_id,
            "customer_id" => $customer_id,
            "tran_ref" => $result['tran_ref'],
            "trans_type" => $result['tran_type'],
            "cart_amount" => $result['cart_amount'],
            "ip" => $result['customer_details']['ip'],
            "trace" => $result['trace']
        ]);
        return  redirect($result['redirect_url']);
        // return redirect()->route($this->store_url.'.customer.orders.index')->with('message',"Product ordered successfully");
    }
    public function paymentresponse($id){
        $store_url = $this->store_url;
        $store_id = CommonController::get_store_id();
        $customer_id = (session()->has('authenticate_user')) ? session('authenticate_user')->customer_id : Auth::guard('customer')->user()->customer_id;
        $store_logo = $this->store_logo;
        $ord_id = Crypt::decrypt($id);
        $trans_data =  DB::table('store_payment_transactions')
            ->select('store_payment_transactions.*')
            ->where('order_id',$ord_id)
            ->first();                       
        $tran_ref = $trans_data->tran_ref;
        $gateway = DB::table('store_payment_credentials')
            ->select('*')
            ->where('store_id',$store_id) 
            ->where('status',1) 
            ->where('is_deleted',0)
            ->first();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://secure.paytabs.sa/payment/query',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "profile_id": 106571,
                "tran_ref": "'.$trans_data->tran_ref.'"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: SRJNHD96GB-JHJMMTKJ6B-TK22TNRRKJ'
            ),
        ));
        $response = curl_exec($curl);        
        curl_close($curl);
        $result = json_decode($response,TRUE);  
        $data_fetch = DB::table('store_payment_responses')->where('order_id',$ord_id)->get()->count();
        if($data_fetch == 0){
            DB::table('store_payment_responses')->insert([
                "store_id" => $store_id,
                "order_id" => $ord_id,
                "customer_id" => $customer_id,
                "tran_ref" => $result['tran_ref'],
                "tran_type" => $result['tran_type'],
                "cart_id" => $result['cart_id'],
                "cart_desc" => $result['cart_description'],
                "cart_currency" => $result['cart_currency'],
                "cart_amount" => $result['cart_amount'],
                "tran_currency" => $result['tran_currency'],
                "tran_total" => $result['tran_total'],
                "cus_name" => $result['customer_details']['name'],
                "cus_email" => $result['customer_details']['email'],
                "cus_street1" => $result['customer_details']['street1'],
                "cus_city" => $result['customer_details']['city'],
                "cus_state" => $result['customer_details']['state'],
                "cus_country" => $result['customer_details']['country'],
                "cus_zip" => $result['customer_details']['zip'],
                "cus_ip" => $result['customer_details']['ip'],
                "ship_name" => $result['shipping_details']['name'],
                "ship_email" => $result['shipping_details']['email'],
                "ship_street1" => $result['shipping_details']['street1'],
                "ship_city" => $result['shipping_details']['city'],
                "ship_state" => $result['shipping_details']['state'],
                "ship_country" => $result['shipping_details']['country'],
                "ship_zip" => $result['shipping_details']['zip'],
                "response_status" => $result['payment_result']['response_status'],
                "response_code" => $result['payment_result']['response_code'],
                "response_message" => $result['payment_result']['response_message'],
                "payment_method" => $result['payment_info']['payment_method'],
                "card_type" => $result['payment_info']['card_type'],
                "card_scheme" => $result['payment_info']['card_scheme'],
                "payment_desc" => $result['payment_info']['payment_description'],
                "expiry_month" => $result['payment_info']['expiryMonth'],
                "expiry_year" => $result['payment_info']['expiryYear'],
                "merchant_id" => $result['merchantId'],
                "trace" => $result['trace']
            ]);
            DB::table('online_store_order_details')->where('online_order_id',$ord_id)->update([
                "payment_status" => $result['payment_result']['response_status'],
                "payment_code"  => $result['payment_result']['response_code'],
                "payment_message" => $result['payment_result']['response_message'],
                "payment_ref" =>   $result['tran_ref']

            ]);
        }
        $resp = [];
        $resp['name'] = $result['customer_details']['name'];
        $resp['order_id'] = $ord_id;
        $resp['amount'] = $result['tran_total'];
        $resp['status'] = $result['payment_result']['response_status'];
        $resp['code'] = $result['payment_result']['response_code'];
        $resp['message'] = $result['payment_result']['response_message'];
        $resp['time'] = $result['payment_result']['transaction_time'];
        $resp['ref'] = $result['tran_ref'];
        $resp['account'] = $result['payment_info']['payment_description'];
        $red_url =  route($this->store_url .'.customer.orders.show',Crypt::encrypt($ord_id));
        return view('customer.paymentresponse',compact('store_logo','trans_data','resp','store_url','red_url'));
    }
    public function couponCodeDetails(Request $request)
    {
        $discountsQuery = DB::table('store_discount')
        ->leftJoin('store_product_discount', function ($join) {
            $join->on('store_discount.discount_id', '=', 'store_product_discount.discount_id')
                ->where('store_product_discount.is_deleted', '=', 0)
                ->where('store_product_discount.status', '=', 1);
        })
        ->where('store_discount.discount_method', '=', 'code')
        ->where('store_discount.discount_name', '=', $request->coupon_code)
        ->where('store_discount.discount_valid_from', '<=', Carbon::now())
        ->where(function ($query) {
            $query->where('store_discount.store_type', 'online')
                ->orWhere('store_discount.store_type','both');
        })
        ->where(function ($query) {
            $query->where('store_discount.discount_valid_to', '>=', Carbon::now())
                ->orWhereNull('store_discount.discount_valid_to');
        })
        ->select('store_discount.discount_id','product_discount_type','discount_value','discount_type','product_discount_id','product_id','variant_id','min_require_type','min_value','max_discount_uses','max_value','once_per_order');
        $discounts = $discountsQuery->get()->toArray();
        return response()->json(['store_discount' =>$discounts]);
    }
}
