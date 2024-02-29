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
                                        <h3 class="page-title">All Permission</h3>
                                    </div>
                                    <div class="col-md-6 text-right ">
                                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.permission.create') }}"><button class="btn btn-primary" type="button"><i class="fa fa-plus"></i> <span>Add Permission</span> </button></a>					
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="permission-list">
                                        <input type="hidden" class="list-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.permission.index')}}">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Permission Name</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
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
                list_url = $('#permission-list').find(".list_url").val();
                if ( $.fn.dataTable.isDataTable( '#permission-list' ) )
                    permission_table.destroy();
                permission_table = $('#permission-list').DataTable({
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
                        { "data": "permission_name" },  
                        { "data": "description" },
                        { "data": "status","orderable": false,"searchable":false},
                        { "data": "action","orderable": false,"searchable":false},
                    ]	 
                });
            });
            $(document).on("change",".permission-status",function(){
                _this = $(this);
                status_value = (this.checked) ? 1 : 0;
                permission_id = _this.closest("tr").find(".permission_id").val();
                status_url = _this.closest("table").find(".status_url").val();
                $.ajax({
                    url: status_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,permission_id: permission_id,status_value:status_value},
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
            $(document).on("click",".delete-permission",function() {
                if(confirm("Are you sure want to delete?"))
                    return true;
                else
                    return false;
            });
        </script>
    </body>
</html>