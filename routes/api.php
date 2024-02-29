<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Models\Admin\Store;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\Cashier\LoginController as CashierLoginController;
use App\Http\Controllers\Api\Cashier\CartController as InStoreCartController;
use App\Http\Controllers\Api\Cashier\PlaceorderController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
$store_details = Store::where('status',1)->get('store_id');
if(isset($store_details) && !empty($store_details)) {
    foreach($store_details as $store) {
        $store_id = $store->store_id;
        Route::group(['prefix' =>"$store_id", 'as' => "$store_id."], function () {
            Route::post("/login", [LoginController::class, 'login'])->name("login");
            Route::post('/register', [LoginController::class, 'register'])->name('register');
            Route::post('/add-category', [CategoryController::class, 'addCategory'])->name('add-category');
            Route::post('/all-category-list', [CategoryController::class, 'allCategoryList'])->name('all-category-list');
            Route::post('/product-list', [ProductController::class, 'productList'])->name('product-list');
            Route::post('/add-wishlist', [WishlistController::class, 'addWishList'])->name('add-wishlist');
            Route::post('/wishlist', [WishlistController::class, 'wishlist'])->name('wishlist');
            Route::post('/promotion-list', [PromotionController::class, 'promotionList']);
            Route::post('/forgot-password', [ForgotPasswordController::class, 'forgetPassword']);
            Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOTP']);
            Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
            Route::post('/change-password', [CustomerController::class, 'changePassword']);
            Route::post('/update-profile', [CustomerController::class, 'updateProfile']);
            Route::post('/profile', [CustomerController::class, 'getProfile']);
            Route::post('/add-to-cart', [CartController::class, 'addToCart']);
            Route::post('/cart-list', [CartController::class, 'cartlist']);
            Route::post('/proceed-to-checkout', [CartController::class, 'checkoutProduct']);
            Route::post('/add-address', [AddressController::class, 'addAddress']);
            Route::post('/view-address', [AddressController::class, 'viewAddress']);
            Route::post('/remove-address', [AddressController::class, 'removeAddress']);
            Route::post('/orders', [OrdersController::class, 'ordersList']);
            Route::post("/cashier-login", [CashierLoginController::class, 'login']);
            Route::group(['prefix' => 'cashier', 'as' => 'cashier'], function () { 
                Route::post("/add-to-cart", [InStoreCartController::class, 'addToCart']);
                Route::post("/view-cart", [InStoreCartController::class, 'cartlist']);
                Route::post('/product-list', [PlaceorderController::class, 'productList'])->name('product-list');

            });
        });
    }
}
Route::post('city-list', [CommonController::class,'cityList'])->name('city-list');
Route::post('state-list', [CommonController::class,'stateList'])->name('state-list'); 
Route::post('country-list', [CommonController::class,'countryList'])->name('country-list');