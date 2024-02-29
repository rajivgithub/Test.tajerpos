@php
    $sidebar_background_color = (!empty(Auth::user()->is_admin) && Auth::user()->is_admin == 2) ? "style=background-color:#1e2122" : "style=background-color:#00426a";
@endphp
<div class="page-loader">
    <div class="spinner" {{$sidebar_background_color}} ></div>
</div> 
<header class="main-header">
    <div class="d-flex align-items-center logo-box justify-content-start" {{$sidebar_background_color}}>
        <a href="#" class="waves-effect waves-light nav-link d-none d-md-inline-block mx-10 push-btn bg-transparent hover-primary" data-toggle="push-menu" role="button">
            <!-- <span class="icon-Align-left"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span> -->
        </a>	
        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.home') }}" class="logo">
            <div class="logo-lg">
                @if(!empty($store_logo) && !empty($store_logo[0]['store_logo']))
                    <span class="light-logo"><img style="width :120px; height : 69px;" src="{{ $store_logo[0]['store_logo'] }}" alt="logo"></span>
                @else
                    <h4 style="color: #ffffff;font-size: 28px;">LOGO</h4>
                @endif
            </div>
        </a>
    </div>
    <nav class="navbar navbar-static-top">
        <div class="app-menu">
            <ul class="header-megamenu nav">
                <li class="btn-group nav-item d-md-none">
                    <a href="#" class="waves-effect waves-light nav-link push-btn btn-info-light" data-toggle="push-menu" role="button">
                        <span class="icon-Align-left"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>
                    </a>
                </li>
                <li class="btn-group nav-item d-none d-xl-inline-block">
                    <div class="app-menu">
                        <div class="search-bx mx-5">
                            <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.category-search') }}">
                            @csrf
                                <input type="hidden" name="type" value="search">
                                <input type="hidden" class="category-search-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.category-search') }}">
                                <input type="hidden" class="category-list-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.category-list') }}">
                                <input type="hidden" class="product-list-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product-list') }}">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <input type="hidden" class="category-search-name" value="{{ isset($search_type) && !empty($search_type) && $search_type == 'search' && !empty(Session::get('category_id_search')) ? Session::get('category_id_search')[0] : '' }}">
                                        <select class="form-control category-list-search" name="category_id" id="inline-form-category">									   
                                        </select>
                                    </div>
                                    <input type="search" class="form-control product-name-search" name="product_name" id="product-name-search" placeholder="Search Product Name" value="{{ isset($search_type) && !empty($search_type) && $search_type == 'search' && !empty(Session::get('product_name')) ? Session::get('product_name')[0] : '' }}" aria-label="Search" aria-describedby="button-addon2">
                                    <div class="input-group-append">
                                        <button class="btn" id="button-addon3"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="navbar-custom-menu r-side">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    @include('common.language')
                </li>
                <li class="dropdown notifications-menu">
                    <span class="label label-danger">5</span>
                    <a href="#" class="waves-effect waves-light dropdown-toggle btn-danger-light" data-toggle="dropdown" title="Notifications">
                        <i class="icon-Notifications"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                    <ul class="dropdown-menu animated bounceIn">
                        <li class="header">
                            <div class="p-20">
                                <div class="flexbox">
                                    <div><h4 class="mb-0 mt-0">Notifications</h4></div>
                                    <div><a href="#" class="text-danger">Clear All</a></div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <ul class="menu sm-scrol">
                                <li>
                                    <a href="#"><i class="fa fa-users text-info"></i> Curabitur id eros quis nunc suscipit blandit.</a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-warning text-warning"></i> Duis malesuada justo eu sapien elementum, in semper diam posuere.</a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-users text-danger"></i> Donec at nisi sit amet tortor commodo porttitor pretium a erat.</a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-shopping-cart text-success"></i> In gravida mauris et nisi</a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-user text-danger"></i> Praesent eu lacus in libero dictum fermentum.</a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-user text-primary"></i> Nunc fringilla lorem </a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-user text-success"></i> Nullam euismod dolor ut quam interdum, at scelerisque ipsum imperdiet.</a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer"><a href="#">View all</a></li>
                    </ul>
                </li>
                <li class="dropdown messages-menu chat-details">
                    <span class="unread-chat-count"></span>
                    <input type="hidden" class="chat-list-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.chat-list') }}">
                    <a href="#" class="dropdown-toggle btn-primary-light chat-list" title="Messages">
                        <i class="icon-Incoming-mail"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                    <!-- <a href="#" class="dropdown-toggle btn-primary-light chat-list" data-toggle="dropdown" title="Messages">
                        <i class="icon-Incoming-mail"><span class="path1"></span><span class="path2"></span></i>
                    </a> -->
                    <ul class="dropdown-menu animated bounceIn chat-dropdown-list" style="width: 250px;">
                        <li class="header">
                            <div class="p-20">
                                <div class="flexbox">
                                    <div>
                                        <h4 class="mb-0 mt-0">Messages</h4>
                                    </div>
                                    <div>
                                        <!-- <a href="#" class="text-danger">Clear All</a> -->
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <ul class="menu sm-scrol all-chat-list" style="max-height: 250px; overflow-y: scroll;">
                            </ul>
                        </li>
                    </ul>
                </li>
                <!-- User Account-->
                <li class="dropdown user user-menu">
                    <input type="hidden" class="auth-user-name" value="{{ Auth::user()->name }}">
                    <input type="hidden" class="profile-image-name" value="{{ Auth::user()->profile_image }}">
                    <a href="#" class="dropdown-toggle p-0 text-dark hover-primary ml-md-30 ml-10" data-toggle="dropdown" title="User">
                        <span class="pl-30 d-md-inline-block d-none">{{ __('store-admin.hello') }},</span> <strong class="d-md-inline-block d-none">{{ Auth::user()->name }}</strong>
                        @if(!empty(Auth::user()->profile_image))
                            <img class="user-image rounded-circle avatar bg-white mx-10 profile-image" src="{{ Auth::user()->profile_image }}" alt="User" />
                        @else
                            <div class="user-image rounded-circle avatar mx-10 default-profile-image"></div>
                        @endif
                    </a>
                    <ul class="dropdown-menu animated flipInX">
                        <li class="user-body">
                            <a class="dropdown-item" href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.profile') }}"><i class="fa fa-user mr-2"></i> {{ __('store-admin.profile') }}</a>				
                            <a class="dropdown-item" href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.change-password') }}"><i class="fa fa-gear mr-2"></i> {{ __('store-admin.reset_password') }}</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.logout')}}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fa fa-sign-out mr-2"></i> {{ __('store-admin.logout') }}</a>
                            <form id="logout-form" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.logout')}}" method="POST" style="display: none;">
                            @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<aside class="control-sidebar message-area">
    <div class="rpanel-title"><span class="pull-right p-1 btn btn-danger"><i class="fa fa-close text-white close-chat-option" data-toggle="control-sidebar"></i></span></div>
    <div class="media-list media-list-hover mt-10">
        <div class="w-100 user-chat">
            <div class="">
                <div class="p-2 border-bottom ">
                    <div class="row">
                        <div class="col-9 col-md-7">
                            <h5 class="font-size-15 mb-1 message-user-name"></h5>
                        </div>
                    </div>
                </div>
            <div>
            <div class="chat-conversation p-3">
                <ul class="list-unstyled">
                    <div class="scrollbar-container ps ps--active-y chat-box-area">
                    </div>
                </ul>
            </div>
            <div class="p-3 chat-input-section">
                <form action="#" class="typing-area">
                    <div class="row">
                        <div class="col mr-1">
                            <div class="position-relative">
                                <input type="hidden" class="insert-chat-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.insert-chat') }}">
                                <input type="hidden" class="get-chat-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.get-chat') }}">
                                <input type="hidden" class="unread-chat-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.unread-chat-count') }}">
                                <input type="hidden" class="incoming-msg-id" name="incoming_msg_id" value="" >
                                <input type="text" name="message" class="form-control chat-input" placeholder="Enter Message..." value="">
                            </div>
                        </div>
                        <div class="col-auto col"><button style="padding: 4px 8px;" class="btn btn-primary btn-rounded chat-send w-md  btn btn-primary"> <i class="fa fa-send"></i></button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</aside>
<div class="control-sidebar-bg"></div>