<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

class ApiController extends Controller
{
    const HTTP_OK = 200;
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_NOT_FOUND = 404;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    protected function createResponse($message, $status, $store_id = null, $customer_id = null, $data = null)
    {
        $result = [
            'message' => $message,
            'store_id' => $store_id,
            'customer_id' => $customer_id,
            'status' => $status,
        ];

        if ($data !== null) {
            $result['data'] = $data;
        }

        return response()->json(['data' => $result, 'status' => $status], $status);
    }

    protected function createCashierResponse($message, $status, $store_id = null, $admin_id = null, $data = null)
    {
        $result = [
            'message' => $message,
            'store_id' => $store_id,
            'status' => $status,
        ];

        if ($data !== null) {
            $result['data'] = $data;
        }

        return response()->json(['data' => $result, 'status' => $status], $status);
    }

    protected function checkStoreId($store_id)
    {
        $current_url = url()->current();
        $split_url = explode("/",$current_url);
        $url_store_id = (!empty($split_url) && isset($split_url[4])) ? $split_url[4] : "";
        if($url_store_id == $store_id) {
            return true;
        }
        else {
            return $this->createResponse("Store ID is mismatched with the URL store ID", self::HTTP_NOT_FOUND);
        }
    }

    protected function checkAdminInStore($store_id,$admin_id)
    {
        $isRecordExists = User::where([
            ['id','=',$admin_id],
            ['store_id','=',$store_id],
            ['is_admin','=',3],
        ])->exists();
        if ($isRecordExists) {
            return true;
        } else {
            return $this->createResponse("You do not have access.", self::HTTP_UNAUTHORIZED);
        }
    }
}
