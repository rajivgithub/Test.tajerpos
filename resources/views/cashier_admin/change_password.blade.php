<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ __('store-admin.reset_password_title',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header')
        <style>
            #pwd_strength_wrap {
                border: 1px solid #D5CEC8;
                display: none;
                /* float: left; */
                padding: 10px;
                position: relative;
                width: 320px;
            }
            #pwd_strength_wrap:before, #pwd_strength_wrap:after {
                content: ' ';
                height: 0;
                position: absolute;
                width: 0;
                border: 10px solid transparent; /* arrow size */
            }
            #pwd_strength_wrap:before {
                border-left: 7px solid rgba(0, 0, 0, 0);
                border-bottom: 7px solid rgba(0, 0, 0, 0.1);
                border-right: 7px solid rgba(0, 0, 0, 0);
                content: "";
                display: inline-block;
                left: 5px;
                position: absolute;
                top: -18px;
            }
            #pwd_strength_wrap:after {
                border-right: 6px solid rgba(0, 0, 0, 0);
                border-bottom: 6px solid #fff;
                border-left: 6px solid rgba(0, 0, 0, 0);
                content: "";
                display: inline-block;
                left: 6px;
                position: absolute;
                top: -15px;
            }
            #pswd_info ul {
                list-style-type: none;
                margin: 5px 0 0;
                padding: 0;
            }
            #pswd_info ul li {
                background: url(icon_pwd_strength.png) no-repeat left 2px;
                padding: 0 0 0 20px;
            }
            #pswd_info ul li.valid {
                background-position: left -42px;
                color: green;
            }
            #passwordStrength {
                display: block;
                height: 5px;
                margin-bottom: 10px;
                transition: all 0.4s ease;
            }
            .strength0 {
                background: none; /* too short */
                width: 0px;
            }
            .strength1 {
                background: none repeat scroll 0 0 #FF4545;/* weak */
                width: 25px;
            }
            .strength2 {
                background: none repeat scroll 0 0 #FFC824;/* good */
                width: 75px;
            }
            .strength3 {
                    background: none repeat scroll 0 0 #6699CC;/* strong */
                width: 100px;
            }
            
            .strength4 {
                    background: none repeat scroll 0 0 #008000;/* best */
                width: 150px;
            }
        </style>
    </head>
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <section class="content ">
                        <div class="row">
                            <div class="col-lg-6 offset-lg-3">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h4 class="mb-0">{{ trans('store-admin.reset_password_cart_title') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.update-password') }}"  enctype="multipart/form-data">
                                        @csrf
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.current_password') }}<span>*</span></label>
                                                <div class="input-group">
                                                    <input type="password" data-min="8" data-max="100" data-label = "{{ __('store-admin.current_password') }}" data-pattern="^[A-Za-z\u0600-\u06FF0-9!@#$%^&*_=.,~/<:;?+-]+$"  onkeypress="return restrictCharacters(event)" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" name="current_password" class="form-control input-field required-field form-input-field">
                                                    <div class="input-group-addon"><span class="fa fa-fw fa-eye field_icon user-password"></span></div>
                                                </div>    
                                                @if ($errors->has('current_password'))
                                                    <span class="text-danger error-message">{{ $errors->first('current_password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.new_password') }}<span>*</span></label>
                                                <div class="input-group">
                                                    <input type="password" data-min="8" data-max="100" data-label = "{{ __('store-admin.new_password') }}" data-pattern="^[A-Za-z\u0600-\u06FF0-9!@#$%^&*_=.,~/<:;?+-]+$"  onkeypress="return restrictCharacters(event)" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" name="new_password" class="form-control input-field required-field form-input-field new-password">
                                                    <div class="input-group-addon"><span class="fa fa-fw fa-eye field_icon user-password"></span></div>
                                                </div>  
                                                @if ($errors->has('new_password'))
                                                    <span class="text-danger error-message">{{ $errors->first('new_password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                                <div id="pwd_strength_wrap">
                                                    <div id="passwordDescription">Password not entered</div>
                                                    <div id="passwordStrength" class="strength0"></div>
                                                    <div id="pswd_info">
                                                        <strong>Strong Password Tips:</strong>
                                                        <ul>
                                                            <li class="invalid" id="length">At least 8 characters</li>
                                                            <li class="invalid" id="pnum">At least one number</li>
                                                            <li class="invalid" id="capital">At least one lowercase &amp; one uppercase letter</li>
                                                            <li class="invalid" id="spchar">At least one special character</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.confirm_password') }}<span>*</span></label>
                                                <div class="input-group">
                                                    <input type="password" data-min="8" data-max="100" data-label = "{{ __('store-admin.confirm_password') }}" data-pattern="^[A-Za-z\u0600-\u06FF0-9!@#$%^&*_=.,~/<:;?+-]+$"  onkeypress="return restrictCharacters(event)" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" name="confirm_password" class="form-control input-field required-field form-input-field">
                                                    <div class="input-group-addon"><span class="fa fa-fw fa-eye field_icon user-password"></span></div>
                                                </div> 
                                                @if ($errors->has('confirm_password'))
                                                    <span class="text-danger error-message">{{ $errors->first('confirm_password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="text-right">
                                                <button class="btn btn-primary" id="save-password">{{ __('store-admin.reset_password') }}</button>
                                            </div>     
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            @include('common.cashier_admin.copyright')
        </div>
        @include('common.cashier_admin.footer')
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script>
            $(document).on("click","#save-password",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
            //Hide and show the password
            $(function () {
                $(document).on("click",".user-password",function() {
                    $(this).toggleClass("fa-eye fa-eye-slash");
                    var type = $(this).hasClass("fa-eye-slash") ? "text" : "password";
                    $(this).closest(".input-field-div").find(".input-field").attr("type", type);
                });
            });
        </script>
    </body>
</html>