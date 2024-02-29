<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ __('store-admin.customers_page_title',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header')
    </head>
    @php
        $prefix_url = config('app.module_prefix_url');
    @endphp
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')  
            <div class="content-wrapper" >
                <div class="container-full">
                    <div class="content-header px-30">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="page-title">{{ __('store-admin.all_customers') }}</h3>
                            </div>
                        </div>
                    </div>
                    <section class="content">
                        <div class="card mb-4 product-list">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="customer-report-table">
                                        <input type="hidden" class="list_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.customers.index') }}">
                                        <input type="hidden" class="status-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.customers.update') }}">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th scope="col">{{ __('store-admin.customer_name') }}</th>
                                                <th scope="col">{{ __('store-admin.email') }}</th>
                                                <th scope="col">{{ __('store-admin.phone_number') }}</th>
                                                <th scope="col">{{ __('store-admin.created_at') }}</th>
                                                <th scope="col">{{ __('store-admin.status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="6">{{ trans('datatables.sEmptyTable') }}</td>
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
        <script src="{{ URL::asset('assets/cashier-admin/vendor_components/moment/min/moment.min.js') }}"></script>
	    <script src="{{ URL::asset('assets/cashier-admin/vendor_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
        <script>
            $(document).ready(function() {
                list_url = $('#customer-report-table').find(".list_url").val();
                if ( $.fn.dataTable.isDataTable( '#customer-report-table' ) )
                    customers_list_table.destroy();
                    customers_list_table = $('#customer-report-table').DataTable({
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
                        "data":{_type: 'all'},
                    },
                    "columns": [
                        { "data": "id","searchable":false},
                        { "data": "customer_name"},
                        { "data": "email" },
                        { "data": "phone_number" },
                        { "data": "created_at" }, 
                        { "data": "status","orderable": false,"searchable":false},
                    ],
                });
            });
            $(document).on("change",".customer-status",function(event) {
                event.preventDefault();
                status_value = (this.checked) ? 1 : 0;
                customer_id = $(this).closest("tr").find(".customer-id").val();
                status_url = $(this).closest("table").find(".status-url").val();
                $.ajax({
                    url: status_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,customer_id: customer_id,status_value: status_value},
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
        </script>
   </body>
</html>