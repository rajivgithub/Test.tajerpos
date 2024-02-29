<!DOCTYPE html>
<html lang="en">
    <head>
        @include('common.cashier_admin.header')
    </head>
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <section class="content">
                        <div class="box">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="content-header px-30">
                                        <div class="d-flex row align-items-center ">
                                            <div class="col-md-6 mr-auto">
                                                <h3 class="page-title">Permission Information</h3>
                                            </div>
                                        </div>
                                        <hr class="mb-0">
                                    </div>
                                    <div class="box-body">
                                        <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.permission.store') }}">
                                        @csrf
                                            <input type="hidden" name="mode" value={{$mode}}> 
                                            <input type="hidden" name="permission_id" class="permission-id" value="{{!empty($permission_details) && !empty($permission_details[0]->permission_id) ? $permission_details[0]->permission_id : '' }}">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Permission Name</label>
                                                <input type="text" data-label = "Permission Name" placeholder="Type here" value="{{!empty($permission_details) && !empty($permission_details[0]->permission_name) ? $permission_details[0]->permission_name : '' }}" name="permission_name" class="form-control required-field">
                                                @if ($errors->has('permission_name'))
                                                    <span class="text-danger error-message">{{ $errors->first('permission_name') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Description</label>
                                                <textarea data-label = "Description" placeholder="Description" name="description" class="form-control">{{!empty($permission_details) && !empty($permission_details[0]->description) ? $permission_details[0]->description : '' }}</textarea>
                                                @if ($errors->has('description'))
                                                    <span class="text-danger error-message">{{ $errors->first('description') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mt-3">
                                                <button class="btn btn-primary" id="save-permission-info">Save</button>
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
        <script>
            $(document).on("click","#save-permission-info",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
        </script>
    </body>
</html>