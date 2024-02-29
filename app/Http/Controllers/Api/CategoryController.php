<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Validator;
use App\Models\StoreAdmin\Category;
use DB;

class CategoryController extends ApiController
{
    public function allCategoryList(Request $request) {
        $validator = Validator::make($request->all(), [ 
            'store_id'=>'required'
        ]);
        if ($validator->fails()) 
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        else {
            $category_details = Category::select('category_name', 'icon', 'category_id',DB::raw('(SELECT COUNT(DISTINCT sp.product_id) FROM store_products AS sp LEFT JOIN store_sub_category on sp.sub_category_id = store_sub_category.sub_category_id LEFT JOIN store_product_variants_combination as spvc on sp.product_id = spvc.product_id WHERE sp.category_id = store_category.category_id AND sp.is_deleted = 0 AND status_type = "publish" AND (CASE WHEN sp.sub_category_id > 0 THEN store_sub_category.status = 1 AND store_sub_category.is_deleted = 0 ELSE TRUE END) AND (CASE WHEN type_of_product = "single" THEN (trackable = 1 AND unit > 0) OR trackable = 0 WHEN type_of_product = "variant" THEN (on_hand > 0 OR on_hand IS NULL OR on_hand = "") AND spvc.is_deleted = 0 AND spvc.variants_combination_id IS NOT NULL ELSE TRUE END)) AS product_count'))
            ->where([
                ['is_deleted', '=', 0],
                ['status', '=', 1],
                ['store_id','=',$request->store_id]
            ])
            ->having('product_count', '>', 0)
            ->get();
            return $this->createResponse('All Category List', self::HTTP_OK,$request->store_id,'',$category_details);
        }
    }

}
