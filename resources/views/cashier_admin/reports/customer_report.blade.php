<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ __('store-admin.customer_report_page_title',['company' => Auth::user()->company_name]) }}</title>
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
                                <h3 class="page-title">{{ __('store-admin.customer_report') }}</h3>
                            </div>
                        </div>
                    </div>
                    <section class="content">
                        <div class="card mb-4 product-list">
                            <!-- <header class="card-header"> -->
                                <div class="card-header row align-items-center">
                                    <div class="col-12 col-lg-12 col-md-12 d-lg-flex">
                                        <div class="input-group col-lg-8 col-md-12">
                                            <!-- width:265px; -->
                                            <button type="button" class="btn btn-default pull-right btn-rounded" id="daterange-btn" style="background: white;color: #ff5843;">
                                                <span>
                                                <i class="fa fa-calendar"></i> Date range picker
                                                </span>
                                                <i class="fa fa-caret-down"></i>
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-md-12 mt-md-3 text-right">
                                            <button class="btn btn-success export-customer-report-data" data-export-type="pdf" type="button"> <span>{{ __('store-admin.pdf') }}</span> </button>
                                            <button class="btn btn-danger export-customer-report-data" data-export-type="excel" type="button"> <span>{{ __('store-admin.excel') }}</span> </button>
                                        </div>  
                                    </div>
                                </div>
                            <!-- </header> -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="customer-report-table">
                                        <input type="hidden" class="list_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.reports.customer-report') }}">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th scope="col">{{ __('store-admin.customer_name') }}</th>
                                                <th scope="col">{{ __('store-admin.email') }}</th>
                                                <th scope="col">{{ __('store-admin.phone_number') }}</th>
                                                <th scope="col">{{ __('store-admin.created_at') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="5">{{ trans('datatables.sEmptyTable') }}</td>
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
                $('#daterange-btn').daterangepicker(
                    {
                        ranges   : {
                        'Today'       : [moment(), moment()],
                        'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                        'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                        },
                        startDate: moment().subtract(29, 'days'),
                        endDate  : moment()
                    },
                    function (start, end) {
                        $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                        startDate = $("#daterange-btn").data('daterangepicker').startDate.format('Y-MM-DD');
                        endDate = $("#daterange-btn").data('daterangepicker').endDate.format('Y-MM-DD');
                        customerList(startDate,endDate);
                    }
                );
                $('#daterange-btn span').html(moment().subtract('days', 29).format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
                startDate = $('#daterange-btn').data('daterangepicker').startDate.format('Y-MM-DD');
                endDate = $('#daterange-btn').data('daterangepicker').endDate.format('Y-MM-DD');
                customerList(startDate,endDate);
            });
            function customerList(startDate,endDate,exportType = '') {
                list_url = $('#customer-report-table').find(".list_url").val();
                if(exportType != "") {
                    $.ajax({
                        url: list_url,
                        type: 'get',
                        xhrFields: {
                            responseType: exportType === "pdf" ? 'blob' : undefined
                        },
                        dataType: exportType !== "pdf" ? 'json' : undefined,
                        "data":{startDate: startDate,endDate:endDate,export_type:exportType},
                        success: function(response){
                            if(exportType == "pdf") {
                                var blobUrl = URL.createObjectURL(response);
                                var link = document.createElement('a');
                                link.href = blobUrl;
                                link.download = 'customer-report.pdf';
                                link.click();
                                URL.revokeObjectURL(blobUrl);
                            }
                            if(exportType == "excel") {
                                var fileUrl = response.file_url;
                                window.location.href = fileUrl; 
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(error);
                        }
                    });
                } else {
                    if ( $.fn.dataTable.isDataTable( '#customer-report-table' ) )
                        transaction_list_table.destroy();
                        transaction_list_table = $('#customer-report-table').DataTable({
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
                            "data":{startDate: startDate,endDate:endDate},
                        },
                        "columns": [
                            { "data": "id","searchable":false},
                            { "data": "customer_name"},
                            { "data": "email" },
                            { "data": "phone_number" },
                            { "data": "created_at" }, 
                        ],
                    });
                }
            }

            $(document).on("click",".export-customer-report-data",function(){
                exportType = $(this).attr("data-export-type");
                startDate = $('#daterange-btn').data('daterangepicker').startDate.format('Y-MM-DD');
                endDate = $('#daterange-btn').data('daterangepicker').endDate.format('Y-MM-DD');
                customerList(startDate,endDate,exportType);
            });
        </script>
   </body>
</html>