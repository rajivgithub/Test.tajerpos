<!DOCTYPE html>
<html lang="en">
   <head>
        @include('common.store_admin.header')
   </head>
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.store_admin.navbar')
            @include('common.store_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <section class="content ">
                        <div class="row">
                            <div class="col-lg-6 offset-lg-3">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h4 class="mb-0">Change password</h4>
                                    </div>
                                    <div class="card-body">
                                        <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.update-password') }}"  enctype="multipart/form-data">
                                        @csrf
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Current Password</label>
                                                <div class="input-group">
                                                    <input type="password" data-label = "Current Password" name="current_password" placeholder="Current Password" class="form-control input-field required-field">
                                                    <div class="input-group-addon"><span class="fa fa-fw fa-eye field_icon user-password"></span></div>
                                                </div>    
                                                @if ($errors->has('current_password'))
                                                    <span class="text-danger error-message">{{ $errors->first('current_password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">New Password</label>
                                                <div class="input-group">
                                                    <input type="password" data-label = "New Password" placeholder="New Password" name="new_password" class="form-control input-field required-field">
                                                    <div class="input-group-addon"><span class="fa fa-fw fa-eye field_icon user-password"></span></div>
                                                </div>  
                                                @if ($errors->has('new_password'))
                                                    <span class="text-danger error-message">{{ $errors->first('new_password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Confirm Password</label>
                                                <div class="input-group">
                                                    <input type="password" data-label = "Confirm Password" placeholder="Confirm Password" name="confirm_password" class="form-control input-field required-field">
                                                    <div class="input-group-addon"><span class="fa fa-fw fa-eye field_icon user-password"></span></div>
                                                </div> 
                                                @if ($errors->has('confirm_password'))
                                                    <span class="text-danger error-message">{{ $errors->first('confirm_password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="text-right">
                                                <button class="btn btn-primary" id="save-password">Change password</button>
                                            </div>     
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            @include('common.store_admin.copyright')
        </div>
        @include('common.store_admin.footer')
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