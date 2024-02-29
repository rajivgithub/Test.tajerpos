<!DOCTYPE html>
<html lang="en">
    <head>
        @php $role_name = Auth::user()->is_admin == 2 ? __('store-admin.store_admin') : __('store-admin.cashier');  @endphp
        <title>{{ __('store-admin.all_discounts_page_title',['company' => Auth::user()->company_name, 'role_name' => $role_name]) }}</title>
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
                                <div class="d-flex row align-items-center ">
                                    <div class="mr-auto">
                                        <h3 class="page-title">{{ __('store-admin.discounts_title') }}</h3>
                                    </div>
                                    <div class="text-right ">		
                                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.store-discount.create') }}"><button class="btn btn-primary" type="button"><i class="fa fa-plus"></i> <span>{{ __('store-admin.add_discount') }}</span> </button></a>					
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap table-bordered display table-striped" id="store-order-discount">
                                        <thead>
                                            <tr>
                                                <th>#</th>  
                                                <th scope="col">{{ __('store-admin.title_code') }}</th>  
                                                <th scope="col">{{ __('store-admin.method') }}</th>    
                                                <th scope="col">{!! __('store-admin.coupon_code_applicability') !!}</th>
                                                <th scope="col">{{ __('store-admin.type') }}</th>           
                                                <th scope="col">{{ __('store-admin.from') }}</th>
                                                <th scope="col">{{ __('store-admin.to') }}</th>
                                                <th scope="col">{{ __('store-admin.status') }}</th>
                                                <th scope="col">{{ __('store-admin.value') }}</th>
                                                <th scope="col" class="text-end">{{ __('store-admin.action') }}</th> 
                                            </tr>
                                        </thead>
                                        <tbody class="status-tbody">
                                            @if(isset($store_discount) && !empty($store_discount))
                                                @foreach($store_discount as $key => $status)
                                                    <tr>
                                                        <input type="hidden" name="status_id" class="status-id" value="{{ Crypt::encrypt($status['discount_id']) }}">
                                                        <input type="hidden" class="remove-status-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.store-discount.destroy',Crypt::encrypt($status['discount_id']))}}">
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $status['discount_name'] }}</td>
                                                        <td>{{ ($status['discount_method'] == "code") ? "Code" : "Automatic" }}</td>
                                                        <td>{{ ucfirst($status['store_type']) }}</td>
                                                        <td>{{ ($status['product_discount_type'] == "all") ? "All Produts" : "Specific Products" }}</td>
                                                        <td>{{ $status['discount_valid_from'] }}</td>
                                                        <td>{{ $status['discount_valid_to'] }}</td>
                                                        <td>
                                                            @if ($status['discount_status'] === 'Active')
                                                                <span class="badge badge-success">Active</span>
                                                            @elseif ($status['discount_status'] === 'Expired')
                                                                <span class="badge badge-secondary">Expired</span>
                                                            @elseif ($status['discount_status'] === 'Scheduled')
                                                                <span class="badge badge-warning">Scheduled</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $status['discount_type'] == "flat" ? "FLAT ".$status['discount_value'] : $status['discount_value']."%" }}</td> 
                                                        <td>
                                                            <a class='btn btn-circle btn-danger btn-xs' href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.store-discount.create', Crypt::encrypt($status['discount_id'])) }}"><i class='fa fa-edit'></i></a>
                                                            <a class='btn btn-circle btn-primary btn-xs delete-discount' href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.store-discount.destroy', Crypt::encrypt($status['discount_id'])) }}"><i class='fa fa-trash'></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr class="empty-row">      
                                                    <td class="text-center" colspan="10">Data not found..!</td> 
                                                </tr>
                                            @endif
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
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script>
            $(document).ready(function() {
                $("#store-order-discount").DataTable({
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
                    }
                });
            }); 
            $(document).on("click",".save-store-order-status",function() {
                check_fields = validateFields($(this));
                if((check_fields > 0) || (unique_error > 0))
                    return false;
                else 
                    return true;
            });
            $(document).on("click",".delete-discount",function(event) {
                event.preventDefault();
                delete_discount_link = $(this).attr("href");
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
                        $(location).attr('href',delete_discount_link);
                    }
                });
            });
        </script>
    </body>
</html>