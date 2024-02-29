<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ ($mode == "add") ? trans('store-admin.add_payment_gateway',['company' => Auth::user()->company_name]) : trans('store-admin.edit_payment_gateway',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header') 
        <style>
            .img-md {
                width: 112px;
                height: 112px;
            }
            .img-sm {
                width: 60px !important;
                height: 60px !important;
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
                    <section class="content">
                        <div class="card mb-4">
                            <div class="content-header">
                                <div class="d-flex align-items-center">
                                    <div class="mr-auto">
                                        @php
                                            $page_title = ($mode == "add") ? __('store-admin.add_payment_gateway') : __('store-admin.edit_payment_gateway');
                                        @endphp
                                        <h3 class="page-title">{{$page_title}}</h3>
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="card-body">
                                <form  method="POST" id="categoryForm" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.managepayment.store') }}" enctype="multipart/form-data">
                                @csrf

                                    <input type="hidden" name="mode" value={{$mode}}> 
                                    <input type="hidden" name="gateway_id" class="category-id" value="{{!empty($gateway_details) && !empty($gateway_details[0]->id) ? $gateway_details[0]->id : '' }}"> 
                                    <div class="row">
                                    <div class="col-lg-8">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.payment_gateway_name') }}<span></span></label>
                                                <div class="input-group">
                                                  
                                                    <input disabled type="text" data-label = "{{ __('store-admin.name') }}"   data-max="100" name="payment_name" value = "{{ __('store-admin.paytabs') }}" class="form-control required-field form-input-field">
                                                </div>
                                         
                                            </div>
                                            
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.payment_currency') }}<span></span></label>
                                                <div class="input-group">                                               
                                                    <input disabled type="text" name="payment_currency" value = "{{!empty($gateway_details) && !empty($gateway_details[0]->client_currency) ? $gateway_details[0]->client_currency : 'SAR' }}"   class="form-control  form-input-field">
                                                </div>
                       
                                            </div>

                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.payment_profile_id') }}<span>*</span></label>
                                                <div class="input-group">
                                               
                                                    <input type="text" required  name="profile_id" value = "{{!empty($gateway_details) && !empty($gateway_details[0]->client_id) ? $gateway_details[0]->client_id : '' }}"   class="form-control  form-input-field">
                                                </div>
                                            
                                            </div>

                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.payment_Url') }}</label>
                                                <div class="input-group">
                                                    <input type="text"  required name="payment_url" value = "{{!empty($gateway_details) && !empty($gateway_details[0]->client_url) ? $gateway_details[0]->client_url : '' }}" class="form-control  form-input-field ">
                                                </div>                                       
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.payment_authorization') }}</label>
                                                <div class="input-group">
                                                    <input type="text"   name="auth" value="{{!empty($gateway_details) && !empty($gateway_details[0]->client_secret) ? $gateway_details[0]->client_secret : '' }}" class="form-control  form-input-field ">
                                                   
                                                </div>
                                         
                                            </div>
                                            
                                        </div>
                                        <div class="col-lg-12">
                                            <button class="btn btn-primary" id="save-category-info">{{ __('store-admin.save') }}</button>
                                        </div>
                                    </div>
                                </form>
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
            $(document).on("click","#save-category-info",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
        </script>
    </body>
</html>