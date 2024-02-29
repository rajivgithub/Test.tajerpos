<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\StoreAdmin\DashboardController as StoreAdminDashboardController;
use App\Models\Admin\Store;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\StoreAdmin\RolesController;
use App\Http\Controllers\StoreAdmin\PermissionController;
use App\Http\Controllers\StoreAdmin\UserController;
use App\Http\Controllers\StoreAdmin\CategoryController;
use App\Http\Controllers\StoreAdmin\SubCategoryController;
use App\Http\Controllers\StoreAdmin\ProductController;
use App\Http\Controllers\StoreAdmin\InventoryController;
use App\Http\Controllers\StoreAdmin\ThemeController;
use App\Http\Controllers\StoreAdmin\FlashDealController;
use App\Http\Controllers\StoreAdmin\SubscriberController;
use App\Http\Controllers\StoreAdmin\PaymentGatewayController;
use App\Http\Controllers\StoreAdmin\ShippingController;
use App\Http\Controllers\StoreAdmin\ApiCredentialsController;
use App\Http\Controllers\StoreAdmin\NewsLettersController;
use App\Http\Controllers\StoreAdmin\CouponController;
use App\Http\Controllers\StoreAdmin\ReportController;
use App\Http\Controllers\StoreAdmin\SupportController;
use App\Http\Controllers\StoreAdmin\StoreOrderController;
use App\Http\Controllers\CashierAdmin\CashierController;
use App\Http\Controllers\CashierAdmin\PlaceOrderController;
use App\Http\Controllers\CashierAdmin\StoreOrderController as OrderController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ForgotPasswordController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\SocialShareController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\AddToCartController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SuperAdmin\LoginController as SuperAdminLoginController;
use App\Http\Controllers\SuperAdmin\EmailController;
use App\Http\Controllers\StoreAdmin\AnalystController;
use App\Http\Controllers\CashierAdmin\ReportsController;
use App\Http\Controllers\CashierAdmin\StoreOrderStatusController;
use App\Http\Controllers\CashierAdmin\OnlineOrderStatusController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\StoreAdmin\TaxController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\CashierAdmin\StorePlaceOrderPreferController;
use App\Http\Controllers\CashierAdmin\OrderMethodsController;
use App\Http\Controllers\CashierAdmin\StoreDiscountController;
use App\Http\Controllers\StoreAdmin\OnlineOrderController;
use App\Http\Controllers\StoreAdmin\CustomerController;
use App\Http\Controllers\StoreAdmin\CustomerInquiriesController;
use App\Http\Controllers\StoreAdmin\CustomerBannersController;
use App\Http\Controllers\Customer\ContactUsController;
use App\Http\Controllers\GeneralSettingsController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\StoreAdmin\PaymentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/example', function () {
    // Call the view file 'example.blade.php' in the 'resources/views' directory
    return view('customer.emails.app.forget_password');
});
Route::get('/update-product-image', [CronController::class, 'updateImage']);
Route::group(['middleware' => 'prevent-back-history'],function(){
    Auth::routes();
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::post('/email-exist', [DashboardController::class, 'isEmailExist'])->name('email-exist');
    Route::post('/set-language', [LanguageController::class, 'setLanguage'])->name('set-language');
    $store_details = Store::where('status',1)->get('store_url','store_id');
    if(isset($store_details) && !empty($store_details)) {
        foreach($store_details as $store) {
            $store_url = $store->store_url;
            Route::group(['prefix' =>"$store_url", 'as' => "$store_url.", 'middleware' => ['customer_access']], function () {
                Route::get("/customer-login/{type?}", [CustomerDashboardController::class, 'showLogin'])->name("customer-login");
                Route::post("/customer-login", [CustomerDashboardController::class, 'login'])->name("customer-login");
                Route::get("/customer-register/{type?}", [CustomerDashboardController::class, 'showRegister'])->name("customer-register");
                Route::post("/customer-register", [CustomerDashboardController::class, 'register'])->name("customer-register");
                Route::get("/customer-forget-password", [ForgotPasswordController::class, 'showForgetPassword'])->name("customer-forget-password");
                Route::post('/customer-forget-password', [ForgotPasswordController::class, 'forgetPassword'])->name('customer-forget-password'); 
                Route::get('/customer-reset-password/{token}', [ForgotPasswordController::class, 'showResetPassword'])->name('customer-reset-password');
                Route::post('/customer-reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('customer-reset-password');
                Route::group(['prefix' => 'customer', 'as' => 'customer.'], function ()  {
                    Route::get('/home', [CustomerDashboardController::class, 'home'])->name("home");
                    Route::get('/single-product/category/{category_id}/{category_name}/product/{product_id}/{product_name}/{variant_option_id?}/{type?}', [CustomerProductController::class, 'singleProduct']);
                    Route::get('/category/{category_name?}', [CustomerProductController::class, 'categoryProduct'])->name("category");
                    Route::get('/category/{category_name?}/sub-category/{sub_category_name?}', [CustomerProductController::class, 'categoryProduct'])->name("sub-category");
                    Route::post('/category-product', [CustomerProductController::class, 'getCategoryProduct'])->name("category-product");
                    Route::get('/social-share', [SocialShareController::class, 'index']);
                    Route::get('/products-by-category', [CustomerProductController::class, 'getProductsByCategory']);
                    Route::post('/products-by-category', [CustomerProductController::class, 'productsByCategory'])->name('products-by-category');
                    Route::post('/add-to-cart', [AddToCartController::class, 'addToCart'])->name('add-to-cart');
                    Route::get('/view-cart', [AddToCartController::class, 'viewCart'])->name('view-cart');
                    Route::post('/get-product-quantity', [AddToCartController::class, 'quantityBySession'])->name('get-product-quantity');
                    Route::get('/get-product-count', [AddToCartController::class, 'getProductCount'])->name('get-product-count');
                    Route::get('/get-store-details', [CustomerDashboardController::class, 'getStoreDetails'])->name('get-store-details');
                    Route::get('/contact-us', [ContactUsController::class, 'showContactUs'])->name('contact-us');
                    Route::post('/contact-us', [ContactUsController::class, 'saveQueries'])->name('contact-us');
                    Route::get('/registation-us', [ContactUsController::class, 'index'])->name('registation-us');
                    Route::get('/variants-by-product', [CustomerProductController::class, 'variantsByProduct'])->name('variants-by-product');
                    Route::post('/save-wishlist-data', [WishlistController::class, 'saveWishlistInSession'])->name('save-wishlist-data');
                });
                Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['is_customer']], function ()  {
                    Route::get('/dashboard', [CustomerDashboardController::class, 'dashboard'])->name("dashboard");
                    Route::post('/profile', [CustomerDashboardController::class, 'updateProfile'])->name("profile");
                    Route::post('/update-password', [CustomerDashboardController::class, 'updatePassword'])->name("update-password");
                    Route::post('/profile-image', [CustomerDashboardController::class, 'profileImage'])->name("profile-image");
                    Route::post('/remove-profile-image', [CustomerDashboardController::class, 'removeProfileImage'])->name("remove-profile-image");
                    Route::resource('/address', AddressController::class);
                    Route::post("/logout", [CustomerDashboardController::class, 'logout'])->name("logout");
                    Route::get('/checkout', [CheckoutController::class, 'productCheckout'])->name('checkout');
                    Route::post('/get-coupon-code-details', [CheckoutController::class, 'couponCodeDetails'])->name('get-coupon-code-details');
                    Route::post('/placeorder', [CheckoutController::class, 'placeorder'])->name('placeorder');
                    Route::resource('/orders', CustomerOrderController::class);
                    Route::post('/orders-product', [CustomerOrderController::class, 'getOrdersProducts'])->name('orders-product');
                    Route::post('/show-wishlist-product', [WishlistController::class, 'show'])->name('show-wishlist-product');
                    Route::resource('/wishlist', WishlistController::class,['except' => ['show']]);
                    Route::get('/payment/response/{id}', [ CheckoutController::class,'paymentresponse'])->name('customer.payment-response');
                });
            });
        }
    }
    Route::post('get-logo-image', [ GeneralSettingsController::class,'getLogoImage'])->name('get-logo-image');
    Route::group(['prefix' => config('app.prefix_url'), 'as' => config('app.prefix_url')."."], function () {
        //Clear the cache
        Route::get('/clear-cache', function() {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            Artisan::call('view:clear');
            return "Cleared!";
        });
        Route::get('/super-admin', [SuperAdminLoginController::class, 'showLogin'])->name('super-admin');
        Route::post('/super-admin', [SuperAdminLoginController::class, 'login'])->name('super-admin');
        Route::post('general-settings', [ GeneralSettingsController::class,'generalSettings'])->name('general-settings');
        Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['is_admin']], function () {
            Route::post("/logout", [DashboardController::class, 'logout'])->name("logout");
            Route::get('/home', [DashboardController::class, 'dashboard'])->name('home');
            Route::get('/profile', [DashboardController::class, 'editProfile'])->name('profile');
            Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('profile'); 
            Route::get('/change-password', [DashboardController::class, 'changePassword'])->name('change-password');
            Route::post('/change-password', [DashboardController::class, 'updatePassword'])->name('update-password'); 
            Route::get('store/create/{id?}', [ StoreController::class,'create'])->name('store.create');
            Route::post('store/update', [ StoreController::class,'update'])->name('store.update'); 
            Route::post('store/update-status', [ StoreController::class,'updateStatus'])->name('store.update-status'); 
            Route::get('store/destroy/{id}', [ StoreController::class,'destroy'])->name('store.destroy'); 
            Route::resource('store', StoreController::class,['except' => ['create','update','destroy']]);
            Route::get('store/payment/{id}', [ StoreController::class,'paymentHistory'])->name('store.payment');
            // Route::post('store/payment-history', [ StoreController::class,'savePayment'])->name('store.payment-history'); 
            Route::post('store/url-exist', [ StoreController::class,'isUrlExist'])->name('store.url-exist'); 
            Route::get('store/add-payment/{store_id?}/{type?}', [ StoreController::class,'createPayment'])->name('store.add-payment'); 
            Route::post('store/add-payment', [ StoreController::class,'storePayment'])->name('store.add-payment'); 
            Route::post('store/reminder-notification', [ StoreController::class,'sendReminder'])->name('store.reminder-notification'); 
            Route::get('store/invoice/{id}/{type?}', [ StoreController::class,'invoice'])->name('store.invoice');
            Route::get('chat-list', [AdminChatController::class,'adminUserList'])->name('chat-list'); 
            Route::post('insert-chat', [AdminChatController::class,'insertChat'])->name('insert-chat'); 
            Route::get('get-chat', [AdminChatController::class,'getChat'])->name('get-chat'); 
            Route::get('unread-chat-count', [AdminChatController::class,'unreadChatCount'])->name('unread-chat-count'); 
            Route::get('general-settings', [ GeneralSettingsController::class,'adminGeneralSettings'])->name('general-settings');
        });
        $store_details = Store::where('status',1)->get('store_url','store_id');
        if(isset($store_details) && !empty($store_details)) {
            foreach($store_details as $store) {
                $store_url = $store->store_url;
                Route::get('/customer-invoice/{id}', [CommonController::class,'invoice'])->name('customer-invoice');
                Route::get("$store_url", [LoginController::class, 'showLogin'])->name("$store_url");
                Route::post("$store_url", [LoginController::class, 'login'])->name("$store_url");
                Route::group(['prefix' =>"$store_url", 'as' => "$store_url."], function () {  
                    Route::post('sub-category-list', [SubCategoryController::class,'subCategoryList'])->name('sub-category-list');
                    Route::post('check-unique-barcode', [ProductController::class,'checkUniqueBarcode'])->name('check-unique-barcode');
                    Route::group(['prefix' => config('app.module_prefix_url'), 'as' => config('app.module_prefix_url').'.', 'middleware' => ['is_cashier_admin']], function () {
                        Route::get('/home', [CashierController::class, 'dashboard'])->name("home");
                        Route::post("/logout", [CashierController::class, 'logout'])->name("logout");
                        Route::get('/profile', [CashierController::class, 'editProfile'])->name('profile');
                        Route::post('/profile', [CashierController::class, 'updateProfile'])->name('profile'); 
                        Route::post('/update-store-details', [CashierController::class, 'updateStoreDetails'])->name('update-store-details'); 
                        Route::get('/change-password', [CashierController::class, 'changePassword'])->name('change-password');
                        Route::post('/change-password', [CashierController::class, 'updatePassword'])->name('update-password'); 
                        Route::get('place-order/index/{type?}', [ PlaceOrderController::class,'index'])->name('place-order.index'); 
                        Route::get('place-order/view-cart', [ PlaceOrderController::class,'view_cart'])->name('place-order.view-cart');  
                        Route::post('place-order/category-product-list', [ PlaceOrderController::class,'categoryProductList'])->name('place-order.category-product-list'); 
                        Route::post('place-order/variants-by-product', [ PlaceOrderController::class,'variantsByProduct'])->name('place-order.variants-by-product'); 
                        Route::resource('place-order', PlaceOrderController::class,['except' => ['show']]); 
                        Route::post('place-order/get-coupon-code-details', [ PlaceOrderController::class,'couponCodeDetails'])->name('place-order.get-coupon-code-details'); 
                        Route::get('store-order/download-invoice/{id}', [OrderController::class, 'downloadInvoice'])->name('store-order.download-invoice'); 
                        Route::get('store-order/destroy/{id}', [OrderController::class,'destroy'])->name('store-order.destroy');
                        Route::post('store-order/update', [OrderController::class,'update'])->name('store-order.update');
                        Route::post('store-order/phone-number-exist', [OrderController::class,'isPhoneNumberExist'])->name('store-order.phone-number-exist');
                        Route::resource('store-order', OrderController::class,['except' => ['destroy','update']]);
                        Route::post('online-orders/update', [OnlineOrderController::class,'update'])->name('online-orders.update');
                        Route::get('online-orders/destroy/{id}', [OnlineOrderController::class,'destroy'])->name('online-orders.destroy');
                        Route::resource('online-orders', OnlineOrderController::class,['except' => ['destroy','update']]);
                        Route::get('product/create/{id?}', [ProductController::class,'create'])->name('product.create');
                        Route::post('product/update', [ ProductController::class,'update'])->name('product.update'); 
                        Route::get('product/destroy/{id}', [ ProductController::class,'destroy'])->name('product.destroy'); 
                        Route::post('product/remove-image', [ ProductController::class,'removeImage'])->name('product.remove-image'); 
                        Route::get('product/import', [ ProductController::class,'import'])->name('product.import'); 
                        Route::get('product/reviews', [ ProductController::class,'reviews'])->name('product.reviews'); 
                        Route::resource('product', ProductController::class,['except' => ['create','update','destroy']]);
                        Route::get('category/create/{id?}', [ CategoryController::class,'create'])->name('category.create');
                        Route::post('category/update', [ CategoryController::class,'update'])->name('category.update'); 
                        Route::post('category/update-order-number', [ CategoryController::class,'updateOrderNumber'])->name('category.update-order-number'); 
                        Route::get('category/destroy/{id}', [ CategoryController::class,'destroy'])->name('category.destroy'); 
                        Route::resource('category', CategoryController::class,['except' => ['create','update','destroy']]);
                        Route::post('category/import', [ CategoryController::class,'import'])->name('category.import'); 
                        Route::get('sub-category/create/{id?}', [SubCategoryController::class,'create'])->name('sub-category.create');
                        Route::post('sub-category/update', [SubCategoryController::class,'update'])->name('sub-category.update'); 
                        Route::get('sub-category/destroy/{id}', [SubCategoryController::class,'destroy'])->name('sub-category.destroy');
                        Route::post('sub-category/update-order-number', [ SubCategoryController::class,'updateOrderNumber'])->name('sub-category.update-order-number');  
                        Route::post('product/update-order-number', [ ProductController::class,'updateOrderNumber'])->name('product.update-order-number');  
                        Route::resource('sub-category', SubCategoryController::class,['except' => ['create','update','destroy']]);
                        Route::get('reports/transaction-report', [ReportsController::class,'transactionReport'])->name('reports.transaction-report');
                        Route::get('reports/customer-report', [ReportsController::class,'customerReport'])->name('reports.customer-report');
                        Route::resource('store-order-status', StoreOrderStatusController::class);
                        Route::resource('online-order-status', OnlineOrderStatusController::class);
                        Route::post('insert-chat', [ChatController::class,'insertChat'])->name('insert-chat'); 
                        Route::get('chat-list', [ChatController::class,'chatList'])->name('chat-list'); 
                        Route::get('get-chat', [ChatController::class,'getChat'])->name('get-chat'); 
                        Route::get('unread-chat-count', [ChatController::class,'unreadChatCount'])->name('unread-chat-count'); 
                        Route::get('category-list', [CommonController::class,'get_category_details'])->name('category-list');
                        Route::post('product-list', [ProductController::class,'get_product_details'])->name('product-list');                        
                        Route::post('category-search', [CommonController::class,'categorySearch'])->name('category-search');
                        Route::get('analytics', [AnalystController::class,'index'])->name('analytics');
                        Route::post('analyst-report', [AnalystController::class,'analystReport'])->name('analyst-report');
                        Route::get('product-inventory', [InventoryController::class,'inventoryList'])->name('product-inventory');
                    });
                    Route::group(['prefix' => config('app.module_prefix_url'), 'as' => config('app.module_prefix_url').'.', 'middleware' => ['is_store_admin']], function () {
                        Route::resource('shipping', ShippingController::class);
                        Route::get('roles/create/{id?}', [ RolesController::class,'create'])->name('roles.create');
                        Route::post('roles/update', [ RolesController::class,'update'])->name('roles.update'); 
                        Route::get('roles/destroy/{id}', [ RolesController::class,'destroy'])->name('roles.destroy'); 
                        Route::resource('roles', RolesController::class,['except' => ['create','update','destroy']]);
                        Route::get('permission/create/{id?}', [ PermissionController::class,'create'])->name('permission.create');
                        Route::post('permission/update', [ PermissionController::class,'update'])->name('permission.update'); 
                        Route::get('permission/destroy/{id}', [ PermissionController::class,'destroy'])->name('permission.destroy'); 
                        Route::resource('permission', PermissionController::class,['except' => ['create','update','destroy']]);
                        Route::get('users/create/{id?}', [ UserController::class,'create'])->name('users.create');
                        Route::post('users/update', [ UserController::class,'update'])->name('users.update'); 
                        Route::get('users/destroy/{id}', [ UserController::class,'destroy'])->name('users.destroy'); 
                        Route::resource('users', UserController::class,['except' => ['create','update','destroy']]);
                        Route::get('themes/create/{id?}', [ThemeController::class,'create'])->name('themes.create');
                        Route::post('themes/update', [ ThemeController::class,'update'])->name('themes.update'); 
                        Route::get('themes/destroy/{id}', [ ThemeController::class,'destroy'])->name('themes.destroy');
                        Route::resource('tax', TaxController::class); 
                        // Route::resource('store-place-order-prefer', StorePlaceOrderPreferController::class);
                        Route::resource('store-order-methods', OrderMethodsController::class);
                        Route::get('store-discount/create/{id?}', [StoreDiscountController::class,'create'])->name('store-discount.create');
                        Route::post('store-discount/update', [ StoreDiscountController::class,'update'])->name('store-discount.update'); 
                        Route::get('store-discount/destroy/{id}', [ StoreDiscountController::class,'destroy'])->name('store-discount.destroy'); 
                        Route::resource('store-discount', StoreDiscountController::class,['except' => ['create','update','destroy']]);
                        Route::post('customers/update', [ CustomerController::class,'update'])->name('customers.update'); 
                        Route::resource('customers', CustomerController::class,['except' => ['update']]);
                        Route::resource('customer-inquiries', CustomerInquiriesController::class,['except' => ['destroy']]);
                        Route::get('customer-banners/create/{id?}', [CustomerBannersController::class,'create'])->name('customer-banners.create');
                        Route::post('customer-banners/update', [ CustomerBannersController::class,'update'])->name('customer-banners.update'); 
                        Route::resource('customer-banners', CustomerBannersController::class,['except' => ['create','update','destroy']]);
                        Route::get('customer-inquiries/destroy/{id}', [ CustomerInquiriesController::class,'destroy'])->name('customer-inquiries.destroy'); 
                        Route::get('managepayment/create/{id?}', [ PaymentController::class,'create'])->name('managepayment.create');
                        Route::post('managepayment/update', [ PaymentController::class,'update'])->name('managepayment.update'); 
                        Route::get('managepayment/destroy/{id}', [ PaymentController::class,'destroy'])->name('managepayment.destroy'); 
                        Route::resource('managepayment', PaymentController::class,['except' => ['create','update','destroy']]);

                    });
                });
            }
        }
    });
    Route::post('city-list', [CommonController::class,'cityList'])->name('city-list');
    Route::post('state-list', [CommonController::class,'stateList'])->name('state-list');
    Route::post('timezone-list', [CommonController::class,'timezoneList'])->name('timezone-list');
});


