<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ __('store-admin.transaction_report_page_title',['company' => Auth::user()->company_name]) }}</title>
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
                                <h3 class="page-title">{{ __('store-admin.transaction_report') }}</h3>
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
                                        <div class="col-lg-4 col-md-12 text-right mt-md-3">
                                            <button class="btn btn-success export-transaction-report-data" data-export-type="pdf" type="button"> <span>{{ __('store-admin.pdf') }}</span> </button>
                                            <button class="btn btn-danger export-transaction-report-data" data-export-type="excel" type="button"> <span>{{ __('store-admin.excel') }}</span> </button>
                                        </div>  
                                    </div>
                                </div>
                            <!-- </header> -->
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="transaction-report-table">
                                        <input type="hidden" class="list_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.reports.transaction-report') }}">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th scope="col">{{ __('store-admin.order_id') }}</th>
                                                <th scope="col">{{ __('store-admin.ordered_at') }}</th>
                                                <th scope="col">{{ __('store-admin.order_type') }}</th>
                                                <th scope="col">{{ __('store-admin.product') }}</th>
                                                <th scope="col">{{ __('store-admin.variants') }}</th>
                                                <th scope="col">{{ __('store-admin.quantity') }}</th>
                                                <th scope="col">{{ __('store-admin.order_status') }}</th>
                                                <th scope="col">{{ __('store-admin.price') }}</th>
                                                <th scope="col">{{ __('store-admin.tax') }}</th>
                                                <th scope="col">{{ __('store-admin.sub_total') }}</th>
                                                <th scope="col">{{ __('store-admin.total_tax') }}</th>
                                                <th scope="col">{{ __('store-admin.total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="13">{{ trans('datatables.sEmptyTable') }}</td>
                                            </tr>
                                        </tbody>
                                        <!-- <tfoot>
                                            <tr>
                                                <td class="text-center" colspan="10">&nbsp;</td>
                                                <td class="text-right">{{ __('store-admin.total') }} Amount</td>
                                                <td class = "total-amount"></td>
                                            </tr>
                                        </tfoot> -->
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
                        transactionList(startDate,endDate);
                    }
                );
                $('#daterange-btn span').html(moment().subtract('days', 29).format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
                startDate = $('#daterange-btn').data('daterangepicker').startDate.format('Y-MM-DD');
                endDate = $('#daterange-btn').data('daterangepicker').endDate.format('Y-MM-DD');
                transactionList(startDate,endDate);
            });
            function groupTransactionList() {
                var topMatchTd,ordered_at_td,sub_total_td,total_tax_td,total_td,order_type_td;
                var previousValue = "";
                var rowSpan = 1;
                $('.order-number').each(function(){
                    if($(this).text() == previousValue)
                    {
                        rowSpan++;
                        $(topMatchTd).attr('rowspan',rowSpan);
                        $(ordered_at_td).attr('rowspan',rowSpan);
                        $(sub_total_td).attr('rowspan',rowSpan);
                        $(total_tax_td).attr('rowspan',rowSpan); 
                        $(total_td).attr('rowspan',rowSpan);
                        $(order_type_td).attr('rowspan',rowSpan);
                        $(this).closest("tr").find("td:eq(11)").remove();
                        $(this).closest("tr").find("td:eq(10)").remove();
                        $(this).closest("tr").find("td:eq(9)").remove();
                        $(this).closest("tr").find("td:eq(3)").remove();
                        $(this).closest("tr").find("td:eq(2)").remove();
                        $(this).remove();
                    }
                    else
                    {
                        topMatchTd = $(this);
                        ordered_at_td = $(this).closest("tr").find("td:eq(2)");
                        order_type_td = $(this).closest("tr").find("td:eq(3)");
                        sub_total_td = $(this).closest("tr").find("td:eq(10)");
                        total_tax_td = $(this).closest("tr").find("td:eq(11)");
                        total_td = $(this).closest("tr").find("td:eq(12)");
                        rowSpan = 1;
                    }
                    previousValue = $(this).text();
                });
            }
            function transactionList(startDate,endDate,exportType = "") {
                list_url = $('#transaction-report-table').find(".list_url").val();
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
                                link.download = 'transaction-report.pdf';
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
                    if ( $.fn.dataTable.isDataTable( '#transaction-report-table' ) )
                        transaction_list_table.destroy();
                        transaction_list_table = $('#transaction-report-table').DataTable({
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
                            { "data": "id","orderable": false,"searchable":false},
                            { "data": "order_number"},
                            { "data": "created_at" }, 
                            { "data": "order_type" },
                            { "data": "product_name" },  
                            { "data": "product_variants" }, 
                            { "data": "quantity" },  
                            { "data": "status_name" },  
                            { "data": "product_price" }, 
                            { "data": "product_tax"},
                            { "data": "sub_total_amount"},
                            { "data": "total_tax_amount"},
                            { "data": "total_amount"},
                        ],	 
                        "drawCallback": function(settings) {
                            // total_sum_amount = settings.json.total_sum_amount;
                            // $("#transaction-report-table").find(".total-amount").text(total_sum_amount);
                            $('tr td:nth-child(2)').each(function (){
                                $(this).addClass('order-number');
                            });
                            $('tr td:nth-child(12)').each(function (){
                                $(this).addClass('sum-total-amount');
                            });
                            groupTransactionList();
                        },
                    });
                }
            }

            $(document).on("click",".export-transaction-report-data",function(){
                exportType = $(this).attr("data-export-type");
                startDate = $('#daterange-btn').data('daterangepicker').startDate.format('Y-MM-DD');
                endDate = $('#daterange-btn').data('daterangepicker').endDate.format('Y-MM-DD');
                transactionList(startDate,endDate,exportType);
            });

            $(".download-transaction-report").submit(function(){
                startDate = $('#daterange-btn').data('daterangepicker').startDate.format('Y-MM-DD');
                endDate = $('#daterange-btn').data('daterangepicker').endDate.format('Y-MM-DD');
                $(this).find(".startDate").val(startDate);
                $(this).find(".endDate").val(endDate);
                return true;
            });
        </script>
   </body>
</html>