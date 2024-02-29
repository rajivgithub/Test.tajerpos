<!DOCTYPE html>
<html lang="en">
    <head>
        @include('common.cashier_admin.header') 
        <!-- <style>
            .myInput {
                display: none;
            }
        </style> -->
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
                                <div class="d-flex row align-items-center ">
                                    <div class="mr-auto">
                                        <h3 class="page-title">All Categories</h3>
                                    </div>
                                    <div class="text-right ">
                                        <button class="btn btn-success export-category-data" data-export-type="pdf" type="button"> <span>PDF</span> </button>
                                        <button class="btn btn-danger export-category-data" data-export-type="excel" type="button"> <span>Excel</span> </button>		
                                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.category.create') }}"><button class="btn btn-primary" type="button"><i class="fa fa-plus"></i> <span>Add Category</span> </button></a>					
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                <table class="table table-hover nowrap table-bordered display table-striped" id="category-list">
                                    <input type="hidden" class="list-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.category.index')}}">
                                    <input type="hidden" class="update-order-no-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.category.update-order-number')}}">
                                    <thead>
                                        <tr>
                                            <th>#</th>  
                                            <th scope="col">Category</th>                    
                                            <th scope="col">Category ID</th>
                                            <th scope="col">Category Image</th>
                                            <!-- <th scope="col">Banner</th> -->
                                            <th scope="col">Icon</th>
                                            <!-- <th scope="col">Ordering Number</th> -->
                                            <!-- <th scope="col">Featured</th> -->
                                            <th scope="col">Created At</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-end">Action</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>      
                                            <td class="text-center" colspan="8">Data not found..!</td> 
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>      
                                            <td style="display: none;"></td>  
                                            <!-- <td style="display: none;"></td>       -->
                                            <!-- <td style="display: none;"></td> -->
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
        <!-- <script src="{{ URL::asset('assets/cashier-admin/vendor_components/sweetalert/sweetalert.min.js') }}"></script>  
        <script src="{{ URL::asset('assets/cashier-admin/vendor_components/sweetalert/jquery.sweet-alert.custom.js') }}"></script>   -->
        <script>
            $(document).on("change",".category-status",function(event) {
                event.preventDefault();
                value = (this.checked) ? 1 : 0;
                category_id = $(this).closest("tr").find(".category-id").val();
                status_url = $(this).closest("tr").find(".status-url").val();
                type = $(this).attr("data-type");
                $.ajax({
                    url: status_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,category_id: category_id,value: value,type : type},
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
            $(document).ready(function() {
                categoryList();
                // list_url = $('#category-list').find(".list_url").val();
                // if ( $.fn.dataTable.isDataTable( '#category-list' ) )
                //     category_table.destroy();
                // category_table = $('#category-list').DataTable({
                //     "processing": true,
                //     "serverSide": true,
                //     "order": [[ 0, "desc" ]],
                //     "ajax": {
                //         "url": list_url,
                //         "dataType": "json",
                //         "type": "get",
                //         "data":{type: 'all'},
                //     },
                //     "columns": [
                //         { "data": "id" },
                //         { "data": "category_name" },  
                //         { "data": "category_number" },
                //         { "data": "category_image" },  
                //         // { "data": "banner" },  
                //         { "data": "icon" },
                //         // { "data": "order_number" },
                //         // { "data": "featured","orderable": false,"searchable":false}, 
                //         { "data": "created_at"},  
                //         { "data": "status","orderable": false,"searchable":false},
                //         { "data": "action","orderable": false,"searchable":false},
                //     ]
                // });
                /*$(".editable-category-order").click(function() { 
                    $(this).hide();
                    var t = $('.editable-category-order').html();
                    $('.category-order-number').val(t);
                    $('.category-order-number').show();
                });
                $(".category-order-number").blur(function() {  
                    $(this).hide();
                    var t = $('.category-order-number').val();
                    $('.editable-category-order').html(t);
                    $('.editable-category-order').show();
                });*/
    
                /*$(".myText").click(function() { 
                    $(this).hide();
                    var t = $('.myText').html();
                    $('.myInput').val(t);
                    $('.myInput').show();
                });
                
                $(".myInput").blur(function() {  
                    $(this).hide();
                    var t = $('.myInput').val();
                    $('.myText').html(t);
                    $('.myText').show();
                });*/

            });
            $(document).on("change",".category-order-number",function(event) {
                event.preventDefault();
                old_order_number = $(this).attr("data-order-number");
                order_number = $(this).val();
                category_id = $(this).closest("tr").find(".category-id").val();
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
            $(document).on("click",".delete-category",function(event) {
                event.preventDefault();
                delete_category_link = $(this).attr("href");
                swal({
                    title: `Are you sure you want to delete this record?`,
                    text: "If you delete this, it will be gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $(location).attr('href',delete_category_link);
                    }
                });
            });
            function categoryList(exportType = "") {
                list_url = $('#category-list').find(".list-url").val();
                if(exportType != "") {
                    $.ajax({
                        url: list_url,
                        type: 'get',
                        xhrFields: {
                            responseType: exportType === "pdf" ? 'blob' : undefined
                        },
                        dataType: exportType !== "pdf" ? 'json' : undefined,
                        "data":{type: 'all',export_type:exportType},
                        success: function(response){
                            if(exportType == "pdf") {
                                var blobUrl = URL.createObjectURL(response);
                                var link = document.createElement('a');
                                link.href = blobUrl;
                                link.download = 'category-details.pdf';
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
                    if ( $.fn.dataTable.isDataTable( '#category-list' ) )
                        category_table.destroy();
                    category_table = $('#category-list').DataTable({
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
                            { "data": "category_name" },  
                            { "data": "category_number" },
                            { "data": "category_image" },  
                            // { "data": "banner" },  
                            { "data": "icon" },
                            // { "data": "order_number" },
                            // { "data": "featured","orderable": false,"searchable":false}, 
                            { "data": "created_at"},  
                            { "data": "status","orderable": false,"searchable":false},
                            { "data": "action","orderable": false,"searchable":false},
                        ]
                    });
                }
            }
            $(document).on("click",".export-category-data",function(){
                exportType = $(this).attr("data-export-type");
                categoryList(exportType);
            });
        </script>
    </body>
</html>