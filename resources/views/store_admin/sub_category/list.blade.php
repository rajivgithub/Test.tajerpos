<!DOCTYPE html>
<html lang="en">
    <head>  
        @php $role_name = Auth::user()->is_admin == 2 ? __('store-admin.store_admin') : __('store-admin.cashier');  @endphp
        <title>{{ __('store-admin.subcategory_list_title',['company' => Auth::user()->company_name, 'role_name' => $role_name]) }}</title>
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
                            <div class="content-header px-30">
                                <div class="d-lg-flex align-items-center">
                                    <div class="mr-auto">
                                        <h3 class="page-title">{{ __('store-admin.all_sub_categories') }}</h3>
                                    </div>
                                    <div class="text-right">
                                        <button class="btn btn-sm btn-success export-sub-category-data" data-export-type="pdf" type="button"> <span>PDF</span> </button>
                                        <button class="btn btn-sm btn-danger export-sub-category-data" data-export-type="excel" type="button"> <span>Excel</span> </button>
                                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.sub-category.create') }}"><button class="btn btn-sm btn-primary" type="button"><i class="fa fa-plus"></i> <span>{{ __('store-admin.add_sub_category') }}</span> </button></a>					
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="box-body sub-category-list">
                                <div class="row gx-3">
                                    <div class="col-lg-2 col-md-3 col-3 order-lg-1  mb-2">
                                        <select class="form-control bulk-action rounded-pill">
                                            <option selected="">{{ __('store-admin.select_action') }}</option>
                                            <option value="1" data-type="status">{{ __('store-admin.active') }}</option>
                                            <option value="0" data-type="status">{{ __('store-admin.deactivate') }}</option>
                                            <option value="1" data-type="is_deleted">{{ __('store-admin.delete') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-3 order-lg-2 mb-2">
                                        <select class="form-control sort-by-category category-list rounded-pill filter-search-input">
                                            <option value="">{{ __('store-admin.filter_by_category') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-3 order-lg-2 mb-2">
                                        <select class="form-control sort-by-status rounded-pill filter-search-input">
                                            <option value="">{{ __('store-admin.filter_by_status') }}</option>
                                            <option value="1">{{ __('store-admin.active') }}</option>
                                            <option value="0">{{ __('store-admin.deactivate') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-2 order-lg-2 p-md-0">
                                        <button class="btn btn-success rounded-pill apply-filter-search" type="button"> <span>{{ __('store-admin.apply') }} <i class="fa fa-chevron-right" ></i></span> </button>				
                                        <button class="btn btn-outline-success rounded-pill clear-filter-search" type="button" style="border: 1px solid #28a76f;"> <span>{{ __('store-admin.clear') }}</span> </button>				
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-4 order-lg-2 mb-2">&nbsp;</div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-12 order-lg-2 mb-2">
                                        <div class="order-lg-2 error-message"></div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="sub-category-list">
                                        <input type="hidden" class="list-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.sub-category.index')}}">
                                        <input type="hidden" class="update-order-no-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.sub-category.update-order-number')}}">
                                        <input type="hidden" class="update-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.sub-category.update')}}">
                                        <thead>
                                            <tr>  
                                                <th>
                                                    <div class="form-check ms-2">
                                                        <input class="form-check-input" id="checkAll" type="checkbox" value="">
                                                    </div>
                                                </th>                                     
                                                <th>#</th>      
                                                <th scope="col">{{ __('store-admin.category_id') }}</th>                
                                                <th scope="col">{{ __('store-admin.category') }}</th>
                                                <th scope="col">{{ __('store-admin.sub_category_id') }}</th>
                                                <th scope="col">{{ __('store-admin.sub_category') }}</th>
                                                <!-- <th scope="col">Ordering Number</th> -->
                                                <!-- <th scope="col" width="20%">Sub Category Image</th>
                                                <th scope="col">Banner</th>
                                                <th scope="col">Icon</th> -->
                                                <th scope="col">{{ __('store-admin.created_at') }}</th>
                                                <th scope="col">{{ __('store-admin.last_modified_at') }}</th>
                                                <th scope="col">{{ __('store-admin.status') }}</th>
                                                <th scope="col" class="text-end">{{ __('store-admin.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>      
                                                <td class="text-center" colspan="10">{{ trans('datatables.sEmptyTable') }}</td> 
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
            updateURL = $(".update-url").val();
            $(document).on("click","#checkAll",function() {
                $('.sub-category-checkbox').not(this).prop('checked', this.checked);
            });
            $(document).on("change",".sub-category-checkbox",function() {
                var checked = $('input[name="sub_category_checkbox"]:checked').length > 0;
                if ($(".sub-category-checkbox:checked").length === $(".sub-category-checkbox").length) {
                    $("#checkAll").prop("checked", true);
                } else {
                    $("#checkAll").prop("checked", false);
                }
                $(this).closest("section").find(".error-message").text("");
            });
            function UpdateSubCategoryData(updateValue,subCategoryIDs,_this,update_url,_type = null) {
                $.ajax({
                    url: update_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,subCategoryIDs: subCategoryIDs,updateValue:updateValue, _type : _type},
                    success: function(response){
                        toastr.options =
                        {
                            "closeButton" : true,
                            "progressBar" : true
                        }
                        toastr.success(response.message);
                        filterByStatus = _this.closest("section").find(".sort-by-status").val();
                        filterByCategory = _this.closest("section").find(".sort-by-category").val();
                        subCategoryList('',filterByStatus,filterByCategory);
                    }
                });
            }
            $(document).on("change",".sub-category-status",function(event) {
                event.preventDefault();
                _this = $(this);
                type = _this.attr("data-type");
                subCategoryIDs = [];
                statusValue = (this.checked) ? 1 : 0;
                subCategoryID = _this.closest("tr").find(".sub-category-id").val();
                subCategoryIDs.push(subCategoryID);
                UpdateSubCategoryData(statusValue,subCategoryIDs,_this,updateURL,type);
            });
            $(document).on("change",".bulk-action",function(){
                _this = $(this);
                var checked = $('input[name="sub_category_checkbox"]:checked').length > 0;
                if (!checked){
                    $(this).closest("section").find(".error-message").text(translations.choose_error_msg.replace(":type", translations.sub_category)).css("color","#F30000");
                    return false;
                } else if(_this.val() != "") {
                    var i = 0;
                    var subCategoryIDs = [];
                    $($(this).closest(".sub-category-list").find("#sub-category-list").find(".sub-category-checkbox")).each(function(){
                        if(this.checked) {
                            subCategoryIDs[i] = $(this).val();
                            i++;
                        }
                    });
                    updateValue = _this.val();
                    var type = $('option:selected', this).data('type');
                    UpdateSubCategoryData(updateValue,subCategoryIDs,_this,updateURL,type);
                    _this.closest("section").find("#checkAll").prop("checked",false);
                }
                _this[0].selectedIndex = 0;
            });
            $(document).ready(function() {
                subCategoryList();
                $.ajax({ 
                    url: "{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.category-list') }}",
                    type: 'get',
                    success: function(categoryList){
                        categoryListField = $('.sort-by-category');
                        categoryList.forEach(function(category) {
                            categoryListField.append('<option value="' + category.category_id + '">' + category.category_name + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            });
            $(document).on("click",".delete-sub-category",function() {
                event.preventDefault();
                delete_category_link = $(this).attr("href");
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
                        $(location).attr('href',delete_category_link);
                    }
                });
            });
            function subCategoryList(exportType = "",filterByStatus = '',filterByCategory = '') {
                list_url = $('#sub-category-list').find(".list_url").val();
                if(exportType != "") {
                    $.ajax({
                        url: list_url,
                        type: 'get',
                        xhrFields: {
                            responseType: exportType === "pdf" ? 'blob' : undefined
                        },
                        dataType: exportType !== "pdf" ? 'json' : undefined,
                        "data":{type: 'all',export_type:exportType,filterByStatus : filterByStatus,filterByCategory : filterByCategory},
                        success: function(response){
                            if(exportType == "pdf") {
                                var blobUrl = URL.createObjectURL(response);
                                var link = document.createElement('a');
                                link.href = blobUrl;
                                link.download = 'sub-category-details.pdf';
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
                    if ( $.fn.dataTable.isDataTable( '#sub-category-list' ) )
                        sub_category_table.destroy();
                    sub_category_table = $('#sub-category-list').DataTable({
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
                            "data":{type: 'all', filterByStatus : filterByStatus, filterByCategory : filterByCategory},
                        },
                        "columns": [
                            { "data": "checkbox","orderable": false,"searchable":false},
                            { "data": "id" },
                            { "data": "category_number" },
                            { "data": "category_name" },  
                            { "data": "sub_category_number" },
                            { "data": "sub_category_name" },
                            { "data": "created_at"},  
                            { "data": "updated_at"},  
                            { "data": "status","orderable": false,"searchable":false},
                            { "data": "action","orderable": false,"searchable":false},
                        ]	 
                    });
                }
            }
            $(document).on("click",".export-sub-category-data",function(){
                exportType = $(this).attr("data-export-type");
                filterByStatus = $(this).closest("section").find(".sort-by-status").val();
                filterByCategory = $(this).closest("section").find(".sort-by-category").val();
                subCategoryList(exportType,filterByStatus,filterByCategory);
            });
            $(document).on("change",".category-order-number",function(event) {
                event.preventDefault();
                old_order_number = $(this).attr("data-order-number");
                order_number = $(this).val();
                category_id = $(this).closest("tr").find(".sub-category-id").val();
                update_order_no_url = $(this).closest("table").find(".update-order-no-url").val();
                $.ajax({
                    url: update_order_no_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,order_number: order_number,category_id : category_id, old_order_number : old_order_number},
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
            /*$(document).on("change",".sort-by-status",function(event) {
                event.preventDefault();
                filterByStatus = $(this).val();
                filterByCategory = $(this).closest(".sub-category-list").find(".sort-by-category").val();
                subCategoryList('',filterByStatus,filterByCategory);
            });
            $(document).on("change",".sort-by-category",function(event) {
                event.preventDefault();
                filterByStatus = $(this).closest(".sub-category-list").find(".sort-by-status").val();
                filterByCategory = $(this).val();
                subCategoryList('',filterByStatus,filterByCategory);
            });*/
            $(document).on("click",".apply-filter-search",function(event) {
                event.preventDefault();
                filterByCategory = $(this).closest(".sub-category-list").find(".sort-by-category").val();
                filterByStatus = $(this).closest(".sub-category-list").find(".sort-by-status").val();
                subCategoryList('',filterByStatus,filterByCategory);
            });
            $(document).on("click",".clear-filter-search",function(event) {
                event.preventDefault();
                $(this).closest(".sub-category-list").find(".filter-search-input").val("");
                subCategoryList();
            });
        </script>
    </body>
</html>