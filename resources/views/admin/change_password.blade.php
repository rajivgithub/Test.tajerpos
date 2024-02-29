<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ trans('admin.reset_password_title') }}</title>
        @include('common.admin.header')
        <style>
            #pwd_strength_wrap {
                border: 1px solid #D5CEC8;
                display: none;
                float: left;
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
                border-bottom: 7px solid rgba(0, 0, 0, 0);
                border-right: 7px solid rgba(0, 0, 0, 0.1);
                border-top: 7px solid rgba(0, 0, 0, 0);
                content: "";
                display: inline-block;
                left: -18px;
                position: absolute;
                top: 10px;
            }
            #pwd_strength_wrap:after {
                border-bottom: 6px solid rgba(0, 0, 0, 0);
                border-right: 6px solid #fff;
                border-top: 6px solid rgba(0, 0, 0, 0);
                content: "";
                display: inline-block;
                left: -16px;
                position: absolute;
                top: 11px;
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
    <body>
        <div class="page-loader"><div class="spinner"></div></div>  
        <div class="screen-overlay"></div>
        @include('common.admin.navbar')
        <main class="main-wrap">
            @include('common.admin.sidebar')
            <section class="content-main">
                @include('common.admin.search')
                <div class="body-content">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4>{{ trans('admin.reset_password_cart_title') }}</h4>
                                </div>
                                <div class="card-body">
                                    <form  method="POST" action="{{ route(config('app.prefix_url').'.admin.update-password') }}"  enctype="multipart/form-data">
                                    @csrf
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.current_password') }}<span>*</span></label>
                                            <div class="input-group">
                                                <input type="password" data-min="8" data-max="100" data-label = "{{ trans('admin.current_password') }}" data-pattern="^[A-Za-z\u0600-\u06FF0-9!@#$%^&*_=.,~/<:;?+-]+$" onkeypress="return restrictCharacters(event)" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" name="current_password" class="form-control form-input-field input-field required-field">
                                                <div class="input-group-text"><span class="fa fa-fw fa-eye field_icon user-password"></span></div>
                                            </div>    
                                            @if ($errors->has('current_password'))
                                                <span class="text-danger error-message">{{ $errors->first('current_password') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.new_password') }}<span>*</span></label>
                                            <div class="input-group">
                                                <input type="password" data-min="8" data-max="100" data-label = "{{ trans('admin.new_password') }}" data-pattern="^[A-Za-z\u0600-\u06FF0-9!@#$%^&*_=.,~/<:;?+-]+$" onkeypress="return restrictCharacters(event)" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" name="new_password" class="form-control form-input-field input-field required-field new-password">
                                                <div class="input-group-text"><span class="fa fa-fw fa-eye field_icon user-password"></span></div>
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
                                            <label class="form-label">{{ trans('admin.confirm_password') }}<span>*</span></label>
                                            <div class="input-group">
                                                <input type="password" data-min="8" data-max="100" data-label = "{{ trans('admin.confirm_password') }}" data-pattern="^[A-Za-z\u0600-\u06FF0-9!@#$%^&*_=.,~/<:;?+-]+$" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" onkeypress="return restrictCharacters(event)" name="confirm_password" class="form-control form-input-field input-field required-field">
                                                <div class="input-group-text"><span class="fa fa-fw fa-eye field_icon user-password"></span></div>
                                            </div> 
                                            @if ($errors->has('confirm_password'))
                                                <span class="text-danger error-message">{{ $errors->first('confirm_password') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="text-end">
                                            <button class="btn btn-md rounded font-sm hover-up" id="save-password">{{ trans('admin.reset_password') }}</button>
                                        </div>     
                                    </form>
                                </div>
                            </div>
                        </div>          
                    </div>
                </div>
            </section>
            @include('common.admin.footer')
        </main>
        @include('common.admin.script')
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