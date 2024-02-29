<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ __('store-admin.tax_page_title',['company' => Auth::user()->company_name]) }}</title>
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
                        <div class="card mb-4">
                            <div class="content-header">
                                <div class="d-flex align-items-center">
                                    <div class="mr-auto">
                                        <h3 class="page-title">{{ __('store-admin.add_tax_card_title') }}</h3>
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="card-body">
                                <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.tax.store') }}" enctype="multipart/form-data">
                                @csrf
                                    <input type="hidden" name="tax_id" class="tax-id" value="{{!empty($tax_details) && !empty($tax_details[0]->tax_id) ? Crypt::encrypt($tax_details[0]->tax_id) : '' }}">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.tax_percentage') }}</label>
                                                <input type="hidden" id="old-tax-percentage" name="old_tax_value" class="form-control old-tax-percentage" value="{{!empty($tax_details) && !empty($tax_details[0]->tax_percentage) ? $tax_details[0]->tax_percentage : '' }}">
                                                <input type="number" id="tax-percentage" data-label = "{{ __('store-admin.tax_percentage') }}" step="0.01" min="0" name="tax_percentage" class="form-control tax-percentage required-field form-input-field" value="{{!empty($tax_details) && !empty($tax_details[0]->tax_percentage) ? $tax_details[0]->tax_percentage : '' }}">
                                                <span class="error error-message"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">&nbsp;</div>
                                        <div class="col-lg-12">
                                            <button type="submit" class="btn btn-primary" id="save-tax-info">{{ __('store-admin.add_tax') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <div class="content-header px-30">
                                        <div class="d-flex row align-items-center ">
                                            <div class="col-md-6 mr-auto">
                                                <h3 class="page-title">{{ __('store-admin.tax_history_details') }}</h3>
                                            </div>
                                        </div>
                                        <hr/>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover nowrap table-bordered" id="tax-history-table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('store-admin.previous_tax_percentage') }}</th>
                                                        <th>{{ __('store-admin.new_tax_percentage') }}</th>
                                                        <th>{{ __('store-admin.created_at') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($tax_history_details) && !empty($tax_history_details))
                                                        @php $i = 0; @endphp
                                                        @foreach($tax_history_details as $tax_history)
                                                            <tr>
                                                                <td>{{ ++$i }}</td>
                                                                <td>{{ $tax_history->old_tax_value }}</td>
                                                                <td>{{ $tax_history->new_tax_value }}</td>
                                                                <td>{{ $tax_history->tax_created_at }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr><td colspan = "4">{{ __('datatables.sEmptyTable') }}</td></tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            @include('common.cashier_admin.copyright')
        </div>
        @include('common.cashier_admin.footer')
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#tax-history-table').DataTable({
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
        </script>
        <script>
            $(document).on("click","#save-tax-info",function() {
                var taxPercentage = $('#tax-percentage').val().trim();
                var oldTaxPercentage = $(".old-tax-percentage").val().trim();
                error = 0;
                $('#tax-percentage').closest(".input-field-div").find(".error-message").text("");
                if((taxPercentage != "") && (isNaN(taxPercentage) || (taxPercentage < 0) || (taxPercentage > 100))) {
                    $('#tax-percentage').closest(".input-field-div").find(".error-message").text(translations.percentage_error).css("color", "#F30000");
                    return false;
                } else if(oldTaxPercentage == taxPercentage) {
                    $('#tax-percentage').closest(".input-field-div").find(".error-message").text(translations.new_tax_percentage_error).css("color", "#F30000");
                    return false;
                } else
                    return true;
            });
            $('.tax-percentage').on('keypress', function(evt) {
                return isNumber(evt);
            });
        </script>
    </body>
</html>