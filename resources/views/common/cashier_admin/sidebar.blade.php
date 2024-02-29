@php
    $sidebar_background_color = (!empty(Auth::user()->is_admin) && Auth::user()->is_admin == 2) ? "style=background-color:#1e2122" : "style=background-color:#00426a";
@endphp
<aside class="main-sidebar" {{$sidebar_background_color}}>
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.home') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.profile') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.change-password')) ? 'active' : '' }}">  
                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.home')}}"><i class="fa fa-th-large"></i><span>{{ __('store-admin.dashboard') }}</span></a>
                    </li>
                    <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.view-cart')) ? 'active' : '' }}">
                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.index') }}">
                            <i class="fa fa-shopping-cart"><span class="path1"></span><span class="path2"></span></i>
                            <span>{{ __('store-admin.placeorder') }}</span>					
                        </a>
                    </li>
                    <li class="treeview {{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.show') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.online-orders.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.online-orders.show')) ? 'active' : '' }}">  
                        <a href="#">
                            <i class="fa fa-shopping-basket"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            <span>{{ __('store-admin.orders') }}</span>
                            <span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.show')) ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.store_orders') }}</a></li>
                            <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.online-orders.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.online-orders.show')) ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.online-orders.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.online_orders') }}</a></li>
                        </ul>
                    </li>
                    <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product.create') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product.show')) ? 'active' : '' }}">  
                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product.index')}}">
                            <i class="fa fa-shopping-bag"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <span>{{ __('store-admin.products') }}</span>
                        </a>
                    </li> 
                    <!-- <li class="{{ (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product-inventory') ? 'active' : '' }}">  
                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product-inventory')}}">
                            <i class="fa fa-th-list"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <span>Inventory</span>
                        </a>
                    </li>  -->
                    <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.category.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.category.create') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.category.show')) ? 'active' : '' }}">  
                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.category.index')}}">
                            <i class="fa fa-th-list"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <span>{{ __('store-admin.categories') }}</span>
                        </a>
                    </li> 
                    <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.sub-category.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.sub-category.create') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.sub-category.show')) ? 'active' : '' }}">  
                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.sub-category.index')}}">
                            <i class="fa fa-th-list"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <span>{{ __('store-admin.sub_categories') }}</span>
                        </a>
                    </li> 

                    <li class="treeview {{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order-status.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.online-order-status.index')) ? 'active' : '' }}">  
                        <a href="#">
                            <i class="fa fa-tags"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            <span>{{ __('store-admin.status') }}</span>
                            <span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order-status.index') ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order-status.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.store_order_status') }}</a></li>
                            <li class="{{ (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.online-order-status.index') ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.online-order-status.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.online_order_status') }}</a></li>
                        </ul>
                    </li>
                    @if(!empty(Auth::user()->is_admin) && Auth::user()->is_admin == 2) 
                        <li class="treeview {{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.tax.create') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order-methods.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-discount.index.create')) ? 'active' : '' }}">   
                            <a href="#">
                                <i class="fa fa-cog"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                <span>{{ __('store-admin.configuration') }}</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.tax.create')) ? 'active' : '' }}">  
                                    <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.tax.create') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.taxes') }}</a>
                                </li>
                                <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order-methods.index')) ? 'active' : '' }}">  
                                    <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order-methods.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.order_methods') }}</a>
                                </li>
                                <!-- <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-discount.index')  || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-discount.create')) ? 'active' : '' }}">  
                                    <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-discount.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.discounts') }}</a>
                                </li> -->
                            </ul>
                        </li>
                        <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.users.create') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.users.index')) ? 'active' : '' }}">
                            <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.users.index') }}">
                                <i class="fa fa-shopping-cart"><span class="path1"></span><span class="path2"></span></i>
                                <span>{{ __('store-admin.administrators') }}</span>					
                            </a>
                        </li>
                        <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.managepayment.create') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.managepayment.index')) ? 'active' : '' }}">
                            <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.managepayment.index') }}">
                                <i class="fa fa-credit-card"><span class="path1"></span><span class="path2"></span></i>
                                <span>{{ __('store-admin.payment_gateway') }}</span>					
                            </a>
                        </li>
                        <li class="treeview {{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.customer-inquiries.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.customer-inquiries.show')) ? 'active' : '' }}">  
                            <a href="#">
                                <i class="fa fa-user-circle-o"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                <span>{{ __('store-admin.manage_customers') }}</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="{{ (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.customers.index') ? 'active' : '' }}">  <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.customers.index')}}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.customers') }}</a></li> 
                                <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.customer-inquiries.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.customer-inquiries.show')) ? 'active' : '' }}">  <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.customer-inquiries.index')}}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.customer_inquiries') }}</a></li> 
                                <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.customer-banners.index') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.customer-banners.create')) ? 'active' : '' }}">  <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.customer-banners.index')}}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.banner_settings') }}</a></li> 
                            </ul>
                        </li>
                        
                    @endif
                    <!-- <li class="treeview {{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/product*')) ? 'active' : '' }}">
                        <a href="#">
                            <i class="icon-Clipboard-check"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <span>{{ __('store-admin.products') }}</span>
                            <span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/product/create') ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product.create')}}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Add New product</a></li>
                            <li class="{{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/product*') && !(request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/product/create'))) ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product.index')}}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>All Products</a></li>
                        </ul>
                    </li> -->
                    <!-- <li class="treeview {{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/category*') || request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/sub-category*')) ? 'active' : '' }}">
                        <a href="#">
                            <i class="icon-Dinner"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            <span>{{ __('store-admin.categories') }}</span>
                            <span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/category*') && !(request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/category/create'))) ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.category.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>All Categories</a></li>
                            <li class="{{ request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/category/create') ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.category.create') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Add Categories</a></li>
                            <li class="{{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/sub-category*') && !(request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/sub-category/create'))) ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.sub-category.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>All Sub Categories</a></li>
                            <li class="{{ request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/sub-category/create') ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.sub-category.create') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Add Sub Categories</a></li>
                        </ul>
                    </li> -->
                    <li class="{{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.analytics')) ? 'active' : '' }}">
                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.analytics') }}">
                            <i class="fa fa-id-card-o"><span class="path1"></span><span class="path2"></span></i>
                            <span>{{ __('store-admin.analytics') }}</span>					
                        </a>
                    </li>
                    <li class="treeview {{ ((Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.reports.transaction-report') || (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.reports.customer-report')) ? 'active' : '' }}">  
                        <a href="#">
                            <i class="fa fa-file-text-o"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            <span>{{ __('store-admin.reports') }}</span>
                            <span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.reports.transaction-report') ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.reports.transaction-report') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.transaction_report') }}</a></li>
                            <li class="{{ (Route::currentRouteName() == config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.reports.customer-report') ? 'active' : '' }}"><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.reports.customer-report') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>{{ __('store-admin.customer_report') }}</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="sidebar-widgets">
                    <div class="text-center">
                        <img src="{{ URL::asset('assets/cashier-admin/images/tajer-logo.png') }}" class="sideimg" alt="">
                    </div>
                    <!-- <div class="mx-25 mb-30 pb-20 side-bx bg-primary bg-food-dark rounded20">
                        <div class="text-center">
                            <img src="{{ URL::asset('assets/cashier-admin/images/tajer-logo.png') }}" class="sideimg" alt="">
                            <h3 class="title-bx">Order Now</h3>
                            <a href="#" class="text-white py-10 font-size-16 mb-0">Today is your day <i class="fa fa-angle-right"></i></a>
                        </div>
                    </div> -->
                    <div class="copyright text-left m-25">
                        <p><strong class="d-block">{{ __('store-admin.helpline') }}</strong></p>
                    </div>
                    <div class="copyright text-left m-25">
                        <p>{{ trans('store-admin.copyrights', ['year' => date('Y'), 'company_name' => Auth::user()->company_name]) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</aside>
