<!DOCTYPE html>
<html lang="en">
    <head>
        @php $role_name = Auth::user()->is_admin == 2 ? __('store-admin.store_admin') : __('store-admin.cashier');  @endphp
        <title>{{ __('store-admin.product_list_title',['company' => Auth::user()->company_name, 'role_name' => $role_name]) }}</title>
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
                                        <h3 class="page-title">{{ __('store-admin.all_products') }}</h3>
                                    </div>
                                    <div class="text-right">
                                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.product.create') }}"><button class="btn btn-primary" type="button"> <span>Add New Product</span> </button></a>					
                                    </div>
                                </div>
                            </div>
                            <div class="box-body product-list">
                                <div class="row gx-3">
                                    <div class="col-lg-2 col-md-4 col-6 order-lg-1 order-1 mb-2">
                                        <select class="form-control bulk-action">
                                            <option selected="">Bulk Action</option>
                                            <option value="publish">Publish</option>
                                            <option value="unpublish">Draft</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-6 order-lg-2 order-2 mb-2">
                                        <select class="form-control sort-by-category">
                                            <option value="">All</option>
                                            @if(isset($category_details) && !empty($category_details))
                                                @foreach ($category_details as $category)
                                                    <option value="{{ $category->category_id }}">{{ $category->category_name }}</option> 
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-12 order-lg-4 order-4 p-md-0">
                                        <button class="btn btn-success export-product-data" data-export-type="pdf" type="button"> <span>PDF</span> </button>				
                                        <button class="btn btn-danger export-product-data" data-export-type="excel" type="button"> <span>Excel</span> </button>				
                                    </div>
                                    <div class="col-lg-6 col-md-12 order-lg-3 order-3 error-message "></div>
                                </div>
                                <hr/>
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="product-list-table">
                                        <input type="hidden" class="list_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.product.index') }}">
                                        <input type="hidden" class="update_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.product.update') }}">
                                        <input type="hidden" class="update-order-no-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.product.update-order-number')}}">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="form-check ms-2">
                                                        <input class="form-check-input" id="checkAll" type="checkbox" value="">
                                                    </div>
                                                </th>
                                                <th>{{ __('store-admin.id') }}</th>
                                                <th scope="col">{{ __('store-admin.media') }}</th>
                                                <th scope="col">{{ __('store-admin.product') }}</th>
                                                <!-- <th scope="col">{{ __('store-admin.ordering_number') }}</th> -->
                                                <th scope="col">{{ __('store-admin.type') }}</th>
                                                <th scope="col">{{ __('store-admin.category') }}</th>
                                                <th scope="col">{{ __('store-admin.sub_category') }}</th>
                                                <th scope="col">{{ __('store-admin.price') }}</th>
                                                <th scope="col">{{ __('store-admin.status') }}</th>
                                                <th scope="col">{{ __('store-admin.created_at') }}</th>
                                                <th scope="col" >{{ __('store-admin.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="11">{{ trans('datatables.sEmptyTable') }}</td>
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
                ProductList('all');
            });
            $(document).on("click","#checkAll",function() {
                $('.product-checkbox').not(this).prop('checked', this.checked);
            });
            $(document).on("change",".sort-by-category",function(){
                ProductList('category',$(this).val());
            });
            $(document).on("click",".export-product-data",function(){
                exportType = $(this).attr("data-export-type");
                if($(".sort-by-category").val() != "")
                    ProductList('category',$(".sort-by-category").val(),exportType);
                else
                    ProductList('all','',exportType);
            });
            function ProductList(filter_condition,filter_value="",exportType = "") {
                list_url = $('#product-list-table').find(".list_url").val();
                if(exportType != "") {
                    $.ajax({
                        url: list_url,
                        type: 'get',
                        xhrFields: {
                            responseType: exportType === "pdf" ? 'blob' : undefined
                        },
                        dataType: exportType !== "pdf" ? 'json' : undefined,
                        "data":{type: filter_condition,filter_value:filter_value,export_type:exportType},
                        success: function(response){
                            if(exportType == "pdf") {
                                // Create a blob URL for the response
                                var blobUrl = URL.createObjectURL(response);
                                // Create a temporary link element
                                var link = document.createElement('a');
                                link.href = blobUrl;
                                link.download = 'product-details.pdf';
                                // Trigger the download by clicking the link
                                link.click();
                                // Clean up
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
                    if ( $.fn.dataTable.isDataTable( '#product-list-table' ) )
                        product_list_table.destroy();
                        product_list_table = $('#product-list-table').DataTable({
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
                            "data":{type: filter_condition,filter_value:filter_value},
                        },
                        "columns": [
                            { "data": "checkbox","orderable": false,"searchable":false},
                            { "data": "product_id" },
                            { "data": "category_image" },
                            { "data": "product_name" },  
                            // { "data": "order_number"}, 
                            { "data": "type_of_product" },  
                            { "data": "category_name" },  
                            { "data": "sub_category_name" },  
                            { "data": "price" }, 
                            { "data": "status" }, 
                            { "data": "created_at"},  
                            { "data": "action","orderable": false,"searchable":false},
                        ]	 
                    });
                }
            }
            // $(document).on("change",".product-status",function(){
            //     _this = $(this);
            //     product_id = [];
            //     status_value = (this.checked) ? 1 : 0;
            //     id = _this.closest("tr").find(".product_id").val();
            //     update_url = _this.closest("table").find(".update_url").val();
            //     _type = _this.attr("data-type");
            //     product_id.push(id);
            //     UpdateProductStatus(status_value,product_id,_this,update_url,_type);
            // });
            $(document).on("change",".product-checkbox",function() {
                var checked = $('input[name="product_checkbox"]:checked').length > 0;
                if ($(".product-checkbox:checked").length === $(".product-checkbox").length) {
                    $("#checkAll").prop("checked", true);
                } else {
                    $("#checkAll").prop("checked", false);
                }
                $(this).closest("section").find(".error-message").text("");
            });
            $(document).on("change",".bulk-action",function(){
                _this = $(this);
                var checked = $('input[name="product_checkbox"]:checked').length > 0;
                if (!checked){
                    $(this)[0].selectedIndex = 0;
                    $(this).closest("section").find(".error-message").text("Please check at least one checkbox").css("color","#F30000");
                    return false;
                } else if(_this.val() != "") {
                    var i = 0;
                    var product_id = [];
                    $($(this).closest(".product-list").find("#product-list-table").find(".product-checkbox")).each(function(){
                        if(this.checked) {
                            product_id[i] = $(this).val();
                            i++;
                        }
                    });
                    status_value = _this.val();
                    update_url = $(this).closest(".product-list").find("#product-list-table").find(".update_url").val();
                    UpdateProductStatus(status_value,product_id,_this,update_url);
                    type = "all";
                    filter_value = '';
                    if(_this.closest(".product-list").find(".sort-by-category").val() != "") {
                        type = "category";
                        filter_value = _this.closest(".product-list").find(".sort-by-category").val();
                    }
                    ProductList(type,filter_value);
                }
            });
            function UpdateProductStatus(status_value,product_id,_this,update_url,_type = null) {
                $.ajax({
                    url: update_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,product_id: product_id,status_value:status_value, _type : _type},
                    success: function(response){
                        toastr.options =
                        {
                            "closeButton" : true,
                            "progressBar" : true
                        }
                        toastr.success(response.message);
                    }
                });
            }
            $(document).on("click",".product-delete",function() {
                event.preventDefault();
                delete_product_link = $(this).attr("href");
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
                        $(location).attr('href',delete_product_link);
                    }
                });
            });
            $(document).on("change",".category-order-number",function(event) {
                event.preventDefault();
                old_order_number = $(this).attr("data-order-number");
                order_number = $(this).val();
                category_id = $(this).closest("tr").find(".encrypted_product_id").val();
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
        </script>
    </body>
</html>
