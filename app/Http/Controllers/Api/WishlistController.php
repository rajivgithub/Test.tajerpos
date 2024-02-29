<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Validator;
use App\Models\Customers\Wishlist;
use Carbon\Carbon;
use DB;

class WishlistController extends ApiController
{
    public function addWishList(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'store_id'=>'required',
            'customer_id'=>'required',
            'type'=>'required|in:add,remove',
            'wishlist_id' => 'required_if:type,remove',
            'product_id' => 'required',
        ]);
        if ($validator->fails()) 
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        else {
            if($request->type == "add") {
                $input = $request->all();
                unset($input['type']);
                Wishlist::create($input);
                return $this->createResponse('Product was added to wishlist successfully', self::HTTP_OK,$request->store_id);
            } else {
                $remove_wishlist = array();
                $remove_wishlist['is_deleted'] = 1;  
                $remove_wishlist['deleted_at'] = Carbon::now()->toDateTimeString();
                $wishlist_exist_query = Wishlist::where([
                    ['product_id', '=', $request->product_id],
                    ['customer_id', '=', $request->customer_id],
                    ['store_id','=',$request->store_id]
                ]);
                if(!empty($request->wishlist_id))
                    $wishlist_exist_query->where('wishlist_id',$request->wishlist_id);
                $wishlist_exist_query->update($remove_wishlist);
                return $this->createResponse('Product was removed from wishlist successfully', self::HTTP_OK,$request->store_id);
            }
            
        }
    }
    public function wishList(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'store_id'=>'required',
            'customer_id'=>'required'
        ]);
        if ($validator->fails()) 
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        else {
            $wishlist_data = Wishlist::select('product_name', 'store_products.product_id', 'store_products.category_image', 'type_of_product', 'wishlist_id', 'trackable', 'unit', 'product_description', 'category_name', 'sub_category_name',
            DB::raw('(SELECT variants_combination_name FROM store_product_variants_combination WHERE is_deleted = 0 AND product_id = store_products.product_id Limit 1) AS variants_combination_name'),
            DB::raw('CASE WHEN (type_of_product = "variant") THEN (SELECT variant_price FROM store_product_variants_combination WHERE is_deleted = 0 AND product_id = store_products.product_id Limit 1) ELSE sp.price END AS price'))
            ->leftJoin('store_products', 'store_products.product_id', '=', 'wishlist.product_id')
            ->leftJoin('store_category', 'store_category.category_id', '=', 'store_products.category_id')
            ->leftJoin('store_sub_category', function($join) {
                $join->on('store_sub_category.sub_category_id', '=', 'store_products.sub_category_id');
            })
            ->leftJoin('store_price as sp', 'store_products.product_id', '=', 'sp.product_id')
            ->where('wishlist.store_id', $request->store_id)
            ->where('wishlist.customer_id', $request->customer_id)
            ->where('wishlist.is_deleted', 0)
            ->where('store_products.is_deleted', 0)
            ->where('store_products.status_type', 'publish')
            ->where('store_products.status', 1)
            ->where('store_category.is_deleted', 0)
            ->where('store_category.status', 1)
            ->whereRaw('CASE WHEN (store_products.sub_category_id > 0) THEN store_sub_category.is_deleted = 0 AND store_sub_category.status = 1 ELSE TRUE END')
            ->distinct('store_products.product_id')
            ->orderByDesc('wishlist_id')->get()->toArray();
            return $this->createResponse('Wishlist according to the customer', self::HTTP_OK,$request->store_id,$request->customer_id,$wishlist_data);
        }
    }
}
