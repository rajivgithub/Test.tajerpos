<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ __('store-admin.instore_order_page_title',['company' => Auth::user()->company_name]) }}</title>
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
                    <section class="content">
                        <div class="box">
                            <div class="content-header">
                                <div class="d-flex align-items-center">
                                    <div class="mr-auto">
                                        <h3 class="page-title">{{ __('store-admin.instore_order_title') }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row gx-3">
                                    <div class="col-lg-2 col-4 col-md-3">
                                        <select class="form-control sort-by-status">
                                            <option value="">{{ __('store-admin.filter_by_status') }}</option>
                                            @if(isset($store_order_status) && !empty($store_order_status))
                                                @foreach($store_order_status as $status)
                                                    <option value="{{ $status['order_methods'] }}">{{ $status['order_methods'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-4 col-md-6 error-message"></div>
                                </div>
                                <hr/>
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="store-order-table">
                                        <input type="hidden" class="list_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.store-order.index') }}">
                                        <input type="hidden" class="update_order_status_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.store-order.update') }}">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="form-check ms-2">
                                                        <input class="form-check-input" id="checkAll" type="checkbox" value="">
                                                    </div>
                                                </th>
                                                <th>#</th>
                                                <th scope="col">{{ __('store-admin.order_number') }}</th>
                                                <th scope="col">{{ __('store-admin.customer_name') }}</th>
                                                <!-- <th scope="col">{{ __('store-admin.email') }}</th> -->
                                                <th scope="col">{{ __('store-admin.order_date_time') }}</th> 
                                                <th scope="col">{{ __('store-admin.order_status') }}</th>
                                                <th scope="col">{{ __('store-admin.total_order_amount') }}</th>
                                                <th scope="col" class="text-end">{{ __('store-admin.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="8">{{ trans('datatables.sEmptyTable') }}</td>
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
                storeOrderList('all');
            });
            $(document).on("change",".filter-by-conditions",function(){
                storeOrderList('filter_by_date',$(this).val());
            });
            $(document).on("change",".sort-by-status",function(){
                storeOrderList('store_order_status',$(this).val());
            });
            $(document).on("click","#checkAll",function() {
                $('.store-order-checkbox').not(this).prop('checked', this.checked);
            });
            function storeOrderList(filter_condition,filter_value="") {
                list_url = $('#store-order-table').find(".list_url").val();
                if ( $.fn.dataTable.isDataTable( '#store-order-table' ) )
                    store_order_table.destroy();
                store_order_table = $('#store-order-table').DataTable({
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
                    "order": [[ 1, "desc" ]],
                    "ajax": {
                        "url": list_url,
                        "dataType": "json",
                        "type": "get",
                        "data":{type: filter_condition,filter_value:filter_value},
                    },
                    "columns": [
                        { "data": "checkbox","orderable": false,"searchable":false},
                        { "data": "order_id" },
                        { "data": "order_number" },
                        { "data": "customer_name" },  
                        // { "data": "email" },   
                        { "data": "ordered_at" }, 
                        { "data": "status_name" }, 
                        { "data": "total_amount" },  
                        { "data": "action","orderable": false,"searchable":false},
                    ]	 
                });
            } 
            $(document).on("change",".store-order-checkbox",function() {
                var checked = $('input[name="store_order_checkbox"]:checked').length > 0;
                $(this).closest("section").find(".error-message").text("");
            });
            $(document).on("change",".bulk-action",function() {
                $(this).css("border","1px solid #86a4c3");
                $(this).closest("section").find(".error-message").text("");
                var checked = $('input[name="store_order_checkbox"]:checked').length > 0;
                _type = $(this).attr("data-type");
                if ((_type == "multi-bulk-action" && !checked)){
                    $(this)[0].selectedIndex = 0; 
                    $(this).closest("section").find(".error-message").text(translations.checkbox_validation).css("color","#F30000");
                    return false;
                } else if($(this).val() == "") {
                    $(this).css("border","2px solid #F30000");
                    $(this).closest("section").find(".error-message").text(translations.choose_order_status).css("color","#F30000");
                    return false;
                } else {
                    update_order_status_url = $(this).closest("section").find(".update_order_status_url").val();
                    status_id = $(this).val();
                    var data = { "_token": CSRF_TOKEN,'order_ids[]' : [],'status_id': status_id};
                    if(_type == "multi-bulk-action") {
                        $(".store-order-checkbox:checked").each(function() {
                            data['order_ids[]'].push($(this).val());
                        });
                    } else {
                        store_order_id = $(this).closest("tr").find(".store-order-checkbox").val();
                        data['order_ids[]'].push(store_order_id);
                    }
                    $.ajax({
                        url: update_order_status_url,
                        type: 'post',
                        data: data,
                        success: function(response){
                            toastr.options =
                            {
                                "closeButton" : true,
                                "progressBar" : true
                            }
                            toastr.success(response.message);
                            if($(".sort-by-status").val() != "") 
                                storeOrderList('store_order_status',$(".sort-by-status").val());
                            else 
                                storeOrderList('all');
                        }
                    });
                }
            });
            $(document).on("click",".order-delete",function() {
                event.preventDefault();
                delete_order_link = $(this).attr("href");
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
                        $(location).attr('href',delete_order_link);
                    }
                });
            });
        </script>
    </body>
</html>
