<aside class="navbar-aside" id="offcanvas_aside">
    <div class="aside-top">
        <a href="{{ url(config('app.prefix_url').'/admin/home') }}" class="brand-wrap">
            <img src="{{ URL::asset('assets/cashier-admin/images/tajer-logo.png') }}" class="logo sidebar-logo" alt="TajerPOS" />
        </a>
        <div>
            <button class="btn btn-icon btn-aside-minimize"><i class="theme-color material-icons md-menu_open"></i></button>
        </div>
    </div>
    <nav>
        <ul class="menu-aside"> 
            <li class="menu-item {{ ((Route::currentRouteName() == config('app.prefix_url').'.admin.home') || (Route::currentRouteName() == config('app.prefix_url').'.admin.profile') || (Route::currentRouteName() == config('app.prefix_url').'.admin.change-password')) ? 'active' : '' }}">  
                <a class="menu-link" href="{{ url(config('app.prefix_url').'/admin/home') }}">
                    <i class="icon material-icons md-dashboard"></i>
                    <span class="text">{{ trans('admin.dashboard') }}</span>
                </a>
            </li>
            <li class="menu-item {{ ((Route::currentRouteName() == config('app.prefix_url').'.admin.store.index') || (Route::currentRouteName() == config('app.prefix_url').'.admin.store.show')) ? 'active' : '' }}">
                <a class="menu-link" href="{{ url(config('app.prefix_url').'/admin/store') }}">
                    <i class="icon material-icons md-verified_user"></i>
                    <span class="text">{{ trans('admin.all_store') }}</span>
                </a>
            </li>
            <li class="menu-item {{ (Route::currentRouteName() == config('app.prefix_url').'.admin.store.create') ? 'active' : '' }}">
                <a class="menu-link" href="{{ url(config('app.prefix_url').'/admin/store/create') }}">
                    <i class="icon material-icons md-shopping_bag"></i>
                    <span class="text">{{ trans('admin.add_new_store') }}</span>
                </a>
            </li>
            <li class="menu-item {{ (Route::currentRouteName() == config('app.prefix_url').'.admin.general-settings') ? 'active' : '' }}">
                <a class="menu-link" href="{{ url(config('app.prefix_url').'/admin/general-settings') }}">
                    <i class="icon material-icons md-settings"></i>
                    <span class="text">{{ trans('admin.general_settings') }}</span>
                </a>
            </li>
        </ul>
        <br />
        <br />
    </nav>
</aside>