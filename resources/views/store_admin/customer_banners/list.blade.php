<!DOCTYPE html>
<html lang="en">
    <head>
        @php $role_name = Auth::user()->is_admin == 2 ? __('store-admin.store_admin') : __('store-admin.cashier');  @endphp
        <title>{{ __('store-admin.banner_page_title',['company' => Auth::user()->company_name, 'role_name' => $role_name]) }}</title>
        @include('common.cashier_admin.header')
        <style>
            .img-sm {
                width: 90px !important;
                height: 45px !important;
            }
        </style>
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
                                <h3 class="page-title">{{ __('store-admin.dashboard_banner_settings') }}</h3>
                            </div>
                            <div class="text-right">
                                <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.customer-banners.create') }}"><button class="btn btn-primary" type="button"> <span>{{ __('store-admin.add_banner') }}</span> </button></a>					
                            </div>
                        </div>
                    </div>
                    <section class="content">
                        <div class="card mb-4 banner-list">
                            <div class="card-body">
                                <div class="row gx-3">
                                    <div class="col-lg-2 col-4 col-md-3">
                                        <select class="form-control bulk-action" data-type='multi-bulk-action'>
                                            <option value="">Bulk Action</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">In Active</option>
                                            <option value="delete">Delete</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-4 col-md-3">
                                        <select class="form-control filter-by-status">
                                            <option value="all">Filter by status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">In Active</option>
                                            <option value="expired">Expired</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-4 col-md-6 error-message"></div>
                                </div>
                                <hr/>
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="banners-list-table">
                                        <input type="hidden" class="list_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.customer-banners.index') }}">
                                        <input type="hidden" class="update_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.customer-banners.update') }}">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="form-check ms-2">
                                                        <input class="form-check-input" id="checkAll" type="checkbox" value="">
                                                    </div>
                                                </th>
                                                <th>#</th>
                                                <th scope="col">{{ __('store-admin.image') }}</th>
                                                <!-- <th scope="col">{{ __('store-admin.url') }}</th> -->
                                                <th scope="col">{{ __('store-admin.start_date') }}</th>
                                                <th scope="col">{{ __('store-admin.end_date') }}</th>
                                                <th scope="col">{{ __('store-admin.sales_channels') }}</th>
                                                <th scope="col">{{ __('store-admin.status') }}</th>
                                                <th scope="col">{{ __('store-admin.created_at') }}</th>
                                                <th scope="col">{{ __('store-admin.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="9">{{ trans('datatables.sEmptyTable') }}</td>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
        <script>
            function displayBanners(filter_condition,filter_value = '') {
                list_url = $('#banners-list-table').find(".list_url").val();
                if ($.fn.dataTable.isDataTable( '#banners-list-table' ))
                    banners_list_table.destroy();
                banners_list_table = $('#banners-list-table').DataTable({
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
                        "data":{_type: filter_condition,filter_value:filter_value},
                    },
                    "columns": [
                        { "data": "checkbox","orderable": false,"searchable":false},
                        { "data": "banner_id" },
                        { "data": "banner_image" },
                        // { "data": "banner_url" },  
                        { "data": "start_date"}, 
                        { "data": "end_date" },  
                        { "data": "banner_type" },  
                        { "data": "status","orderable": false,"searchable":false},
                        { "data": "created_at"},  
                        { "data": "action","orderable": false,"searchable":false},
                    ]	 
                });
            }
            $(document).ready(function() {
                displayBanners('all');
            });
            function updateBannerStatus(status_value,banner_id,_this) {
                update_url = _this.closest("body").find(".update_url").val();
                $.ajax({
                    url: update_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,banner_id: banner_id,status_value:status_value},
                    success: function(response){
                        toastr.options =
                        {
                            "closeButton" : true,
                            "progressBar" : true
                        }
                        toastr.success(response.message);
                        type = _this.closest(".banner-list").find(".filter-by-status").val();
                        displayBanners(type);
                    }
                });
            }
            $(document).on("click",".banner-status",function(){
                _this = $(this);
                banner_id = [];
                status_value = (_this.data('type') == "delete") ? "delete" : (this.checked) ? 'active' : 'inactive';
                id = _this.closest("tr").find(".banner_id").val();
                banner_id.push(id);
                if(_this.data('type') == "delete") {
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
                            updateBannerStatus(status_value,banner_id,_this);
                        }
                    });
                } else {
                    updateBannerStatus(status_value,banner_id,_this);
                }
            });
            $(document).on("click","#checkAll",function() {
                $('.banner-checkbox').not(this).prop('checked', this.checked);
            });
            $(document).on("change",".banner-checkbox",function() {
                var checked = $('input[name="banner_checkbox"]:checked').length > 0;
                $(this).closest("section").find(".error-message").text("");
                if ($(".banner-checkbox:checked").length === $(".banner-checkbox").length) {
                    $("#checkAll").prop("checked", true);
                } else {
                    $("#checkAll").prop("checked", false);
                }
            });
            $(document).on("change",".bulk-action",function(){
                _this = $(this);
                var checked = $('input[name="banner_checkbox"]:checked').length > 0;
                if (!checked){
                    $(this)[0].selectedIndex = 0;
                    $(this).closest("section").find(".error-message").text("Please check at least one checkbox").css("color","#F30000");
                    return false;
                } else if(_this.val() != "") {
                    var i = 0;
                    var banner_id = [];
                    $($(this).closest(".banner-list").find("#banners-list-table").find(".banner-checkbox")).each(function(){
                        if(this.checked) {
                            banner_id[i] = $(this).val();
                            i++;
                        }
                    });
                    status_value = _this.val();
                    updateBannerStatus(status_value,banner_id,_this);
                    $(this).closest("section").find("#checkAll").prop('checked', false);
                    $(this).closest("section").find(".bulk-action").val("");
                }
            });
            $(document).on("change",".filter-by-status",function(){
                displayBanners($(this).val());
            });
        </script>
   </body>
</html>