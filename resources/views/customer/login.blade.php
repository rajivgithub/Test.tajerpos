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
        <div class="login-register-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 offset-md-1">
                        <div class="row bg-cover bg-center align-items-center  dark-overlay p-0" style="background-image: url('/assets/customer/images/auth.jpg')">
                            <div class="col-lg-6 p-0">
                                <div class="andro_auth-description dark-overlay-2" >
                                    <div class="andro_auth-description-inner">
                                        <h2>{{ __('customer.welcome_back') }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 p-0">
                                <form  method="POST" action="{{ route($store_url.'.customer-login') }}" autocomplete="off">
                                @csrf
                                    <input type="hidden" name="current_url" value="{{ $current_url }}">
                                    <div class="login-form andro_auth-form">
                                        <h4 class="login-title text-center mb-2">{{ __('customer.sign_in') }}</h4>
                                        <p class="text-center mb-4">{{ __('customer.login_desc') }}</p>
                                        <div class="row">
                                            <div class="col-lg-12 input-field-div">
                                                <input type="email" data-max="100" data-error-msg="{{ __('validation.email_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF._%+\-@]+$" onkeypress="return restrictCharacters(event)" class="required-field form-input-field" data-label = "{{ __('customer.username') }}" placeholder="{{ __('customer.username') }}" name="email" value="">
                                                @if ($errors->has('email'))
                                                    <span class="text-danger error-message">{{ $errors->first('email') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="col-lg-12 input-field-div">
                                                <input type="password" data-max="100" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF!@#$%^&*_=.,~/<:;?+\-]+$" onkeypress="return restrictCharacters(event)" class="required-field form-input-field" data-label = "{{ __('customer.password') }}" placeholder="{{ __('customer.password') }}" name="password" value="">
                                                @if ($errors->has('password'))
                                                    <span class="text-danger error-message">{{ $errors->first('password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <!-- <div class="col-sm-12 pt-1 mt-md-0">
                                                <div class="forgotton-password_info float-end">
                                                    <a href="{{ route($store_url.'.customer-forget-password') }}">{{ __('customer.forgot_password') }}?</a>
                                                </div>
                                            </div> -->
                                            <div class="col-lg-12 pt-4 text-center">
                                                <button class="btn custom-btn md-size" id="customer-login">{{ __('customer.sign_in') }}</button>
                                            </div>
                                            <div class="andro_auth-seperator">
                                                <span>{{ __('customer.or') }}</span>
                                            </div>
                                            <!-- <div class="andro_social-login">
                                                <button type="button" class="andro_social-login-btn facebook"><i class="ion-social-facebook"></i> Continue with Facebook </button>
                                                <button type="button" class="andro_social-login-btn google"><i class="ion-social-google"></i> Continue with Google</button>
                                            </div> -->
                                            <p class="text-center">{{ __('customer.register_content') }} <a href="{{ route($store_url.'.customer-register') }}">{{ __('customer.create_one') }}</a> </p>
                                        </div>
                                    </div>
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