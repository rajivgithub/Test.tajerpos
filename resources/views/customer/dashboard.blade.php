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
        <input type="hidden" class="translation-key" value="account_page_title">
        <div class="account-page-area">
            <div class="container">
                <div class="row">
                    @include('common.customer.account_sidebar')
                    <div class="col-sm-12 col-md-8 col-lg-8 pb-30">
                        <div class="account-info">
                            <form method="POST" action="{{ route($store_url.'.customer.profile') }}" class="form-element-data">
                            @csrf
                                <div class="account-setting-item">
                                    <div class="sub-section-title">
                                        <h3 class="title-tag mb-3">{{ __('customer.profile') }}</h3>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('customer.name') }}<span>*</span></label>
                                                <input type="text" class="form-control required-field form-input-field" name="customer_name" data-label = "{{ __('customer.name') }}" data-error-msg="{{ __('validation.invalid_name_err') }}" data-pattern="^[A-Za-z\u0600-\u06FF. ]+$" onkeypress="return restrictCharacters(event)" data-max="100" value="{{ !empty($customer_details) && !empty($customer_details[0]->customer_name) ? $customer_details[0]->customer_name : '' }}" placeholder="">
                                                @if ($errors->has('customer_name'))
                                                    <span class="text-danger error-message">{{ $errors->first('customer_name') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('customer.email') }}<span>*</span></label>
                                                <input type="email" class="form-control required-field form-input-field" name="email" data-label = "{{ __('customer.email') }}" data-error-msg="{{ __('validation.email_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF._%+\-@]+$" onkeypress="return restrictCharacters(event)" data-max="100" value="{{ !empty($customer_details) && !empty($customer_details[0]->email) ? $customer_details[0]->email : '' }}" placeholder="">
                                                @if ($errors->has('email'))
                                                    <span class="text-danger error-message">{{ $errors->first('email') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('customer.phone_number') }}<span>*</span></label>
                                                <input type="text" class="form-control required-field form-input-field" name="phone_number" data-min="10" data-max="12"  data-pattern="^[0-9]+$" data-error-msg="{{ __('validation.invalid_numeric_err') }}" onkeypress="return restrictCharacters(event)" data-label = "{{ __('customer.phone_number') }}" value="{{ !empty($customer_details) && !empty($customer_details[0]->phone_number) ? $customer_details[0]->phone_number : '' }}" placeholder="">
                                                @if ($errors->has('phone_number'))
                                                    <span class="text-danger error-message">{{ $errors->first('phone_number') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="account-setting-item account-setting-button">
                                        <button class="btn btn-small" id="save-profile-info">{{ __('customer.save') }}</button>
                                    </div>
                                </div>
                                <div class="account-setting-item account-setting-button"></div>
                            </form>
                            <form method="POST" action="{{ route($store_url.'.customer.update-password') }}" class="form-element-data">
                            @csrf
                                <div class="account-setting-item account-setting-avatar">
                                    <div class="sub-section-title">
                                        <h3 class="title-tag mb-3">{{ __('customer.reset_password_cart_title') }}</h3>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6 col-lg-6">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('customer.current_password') }}<span>*</span></label>
                                                <div class="input-group">
                                                    <input type="password" data-max="100" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF!@#$%^&*_=.,~/<:;?+\-]+$" onkeypress="return restrictCharacters(event)" data-label = "{{ __('customer.current_password') }}" class="form-control input-field required-field form-input-field" name="current_password" placeholder="">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><span class="fa fa-fw fa-eye field_icon user-password"></span></span>
                                                    </div>
                                                </div>
                                                @if ($errors->has('current_password'))
                                                    <span class="text-danger error-message">{{ $errors->first('current_password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('customer.new_password') }}<span>*</span></label>
                                                <div class="input-group">
                                                    <input type="password" data-max="100" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF!@#$%^&*_=.,~/<:;?+\-]+$" onkeypress="return restrictCharacters(event)" data-label = "{{ __('customer.new_password') }}" class="form-control input-field required-field form-input-field" name="new_password" placeholder="">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><span class="fa fa-fw fa-eye field_icon user-password"></span></span>
                                                    </div>
                                                </div>
                                                @if ($errors->has('new_password'))
                                                    <span class="text-danger error-message">{{ $errors->first('new_password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('customer.confirm_password') }}<span>*</span></label>
                                                <div class="input-group">
                                                    <input type="password" data-max="100" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF!@#$%^&*_=.,~/<:;?+\-]+$" onkeypress="return restrictCharacters(event)" data-label = "{{ __('customer.confirm_password') }}" class="form-control input-field required-field form-input-field" name="confirm_password" placeholder="">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><span class="fa fa-fw fa-eye field_icon user-password"></span></span>
                                                    </div>
                                                </div>
                                                @if ($errors->has('confirm_password'))
                                                    <span class="text-danger error-message">{{ $errors->first('confirm_password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="account-setting-item account-setting-button">
                                        <button class="btn btn-small" id="save-password">{{ __('customer.reset_password') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('common.customer.footer')
        @include('common.customer.script')
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script>
            //Hide and show the password
            $(function () {
                $(document).on("click",".user-password",function() {
                    $(this).toggleClass("fa-eye fa-eye-slash");
                    var type = $(this).hasClass("fa-eye-slash") ? "text" : "password";
                    $(this).closest(".input-field-div").find(".input-field").attr("type", type);
                });
            });
            $(document).on("click","#save-password",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
            $(document).on("click","#upload-profile-image",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            }); 
            $(document).on("click","#save-profile-info",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            }); 
        </script>
    </body>
</html>