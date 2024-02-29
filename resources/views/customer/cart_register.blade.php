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
        <input type="hidden" class="translation-key" value="register_page_title">
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
                            <h5 class="mb-5">{{ __('customer.create_account') }}</h5>
                            <p class="text-center mb-4">{{ __('customer.register_desc') }}</p>
                            <div class="row">
                                <form  method="POST" action="{{ route($store_url.'.customer-register') }}" autocomplete="off">
                                @csrf
                                    <input type="hidden" name="_type" value="{{ Crypt::encrypt('placeorder') }}">
                                    <div class="col-lg-12 input-field-div">
                                        <input type="text" class="required-field" data-max="150" data-label = "{{ __('customer.name') }}" placeholder="{{ __('customer.name') }}" name="customer_name" value="">
                                        @if ($errors->has('customer_name'))
                                        <span class="text-danger error-message">{{ $errors->first('customer_name') }}</span>
                                        @endif
                                        <span class="error error-message"></span>
                                    </div>
                                    <div class="col-lg-12 input-field-div">
                                        <input type="email" class="required-field" data-max="100" data-label = "{{ __('customer.email') }}" name="email" placeholder="{{ __('customer.email') }}">
                                        @if ($errors->has('email'))
                                            <span class="text-danger error-message">{{ $errors->first('email') }}</span>
                                        @endif
                                        <span class="error error-message"></span>
                                    </div>
                                    <div class="col-lg-12 input-field-div">
                                        <input type="text" class="required-field" data-max="20" data-label = "{{ __('customer.phone_number') }}" placeholder="{{ __('customer.phone_number') }}" name="phone_number" onkeypress="return isNumber(event)" value="">
                                        @if ($errors->has('phone_number'))
                                            <span class="text-danger error-message">{{ $errors->first('phone_number') }}</span>
                                        @endif
                                        <span class="error error-message"></span>
                                    </div>
                                    <div class="col-lg-12 input-field-div">
                                        <input type="password" class="required-field" data-max="255" data-label = "{{ __('customer.password') }}" name="password" placeholder="{{ __('customer.password') }}">
                                        @if ($errors->has('password'))
                                            <span class="text-danger error-message">{{ $errors->first('password') }}</span>
                                        @endif
                                        <span class="error error-message"></span>
                                    </div>
                                    <div class="col-lg-12 pt-4 text-center">
                                        <button class="btn custom-btn md-size" id="save-customer-info">{{ __('customer.sign-up') }}</button>
                                    </div>
                                    <div class="andro_auth-seperator">
                                        <span>{{ __('customer.or') }}</span>
                                    </div>
                                    <p class="text-center">{{ __('customer.login_content') }} <a href="{{ url($store_url.'/customer-login/'.Crypt::encrypt('placeorder')) }}">{{ __('customer.sign_in') }}</a> </p>
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
            $(document).on("click","#save-customer-info",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
        </script>
    </body>
</html>