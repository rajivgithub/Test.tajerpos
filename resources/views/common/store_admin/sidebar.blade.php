<aside class="main-sidebar">
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="{{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/home') || request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/profile') || request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/change-password')) ? 'active' : '' }}">
                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.home')}}">
                            <i class="fa fa-th-large"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="treeview {{ ((request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/roles*')) || (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/users*')) || (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/permission*'))) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-user-circle-o"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <span>Manage Admin</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.users.index')}}" class="{{ request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/users*') ? 'active' : '' }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Admin Users</a></li>
                            <li><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.roles.index')}}" class="{{ request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/roles*') ? 'active' : '' }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Roles</a></li>
                            <li><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.permission.index')}}" class="{{ request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/permission*') ? 'active' : '' }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Permissions</a></li>
                        </ul>
                    </li>
                    <li class="treeview {{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/product*')) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-shopping-bag"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <span>Manage Products</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product.create')}}" class="{{ request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/product/create') ? 'active' : '' }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Add New product</a></li>
                            <li><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product.index')}}" class="{{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/product*') && !(request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/product/create')) && !(request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/product/import')) && !(request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/product/reviews')))  ? 'active' : '' }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>All Products</a></li>
                            <!-- <li><a href="bulk-import.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Bulk Import</a></li>
                            <li><a href="#"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Bulk Export</a></li>
                            <li><a href="product-reviews.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Product Reviews</a></li> -->
                        </ul>
                    </li>
                    <li class="treeview {{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/category*') || request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/sub-category*')) ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-th-list"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            <span>Manage Categories</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.category.index') }}" class="{{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/category*') && !(request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/category/create'))) ? 'active' : '' }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>All Categories</a></li>
                            <li><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.category.create') }}" class="{{ request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/category/create') ? 'active' : '' }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Add Categories</a></li>
                            <li><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.sub-category.index') }}" class="{{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/sub-category*') && !(request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/sub-category/create'))) ? 'active' : '' }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>All Sub Categories</a></li>
                            <li><a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.sub-category.create') }}" class="{{ request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/sub-category/create') ? 'active' : '' }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Add Sub Categories</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/store-order*')) ? 'active' : '' }}">
                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.index') }}"><i class="fa fa-shopping-basket"></i><span>Store Orders</span></a>
                    </li>

                    <!-- <li class="treeview">
                        <a href="#">
                            <i class="fa fa-shopping-basket"><span class="path1"></span><span class="path2"></span></i>
                            <span>Manage Orders</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="online-orders.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Online Orders</a></li>
                            <li><a href="store-orders.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Store Orders</a></li>
                        </ul>
                    </li> -->
                    <!-- <li class="treeview">
                        <a href="#">
                            <i class="fa fa-file-text-o"><span class="path1"></span><span class="path2"></span></i>
                            <span>Manage Reports</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="online-sale-report.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Online Sale Reports</a></li>
                            <li><a href="stock-report.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Products Stock</a></li>
                            <li><a href="wish-report.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Products wishlist</a></li>
                            <li><a href="user-search-report.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>User Searches</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="manage-colors.php">
                            <i class="fa fa-tint"><span class="path1"></span><span class="path2"></span></i>
                            <span>Theme Colors</span>					
                        </a>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-tags"><span class="path1"></span><span class="path2"></span></i>
                            <span>Manage Promote</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="flash-deals.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Flash deals</a></li>
                            <li><a href="newsletter.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Newsletters</a></li>
                            <li><a href="subscribers.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Subscribers</a></li>
                            <li><a href="coupon.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Coupon</a></li>
                        </ul>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-question-circle"><span class="path1"></span><span class="path2"></span></i>
                            <span>Support</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="flash-deals.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Ticket</a></li>
                            <li><a href="newsletter.php"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Product Queries</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="general-setting.php">
                            <i class="fa fa-cog"><span class="path1"></span><span class="path2"></span></i>
                            <span>General Settings</span>					
                        </a>				
                    </li>
                    <li>
                        <a href="manage-vax-tax.php">
                            <i class="fa fa-file-text-o"><span class="path1"></span><span class="path2"></span></i>
                            <span>Manage Vat & TAX</span>					
                        </a>				
                    </li>
                    <li>
                        <a href="email-setting.php">
                            <i class="fa fa-envelope-o"><span class="path1"></span><span class="path2"></span></i>
                            <span>Manage Email Settings</span>					
                        </a>				
                    </li>
                    <li>
                        <a href="payment-method.php">
                            <i class="fa fa-id-card-o"><span class="path1"></span><span class="path2"></span></i>
                            <span>Manage Payment Gateway</span>					
                        </a>				
                    </li>
                    <li>
                        <a href="file-system.php">
                            <i class="fa fa-file-code-o"><span class="path1"></span><span class="path2"></span></i>
                            <span>File System Configuration</span>					
                        </a>				
                    </li>
                    <li>
                        <a href="shipping-configuration.php">
                            <i class="fa fa-truck"><span class="path1"></span><span class="path2"></span></i>
                            <span>Manage Shipping</span>					
                        </a>				
                    </li>
                    <li>
                        <a href="manage-api.php">
                            <i class="fa fa-map-signs"><span class="path1"></span><span class="path2"></span></i>
                            <span>Manage Api Credentials</span>					
                        </a>				
                    </li> -->
                </ul>
                <hr/>
                <div class="sidebar-widgets">
                    <div class="copyright text-left m-25">
                        <p><strong class="d-block">eMonta Cashier Dashboard</strong> © 2023 All Rights Reserved</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</aside>