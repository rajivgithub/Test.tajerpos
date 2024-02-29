<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ __('store-admin.admin_list_title',['company' => Auth::user()->company_name]) }}</title>
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
                                        <h3 class="page-title">{{ __('store-admin.store_cashier_list') }}</h3>
                                    </div>
                                    <div class="col-md-6 text-right ">
                                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.users.create') }}"><button class="btn btn-primary" type="button"><i class="fa fa-plus"></i> <span>{{ __('store-admin.add_admins') }}</span> </button></a>					
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="admin-users-table">
                                        <input type="hidden" class="list_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.users.index') }}">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('store-admin.name') }}</th>
                                                <th>{{ __('store-admin.email_address') }}</th>
                                                <th>{{ __('store-admin.phone_number') }}</th>
                                                <th>{{ __('store-admin.role') }}</th>
                                                <th>{{ __('store-admin.status') }}</th>
                                                <th class="text-end">{{ __('store-admin.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>   
                                                <td class="text-center" colspan="7">{{ trans('datatables.sEmptyTable') }}</td>       
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
        <script>
            $(document).ready(function() {
                list_url = $('#admin-users-table').find(".list_url").val();
                if ( $.fn.dataTable.isDataTable( '#admin-users-table' ) )
                    admin_users_table.destroy();
                admin_users_table = $('#admin-users-table').DataTable({
                    "language": {
                        "sEmptyTable": "{{ trans('datatables.sEmptyTable') }}",
                        "sInfo": "{{ trans('datatables.sInfo', ['start' => '_START_', 'end' => '_END_', 'total' => '_TOTAL_']) }}",
                        "sInfoEmpty": "{{ trans('datatables.sInfoEmpty') }}",
                        "sInfoFiltered": "{{ trans('datatables.sInfoFiltered') }}",
                        "sInfoPostFix": "{{ trans('datatables.sInfoPostFix') }}",
                        "sInfoThousands": "{{ trans('datatables.sInfoThousands') }}",
                        "sLengthMenu": "{{ trans('datatables.sLengthMenu') }}",
                        "sLoadingRecords": "{{ trans('datatables.sLoadingRecords') }}",
                        "sProcessing": "{{ trans('datatables.sProcessing') }}",
                        "sSearch": "{{ trans('datatables.sSearch') }}",
                        "sZeroRecords": "{{ trans('datatables.sZeroRecords') }}",
                        "oPaginate": {
                            "sFirst": "{{ trans('datatables.oPaginate.sFirst') }}",
                            "sLast": "{{ trans('datatables.oPaginate.sLast') }}",
                            "sNext": "{{ trans('datatables.oPaginate.sNext') }}",
                            "sPrevious": "{{ trans('datatables.oPaginate.sPrevious') }}"
                        },
                        "oAria": {
                            "sSortAscending": "{{ trans('datatables.oAria.sSortAscending') }}",
                            "sSortDescending": "{{ trans('datatables.oAria.sSortDescending') }}"
                        }
                    },
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
                        { "data": "name" },  
                        { "data": "email" },
                        { "data": "phone_number" },
                        { "data": "role_name" },
                        { "data": "status","orderable": false,"searchable":false},
                        { "data": "action","orderable": false,"searchable":false},
                    ]	 
                });
            });
            $(document).on("change",".admin-user-status",function(){
                _this = $(this);
                status_value = (this.checked) ? 1 : 0;
                user_id = _this.closest("tr").find(".user_id").val();
                status_url = _this.closest("table").find(".status_url").val();
                $.ajax({
                    url: status_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,user_id: user_id,status_value:status_value},
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
            $(document).on("click",".admin-user-delete",function() {
                event.preventDefault();
                delete_admin_link = $(this).attr("href");
                swal({
                    title: translations.delete_confirmation_title,
                    text: translations.delete_confirmation_text,
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: translations.cancel_button_text,
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm: {
                            text: translations.ok_button_text,
                            value: true,
                            visible: true,
                            closeModal: true,
                        },
                    },
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $(location).attr('href',delete_admin_link);
                    }
                });
            });
        </script>
    </body>
</html>