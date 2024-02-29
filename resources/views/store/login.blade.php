<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>{{ !empty($store_details) && !empty($store_details[0]->store_name) ? $store_details[0]->store_name : 'TajerPOS' }} | {{ __('store-admin.login_title') }}</title>
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('assets/store/images/theme/favicon.png') }}" />
        <!-- Template CSS -->
        <link rel="stylesheet" href="{{ URL::asset('assets/store/css/vendors_css.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
        <style>
            body,html {
                height: 100% !important;
            }
            body.rtl{
                text-align: right !important;
                direction: rtl;
			}
			.rtl .text-right{text-align: left !important;}
            .form-control{    
                background: #0f4161 !important;
                border: 1px solid #cfcfcf;    
                border-radius: 0px;
                color: #fff !important;
                height: 47px;
            }
            .input-group-text {
                background: #0f4161;
                color: #fff;
                border: 1px solid #cfcfcf;
                font-size: 20px;
            }
            .form-control::-webkit-input-placeholder {
                color: #e9e9e9;
            }
            body::before{
                content: "";
                background: rgb(2 76 120 / 84%);
                height: 100%;
                position: absolute;
                width: 100%;
                top: 0;
            }
            .site-language {
                background: #ff7200;
                border: 1px solid #ff7200;
                padding: 4px 10px;
                border-radius: 7px;
                color: #ffffff;
                font-size: 14px;
                top: 15px;
                position: absolute;
                right: 15px;
            }
            .rtl .site-language{
                right: unset;
                left: 15px;
            }
        </style>
    </head>
    @php
        $logo_image = !empty($store_details) && !empty($store_details[0]->store_logo) ? $store_details[0]->store_logo : URL::asset('assets/store/images/logo.png');
        $background_image = !empty($store_details) && !empty($store_details[0]->store_background_image) ? $store_details[0]->store_background_image : URL::asset('assets/store/images/login-bg.jpg');
    @endphp
    <body style="background:url('{{ $background_image }}') no-repeat center center /cover;">  
        <div class="container-fluid h-100">
            <div class="text-right">
                @include('common.language')
            </div>
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-md-4">&nbsp;</div>
                <div class="col-md-4">
                    <form method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url) }}" novalidate>
                    @csrf
                        <div class="" style="">
                            <center><a href="#"><img src="{{ $logo_image }}" class="logo mb-2" alt="{{ !empty($store_details) && !empty($store_details[0]->store_name) ? $store_details[0]->store_name : 'eMonta' }}" style=""/></a></center>
                            <h2 class="text-center" style="color:#fff;"><b>{{ __('store-admin.welcome_message')}} {{ !empty($store_details) && !empty($store_details[0]->store_name) ? $store_details[0]->store_name : 'eMonta' }}</b></h2>
                            <p class="text-center mb-4" style="color:#c7c7c7;">{{ __('store-admin.login_prompt') }}</p>
                            <br/>
                            <input type="hidden" name="store_url" value="{{config('app.prefix_url').'.'.$store_url}}">
                            <div>
                                <div class="input-field-div mb-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><img src="{{ URL::asset('assets/store/images/1-01.png') }}"></div>
                                        </div>
                                        <input type="email" data-max="100" class="required-field form-input-field form-control input-group-field" data-error-msg="{{ __('validation.email_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF._%+\-@]+$" onkeypress="return restrictCharacters(event)" data-page="login" data-label = "{{ __('store-admin.email')}}" id="inlineFormInputGroup" name="email" value="{{ old('email') }}" placeholder="{{ __('store-admin.email_placeholder')}}" required autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback error-message" role="alert" style="display:block;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <span class="error error-message"></span>
                                </div>
                                <div class="input-field-div mb-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><img src="{{ URL::asset('assets/store/images/1-02.png') }}"></div>
                                        </div>
                                        <input type="password" data-max="100" data-label = "{{ __('store-admin.password')}}" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF!@#$%^&*_=.,~/<:;?+\-]+$" onkeypress="return restrictCharacters(event)" class="required-field form-input-field form-control input-group-field" data-page="login" id="inlineFormInputGroup" placeholder="{{ __('store-admin.password_placeholder')}}" name="password" required autocomplete="password">
                                        @error('password')
                                            <span class="invalid-feedback error-message" role="alert" style="display:block;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <span class="error error-message"></span>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="text-center mx-4"><button class="btn btn-md rounded-0" id="submit-login" style="color: #fff;border: 2px solid #fff;padding: 4px 30px;font-weight: 700;">{{ __('store-admin.sign_in_button')}}</button></div>
                    </form>
                </div>
                <div class="col-md-4">&nbsp;</div>
            </div>
        </div>
        <script>
            window.langTranslations = @json(trans('validation'));
        </script>
        <!-- Vendor JS -->
        <script src="{{ URL::asset('assets/store/js/vendors.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script src="{{ URL::asset('assets/js/common.js') }}"></script>
        <script>
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $(document).on("click","#submit-login",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
        </script>
    </body>
</html>