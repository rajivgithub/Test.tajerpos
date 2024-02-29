<!DOCTYPE html>
<html lang="en">
    <head>
        @php $role_name = Auth::user()->is_admin == 2 ? __('store-admin.store_admin') : __('store-admin.cashier');  @endphp
        <title>{{ ($mode == "add") ? trans('store-admin.add_discount_page_title',['company' => Auth::user()->company_name]) : trans('store-admin.edit_discount_page_title',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header') 
        <style>
            .has-search .form-control {
                padding-left: 2.375rem;
            }
            .has-search .form-control-feedback {
                position: absolute;
                z-index: 2;
                display: block;
                width: 2.375rem;
                height: 2.375rem;
                line-height: 2.375rem;
                text-align: center;
                pointer-events: none;
                color: #aaa;
            }
            .my-search-tab .form-check-input{
                margin-left: 0rem;
            }
            .specific-product-model-scroll {
                max-height: 500px;
                overflow-y: auto; 
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
                        <div class="row">
                            @php
                                $prefix_url = config('app.module_prefix_url');
                                $page_title = ($mode == "add") ? __('store-admin.create_product_discount') : __('store-admin.edit_product_discount');
                            @endphp
                            <div class="col-12">
                                <div class="box">
                                    <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.store-discount.store') }}" enctype="multipart/form-data">
                                    @csrf
                                        <input type="hidden" name="specific_product_data" value="{{!empty($product_data) ? json_encode($product_data) : '' }}" class="specific-product-data">
                                        <input type="hidden" name="mode" class="mode" value={{$mode}}>  
                                        <input type="hidden" class="save-product-variant" value="{{!empty($save_product_variant) ? json_encode($save_product_variant) : '' }}">
                                        <input type="hidden" name="discount_id" class="discount-id" value="{{!empty($discount_details) && !empty($discount_details[0]->discount_id) ? Crypt::encrypt($discount_details[0]->discount_id) : '' }}">
                                        <div class="content-header">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-auto">
                                                    <h3 class="page-title">{{ $page_title }}</h3>
                                                </div>
                                            </div>
                                            <hr/>
                                        </div>
                                        <div class="box-body">
                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h4 class="mb-0">{{ __('store-admin.amount_off_products') }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-4 input-field-div">
                                                                <label class="form-label">{{ __('store-admin.discount_method') }}</label>
                                                                <div class="form-check pl-0">
                                                                    <input class="form-check-input discount-method" type="radio" data-label = "{{ __('store-admin.discount_method') }}" name="discount_method" id="exampleRadios1" value="code" {{ ($mode === "add" || (!empty($discount_details) && !empty($discount_details[0]->discount_method) && $discount_details[0]->discount_method == "code")) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="exampleRadios1">{{ __('store-admin.discount_code') }}</label>
                                                                </div>
                                                                <div class="form-check pl-0">
                                                                    <input class="form-check-input discount-method" type="radio" name="discount_method" data-label = "{{ __('store-admin.discount_method') }}" id="exampleRadios2" value="automatic" {{ (!empty($discount_details) && !empty($discount_details[0]->discount_method) && $discount_details[0]->discount_method == "automatic") ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="exampleRadios2">{{ __('store-admin.automatic_discount') }}</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row discount-code-field dnone">
                                                        <div class="col-md-12"><label class="form-label">{{ __('store-admin.discount_code') }}<span>*</span></label></div>
                                                        <div class="col-md-8">
                                                            <div class="mb-4 input-field-div">
                                                                <input type="text" data-max="150" class="form-control form-input-field discount-code"  data-pattern="^[A-Za-z\u0600-\u06FF0-9_\-]*$" data-error-msg="{{ __('validation.invalid_discount_code') }}" onkeypress="return restrictCharacters(event)" name="discount_code" data-label = "{{ __('store-admin.discount_code') }}" value="{{(!empty($discount_details) && !empty($discount_details[0]->discount_name) && !empty($discount_details[0]->discount_method) && $discount_details[0]->discount_method == 'code') ? $discount_details[0]->discount_name : '' }}">
                                                                <button class="btn btn-default btn-md generate-discount-code" type="button" id="button-addon2"><span>{{ __('store-admin.generate') }}</span></button>
                                                            </div>
                                                            <p class="mb-0">
                                                                <span>{{ __('store-admin.customers_cashiers_code_checkout') }}</span><br/>
                                                                @if ($errors->has('discount_name'))
                                                                    <span class="text-danger error-message">{{ $errors->first('discount_name') }}</span>
                                                                @endif
                                                                <span class="error error-message"></span>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-4">&nbsp;</div>
                                                    </div>
                                                    <div class="row discount-automatic-field dnone">
                                                        <div class="col-md-12"><label class="form-label">{{ __('store-admin.title') }}<span>*</span></label></div>
                                                        <div class="col-md-8">
                                                            <div class="mb-4 input-field-div">
                                                                <input type="text" data-max="150" class="form-control form-input-field product-name" name="discount_name" data-label = "{{ __('store-admin.title') }}" data-pattern="^[A-Za-z\u0600-\u06FF0-9,._&\/+()\-\s|]*$" data-error-msg="{{ __('validation.invalid_category_err') }}" onkeypress="return restrictCharacters(event)" value="{{(!empty($discount_details) && !empty($discount_details[0]->discount_name) && !empty($discount_details[0]->discount_method) && $discount_details[0]->discount_method == 'automatic') ? $discount_details[0]->discount_name : '' }}">
                                                                <span>{{ __('store-admin.cart_checkout_visibility') }}</span><br/>
                                                                @if ($errors->has('discount_name'))
                                                                    <span class="text-danger error-message">{{ $errors->first('discount_name') }}</span>
                                                                @endif
                                                                <span class="error error-message"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">&nbsp;</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-4 input-field-div">
                                                                <label class="form-label">{{ __('store-admin.coupon_code_applicability') }}<span>*</span></label>
                                                                <div class="form-check pl-0">
                                                                    <input class="form-check-input store-type" type="radio" data-label = "{{ __('store-admin.coupon_code_applicability') }}" name="store_type" id="store-type-radios1" value="online" {{ ($mode === "add" || (!empty($discount_details) && !empty($discount_details[0]->store_type) && $discount_details[0]->store_type == "online")) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="store-type-radios1">{{ __('store-admin.online_store') }}</label>
                                                                </div>
                                                                <div class="form-check pl-0">
                                                                    <input class="form-check-input store-type" type="radio" name="store_type" data-label = "{{ __('store-admin.coupon_code_applicability') }}" id="store-type-radios2" value="offline" {{ (!empty($discount_details) && !empty($discount_details[0]->store_type) && $discount_details[0]->store_type == "offline") ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="store-type-radios2">{{ __('store-admin.offline_store') }}</label>
                                                                </div>
                                                                <div class="form-check pl-0">
                                                                    <input class="form-check-input store-type" type="radio" name="store_type" data-label = "{{ __('store-admin.coupon_code_applicability') }}" id="store-type-radios3" value="both" {{ (!empty($discount_details) && !empty($discount_details[0]->store_type) && $discount_details[0]->store_type == "both") ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="store-type-radios3">{{ __('store-admin.both') }}</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h4 class="mb-0">{{ __('store-admin.value') }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row input-field-div">
                                                        <div class="col-lg-4 col-md-8">
                                                            <div class="btn-group discount-type-field">
                                                                <input type="hidden" name="discount_type" class="discount-type" value="{{ (!empty($discount_details) && !empty($discount_details[0]->discount_type)) ? $discount_details[0]->discount_type : 'percent' }}">
                                                                <button type="button" data-type="percent" class="btn discount-type-btn {{ ($mode === 'add' || (!empty($discount_details) && !empty($discount_details[0]->discount_type) && $discount_details[0]->discount_type == 'percent')) ? 'btn-dark' : 'btn-default' }}">{{ __('store-admin.percentage') }}</button>
                                                                <button type="button" data-type="flat" class="btn discount-type-btn {{ (!empty($discount_details) && !empty($discount_details[0]->discount_type) && $discount_details[0]->discount_type == 'flat') ? 'btn-dark' : 'btn-default' }}">{{ __('store-admin.fixed_amount') }}</button>							 
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-8">
                                                            <input type="text" data-pattern="^[0-9.]*$" data-error-msg="{{ __('validation.invalid_amount_err') }}" onkeypress="return restrictCharacters(event)" name="discount_value" class="form-control required-field form-input-field discount-value" data-label = "{{ __('store-admin.discount_value') }}" value="{{!empty($discount_details) && !empty($discount_details[0]->discount_value) ? $discount_details[0]->discount_value : '' }}">
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="col-md-4">&nbsp;</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h4 class="mb-0">{{ __('store-admin.applies_to') }}</h4>
                                                </div>
                                                <div class="card-body discount-type-field input-field-div">
                                                    <div class="form-check pl-0">
                                                        <input class="form-check-input discount-type" type="radio" data-label = "{{ __('store-admin.discount_type') }}" name="product_discount_type" id="exampleRadios3" value="all" {{ ($mode === "add" || (!empty($discount_details) && !empty($discount_details[0]->product_discount_type) && $discount_details[0]->product_discount_type == "all")) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="exampleRadios3">{{ __('store-admin.all_products') }}</label>
                                                    </div>
                                                    <div class="form-check pl-0">
                                                        <input class="form-check-input discount-type" type="radio" name="product_discount_type" data-label = "{{ __('store-admin.discount_type') }}" id="exampleRadios4" value="specific" {{ (!empty($discount_details) && !empty($discount_details[0]->product_discount_type) && $discount_details[0]->product_discount_type == "specific") ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="exampleRadios4">{{ __('store-admin.specific_product') }}</label>
                                                    </div>
                                                    <div class="specific-product-field dnone">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <div class="input-group mt-3 mb-3">
                                                                    <input type="text" class="form-control" aria-describedby="basic-addon2">
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-danger get-all-products" type="button">{{ __('store-admin.browse') }}</button>
                                                                    </div>
                                                                </div>
                                                                <table class="table table-hover table-bordered product-discount-list-table dnone">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>{{ __('store-admin.product_image') }}</th>
                                                                            <th>{{ __('store-admin.product') }}</th>
                                                                            <th>{{ __('store-admin.action') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="product-discount-list"></tbody>
                                                                </table>
                                                            </div>
                                                            <div class="col-md-4">&nbsp;</div>
                                                        </div>
                                                    </div>
                                                    <div class='form-check discount-code-field mt-2' style="padding-left: 23px !important;">
                                                        <input class="form-check-input" type="checkbox" name="once_per_order" value="1" {{ ($mode === "add" || (!empty($discount_details) && !empty($discount_details[0]->once_per_order) && $discount_details[0]->once_per_order == 1)) ? 'checked' : '' }}>
                                                        <label class="form-check-label">{{ __('store-admin.only_apply_once_per_order') }}</label>
                                                        <p>{{ __('store-admin.not_selected_description') }}</p>
                                                    </div> 
                                                    <div class="modal fade" id="specific-product-model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLongTitle">{{ __('store-admin.add_products') }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body specific-product-model-scroll">
                                                                    <div class="form-group has-search mb-3">
                                                                        <span class="fa fa-search form-control-feedback"></span>
                                                                        <input type="text" class="form-control" id="searchInput" placeholder="Search">
                                                                    </div>
                                                                    <table class="table my-search-tab discount-products-table">
                                                                        <tbody class="search-discount-products">
                                                                            @php $product_id = []; @endphp
                                                                            @foreach ($product_variant_details as $record)
                                                                                @if ($record['type_of_product'] === 'single')
                                                                                <tr class="single-product-{{ $record['product_id'] }}" data-product-id="{{ $record['product_id'] }}" data-product-type="{{ $record['type_of_product'] }}">
                                                                                    <td scope="row"><input class="select-product form-check-input product-checkbox-{{ $record['product_id'] }}" type="checkbox" value="" {{ (!empty($product_data) && array_key_exists($record['product_id'],$product_data)) ? 'checked' : '' }} id="defaultCheck1"></td>
                                                                                    <input type="hidden" class="product-id" value="{{ $record['product_id'] }}">
                                                                                    <td><img src="{{ $record['category_image'] }}" class="product-image" alt=""></td>
                                                                                    <td class="product-name">{{ $record['product_name'] }}</td>
                                                                                    <td>{{ $record['unit'] != "" ? $record['unit']." available" : "" }} </td>
                                                                                    <td>SAR {{ $record['price'] }}</td>
                                                                                </tr>
                                                                                @elseif ($record['type_of_product'] === 'variant')
                                                                                    @if(!in_array($record['product_id'],$product_id))
                                                                                        <tr class="single-product-{{ $record['product_id'] }} select-variant-product" data-type="all" data-product-id="{{ $record['product_id'] }}" data-product-type="{{ $record['type_of_product'] }}">
                                                                                            <td scope="row"><input class="form-check-input variant-check-all product-checkbox-{{ $record['product_id'] }}" type="checkbox" value="" id="defaultCheck1"></td>
                                                                                            <td class="product-name">{{ $record['product_name'] }}</td>
                                                                                            <td colspan="2">&nbsp;</td>
                                                                                        </tr>
                                                                                        @php
                                                                                            $product_id[] = $record['product_id'];
                                                                                        @endphp
                                                                                    @endif
                                                                                    <tr class="variant-product-{{ $record['product_id'] }} variant-product-{{ $record['product_id'] }}-{{ $record['variants_combination_id'] }}" data-product-id="{{ $record['product_id'] }}" data-product-type="{{ $record['type_of_product'] }}">
                                                                                        <td scope="row"><input class="select-product form-check-input variant-product select-variant-product select-variant-product-{{ $record['product_id'] }} product-checkbox-{{ $record['product_id'] }}-{{ $record['variants_combination_id'] }}" type="checkbox" value="" {{ (!empty($product_data) && array_key_exists($record['product_id'],$product_data) && (in_array($record['variants_combination_id'],$product_data[$record['product_id']]))) ? 'checked' : '' }} id="defaultCheck1"></td>
                                                                                        <input type="hidden" class="product-id" value="{{ $record['product_id'] }}">
                                                                                        <input type="hidden" class="variant-id" value="{{ $record['variants_combination_id'] }}">
                                                                                        <td><img src="{{ $record['category_image'] }}" class="product-image" alt=""></td>
                                                                                        <td class="product-name">{{ $record['variants_combination_name'] }}</td>
                                                                                        <td>{{ $record['on_hand'] != "" ? $record['on_hand']." available" : "" }} </td>
                                                                                        <td>SAR {{ $record['variant_price'] }}</td>
                                                                                    </tr>
                                                                                @endif
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="modal-footer text-right">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('store-admin.cancel') }}</button>
                                                                    <button type="button" class="btn btn-primary select-product-to-discount">{{ __('store-admin.add') }}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="variants-product-model" tabindex="-1" role="dialog"  aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title edit-variants-title" id="exampleModalLongTitle">{{ __('store-admin.edit_variants') }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <table class="table variants-products-table">
                                                                        <tbody class="variants-products-tbody">
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="modal-footer text-right">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('store-admin.cancel') }}</button>
                                                                    <button type="button" class="btn btn-primary update-variants">{{ __('store-admin.save') }}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="error error-message"></span>
                                                    <span class="error error-info"></span>
                                                </div>
                                            </div>
                                            <div class="card mb-4 discount-code-field">
                                                <div class="card-header">
                                                    <h4 class="mb-0">{{ __('store-admin.min_purchase_requirements') }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-check pl-0">
                                                        <input class="form-check-input min-require-type" type="radio" data-label = "{{ __('store-admin.min_purchase_requirements') }}" name="min_require_type" id="exampleRadios5" value="no" {{ ($mode === "add" || empty($discount_details[0]->min_require_type) || (!empty($discount_details) && !empty($discount_details[0]->min_require_type) && $discount_details[0]->min_require_type == "no")) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="exampleRadios5">{{ __('store-admin.no_min_requirements') }}</label>
                                                    </div>
                                                    <div class="form-check pl-0">
                                                        <input class="form-check-input min-require-type" type="radio" data-label = "{{ __('store-admin.min_purchase_requirements') }}" name="min_require_type" id="exampleRadios6" value="amount" {{ (!empty($discount_details) && !empty($discount_details[0]->min_require_type) && $discount_details[0]->min_require_type == "amount") ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="exampleRadios6">{{ __('store-admin.min_purchase_amount') }}</label>
                                                        <div class="row min-req-fields input-field-div dnone">
                                                            <div class="col-md-4">
                                                                <input type="text" name="min_amount" id="min-amount" data-label = "{{ __('store-admin.minimum_purchase') }}" class="min-amount min-field form-input-field form-control" value="{{ (!empty($discount_details) && !empty($discount_details[0]->min_require_type) && $discount_details[0]->min_require_type == 'amount' && $discount_details[0]->min_value != '') ? $discount_details[0]->min_value : '' }}" style="margin-left:35px;">
                                                                <span class="error error-message" style="margin-left:35px;"></span>
                                                            </div>
                                                            <div class="col-md-8">&nbsp;</div>
                                                        </div>
                                                    </div>
                                                    <div class="form-check pl-0">
                                                        <input class="form-check-input min-require-type" type="radio" name="min_require_type" data-label = "{{ __('store-admin.min_purchase_requirements') }}" id="exampleRadios7" value="quantity" {{ (!empty($discount_details) && !empty($discount_details[0]->min_require_type) && $discount_details[0]->min_require_type == "quantity") ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="exampleRadios7">{{ __('store-admin.min_quantity_items') }}</label>
                                                        <div class="row min-req-fields input-field-div dnone">
                                                            <div class="col-md-4">
                                                                <input type="text" name="min_quantity" id="min-quantity" data-label = "{{ __('store-admin.min_quantity') }}" class="min-quantity min-field form-input-field form-control" value="{{ (!empty($discount_details) && !empty($discount_details[0]->min_require_type) && $discount_details[0]->min_require_type == 'quantity' && $discount_details[0]->min_value != '') ? $discount_details[0]->min_value : '' }}" style="margin-left:35px;">
                                                                <span class="error error-message" style="margin-left:35px;"></span>
                                                            </div>
                                                            <div class="col-md-8">&nbsp;</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card mb-4 discount-code-field">
                                                <div class="card-header">
                                                    <h4 class="mb-0">{{ __('store-admin.max_discount_uses') }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-check pl-0">
                                                        <input class="form-check-input max-require-type" type="radio" name="max_discount_uses" id="exampleRadios5" value="no" {{ ($mode == "add" || (!empty($discount_details) && !empty($discount_details[0]->max_discount_uses) && $discount_details[0]->max_discount_uses == "no")) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="exampleRadios5">{{ __('store-admin.no_max_discounts') }}</label>
                                                    </div>
                                                    <!-- <div class="form-check pl-0">
                                                        <input class="form-check-input max-require-type" type="radio" name="max_discount_uses" id="exampleRadios10" value="multiple" {{ ((!empty($discount_details) && !empty($discount_details[0]->max_discount_uses) && $discount_details[0]->max_discount_uses == "multiple")) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="exampleRadios10">Limit number of times this discount can be used in total</label>
                                                        <div class="row max-req-fields dnone input-field-div">
                                                            <div class="col-md-4">
                                                                <input type="text" name="discounts_limit" data-label = "Total usage limit" class="discounts-limit max-field form-control" value="{{ (!empty($discount_details) && !empty($discount_details[0]->max_discount_uses) && $discount_details[0]->max_discount_uses == 'multiple' && $discount_details[0]->max_value != '') ? $discount_details[0]->max_value : '' }}" style="margin-left:35px;">
                                                                <span class="error error-message" style="margin-left:35px;"></span>
                                                            </div>
                                                            <div class="col-md-8">&nbsp;</div>
                                                        </div>
                                                    </div> -->
                                                    <div class="form-check pl-0">
                                                        <input class="form-check-input max-require-type" type="radio" data-label = "{{ __('store-admin.max_discount_amount') }}" name="max_discount_uses" id="exampleRadios9" value="max_discount" {{ ((!empty($discount_details) && !empty($discount_details[0]->max_discount_uses) && $discount_details[0]->max_discount_uses == "max_discount")) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="exampleRadios9">{{ __('store-admin.max_discount_amount') }}</label>
                                                        <div class="row max-req-fields dnone input-field-div">
                                                            <div class="col-md-4">
                                                                <input type="text" name="discounts_amount_limit" data-label = "{{ __('store-admin.max_discount_amount') }}" class="discounts-amount-limit max-field form-control form-input-field" value="{{ (!empty($discount_details) && !empty($discount_details[0]->max_discount_uses) && $discount_details[0]->max_discount_uses == 'max_discount' && $discount_details[0]->max_value != '') ? $discount_details[0]->max_value : '' }}" style="margin-left:35px;">
                                                                <span class="error error-message" style="margin-left:35px;"></span>
                                                            </div>
                                                            <div class="col-md-8">&nbsp;</div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="form-check pl-0">
                                                        <input class="form-check-input max-require-type" type="radio" name="max_discount_uses" id="exampleRadios8" value="single" {{ (!empty($discount_details) && !empty($discount_details[0]->max_discount_uses) && $discount_details[0]->max_discount_uses == "single") ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="exampleRadios8">Limit to one use per customer</label>
                                                    </div> -->
                                                </div>
                                            </div>
                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h4 class="mb-0">{{ __('store-admin.validity') }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-6 input-field-div">
                                                            <label>{{ __('store-admin.start_date') }}</label>
                                                            <input type="date" placeholder="Type here" name="discount_valid_from" class="form-control required-field form-input-field discount-dates discount-start-date" data-label = "{{ __('store-admin.start_date') }}" value="{{!empty($discount_details) && !empty($discount_details[0]->discount_valid_from) ? $discount_details[0]->discount_valid_from : '' }}">
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="col-lg-4 col-md-6 input-field-div">
                                                            <label>{{ __('store-admin.end_date') }}</label>
                                                            <input type="date" placeholder="Type here" name="discount_valid_to" class="form-control discount-dates discount-end-date" value="{{!empty($discount_details) && !empty($discount_details[0]->discount_valid_to) ? $discount_details[0]->discount_valid_to : '' }}">
                                                            <span class="error error-message"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 form-actions mt-10" style="text-align: right; width: 100%;">
                                            <button type="submit" class="btn btn-primary mb-2 save-products-discount-info"> {{ __('store-admin.save') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            @include('common.cashier_admin.copyright')
        </div>
        @include('common.cashier_admin.footer')
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script>
            function hideShowField(checkedValue,_this) {
                if(checkedValue == "specific") {
                    _this.closest(".discount-type-field").find(".specific-product-field").removeClass("dnone");
                } else {
                    _this.closest(".discount-type-field").find(".specific-product-field").addClass("dnone");
                }
            }
            $(document).on("change",".discount-type",function() {
                var checkedValue = $("input[name='" + $(this).attr("name") + "']:checked").val();
                hideShowField(checkedValue,$(this));
            });
            $(document).on("keyup","#searchInput",function() {
                var searchValue = $(this).val().toLowerCase();
                $(this).closest("table").find("tr").removeClass("filter-product");
                $(".discount-products-table").find(".search-discount-products tr").each(function() {
                    var productName = $(this).find('.product-name').text().toLowerCase();
                    if (productName.indexOf(searchValue) != -1) {
                        $(this).closest("tr").show();
                        $(this).closest("tr").addClass("filter-product");
                    } else {
                        $(this).closest("tr").hide();
                        $(this).closest("tr").removeClass("filter-product");
                    }
                });
                $(".filter-product").each(function() {
                    product_id = $(this).data("product-id");
                    product_type = $(this).data("product-type");
                    if(product_type == "variant") {
                        _type = $(this).data("type");
                        if(_type == "all") {
                            $(".variant-product-"+product_id).show(); 
                        } else {
                            $(".single-product-"+product_id).show(); 
                        }
                    }
                });
            });
            $(document).ready(function() {
                $('.variant-check-all').click(function() {
                    product_id = $(this).closest("tr").attr("data-product-id");
                    if ($(this).is(':checked')) {
                        $('.select-variant-product-'+product_id).prop('checked', true);
                    } else {
                        $('.select-variant-product-'+product_id).prop('checked', false);
                    }
                }); 
                $('.variant-product').each(function() {
                    product_id = $(this).closest("tr").attr("data-product-id");
                    var checkall = 0;
                    if ($(this).is(':checked')) {
                        $(".variant-product-"+product_id).find(".variant-product").each(function() {
                            if (!$(this).is(':checked')) 
                                checkall++;
                        });
                        if(checkall == 0)
                            $('.single-product-'+product_id).find(".variant-check-all").prop('checked', true);
                    } else 
                        $('.single-product-'+product_id).find(".variant-check-all").prop('checked', false);
                });
                $('.variant-product').click(function() {
                    product_id = $(this).closest("tr").attr("data-product-id");
                    var checkall = 0;
                    if ($(this).is(':checked')) {
                        $(".variant-product-"+product_id).find(".variant-product").each(function() {
                            if (!$(this).is(':checked')) 
                                checkall++;
                        });
                        if(checkall == 0)
                            $('.single-product-'+product_id).find(".variant-check-all").prop('checked', true);
                    } else 
                        $('.single-product-'+product_id).find(".variant-check-all").prop('checked', false);
                });
                var checkedValue = $("input[name='product_discount_type']:checked").val();
                hideShowField(checkedValue,$(".discount-type"));
                start_data = $(".discount-start-date").val();
                if(start_data == "") {
                    var today = new Date().toISOString().split('T')[0];
                    $('.discount-dates').attr('min', today);
                }
                showDiscountMethodFields();
                minRequireFields($(".min-require-type"));
                maxRequireFields($(".max-require-type"));
                discount_type = $(".discount-type").val();
                if(discount_type == "flat")
                    $(".discount-value").attr('data-max',6);
                else 
                    $(".discount-value").attr('data-max',4);
            });
            $(document).on("change",".discount-start-date",function() {
                start_date = new Date($(this).closest(".row").find(".discount-start-date").val());
                end_date = $(this).closest(".row").find(".discount-end-date").val();
                $(this).closest(".row").find(".discount-end-date").attr('min', start_date.toISOString().split('T')[0]);
                if(end_date != "")
                    $(this).closest(".row").find(".discount-end-date").val(start_date.toISOString().split('T')[0]);
            });
            if($(".mode").val() == "edit") {
                var variant_data = ($(".save-product-variant").val() != "" && $(".save-product-variant").val() != undefined) ? $.parseJSON($(".save-product-variant").val()) : [];
                product_ids = []; product_variant_ids = [];
                showSelectedProduct($(".save-product-variant"));
            } else {
                var variant_data = [];
            }
            var product_ids = [];
            $(document).on("click", ".select-product-to-discount", function() {
                _this = $(this);
                variant_data = []; product_ids = []; product_variant_ids = [];
                showSelectedProduct(_this,'save');
                $('#specific-product-model').modal('hide');
            });
            function showSelectedProduct(_this,_type = '') {
                product_list = ""; var i =0;
                $(".select-product").each(function() {
                    if ($(this).is(":checked")) {
                        var product_type = $(this).closest("tr").attr("data-product-type");
                        var product_id = $(this).closest("tr").find(".product-id").val();
                        clone_product_img = $(this).closest("tr").find(".product-image").clone();
                        var variant_id = $(this).closest("tr").find(".variant-id").val();
                        clone_product_img.css({
                            "height": "50px", 
                            "width": "50px" 
                        });
                        product_name  = $(this).closest("tr").find(".product-name").text();
                        if ($.inArray(product_id, product_ids) === -1) {
                            product_ids.push(product_id);
                        }
                        if(_type == 'save') {
                            var variants_details = {};
                            variants_details.product_id = product_id;
                            if (product_type === "variant") {
                                variants_details.variant_id = variant_id; 
                            }
                            variant_data.push(variants_details);
                        }
                        if (product_type === "variant" && ($.inArray(product_id, product_variant_ids) === -1)) {
                            product_variant_ids.push(product_id);
                            total_variants_count = $(".select-variant-product-"+product_id).length;
                            selected_variants_count = $(".select-variant-product-"+product_id+":checked").length;
                            product_name = $(".single-product-"+product_id).find(".product-name").text();
                            product_list += "<tr class='selected-product-"+product_id+"'><td>"+(++i)+"</td><td><input type='hidden' class='remove-product-id' value='"+product_id+"'><input type='hidden' class='product-name' value='"+product_name+"'>"+clone_product_img[0].outerHTML+"</td><td class='selected-product-name'>"+product_name+"("+selected_variants_count+" of "+total_variants_count+" variants selected)"+"</td><td><a class='btn btn-circle btn-primary btn-xs product-discount-edit' href='#'><i class='fa fa-edit'></i></a> <a class='btn btn-circle btn-danger btn-xs product-discount-delete' href='#'><i class='fa fa-close'></i></a></td></tr>";
                        } else if(product_type === "single"){
                            product_list += "<tr class='selected-product-"+product_id+"'><td>"+(++i)+"</td><td><input type='hidden' class='remove-product-id' value='"+product_id+"'><input type='hidden' class='product-name' value='"+product_name+"'>"+clone_product_img[0].outerHTML+"</td><td class='selected-product-name'>"+product_name+"</td><td><a class='btn btn-circle btn-danger btn-xs product-discount-delete' href='#'><i class='fa fa-close'></i></a></td></tr>";
                        }
                    }
                });
                if(product_list != "") {
                    _this.closest("form").find(".product-discount-list").html(product_list);
                    _this.closest("form").find(".product-discount-list-table").removeClass("dnone");
                } else {
                    _this.closest("form").find(".product-discount-list").html("");
                    _this.closest("form").find(".product-discount-list-table").addClass("dnone");
                }
            }
            $(document).on("click",".get-all-products",function() {
                $(".select-product").prop("checked", false);
                $(".variant-check-all").prop("checked", false);
                if(variant_data.length > 0) {
                    $(variant_data).each(function(key,val) {
                        product_id = val.product_id;
                        variant_id = val.variant_id;
                        if ($.inArray(product_id, product_ids) === -1) {
                            product_ids.push(product_id);
                        }
                        if($(".select-product").hasClass("product-checkbox-"+product_id)) {
                            product_type = $(".product-checkbox-"+product_id).closest("tr").data("product-type");
                            if(product_type == "single") 
                                $(".product-checkbox-"+product_id).prop("checked", true);
                        } else if($(".select-product").hasClass("product-checkbox-"+product_id+"-"+variant_id)) {
                            $(".product-checkbox-"+product_id+"-"+variant_id).prop("checked", true);
                        }
                    });
                }
                if(product_ids.length > 0) {
                    $(product_ids).each(function(key,product_id) {
                        var checkall = 0;
                        $(".variant-product-"+product_id).find(".variant-product").each(function() {
                            if (!$(this).is(':checked')) 
                                checkall++;
                        });
                        if(checkall == 0)
                            $('.single-product-'+product_id).find(".variant-check-all").prop('checked', true);
                    });
                }
                $('#specific-product-model').modal('show');
            });
            $(document).on("click",".discount-type-btn",function() {
                discount_type = $(this).attr("data-type");
                if(discount_type == "flat")
                    $(this).closest(".input-field-div").find(".discount-value").attr('data-max',6);
                else 
                    $(this).closest(".input-field-div").find(".discount-value").attr('data-max',3);
                $(this).closest(".discount-type-field").find(".discount-type").val(discount_type);
                $(".discount-type-btn").removeClass("btn-default");
                $(".discount-type-btn").removeClass("btn-dark");
                $(".discount-type-btn").not(this).addClass("btn-default");
                $(this).addClass("btn-dark");
            }); 
            $(document).on("click",".product-discount-edit",function(event) {
                event.preventDefault();
                _this = $(this);
                _this.closest("body").find(".variants-products-tbody").html("");
                product_name = $(this).closest("tr").find(".product-name").val();
                product_id =  $(this).closest("tr").find(".remove-product-id").val();
                $(this).closest("body").find(".edit-variants-title").text("Edit variants of the "+product_name);
                var variant_id_by_product = [];
                if(variant_data.length > 0) {
                    variant_id_by_product = variant_data
                        .filter(function(item) {
                            return item.product_id === product_id;
                        })
                        .map(function(item) {
                            return item.variant_id;
                        });
                }
                variants_data = '';
                $(".variant-product-"+product_id).each(function() {
                    clone_variant_row = $(this).clone();
                    variants_data += clone_variant_row[0].outerHTML;
                });
                _this.closest("body").find(".variants-products-tbody").html(variants_data);
                _this.closest("body").find(".variants-products-tbody tr").each(function() {
                    variant_id = $(this).find(".variant-id").val();
                    check_exist = variant_data.find(function(item) {
                        return item.variant_id === variant_id;
                    }) !== undefined;
                    if (check_exist) 
                        $(this).find(".select-product").prop("checked", true);
                    
                });
                $("#variants-product-model").modal('show');
            });
            $(document).on("click",".product-discount-delete",function(event) {
                event.preventDefault();
                _this = $(this).closest(".product-discount-list");
                product_id = $(this).closest("tr").find(".remove-product-id").val();
                variant_data = variant_data.filter(function(item) {
                    return item.product_id != product_id;
                });
                $(this).closest("tr").remove();
                i = 0;
                _this.find("tr").each(function() {
                    $(this).find("td:eq(0)").text(++i);
                });
            });
            $(document).on("click",".save-products-discount-info",function() {
                var product_discount_type = $("input[name='product_discount_type']:checked").val();
                var start_date = $(this).closest("form").find(".discount-start-date").val();
                var end_date = $(this).closest("form").find(".discount-end-date").val();
                error_fields = 0;
                $(".discount-type").closest(".input-field-div").find(".error-info").text("");
                if(variant_data.length <= 0 && product_discount_type == "specific") {
                    $(".discount-type").closest(".input-field-div").find(".error-info").text("Please choose the products").css("color", "#F30000");
                    error_fields++;
                }
                check_fields = validateFields($(this));
                if((check_fields > 0) || (error_fields > 0))
                    return false;
                else if(end_date != "" && (new Date(end_date) < new Date(start_date))) {
                    $(".discount-end-date").closest(".input-field-div").find(".error-message").text("Discount end date should be greather than the start date").css("color", "#F30000");
                    return false;
                }
                else {
                    $(this).closest("form").find(".specific-product-data").val(JSON.stringify(variant_data));
                    return true;
                }
            }); 
            $(document).on("click",".update-variants",function(event) {
                event.preventDefault();
                _this = $(this);
                product_id = $(this).closest("#variants-product-model").find(".variants-products-tbody").find(".product-id").val();
                variant_data = variant_data.filter(function(item) {
                    return item.product_id !== product_id;
                });
                check_exist = 0;
                $(this).closest("#variants-product-model").find(".variants-products-tbody tr").each(function() {
                    if($(this).find(".variant-product").is(':checked')) {
                        variant_id = $(this).find(".variant-id").val();
                        var product_type = $(this).attr("data-product-type");
                        var variants_details = {};
                        variants_details.product_id = product_id;
                        variants_details.variant_id = variant_id; 
                        variant_data.push(variants_details);
                        check_exist++;
                    }
                });
                total_variants_count = _this.closest("#variants-product-model").find(".select-variant-product-"+product_id).length;
                selected_variants_count = _this.closest("#variants-product-model").find(".select-variant-product-"+product_id+":checked").length;
                if(check_exist > 0) {
                    product_name = $(".single-product-"+product_id).find(".product-name").text();
                    $(".selected-product-"+product_id).find(".selected-product-name").text(product_name+"("+selected_variants_count+" of "+total_variants_count+" variants selected)");
                } else {
                    $(".selected-product-"+product_id).remove();
                }
                i = 0;
                _this.closest("body").find(".product-discount-list tr").each(function() {
                    $(this).find("td:eq(0)").text(++i);
                });
                $('#variants-product-model').modal('hide');
            });
            $(document).on("change",".discount-method",function() {
                showDiscountMethodFields();
            }); 
            function showDiscountMethodFields() {
                var discount_method = $("input[name='discount_method']:checked").val();
                $(".discount-code-field").find(".required-field").removeClass("required-field");
                if(discount_method == "code") {
                    $(".discount-code-field").removeClass("dnone");
                    $(".discount-automatic-field").addClass("dnone");
                    $(".discount-automatic-field").find(".product-name").removeClass("required-field");
                    $(".discount-code-field").find(".discount-code").addClass("required-field");
                    $(".discount-code-field").find(".discount-code").attr('data-pattern','')
                    minRequireFields($(".min-require-type"));
                    maxRequireFields($(".max-require-type"));
                } else {
                    $(".discount-code-field").addClass("dnone");
                    $(".discount-automatic-field").removeClass("dnone");
                    $(".discount-automatic-field").find(".product-name").addClass("required-field");
                    $(".discount-code-field").find(".discount-code").removeClass("required-field");
                }
            } 
            $(document).on("click",".generate-discount-code",function() {
                const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                let coupon_code = '';
                for (let i = 0; i < 12; i++) {
                    const randomIndex = Math.floor(Math.random() * characters.length);
                    coupon_code += characters.charAt(randomIndex);
                }
                $(this).closest(".discount-code-field").find(".discount-code").val(coupon_code);
            }); 
            function minRequireFields(_this) {
                var min_require_type = $("input[name='min_require_type']:checked").val();
                _this.closest(".discount-code-field").find(".min-req-fields").addClass("dnone");
                _this.closest(".discount-code-field").find(".min-field").removeClass("required-field");
                if(min_require_type == "amount") {
                    _this.closest(".discount-code-field").find(".min-amount").closest(".min-req-fields").removeClass("dnone");
                    _this.closest(".discount-code-field").find(".min-amount").addClass("required-field");
                }
                if(min_require_type == "quantity") {
                    _this.closest(".discount-code-field").find(".min-quantity").closest(".min-req-fields").removeClass("dnone");
                    _this.closest(".discount-code-field").find(".min-quantity").addClass("required-field");
                }
            }
            function maxRequireFields(_this) {
                var max_require_type = $("input[name='max_discount_uses']:checked").val();
                _this.closest(".discount-code-field").find(".max-req-fields").addClass("dnone");
                _this.closest(".discount-code-field").find(".max-field").removeClass("required-field");
                if(max_require_type == "multiple") {
                    _this.closest(".discount-code-field").find(".discounts-limit").closest(".max-req-fields").removeClass("dnone");
                    _this.closest(".discount-code-field").find(".discounts-limit").addClass("required-field");
                }
                if(max_require_type == "max_discount") {
                    _this.closest(".discount-code-field").find(".discounts-amount-limit").closest(".max-req-fields").removeClass("dnone");
                    _this.closest(".discount-code-field").find(".discounts-amount-limit").addClass("required-field");
                }
            }
            $(document).on("change",".min-require-type",function() {
                minRequireFields($(this));
            }); 
            $(document).on("change",".max-require-type",function() {
                maxRequireFields($(this)); 
            }); 
        </script>
    </body>
</html>