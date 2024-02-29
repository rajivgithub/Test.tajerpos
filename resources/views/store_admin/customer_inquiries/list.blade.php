<!DOCTYPE html>
<html lang="en">
    <head>
        @include('common.cashier_admin.header')
        <style>
            .message-column {
                max-width: 350px; /* Set your desired maximum width here */
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
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
                                <h3 class="page-title">All Customer Inquiries</h3>
                            </div>
                        </div>
                    </div>
                    <section class="content">
                        <div class="card mb-4 product-list">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="customer-inquiries-table">
                                        <input type="hidden" class="list_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.customer-inquiries.index') }}">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Phone Number</th>
                                                <th scope="col" class="">Message</th>
                                                <th scope="col">Created At</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="7">Data not found..!</td>
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
                list_url = $('#customer-inquiries-table').find(".list_url").val();
                if ( $.fn.dataTable.isDataTable( '#customer-inquiries-table' ) )
                    customer_inquiries_table.destroy();
                    customer_inquiries_table = $('#customer-inquiries-table').DataTable({
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
                        { "data": "contactor_id","searchable":false},
                        { "data": "contactor_name"},
                        { "data": "contactor_email" },
                        { "data": "contactor_phone_no" },
                        { "data": "contactor_message" },
                        { "data": "created_at" }, 
                        { "data": "action","orderable": false,"searchable":false},
                    ],
                    "initComplete": function () {
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    }
                });
            });
            $(document).on("click",".inquiries-delete",function() {
                event.preventDefault();
                event.stopImmediatePropagation();
                delete_inquiries_link = $(this).attr("href");
                console.log("delete_inquiries_link "+delete_inquiries_link);
                swal({
                    title: `Are you sure you want to delete this record?`,
                    text: "If you delete this, it will be gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $(location).attr('href',delete_inquiries_link);
                    }
                });
            });
        </script>
   </body>
</html>