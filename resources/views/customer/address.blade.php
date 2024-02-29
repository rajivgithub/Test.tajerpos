<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        @include('common.customer.header')
    </head>
    <body>
        <div class="body_overlay"></div>
        @include('common.customer.mobile_navbar')
        @include('common.customer.navbar')
        @include('common.customer.mini_cart')
        @include('common.customer.breadcrumbs')
        <input type="hidden" class="translation-key" value="address_page_title">
        <input type="hidden" class="address-list-url" value="{{ route($store_url.'.customer.address.index') }}">
        <div class="account-page-area">
            <div class="container">
                <div class="row">
                    @include('common.customer.account_sidebar')
                    <div class="col-sm-12 col-md-8 col-lg-8 pb-30">
                        <div class="account-info">
                            <h3 class="title-tag mb-3">{{ __('customer.your_addresses') }}</h3>
                            <div class="row gy-3 address-details-container">
                            </div>
                            <div class="dnone clone-address-details">
                                <div class="col-lg-4 col-sm-6">
                                    <a href="#address" class="card bg-light bg-opacity-25 border border-light-subtle shadow-none h-100 text-center">
                                        <div class="card-body d-flex justify-content-center align-items-center">
                                            <div>
                                                <div class="fs-4xl mb-2"><i class="fa fa-plus-circle"></i></div>
                                                <div class="fw-medium mt-n1 text-primary-emphasis stretched-link address-details-info" data-type="add" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">{{ __('customer.add_address') }}</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('common.customer.address_popup')
        @include('common.customer.footer')
        @include('common.customer.script')
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
        <script src="{{ URL::asset('assets/customer/js/address.js') }}"></script>
    </body>
</html>