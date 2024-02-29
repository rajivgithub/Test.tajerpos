<header class="header_section">
    <div class="header_top">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="header_top_inner d-flex justify-content-between">
                        <div class="welcome_text">
                            <p>{{ __('customer.welcome_message') }}</p>
                        </div>
                        <div class="header_top_sidebar d-flex align-items-center">
                            <ul class="d-flex">
                                <li><i class="icofont-phone"></i> <a class="customer-store-phone-number" href="#"></a>
                                </li>
                                <li><i class="icofont-envelope"></i> <a class="customer-store-email-id" href="#"></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <input type="hidden" class="get-product-count-url" value="{{ route($store_url.'.customer.get-product-count') }}">
                <input type="hidden" class="store-details-url" value="{{ route($store_url.'.customer.get-store-details') }}">
                <input type="hidden" class="add-wishlist-url" value="{{ route($store_url.'.customer.wishlist.store') }}">
                <input type="hidden" class="wishlist-url" value="{{ route($store_url.'.customer.show-wishlist-product') }}">
                <input type="hidden" class="add-to-cart-url" value="{{ route($store_url.'.customer.add-to-cart') }}">
                <input type="hidden" class="get-product-quantity-url" value="{{ route($store_url.'.customer.get-product-quantity') }}">
                <input type="hidden" class="variants-by-product" value="{{ route($store_url.'.customer.variants-by-product') }}">
                <div class="main_header d-flex justify-content-between align-items-center">
                    <div class="main_menu d-none d-lg-block">
                        <nav>
                            <ul class="d-flex">
                                <li class="megamenu-holder">
                                    <a class="{{ (Route::currentRouteName() == $store_url.'.customer.home') ? 'active' : '' }}" href="{{ route($store_url.'.customer.home') }}">{{ __('customer.home') }}</a>
                                    <!-- <ul class="megamenu grid-container">
                                        <li class="grid-item">
                                            <span class="title">List of Categories</span>
                                            <ul>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Cold coffee</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Hot Coffee</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Mojito</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Ice Tea</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="grid-item">
                                            <span class="title">List of Categories</span>
                                            <ul>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Croissant</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Clup</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Manooshat</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Sweet & Snacks</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="grid-item">
                                            <span class="title">List of Categories</span>
                                            <ul>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Salads</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Juice</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Other</a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="grid-item">
                                            <span class="title">List of Categories</span>
                                            <ul>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Croissant</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Clup</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Manooshat</a>
                                                </li>
                                                <li>
                                                    <a href="shop-left-sidebar.php">Sweet & Snacks</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul> -->
                                </li>
                                <li><a class="{{ (Route::currentRouteName() == $store_url.'.customer.category') ? 'active' : '' }}" href="{{ route($store_url.'.customer.category',Crypt::encrypt('products')) }}">{{ __('customer.products') }}</a></li>
                                <li><a class="{{ (Route::currentRouteName() == $store_url.'.customer.contact-us') ? 'active' : '' }}" href="{{ route($store_url.'.customer.contact-us') }}">{{ __('customer.contact_us') }}</a></li>
                                <!-- <li><a href="#">About</a></li> -->
                            </ul>
                        </nav>
                    </div>
                    <div class="header_logo">
                        <a class="sticky_none" href="{{ route($store_url.'.customer.home') }}"><img class="logo-in-customer" src="" alt=""></a>
                    </div>
                    <div class="header_account ">
                        <ul class="d-flex">
                            <li>
                                @include('common.language')
                            </li>
                            <li class="header_wishlist account_link">
                                <a href="{{ route($store_url.'.customer.dashboard') }}" ><i class="pe-7s-user"><span style="font-size:15px;">{{ (auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $store_id) ? session('authenticate_user')->customer_name : ""; }}</span></i></a>
                            </li>
                            @if((auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $store_id))
                                <li class="header_wishlist"><a href="{{ route($store_url.'.customer.wishlist.index') }}"><i class="pe-7s-like"></i></a></li>
                            @endif
                            <li class="shopping_cart"><a href="{{ route($store_url.'.customer.view-cart') }}"><i class="pe-7s-shopbag"></i></a>
                                <span class="shopping-cart-count"></span>
                            </li>
                            <!-- <li class="shopping_cart mini-cart-popup"><a href="#"><i class="pe-7s-shopbag"></i></a>
                                <span class="shopping-cart-count"></span>
                            </li> -->
                        </ul>
                        <div class="canvas_open">
                            <a href="javascript:void(0)"><i class="ion-navicon"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>