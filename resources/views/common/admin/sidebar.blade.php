<header class="main-header navbar">
    <div class="col-search"></div>
    <div class="col-nav">
        <button class="btn btn-icon btn-mobile me-auto" data-trigger="#offcanvas_aside"><i class="material-icons md-apps"></i></button>
        <ul class="nav">
            <!-- <li class="nav-item">
                <input type="text" placeholder="Search..." class="form-control search-store" value="">
            </li> -->
            <!-- <li class="dropdown nav-item ">
                <a class="nav-link btn-icon" class="dropdown-toggle" data-bs-toggle="dropdown" href="#" id="dropdownAccount" aria-expanded="false">
                    <i class="material-icons md-notifications animation-shake"></i>
                    <span class="badge rounded-pill"></span>
                </a> -->
                <!-- <div class="dropdown-menu dropdown-menu-end noti" aria-labelledby="dropdownAccount">
                    <a class="dropdown-item" href="#">You have 10 notifications</a>
                    <div class="dropdown-divider"></div>
                    <div class="d-flex align-items-center note-img p-2 border-bottom">	
                        <div class="col-1 me-2">
                            <p><span class="first-2 p-1 rounded-circle">12</span></p>
                        </div>
                        <div class="col-9"><h6>Delivery processing</h6></div>
                        <div class="col-2 text-center">10 min.</div>
                    </div>
                    <div class="d-flex align-items-center note-img p-2 border-bottom">	
                        <div class="col-1 me-2">
                            <p><span class="first-2 p-1 rounded-circle">55</span></p>
                        </div>
                        <div class="col-9"><h6>Order Complete</h6></div>
                        <div class="col-2 text-center">1hr</div>
                    </div>
                    <div class="d-flex align-items-center note-img p-2 border-bottom">	
                        <div class="col-1 me-2">
                            <p><span class="first-2 p-1 rounded-circle">10</span></p>
                        </div>
                        <div class="col-9"><h6>Tickets Generated</h6></div>
                        <div class="col-2 text-center">2hr</div>
                    </div>
                    <div class="d-flex align-items-center note-img p-2 ">	
                        <div class="col-1 me-2">
                            <p><span class="first-2 p-1 rounded-circle">12</span></p>
                        </div>
                        <div class="col-9"><h6>Delivery Complete</h6></div>
                        <div class="col-2 text-center">6hr.</div>
                    </div>						
                </div>
            </li> -->
            <!-- <li class="nav-item">
                <a class="nav-link btn-icon" href="#" >
                    <i class="fa fa-comment"></i>
                    <span class="badge rounded-pill"></span>
                </a>
            </li> -->
            <li class="nav-item">
                @include('common.language')
            </li> 
            <li class="dropdown nav-item admin-chat-details">
                <input type="hidden" class="chat-list-url" value="{{ route(config('app.prefix_url').'.admin.chat-list') }}">
                <a class="nav-link btn-icon admin-chat-list" href="#" id="dropdownAccount1">
                    <i class="fa fa-comment"></i>
                    <span class="admin-unread-chat-count" style="width:15px;height:15px;"></span>
                    <!-- <span class="badge rounded-pill"></span> -->
                </a>
                <!-- <a class="nav-link btn-icon admin-chat-list" href="#" id="dropdownAccount1" aria-expanded="false">
                    <i class="fa fa-comment"></i>
                    <span class="badge rounded-pill"></span>
                </a> -->
                <div class="dropdown-menu dropdown-menu-end noti admin-chat-dropdown-list">
                    <div class="d-flex justify-content-between px-2">
                        <div>
                            <h4 class="mb-0 mt-0">Messages</h4>
                        </div>
                        <div>
                            <a href="#" class="text-danger">View All</a>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <ul class="menu sm-scrol admin-chat-all-list" style="max-height: 200px; overflow-y: scroll;">
                    </ul>
                </div>
            </li>
            <li class="dropdown nav-item">
                <input type="hidden" class="auth-user-name" value="{{ Auth::user()->name }}">
                <input type="hidden" class="profile-image-name" value="{{ Auth::user()->profile_image }}">
                <a class="dropdown-toggle" data-bs-toggle="dropdown" href="#" id="dropdownAccount" aria-expanded="false"> 
                    @if(!empty(Auth::user()->profile_image))
                        <img class="icon-xs profile-image" src="{{ Auth::user()->profile_image }}" alt="User" />
                    @else
                        <div class="icon-xs default-profile-image"></div>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownAccount">
                    <a class="dropdown-item" href="{{ route(config('app.prefix_url').'.admin.profile') }}"><i class="material-icons md-perm_identity"></i>{{ trans('admin.edit_profile') }}</a>
                    <a class="dropdown-item" href="{{ route(config('app.prefix_url').'.admin.change-password') }}"><i class="material-icons md-settings"></i>{{ trans('admin.reset_password') }}</a>                             
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route(config('app.prefix_url').'.admin.logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="material-icons md-exit_to_app"></i>{{ trans('admin.logout') }}</a>
                    <form id="logout-form" action="{{ route(config('app.prefix_url').'.admin.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</header>
<aside class="control-sidebar message-area">
    <div class="rpanel-title"><span class="pull-right p-1 btn btn-danger"><i class="fa fa-close text-white admin-close-chat-option" data-toggle="control-sidebar"></i></span></div>
    <div class="media-list media-list-hover">
        <div class="w-100 user-chat">
            <div class="">
                <div class="p-2 border-bottom">
                    <div class="row">
                        <div class="col-9 col-md-7">
                            <h5 class="font-size-15 mb-1 message-user-name"></h5>
                            <!-- <p class="text-muted mb-0"><i class="fa fa-circle text-success align-middle me-2"></i>Active Now</p> -->
                        </div>
                    </div>
                </div>
                <div>
                    <div class="chat-conversation p-3">
                        <ul class="list-unstyled">
                            <div class="scrollbar-container ps ps--active-y admin-chat-box-area">
                            </div>
                        </ul>
                    </div>
                    <div class="p-3 chat-input-section">
                        <form action="#" class="typing-area">
                            <div class="row">
                                <div class="col mr-1">
                                    <div class="position-relative">
                                        <input type="hidden" class="insert-chat-url" value="{{ route(config('app.prefix_url').'.admin.insert-chat') }}">
                                        <input type="hidden" class="get-chat-url" value="{{ route(config('app.prefix_url').'.admin.get-chat') }}">
                                        <input type="hidden" class="admin-unread-chat-url" value="{{ route(config('app.prefix_url').'.admin.unread-chat-count') }}">
                                        <input type="hidden" class="incoming-msg-id" name="incoming_msg_id" value="" >
                                        <input type="hidden" class="user-store-id" name="user_store_id" value="" >
                                        <input type="hidden" class="get-logo-image" value="{{ route('get-logo-image') }}" >
                                        <input type="hidden" class="get-module-name" value="admin" >
                                        <input type="text" name="message" class="form-control chat-input" placeholder="Enter Message..." value="">
                                    </div>
                                </div>
                                <div class="col-auto col"><button type="button" style="padding: 15px 11px;" class="btn btn-primary btn-rounded chat-send w-md  btn btn-primary"> <i class="fa fa-send"></i></button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>