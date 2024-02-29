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
                                                <h3 class="page-title">Role Information</h3>
                                            </div>
                                        </div>
                                        <hr class="mb-0">
                                    </div>
                                    <div class="box-body">
                                        <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.roles.store') }}">
                                        @csrf
                                            <input type="hidden" name="mode" value={{$mode}}> 
                                            <input type="hidden" name="role_id" class="role-id" value="{{!empty($role_details) && !empty($role_details[0]->role_id) ? $role_details[0]->role_id : '' }}">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Role Name</label>
                                                <input type="text" placeholder="Type here" data-label = "Role Name" name="role_name" class="form-control required-field" value="{{!empty($role_details) && !empty($role_details[0]->role_name) ? $role_details[0]->role_name : '' }}">
                                                @if ($errors->has('role_name'))
                                                    <span class="text-danger error-message">{{ $errors->first('role_name') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Module</th>
                                                        <th>Add</th>
                                                        <th>Edit</th>
                                                        <th>View</th>
                                                        <th>Delete</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!empty($modules_details))
                                                        @foreach($modules_details as $modules)
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden" name="modules_id[]" value="{{ $modules->modules_id }}">
                                                                    {{ $modules->modules_name }}
                                                                </td>
                                                                <td><input type="checkbox" name="permissions[]" {{ (array_key_exists($modules->modules_id,$role_permission_data) && $role_permission_data[$modules->modules_id]['add'] == 1) ? "checked" : "" }} value="{{ $modules->modules_id }}.add"></td>
                                                                <td><input type="checkbox" name="permissions[]" {{ (array_key_exists($modules->modules_id,$role_permission_data) && $role_permission_data[$modules->modules_id]['edit'] == 1) ? "checked" : "" }} value="{{ $modules->modules_id }}.edit"></td>
                                                                <td><input type="checkbox" name="permissions[]" {{ (array_key_exists($modules->modules_id,$role_permission_data) && $role_permission_data[$modules->modules_id]['view'] == 1) ? "checked" : "" }} value="{{ $modules->modules_id }}.view"></td>
                                                                <td><input type="checkbox" name="permissions[]" {{ (array_key_exists($modules->modules_id,$role_permission_data) && $role_permission_data[$modules->modules_id]['delete'] == 1) ? "checked" : "" }} value="{{ $modules->modules_id }}.delete"></td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                            <div class="mt-3">
                                                <button class="btn btn-primary" id="save-role-info">Save</button>
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
            $(document).on("click","#save-role-info",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
        </script>
    </body>
</html>