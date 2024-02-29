<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ ($mode == "add") ? trans('store-admin.add_admin_page_title',['company' => Auth::user()->company_name]) : trans('store-admin.edit_admin_page_title',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header')
    </head>
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <section class="content">
                        <div class="card mb-4">
                            <div class="content-header">
                                <div class="d-flex align-items-center">
                                    <div class="mr-auto">
                                        @php
                                            $page_title = ($mode == "add") ? __('store-admin.add_admin') : __('store-admin.edit_admin');
                                        @endphp
                                        <h3 class="page-title">{{$page_title}}</h3>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div class="card-body">
                                <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.users.store') }}">
                                @csrf
                                    <input type="hidden" name="mode" value={{$mode}}> 
                                    <input type="hidden" class="email-path" value="{{ route('email-exist') }}">
                                    <input type="hidden" name= "store_id" class="store-id" value="{{!empty($admin_user_details) && !empty($admin_user_details[0]->store_id) ? Crypt::encrypt($admin_user_details[0]->store_id) : '' }}">
                                    <input type="hidden" name= "user_id" class="user-id" value="{{!empty($admin_user_details) && !empty($admin_user_details[0]->id) ? Crypt::encrypt($admin_user_details[0]->id) : '' }}">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.name') }}<span>*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                    </div>
                                                    <input type="text" data-label = "{{ __('store-admin.name') }}" data-error-msg="{{ __('validation.invalid_name_err') }}" data-pattern="^[A-Za-z\u0600-\u06FF. ]+$" onkeypress="return restrictCharacters(event)" data-max="100" name="name" value = "{{!empty($admin_user_details) && !empty($admin_user_details[0]->name) ? $admin_user_details[0]->name : '' }}" class="form-control required-field form-input-field">
                                                </div>
                                                @if ($errors->has('name'))
                                                    <span class="text-danger error-message">{{ $errors->first('name') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.phone_number') }}<span>*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                                    </div>
                                                    <input type="text" data-label = "{{ __('store-admin.phone_number') }}" data-min="10" data-max="12" name="phone_number" value = "{{!empty($admin_user_details) && !empty($admin_user_details[0]->phone_number) ? $admin_user_details[0]->phone_number : '' }}" data-pattern="^[0-9]+$" data-error-msg="{{ __('validation.invalid_numeric_err') }}" onkeypress="return restrictCharacters(event)" class="form-control required-field form-input-field">
                                                </div>
                                                @if ($errors->has('phone_number'))
                                                    <span class="text-danger error-message">{{ $errors->first('phone_number') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.email_address') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                                    </div>
                                                    <input type="email" data-label = "{{ __('store-admin.email_address') }}" data-error-msg="{{ __('validation.email_invalid_msg') }}" data-max="100" data-pattern="^[A-Za-z\u0600-\u06FF0-9._%+\-@]+$" onkeypress="return restrictCharacters(event)" data-type="store_admin" name="email" value = "{{!empty($admin_user_details) && !empty($admin_user_details[0]->email) ? $admin_user_details[0]->email : '' }}" class="form-control required-field form-input-field email-field">
                                                </div>
                                                @if ($errors->has('email'))
                                                    <span class="text-danger error-message">{{ $errors->first('email') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.password') }}</label>
                                                <div class="input-group">
                                                    <input type="password" data-label = "{{ __('store-admin.password') }}" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" data-min="8" data-max="100" data-pattern="^[A-Za-z\u0600-\u06FF0-9!@#$%^&*_=.,~/<:;?+-]+$" onkeypress="return restrictCharacters(event)" name="password" value="{{!empty($admin_user_details) && !empty($admin_user_details[0]->plain_password) ? decrypt($admin_user_details[0]->plain_password) : '' }}" class="form-control required-field form-input-field password">
                                                    <div class="input-group-addon"><span id="user-password" class="fa fa-fw fa-eye field_icon"></span></div>
                                                </div>
                                                @if ($errors->has('password'))
                                                    <span class="text-danger error-message">{{ $errors->first('password') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.role') }}</label>
                                                <select class="form-control required-field form-input-field" data-label = "{{ __('store-admin.role') }}" name="role_id">
                                                    <option value="">--Select Role--</option> 
                                                    <option value="2" {{!empty($admin_user_details) && !empty($admin_user_details[0]->role_id) && ($admin_user_details[0]->role_id == 2) ? "selected" : '' }}>Store Admin</option>
                                                    <option value="3" {{!empty($admin_user_details) && !empty($admin_user_details[0]->role_id) && ($admin_user_details[0]->role_id == 3) ? "selected" : '' }}>Cashier Admin</option>
                                                    <!-- @if(isset($role_details) && !empty($role_details))
                                                        @foreach ($role_details as $role)
                                                            <option value="{{ $role->role_id }}" {{!empty($admin_user_details) && !empty($admin_user_details[0]->role_id) && ($admin_user_details[0]->role_id == $role->role_id) ? "selected" : '' }}>{{ $role->role_name }}</option> 
                                                        @endforeach
                                                    @endif -->
                                                </select>
                                                @if ($errors->has('role_id'))
                                                    <span class="text-danger error-message">{{ $errors->first('role_id') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <!-- <div class="mb-4">
                                                <label class="form-label">Grant Cashier Admin Access</label>
                                                <div class="custom-control custom-switch">
                                                    <input class="custom-control-input" type="checkbox" name="is_admin" {{!empty($admin_user_details) && !empty($admin_user_details[0]->is_admin) && $admin_user_details[0]->is_admin == '3' ? 'checked' : '' }} value="3" id="customSwitch_1">
                                                    <label class="custom-control-label" for="customSwitch_1"></label>
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="col-lg-12 text-left">
                                            <button class="btn btn-primary" id="save-admin-users">{{ __('store-admin.save') }}</button>
                                        </div>
                                    </div>
                                </form>
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
            $(document).on("click","#save-admin-users",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
        </script>
    </body>
</html>