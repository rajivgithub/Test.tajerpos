<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ __('store-admin.customer_inquiries_page_title',['company' => Auth::user()->company_name]) }}</title>
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
                <div class="container mt-5">
                    <section class="content">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="content-header">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-auto">
                                                <h3 class="page-title">{{ __('store-admin.customer_inquiry') }}</h3>
                                            </div>
                                        </div>
                                        <hr/>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <div class="mb-4 input-field-div">
                                                    <label class="form-label">{{ __('store-admin.name') }}</label>
                                                    <input type="text" class="form-control" value="{{!empty($customer_inquiries) && !empty($customer_inquiries[0]->contactor_name) ? $customer_inquiries[0]->contactor_name : '' }}" disabled>
                                                </div>
                                                <div class="mb-4 input-field-div">
                                                    <label class="form-label">{{ __('store-admin.email') }}</label>
                                                    <input type="text" class="form-control" value="{{!empty($customer_inquiries) && !empty($customer_inquiries[0]->contactor_email ) ? $customer_inquiries[0]->contactor_email  : '' }}" disabled>
                                                </div>
                                                <div class="mb-4 input-field-div">
                                                    <label class="form-label">{{ __('store-admin.phone_number') }}</label>
                                                    <input type="text" class="form-control" value="{{!empty($customer_inquiries) && !empty($customer_inquiries[0]->contactor_phone_no) ? $customer_inquiries[0]->contactor_phone_no : '' }}" disabled>
                                                </div>
                                                <div class="mb-4 input-field-div">
                                                    <label class="form-label">{{ __('store-admin.comment') }}</label>
                                                    <textarea class="form-control" rows="5" disabled>{{!empty($customer_inquiries) && !empty($customer_inquiries[0]->contactor_message ) ? $customer_inquiries[0]->contactor_message : '' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-lg-8">&nbsp;</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">&nbsp;</div>
                        </div>
                    </section>
                </div>
            </div>
            @include('common.cashier_admin.copyright')
        </div>
        @include('common.cashier_admin.footer')
    </body>
</html>