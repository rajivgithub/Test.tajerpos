<header class="header_section">
    <div class="header_top">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="header_top_inner d-flex justify-content-between">
                        <div class="welcome_text">
                            <p>Welcome to our store</p>
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
                <div class="main_header d-flex justify-content-between align-items-center">
                    <div class="header_logo">
                        <a class="sticky_none" href="{{ route($store_url.'.customer.home') }}"><img class="logo-in-customer" src="{{ URL::asset('assets/customer/images/logo/logo.png') }}" alt=""></a>
                    </div>
                    <div class="main_menu d-none d-lg-block">
                        <nav>
                            <ul class="d-flex">
                                <li><a class="active" href="{{ route($store_url.'.customer.home') }}">Home</a></li>
                                <li><a href="#">About</a></li>
                                <!-- <li class="megamenu-holder">
                                    <a href="shop-left-sidebar.php">Categories</a>
                                    <ul class="megamenu grid-container">
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
                                    </ul>
                                </li> -->
                                <li><a href="#0">Contact</a></li>
                            </ul>
                        </nav>
                    </div>
                    <!--main menu end-->
                    <div class="header_account ">
                        <ul class="d-flex">
                            <li class="header_wishlist account_link">
                                <a href="{{ route($store_url.'.customer.dashboard') }}" ><i class="pe-7s-user"></i></a>
                                <!-- <ul class="dropdown_account_link">
                                    <li><a href="{{ route($store_url.'.customer.dashboard') }}">My Account</a></li>
                                    <li><a href="{{ route($store_url.'.customer-login') }}">Login</a></li>
                                </ul> -->
                            </li>
                            <li class="header_wishlist"><a href="{{ route($store_url.'.customer.wishlist.index') }}"><i class="pe-7s-like"></i></a></li>
                            <li class="shopping_cart"><a href="{{ route($store_url.'.customer.view-cart') }}"><i class="pe-7s-shopbag"></i></a>
                                <span class="shopping-cart-count"></span>
                            </li>
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