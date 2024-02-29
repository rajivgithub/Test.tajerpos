<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\StoreAdmin\CustomerBanners;
use Validator;
use Carbon\Carbon;
use DB;

class PromotionController extends ApiController
{
    public function promotionList(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 
            'store_id'=>'required',
        ]);
        if ($validator->fails()) {
            return $this->createResponse($validator->errors()->first(), self::HTTP_UNPROCESSABLE_ENTITY);
        }
        $checkStoreId = $this->checkStoreId($request->store_id);
        if ($checkStoreId === true) {
            $expiredBanners = CustomerBanners::where([
                ['store_id', '=', $request->store_id],
                ['is_deleted', '=', 0],
                ['end_date', '<', Carbon::now()],
            ])->get();
            if(!empty($expiredBanners)) {
                foreach ($expiredBanners as $banner) {
                    $banner->status = 'expired';
                    $banner->save();
                }
            }
            $banner_list = CustomerBanners::where([
                ['store_id','=', $request->store_id],
                ['is_deleted','=', 0],
                ['status','=', 'active'],
            ])
            ->whereNotIn('banner_type',['web'])
            ->select('banner_id','banner_image','banner_url',DB::raw("DATE_FORMAT(start_date, '%d-%m-%Y %H:%i') as start_date"),DB::raw("DATE_FORMAT(end_date, '%d-%m-%Y %H:%i') as end_date"),'banner_type','status',DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') as banner_created_at"))->get();
            return $this->createResponse("Promotion List according to store ID", self::HTTP_OK, $request->store_id, '',$banner_list);
        } else {
            return $checkStoreId;
        }
    }
}
