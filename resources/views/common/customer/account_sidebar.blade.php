<div class="col-sm-12 col-md-4 col-lg-4 pb-30">
    <div class="account-sidebar around-border">
        <ul class="account-sidebar-list">
            <li class="{{ (request()->is($store_url.'/customer/dashboard')) ? 'active' : '' }}"><a href="{{ route($store_url.'.customer.dashboard') }}">{{ __('customer.your_account') }}</a></li>
            <li class="{{ (request()->is($store_url.'/customer/orders*')) ? 'active' : '' }}"><a href="{{ route($store_url.'.customer.orders.index') }}">{{ __('customer.your_orders') }}</a></li>
            <li class="{{ (request()->is($store_url.'/customer/address*')) ? 'active' : '' }}"><a href="{{ route($store_url.'.customer.address.index') }}">{{ __('customer.your_addresses') }}</a></li>
            <li>
                <a href="{{ route($store_url.'.customer.logout')}}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="material-icons md-exit_to_app"></i>{{ __('customer.sign_out') }}</a>
                <form id="logout-form" action="{{ route($store_url.'.customer.logout')}}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</div>