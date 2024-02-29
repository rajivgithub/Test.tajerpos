<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use Validator;
use App\Models\CashierAdmin\OnlineStoreOrderItems;

class OrdersController extends ApiController
{
    public function ordersList(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'store_id'=> 'required',
            'customer_id'=> 'required', 
            'online_order_id'=> 'required_if:type,view', 
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            $order_details = OnlineStoreOrderItems::leftJoin('online_store_order_details', 'online_store_order_items.order_id', '=', 'online_store_order_details.online_order_id')
                ->leftJoin('online_order_status', 'online_store_order_details.online_order_status', '=', 'online_order_status.order_status_id')
                ->leftJoin('store_products', 'online_store_order_items.product_id', '=', 'store_products.product_id')
                ->leftJoin('customer_address', 'online_store_order_details.address_id', '=', 'customer_address.address_id')
                ->leftJoin('countries', 'customer_address.country_id', '=', 'countries.id')
                ->leftJoin('states', 'customer_address.state_id', '=', 'states.id')
                ->leftJoin('cities', 'customer_address.city_id', '=', 'cities.id')
                ->where([
                    ['online_store_order_items.store_id', '=', $request->store_id],
                    ['online_store_order_items.customer_id', '=', $request->customer_id]
                ])
                ->when(($request->type == "view"), function ($query) use ($request) {
                    $query->where('online_store_order_details.online_order_id', $request->online_order_id);
                })
                ->select('product_name','category_image','product_variants','quantity','sub_total','online_store_order_items.tax_amount','status_name','online_order_id','online_store_order_details.order_number','discount_amount','total_amount','customer_name','online_store_order_items.product_id','street_name','building_name','pincode','countries.name as country_name','states.name as state_name','cities.name as city_name')
                ->selectRaw("DATE_FORMAT(online_store_order_details.created_at, '%d-%m-%Y %H:%i') as ordered_at")
                ->selectRaw("(SELECT GROUP_CONCAT(variants_name) FROM store_product_variants WHERE product_id = store_products.product_id AND is_deleted = 0) as variants_name")
                ->orderBy("online_order_id","desc")
                ->get();
            return $this->createResponse("Customer-specific order list", self::HTTP_OK, $request->store_id, $request->customer_id,$order_details);
        } else {
            return $checkStoreId;
        }
    } 
}
