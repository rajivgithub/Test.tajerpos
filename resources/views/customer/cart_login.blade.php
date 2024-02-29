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
        <input type="hidden" class="translation-key" value="login_page_title">
        <div class="cart-area">
            <div class="container">
                <div class="cko-progress-tracker">
                    <div class="step-1" id="checkout-progress" data-current-step="1">
                        <div class="progress-bar">
                            <div class="step step-1">
                                <a href="{{ route($store_url.'.customer.view-cart') }}">
                                    <span> 1</span>
                                    <div class="step-label">{{ __('customer.bag') }}</div>
                                </a>
                            </div>
                            <div class="step step-2 current">
                                <a href="#">
                                    <span> 2</span>
                                    <div class="step-label">{{ __('customer.sign_in') }}</div>
                                </a>
                            </div>
                            <div class="step step-3">
                                <span> 3</span>
                                <div class="step-label">{{ __('customer.delivery_and_payment') }}</div>
                            </div>
                            <div class="step step-4">
                                <span> 4</span>
                                <div class="step-label">{{ __('customer.confirmation') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-5 justify-content-center">
                    <div class="col-md-6">
                        <div class="login-form p-5 bg-white pt-0">
                            <h5 class="mb-5">{{ __('customer.sign_in') }}</h5>
                            <p class="text-center mb-4">{{ __('customer.login_desc') }}</p>
                            <div class="row">
                                <form  method="POST" action="{{ route($store_url.'.customer-login') }}" autocomplete="off">
                                @csrf
                                    <input type="hidden" name="_type" value="{{ Crypt::encrypt('placeorder') }}">
                                    <div class="col-lg-12 input-field-div">
                                        <input type="email" class="required-field" data-label = "{{ __('customer.username') }}" name="email" placeholder="{{ __('customer.username') }}">
                                        @if ($errors->has('email'))
                                            <span class="text-danger error-message">{{ $errors->first('email') }}</span>
                                        @endif
                                        <span class="error error-message"></span>
                                    </div>
                                    <div class="col-lg-12 input-field-div">
                                        <input type="password" class="required-field" data-label = "{{ __('customer.password') }}" name="password" placeholder="{{ __('customer.password') }}">
                                        @if ($errors->has('password'))
                                            <span class="text-danger error-message">{{ $errors->first('password') }}</span>
                                        @endif
                                        <span class="error error-message"></span>
                                    </div>
                                    <!-- <div class="col-sm-12 pt-1 mt-md-0">
                                        <div class="forgotton-password_info float-end">
                                            <a href="{{ route($store_url.'.customer-forget-password') }}">Forgot Password?</a>
                                        </div>
                                    </div> -->
                                    <div class="col-lg-12 pt-4 text-center">
                                        <button class="btn custom-btn md-size" id="customer-login">{{ __('customer.sign_in') }}</button>
                                    </div>
                                    <div class="andro_auth-seperator">
                                        <span>{{ __('customer.or') }}</span>
                                    </div>
                                    <p class="text-center">{{ __('customer.register_content') }} <a href="{{ url($store_url.'/customer-register/'.Crypt::encrypt('placeorder')) }}">{{ __('customer.create_one') }}</a></p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('common.customer.footer')
        @include('common.customer.script')
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script>
            $(document).on("click","#customer-login",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
        </script>
    </body>
</html>