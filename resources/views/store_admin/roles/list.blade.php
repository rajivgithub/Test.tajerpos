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
                            <div class="content-header px-30">
                                <div class="d-flex row align-items-center ">
                                    <div class="col-md-6 mr-auto">
                                        <h3 class="page-title">All Role</h3>
                                    </div>
                                    <div class="col-md-6 text-right ">
                                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.roles.create') }}"><button class="btn btn-primary" type="button"><i class="fa fa-plus"></i> <span>Add Role</span> </button></a>					
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="roles-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Role Name</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>      
                                                <td class="text-center" colspan="5">Data not found..!</td> 
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>      
                                                <td style="display: none;"></td>  
                                                <td style="display: none;"></td>        
                                            </tr>
                                        </tbody>
                                    </table>
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
            $(document).ready(function() {
                list_url = $('#roles-table').find(".list_url").val();
                if ( $.fn.dataTable.isDataTable( '#roles-table' ) )
                    roles_table.destroy();
                roles_table = $('#roles-table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "order": [[ 0, "desc" ]],
                    "ajax": {
                        "url": list_url,
                        "dataType": "json",
                        "type": "get",
                        "data":{type: 'all'},
                    },
                    "columns": [
                        { "data": "id" },
                        { "data": "role_name" },  
                        { "data": "description" },
                        { "data": "status","orderable": false,"searchable":false},
                        { "data": "action","orderable": false,"searchable":false},
                    ]	 
                });
            });
            $(document).on("change",".role-status",function(){
                _this = $(this);
                status_value = (this.checked) ? 1 : 0;
                role_id = _this.closest("tr").find(".role_id").val();
                status_url = _this.closest("table").find(".status_url").val();
                $.ajax({
                    url: status_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,role_id: role_id,status_value:status_value},
                    success: function(response){
                        toastr.options =
                        {
                            "closeButton" : true,
                            "progressBar" : true
                        }
                        toastr.success(response.message);
                    }
                });
            });
            $(document).on("click",".delete-roles",function() {
                if(confirm("Are you sure want to delete?"))
                    return true;
                else
                    return false;
            });
        </script>
    </body>
</html>