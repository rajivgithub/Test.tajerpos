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
        <input type="hidden" class="translation-key" value="reset_password_page_title">
        <div class="login-register-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 offset-md-1">
                        <div class="row bg-cover bg-center align-items-center  dark-overlay p-0" style="background-image: url('/assets/customer/images/auth.jpg')">
                            <div class="col-lg-6 p-0">
                                <div class="andro_auth-description dark-overlay-2" >
                                    <div class="andro_auth-description-inner">
                                        <h2>{{ __('customer.welcome_back') }}</h2>
                                        <p>{{ __('customer.reset_pwd_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 p-0">
                                <form  method="POST" action="{{ route($store_url.'.customer-reset-password') }}" autocomplete="off">
                                @csrf
                                    <div class="login-form andro_auth-form">
                                        <h4 class="login-title text-center mb-4">{{ __('customer.reset_password') }}</h4>
                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <div class="row">
                                            <div class="col-lg-12 input-field-div">
                                                <input type="email" class="form-control required-field form-input-field" data-error-msg="{{ __('validation.email_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF._%+\-@]+$" onkeypress="return restrictCharacters(event)" data-max="100" data-label = "{{ __('customer.email') }}" placeholder="{{ __('customer.email') }}" name="email" value="">
                                                @if ($errors->has('email'))
                                                    <span class="text-danger error-message">{{ $errors->first('email') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>	
                                        </div>	
                                        <div class="row">
                                            <div class="col-lg-12 input-field-div">
                                                <input type="password" class="form-control required-field form-input-field" data-label = "{{ __('customer.password') }}" placeholder="{{ __('customer.password') }}" name="password" data-max="100" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF!@#$%^&*_=.,~/<:;?+\-]+$" onkeypress="return restrictCharacters(event)" value="">
                                                @if ($errors->has('password'))
                                                    <span class="text-danger error-message">{{ $errors->first('password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>	
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12 input-field-div">
                                                <input type="password" class="form-control required-field form-input-field" data-label = "{{ __('customer.confirm_password') }}" data-max="100" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF!@#$%^&*_=.,~/<:;?+\-]+$" onkeypress="return restrictCharacters(event)" placeholder="{{ __('customer.confirm_password') }}" name="password_confirmation" value="">
                                                @if ($errors->has('password_confirmation'))
                                                    <span class="text-danger error-message">{{ $errors->first('password_confirmation') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>	
                                        </div>	
                                        <div class="col-lg-12 mb-3 pt-4 text-center">	
                                            <button type="submit" class="btn custom-btn md-size" id="reset-password">{{ __('customer.reset_password') }}</button>
                                        </div>	
                                        <div class="andro_auth-seperator">
                                            <span>{{ __('customer.or') }}</span>
                                        </div>
                                        <!-- <div class="andro_social-login"></div> -->
                                        <p class="text-center">{{ __('customer.login_content') }} <a href="{{ route($store_url.'.customer-login') }}">{{ __('customer.sign_in') }}</a> </p> 
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
            $(document).on("click","#reset-password",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
        </script>
    </body>
</html>