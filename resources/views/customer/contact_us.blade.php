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
        <input type="hidden" class="translation-key" value="contact_us_title">
        <div class="contact_page_section mb-100">
            <div class="container">
                <div class="contact_details">
                    <div class="row">
                        <div class="col-lg-7 col-md-6">
                            <div class="contact_info_content">
                                <h2>{{ __('customer.contact_us_description') }}</h2>
                                <div class="contact_info_details mb-45">
                                    <h3>{{ __('customer.store_address') }}</h3>
                                    <h5 class="company-name"></h5>
                                    <p class="customer-store-address"></p>
                                    <p><a href="" class="customer-store-phone-number"></a></p>
                                    <p><a href="#" class="customer-store-email-id"></a></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-6">
                            <div class="contact_form" data-bgimg="{{ URL::asset('assets/customer/images/others/contact-form-bg-shape.png') }}">
                                <h2>{{ __('customer.send_ur_queries') }}</h2>
                                <form method="POST" action="{{ route($store_url.'.customer.contact-us') }}"> 
                                @csrf
                                    <div class="form_input input-field-div">
                                        <input name="contactor_name" data-max="100" class="required-field mb-10" data-label = "{{ __('customer.name') }}" placeholder="{{ __('customer.name') }}*" type="text">
                                        @if ($errors->has('contactor_name'))
                                            <span class="text-danger error-message">{{ $errors->first('contactor_name') }}</span>
                                        @endif
                                        <span class="error error-message"></span>
                                    </div>
                                    <div class="form_input input-field-div">
                                        <input name="contactor_email" data-max="150" class="required-field mb-10" data-label = "{{ __('customer.email') }}" placeholder="{{ __('customer.email') }}*" type="text">
                                        @if ($errors->has('contactor_email'))
                                            <span class="text-danger error-message">{{ $errors->first('contactor_email') }}</span>
                                        @endif
                                        <span class="error error-message"></span>
                                    </div>
                                    <div class="form_input input-field-div">
                                        <input name="contactor_phone_no" data-max="20" class="mb-10" data-label = "{{ __('customer.phone_number') }}" onkeypress="return isNumber(event)" placeholder="{{ __('customer.phone_number') }}" type="text">
                                        @if ($errors->has('contactor_phone_no'))
                                            <span class="text-danger error-message">{{ $errors->first('contactor_phone_no') }}</span>
                                        @endif
                                        <span class="error error-message"></span>
                                    </div>
                                    <div class="form_textarea input-field-div">
                                        <textarea name="contactor_message" class="required-field mb-10" data-label = "{{ __('customer.comment') }}" placeholder="{{ __('customer.comment') }}*"></textarea>
                                        @if ($errors->has('contactor_message'))
                                            <span class="text-danger error-message">{{ $errors->first('contactor_message') }}</span>
                                        @endif
                                        <span class="error error-message"></span>
                                    </div>
                                    <div class="form_input_btn">
                                        <button type="submit" class="btn btn-link submit-contact-us">{{ __('customer.send_msg') }}</button>
                                    </div>
                                    <p class="form-message"></p>
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
            $(document).on("click",".submit-contact-us",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else 
                    return true;     
            });
        </script>
    </body>
</html>