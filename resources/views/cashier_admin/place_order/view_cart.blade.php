<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ trans('store-admin.cart_page_title',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,300&display=swap" rel="stylesheet">
        <style>
            .table-bordered > thead > tr > td, .table-bordered > thead > tr > th {
                background: #0075bb !important;	
            }
            #msform fieldset:not(:first-of-type) {
                display: none;
            }
            #invoice-print1 {
                opacity: 0;
                height: 0;
                transition: opacity 0.3s ease-in-out, height 0s 0.3s;
                overflow: hidden;
            } 
            @media print {
                .content{padding:5px !important;}
                body {
                    visibility: hidden;
                }
                .print-hide{
                    display: none !important;
                }
                #invoice-print1 {
                    font-family: 'Nunito Sans', sans-serif !important;
                    letter-spacing: 3px !important;
                    background-color:white;
                    visibility: visible;
                    position: relative;
                    height: 100% !important;
                    opacity: 1;
                    transition: opacity 0.3s ease-in-out, height 0s;
                }
                .show{padding-right:0px !important;}
                .modal-dialog {
                    max-width: 100% !important;
                    height: 100% !important;
                    margin: 0rem auto;
                }
                #invoice-print1 h6{
                    letter-spacing: 3px !important;
                    font-size:65px!important;
                    font-family: 'Nunito Sans', sans-serif !important;
                }

                #invoice-print1 h4{
                    letter-spacing: 3px !important;
                    font-size:65px !important;
                    font-family: 'Nunito Sans', sans-serif !important;
                }

                #invoice-print1 p{
                    letter-spacing: 3px !important;
                    font-size:55px!important;  
                }
                .billing-date {
                    letter-spacing: 3px !important;
                    font-size:55px!important;
                }
                .billing-items p{
                    letter-spacing: 1px !important;
                    font-size:45!important;  
                }
                .thanks-message{
                    letter-spacing: 2px !important;
                    font-size:45px !important;
                }
            }  
        </style>
    </head>
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <!-- Main content -->
                    <section class="content">
                        <form id="msform"  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.store') }}">
                        @csrf
                            <input type="hidden" class="variant-combinations" value="{{!empty($variant_combination_data) ? json_encode($variant_combination_data) : '' }}">  
                            <input type="hidden" class="tax-percentage" value="{{ (!empty($tax_details) && !empty($tax_details[0]->tax_percentage)) ? $tax_details[0]->tax_percentage : '' }}">
                            <!-- <input type="hidden" class="product-discount" value="{{ (!empty($product_discounts)) ? json_encode($product_discounts) : '' }}"> -->
                            <input type="hidden" class="specific-discount-data" value="{{!empty($productDiscounts) ? json_encode($productDiscounts) : '' }}">
                            <input type="hidden" class="all-discount-data" value="{{!empty($all_discount) ? json_encode($all_discount) : '' }}">
                            <input type="hidden" class="get-discount-data" value="{{!empty($discount_data) ? json_encode($discount_data) : '' }}">
                            <div class="card print-hide" style="margin-bottom:10px !important;">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center input-field-div">
                                                <div class="col-lg-4 text-lg-right pr-0">
                                                    <label class="form-label mb-0">{{ __('store-admin.customer_name') }}</label>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                        </div>
                                                        <input type="text" data-label = "{{ __('store-admin.customer_name') }}" data-error-msg="{{ __('validation.invalid_name_err') }}" data-pattern="^[A-Za-z\u0600-\u06FF. ]+$" onkeypress="return restrictCharacters(event)" data-max="100" name="customer_name" value = "{{!empty($customer_name) && !empty($customer_name[0]) ? $customer_name[0] : '' }}" class="form-control required-field form-input-field customer-name">
                                                    </div>
                                                    <input type="hidden" name="customer_id" class="customer-id" value="">
                                                    @if ($errors->has('customer_name'))
                                                        <span class="text-danger error-message">{{ $errors->first('customer_name') }}</span>
                                                    @endif
                                                    <span class="error error-message"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row align-items-center input-field-div">
                                                <div class="col-lg-4 text-lg-right pr-0">
                                                    <label class="form-label mb-0">{{ __('store-admin.phone_number') }}</label>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                                        </div>
                                                        <input type="text" data-label = "{{ __('store-admin.phone_number') }}" data-min="10" data-max="12" name="phone_number" value = "{{!empty($customer_phone_number) && !empty($customer_phone_number[0]) ? $customer_phone_number[0] : '' }}" data-pattern="^[0-9]+$" data-error-msg="{{ __('validation.invalid_numeric_err') }}" onkeypress="return restrictCharacters(event)" class="form-control required-field form-input-field customer-phone-number">
                                                    </div>
                                                    @if ($errors->has('phone_number'))
                                                        <span class="text-danger error-message">{{ $errors->first('phone_number') }}</span>
                                                    @endif
                                                    <span class="error error-message"></span>                         
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4 print-hide" style="margin-bottom:10px !important;">
                                <div class="card-header">
                                    <h4 class="mb-0">{{ __('store-admin.shopping_cart') }}</h4> 
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered my-cart-tab" id="card-details-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('store-admin.items') }}</th>
                                                    <th>{{ __('store-admin.variants') }}</th>
                                                    <th scope="col">{{ __('store-admin.unit_price') }}</th>
                                                    <th scope="col">{{ __('store-admin.tax') }}</th>
                                                    <!-- <th scope="col">Discount</th> -->
                                                    <th scope="col" width="130">{{ __('store-admin.quantity') }}</th>
                                                    <!-- <th scope="col">Price</th>
                                                    <th scope="col">Total Discount</th> -->
                                                    <th scope="col">{{ __('store-admin.total_amount') }}</th>
                                                    <th scope="col" class="hide-after-submit"></th>
                                                </tr>
                                            </thead>
                                            <input type="hidden" class="view-cart-url" value="{{ url(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/place-order/index')  }}">
                                            <tbody class="card-details-tbody">
                                                @if(isset($product_details) && !empty($product_details) && count($product_details) > 0)
                                                    @foreach ($product_details as $key => $product)
                                                        @if(!empty($variant_combinations) && array_key_exists($product->product_id,$variant_combinations) && !empty($quantity[$product->product_id]))
                                                            @for($i = 0; $i < ($quantity[$product->product_id]);$i++)
                                                                @php
                                                                    $variant_price = $variant_combination_data[$variant_id[$product->product_id][$i]]['variant_price'] != "" ? $variant_combination_data[$variant_id[$product->product_id][$i]]['variant_price'] : 0;
                                                                    $variant_quantity = $variant_combination_data[$variant_id[$product->product_id][$i]]['on_hand'] != "" ? $variant_combination_data[$variant_id[$product->product_id][$i]]['on_hand'] : 0;
                                                                @endphp
                                                                <tr class="product-cart-list">
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <input type="hidden" class="product-name" value="{{ $product->product_name }}">
                                                                            <img src="{{ $product->category_image }}" class="img-fluid mr-2" alt=""> {{ $product->product_name }}
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        @if(!empty($variant_combinations) && array_key_exists($product->product_id,$variant_combinations))
                                                                            <input type="hidden" name="variants_item_name[{{$product->category_id}}][{{$product->product_id}}][]" class="variant-item-name" value="">
                                                                            <select class="form-control select-variants" name="variants_item[{{$product->category_id}}][{{$product->product_id}}][]" data-live-search="true">
                                                                                <option value="">Select</option>
                                                                                @foreach($variant_combinations[$product->product_id] as $variants)
                                                                                    <option data-quantity="{{$variants['on_hand']}}" {{ $variant_id[$product->product_id][$i] == $variants['variants_combination_id'] ? 'selected' : ''}} value="{{$variants['variants_combination_id']}}">{{$variants['variants_combination_name']}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        @endif
                                                                    </td>
                                                                    <td class="single-product-price">SAR {{ number_format((float)($variant_combination_data[$variant_id[$product->product_id][$i]]['variant_price']), 2, '.', '') }}</td> 
                                                                    <td class="single-product-tax">&nbsp;</td>
                                                                    <!-- <td class="single-product-discount">&nbsp;</td> -->
                                                                    <td>
                                                                        <div class="number product-item">
                                                                            <span class="minus">-</span>
                                                                            <input type="hidden" class="product-price" value="{{ $variant_price }}">
                                                                            <input type="hidden" class="product-unit-price" value="{{ $variant_price }}">
                                                                            <input type="hidden" name="product_discount[{{$product->category_id}}][{{$product->product_id}}][]" class="single-discount-price" value="">
                                                                            <input type="hidden" name="total_product_discount[{{$product->category_id}}][{{$product->product_id}}][]" class="total-discount" value="">
                                                                            <input type="hidden" class="product-quantity" value="{{ $get_quantity[$product->product_id][$i] }}">
                                                                            <input type="hidden" class="product-id" value="{{ $product->product_id }}">
                                                                            <input type="hidden" class="tax-type" value="{{ $product->tax_type }}"> 
                                                                            <input type="hidden" class="tax-amount" value="{{ $product->tax_amount }}">
                                                                            <input type="hidden" class="get-product-quantity" value="{{ $variant_quantity }}">
                                                                            <input type="hidden" class="trackable" value="{{ $product['trackable'] }}">
                                                                            <input type="hidden" class="product-unit" value="{{ $product['unit'] }}"> 
                                                                            <input type="hidden" class="type-of-product" value="{{ $product['type_of_product'] }}">
                                                                            <input type="text" name="product_item[{{$product->category_id}}][{{$product->product_id}}][]" value="{{ $get_quantity[$product->product_id][$i] }}" class="quantity" onkeypress="return isNumber(event)">
                                                                            <span class="plus">+</span>
                                                                        </div>
                                                                    </td>
                                                                    <input type="hidden" name="product_amount[{{$product->category_id}}][{{$product->product_id}}][]" class="total-product-price" value="{{ $get_quantity[$product->product_id][$i] * $variant_price }}">
                                                                    <input type="hidden" name="product_tax_amount[{{$product->category_id}}][{{$product->product_id}}][]" class="product-tax-amount" value="">
                                                                    <input type="hidden" name="no_of_products" class="no-of-products" value="">
                                                                    <!-- <td class="product-item-amount">SAR 0.00</td>
                                                                    <td class="total-product-discount">&nbsp;</td> -->
                                                                    <input type="hidden" class="total-product-item-val" value="">
                                                                    <td class="total-product-item-amount">&nbsp;</td>
                                                                    <td class="text-center hide-after-submit"><i class="fa fa-trash delete-product-item"></i></td>
                                                                </tr>
                                                            @endfor
                                                        @else
                                                            <tr class="product-cart-list">
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <input type="hidden" class="product-name" value="{{ $product->product_name }}">
                                                                        <img src="{{ $product->category_image }}" class="img-fluid mr-2" alt=""> {{ $product->product_name }}
                                                                    </div>
                                                                </td>
                                                                <td>--</td>
                                                                <td class="single-product-price">SAR {{ number_format((float)($product->price), 2, '.', '') }}</td>
                                                                <td class="single-product-tax">&nbsp;</td>
                                                                <!-- <td class="single-product-discount">&nbsp;</td>  -->
                                                                <td>
                                                                    <div class="number product-item">
                                                                        <span class="minus">-</span>
                                                                        <input type="hidden" class="product-price" value="{{ $product->price }}">
                                                                        <input type="hidden" class="product-unit-price" value="{{ $product->price }}">
                                                                        <input type="hidden" name="product_discount[{{$product->category_id}}][{{$product->product_id}}][]" class="single-discount-price" value="">
                                                                        <input type="hidden" name="total_product_discount[{{$product->category_id}}][{{$product->product_id}}][]" class="total-discount" value="">
                                                                        <input type="hidden" class="product-quantity" value="{{ $get_quantity[$product->product_id] }}">
                                                                        <input type="hidden" class="product-id" value="{{ $product->product_id }}">
                                                                        <input type="hidden" class="tax-type" value="{{ $product->tax_type }}"> 
                                                                        <input type="hidden" class="tax-amount" value="{{ $product->tax_amount }}">
                                                                        <input type="hidden" class="get-product-quantity" value="{{ $product->unit }}">
                                                                        <input type="hidden" class="trackable" value="{{ $product['trackable'] }}">
                                                                        <input type="hidden" class="product-unit" value="{{ $product['unit'] }}"> 
                                                                        <input type="hidden" class="type-of-product" value="{{ $product['type_of_product'] }}">
                                                                        <input type="text" name="product_item[{{$product->category_id}}][{{$product->product_id}}][]" value="{{ $get_quantity[$product->product_id] }}" class="quantity" onkeypress="return isNumber(event)">
                                                                        <span class="plus">+</span>
                                                                    </div>
                                                                </td>
                                                                <input type="hidden" name="product_amount[{{$product->category_id}}][{{$product->product_id}}][]" class="total-product-price" value="{{ $get_quantity[$product->product_id] * $product->price }}">
                                                                <input type="hidden" name="product_tax_amount[{{$product->category_id}}][{{$product->product_id}}][]" class="product-tax-amount" value="">
                                                                <input type="hidden" name="no_of_products" class="no-of-products" value="">
                                                                <!-- <td class="product-item-amount">SAR {{ number_format((float)($get_quantity[$product->product_id] * $product->price), 2, '.', '') }}</td>
                                                                <td class="total-product-discount">&nbsp;</td> -->
                                                                <input type="hidden" class="total-product-item-val" value="">
                                                                <td class="total-product-item-amount">&nbsp;</td>
                                                                <td class="text-center hide-after-submit"><i class="fa fa-trash delete-product-item"></i></td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <tr><td colspan="7" class="text-center">{{ __('store-admin.cart_empty') }}</td></tr>
                                                @endif
                                            </tbody>
                                            <tfoot class="hide-after-submit">
                                                <tr>
                                                    <td colspan="10">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <a href="#" class="add-more-items">+ {{ __('store-admin.add_more_items') }}</a>
                                                            <a href="#0" class="remove-all-item">{{ __('store-admin.remove_all') }}</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4 print-hide" style="margin-bottom:10px !important;">
                                <div class="card-body">
                                    <div class="row ">
                                        <div class="col-lg-7 mb-3">
                                            @if(isset($prefer_details) && !empty($prefer_details) && $prefer_details->count() > 0)
                                                <p><b>{{ __('store-admin.prefer_prompt') }}</b></p>
                                                <div class="d-flex align-items-center">
                                                    @foreach($prefer_details as $key => $prefer_detail)
                                                        <div class="form-group mr-4 form-check pl-0">
                                                            <input type="radio" class="form-check-input order-type" name="pickup" value="{{$prefer_detail->order_methods_id}}" id="exampleCheck{{$key}}" {{ $key == 0 ? "checked" : "" }}>
                                                            <label class="form-check-label" for="exampleCheck{{$key}}"><b>{{$prefer_detail->order_methods}}</b></label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <!-- <p><b>{{ __('store-admin.apply_coupon') }}</b></p>
                                            <div class="row">
                                                <div class="col-8">
                                                    <input type="text" placeholder="{{ __('store-admin.enter_coupon') }}" name="coupon_code" value = "" class="form-control coupon-code"></p>
                                                    <span class="invalid-coupon-msg"></span>
                                                </div>
                                                <div class="col-4">
                                                    <button type="button" class="btn btn-primary btn-sm apply-coupon-code">{{ __('store-admin.apply') }}</button>
                                                </div>
                                            </div> -->
                                            <p><b>{{ __('store-admin.payment') }}</b></p>
                                            <div class="d-flex align-items-center">
                                                <div class="form-group mr-3 form-check pl-0">
                                                    <input type="radio" class="form-check-input payment-method-field" id="cash-radio" name="payment_method" value="cash" checked>
                                                    <label class="form-check-label" for="cash-radio"><b>{{ __('store-admin.cash') }}</b></label>
                                                </div>
                                                <div class="form-group mr-3 form-check pl-0">
                                                    <input type="radio" class="form-check-input payment-method-field" id="mada-card-radio" name="payment_method" value="mada_card">
                                                    <label class="form-check-label" for="mada-card-radio"><b>{{ __('store-admin.mada_card') }}</b></label>
                                                </div>
                                                <div class="form-group mr-3 form-check pl-0">
                                                    <input type="radio" class="form-check-input payment-method-field" id="visa-card-radio" name="payment_method" value="visa_card">
                                                    <label class="form-check-label" for="visa-card-radio"><b>{{ __('store-admin.visa_card') }}</b></label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-8 order-cash payment-cash-field">
                                                    <input type="text" placeholder="{{ __('store-admin.received_cash') }}" name="cash_in_hand" value = "" class="form-control cash-in-hand"></p>
                                                </div>
											</div>
                                            <!-- <div class="col-lg-5">
                                                <a href="#" id="money-11">
                                                    <div class="change d-flex justify-content-between align-items-center">
                                                        <p class="mb-0 pr-2"><i class="fa fa-check-circle-o text-success fs-5 mr-2"></i>Will you need change?</p>
                                                        <i class="fa fa-angle-double-right"></i>
                                                    </div>
                                                </a>
                                            </div> -->
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <p class="mb-0">{{ __('store-admin.sub_total') }}</p>
                                                </div>
                                                <div>
                                                    <p class="mb-0 cart-sub-total">SAR 0.00</p>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <p class="mb-0">{{ __('store-admin.tax') }}</p>
                                                </div>
                                                <div>
                                                    <p class="mb-0 cart-total-tax">SAR 0.00</p>
                                                </div>
                                            </div>
                                            <!-- <div class="d-flex justify-content-between">
                                                <div>
                                                    <p class="mb-0">Discount</p>
                                                </div>
                                                <div>
                                                    <input type="hidden" name="cart_total_discount" class="cart_total_discount" value="">
                                                    <p class="mb-0 cart-total-discount">SAR 0.00</p>
                                                </div>
                                            </div> -->
                                            <!-- <div class="d-flex justify-content-between coupon-discount-field dnone">
                                                <div>
                                                    <p class="mb-0">Coupon Discount</p>
                                                </div>
                                                <div>
                                                    <input type="hidden" name="coupon_discount_value" class="coupon-discount-value" value="">
                                                    <p class="mb-0 coupon-total-discount">SAR 0.00</p>
                                                </div>
                                            </div> -->
                                            <hr/>
                                            <div class="d-flex justify-content-between order-cash payment-cash-field">
                                                <div>
                                                    <p class="mb-0">{{ __('store-admin.cash_received') }}</p>
                                                </div>
                                                <div>
                                                    <p class="mb-0 order-cash-amount">SAR 0.00</p>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between order-change payment-cash-field">
                                                <div>
                                                    <p class="mb-0">{{ __('store-admin.balance_returned') }}</p>
                                                </div>
                                                <div>
                                                    <p class="mb-0 order-change-amount balance-amount">SAR 0.00</p>
                                                </div>
                                            </div>
                                            <hr class="payment-cash-field"/>
                                            <div class="order-total-dt mb-4 p-0">
                                                <div class="order-total-left-text fsz-18">
                                                    {{ __('store-admin.grand_total') }}
                                                </div>
                                                <input type="hidden" name="total_cart_amount" class="total_cart_amount" value="">
                                                <input type="hidden" name="total_cart_tax_amount" class="total_cart_tax_amount" value="">
                                                <input type="hidden" name="total_cart_sub_total" class="total_cart_sub_total" value="">
                                                <div class="order-total-right-text fsz-18 total-cart-amount">
                                                    SAR 0.00
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="text-right">
                                                <input type="hidden" class="is-form-submitted" value="0">
                                                <a id="addNewOrder" href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.index') }}"><button type="button" class="btn btn-primary btn-sm rounded ">{{ __('store-admin.add_new_order') }}</button></a>
                                                <a href="#"><button type="button" class="btn btn-success btn-sm rounded save-store-order">{{ __('store-admin.save_print') }}</button></a>
                                                <!-- <a href="#"><button type="button" class="btn btn-warning btn-sm rounded invoice-preview-btn dnone" data-toggle="modal" data-target="#invoicePreview">Preview</button></a> -->
                                                <!-- <a href="#"><button class="btn btn-primary btn-sm rounded print-order-invoice" >Print</button></a> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4 p-1" style="margin-bottom:10px !important;" id="invoice-print1">
                                <div class="">
                                    <div class="row">
                                        <div class="col-md-12 fieldset-4"  >
                                            <div class="card">
                                                <div class="">
                                                    <div id="invoice-POS"> 
                                                        <center >
                                                            <?php 
                                                                $address = '';
                                                                if(isset($address_details) && !empty($address_details)) {
                                                                    if(!empty($address_details[0]->store_address))
                                                                        $address .= $address_details[0]->store_address.',';
                                                                    if(!empty($address_details[0]->city_name))
                                                                        $address .= $address_details[0]->city_name.',';
                                                                    if(!empty($address_details[0]->state_name))
                                                                        $address .= $address_details[0]->state_name.',';
                                                                    if(!empty($address_details[0]->country_name))
                                                                        $address .= $address_details[0]->country_name;
                                                                }
                                                            ?>
                                                        </center>
                                                        <div id="mid">
                                                            <div class="info">
                                                                <div class="row justify-content-between">
                                                                    <div class="col-md-12 text-center">
                                                                        <h6>{{ isset($address_details) && !empty($address_details) && !empty($address_details[0]['store_name']) ? $address_details[0]['store_name'] : 'TajerPOS' }}</h6>
                                                                        <p class="text-center">{{$address}}</p>
                                                                        <p>{{ __('store-admin.cashier') }}: {{ Auth::user()->name }}</p>
                                                                        <p class="order-type-name"></p>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <h6>{{ __('store-admin.tax_invoice') }}</h6>
                                                                        <p>{{ __('store-admin.order_number') }} #: <span class="receipt-order-no"></span></p>
                                                                        <span class="billing-date"></span>
                                                                        <p>{{ __('store-admin.customer_name') }}: <span class="inv-customer-name"></span></p>
                                                                        <p>{{ __('store-admin.phone_number') }}: <span class="inv-customer-phoneno"></span></p>
                                                                    </div>
                                                                    <br/>
                                                                </div>
                                                            </div>
                                                            <hr class="pos-invc">
                                                            <div class="d-flex justify-content-between">
                                                                <div class="col-4 p-0 ">
                                                                    <p class="mb-0"><b>{{ __('store-admin.items') }}</b></p>
                                                                </div>
                                                                <div class="col-2 p-0 text-center">
                                                                    <p class="mb-0 text-center"><b>{{ __('store-admin.quantity') }} </b> </p>
                                                                </div>
                                                                <div class="col-3 p-0 text-center">
                                                                    <p class="mb-0 text-center"><b>{{ __('store-admin.unit_price') }}</b></p>
                                                                </div>
                                                                <div class="col-3 p-0 text-right">
                                                                    <p class="mb-0 text-right"><b>Item Total</b></p>
                                                                </div>
                                                            </div>
                                                            <hr class="pos-invc">
                                                            <div class="billing-items"></div>
                                                            <hr class="pos-invc">
                                                            <div class="d-flex justify-content-between">
                                                                <div><p class="mb-0"><b>{{ __('store-admin.sub_total') }}</b></p></div>
                                                                <div><p class="mb-0"><b class="cart-sub-total"></b></p></div>
                                                            </div>
                                                            <div class="d-flex justify-content-between">
                                                                <div><p class="mb-0"><b>{{ __('store-admin.tax') }}</b></p></div>
                                                                <div><p class="mb-0"><b class="cart-total-tax"></b></p></div>
                                                            </div>
                                                            <hr class="pos-invc">
                                                            <div class="d-flex justify-content-between">
                                                                <div>
                                                                    <h4 style="font-size:17px;color:#000;">{{ __('store-admin.grand_total') }}</h4>
                                                                </div>
                                                                <div>
                                                                    <h4 style="font-size:17px;color:#000;" class="total-cart-amount"></h4>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-between order-cash payment-cash-field">
                                                                <div>
                                                                    <p class="mb-0">{{ __('store-admin.cash_received') }}</p>
                                                                </div>
                                                                <div>
                                                                    <p class="mb-0 order-cash-amount">0.00</p>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-between order-change payment-cash-field">
                                                                <div>
                                                                    <p class="mb-0">{{ __('store-admin.balance_returned') }}</p>
                                                                </div>
                                                                <div>
                                                                    <p class="mb-0 order-change-amount">0.00</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr class="pos-invc">
                                                        <p class="text-center thanks-message">Thank's for your Purchase</p>
                                                        <center>
                                                            <div id="qrcodeCanvas1"></div>
                                                        </center>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal" id="invoicePreview">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Invoice</h4>
                                            <button type="button" class="btn-close" data-dismiss="modal">X</button>
                                        </div>
                                        <div class="modal-body" id="invoice-print">
                                            <div class="row">
                                                <div class="col-md-12 fieldset-4">
                                                    <div class="card">
                                                        <div class="card-body  p-4">
                                                            <div id="invoice-POS"> 
                                                                <center>
                                                                    <?php 
                                                                        $address = '';
                                                                        if(isset($address_details) && !empty($address_details)) {
                                                                            if(!empty($address_details[0]->store_address))
                                                                                $address .= $address_details[0]->store_address.',';
                                                                            if(!empty($address_details[0]->city_name))
                                                                                $address .= $address_details[0]->city_name.',';
                                                                            if(!empty($address_details[0]->state_name))
                                                                                $address .= $address_details[0]->state_name.',';
                                                                            if(!empty($address_details[0]->country_name))
                                                                                $address .= $address_details[0]->country_name;
                                                                        }
                                                                    ?>
                                                                    @if(isset($address_details) && !empty($address_details) && !empty($address_details[0]['store_logo']))
                                                                        <img style="width:100px" src="{{ $address_details[0]['store_logo'] }}" class="logo line" alt="{{ isset($address_details) && !empty($address_details) && !empty($address_details[0]['store_name']) ? $address_details[0]['store_name'] : 'TajerPOS' }}">
                                                                    @endif
                                                                </center>
                                                                <hr/>
                                                                <div id="mid">
                                                                    <div class="info">
                                                                        <div class="row justify-content-between">
                                                                            <div class="col-md-7">
                                                                                <h6 class="line">{{ isset($address_details) && !empty($address_details) && !empty($address_details[0]['store_name']) ? $address_details[0]['store_name'] : 'eMonta' }}</h6>
                                                                                <p class="line">{{$address}}</p>
                                                                                <p class="line">{{ __('store-admin.cashier') }}: {{ Auth::user()->name }}</p>
                                                                                <p class="order-type-name line"></p>
                                                                            </div>
                                                                            <div class="col-md-5">
                                                                                <h6 class="line">{{ __('store-admin.tax_invoice') }}</h6>
                                                                                <p class="line">{{ __('store-admin.order_number') }} #: <p class="receipt-order-no"></p></p>
                                                                                <p class="line billing-date"></p>
                                                                                <p class="line">{{ __('store-admin.customer_name') }}: <p class="inv-customer-name"></p></p>
                                                                                <p class="line">{{ __('store-admin.phone_number') }}: <p class="inv-customer-phoneno"></p></p>
                                                                            </div>
                                                                            <br/>
                                                                        </div>
                                                                    </div>
                                                                    <hr class="pos-invc line">
                                                                    <div class="d-flex justify-content-between line">
                                                                        <div class="col-7 p-0 w-50 ">
                                                                            <p class="mb-0"><b>{{ __('store-admin.items') }}</b></p>
                                                                        </div>
                                                                        <!-- <div class="col-3 p-0">
                                                                            <p class="mb-0">{{ __('store-admin.variants') }}</p>
                                                                        </div> -->
                                                                        <div class="col-1 p-0 text-center">
                                                                            <p class="mb-0 text-center"><b>{{ __('store-admin.quantity') }} </b> </p>
                                                                        </div>
                                                                        <div class="col-2 p-0 text-center">
                                                                            <p class="mb-0 text-center"><b>{{ __('store-admin.unit_price') }}</b></p>
                                                                        </div>
                                                                        <div class="col-2 p-0 text-right">
                                                                            <p class="mb-0 text-right"><b>{{ __('store-admin.total_amount') }}</b></p>
                                                                        </div>
                                                                        <!-- <div class="col-2 p-0 text-center">
                                                                            <p class="mb-0">Quantity x Price</p>
                                                                        </div>
                                                                        <div class="col-2 p-0 text-center">
                                                                            <p class="mb-0">{{ __('store-admin.tax') }}</p>
                                                                        </div>
                                                                        <div class="col-2 p-0 text-right">
                                                                            <p class="mb-0">Total Amount</p>
                                                                        </div> -->
                                                                    </div>
                                                                    <hr class="pos-invc line">
                                                                    <div class="billing-items"></div>
                                                                    <hr class="pos-invc line">
                                                                    <div class="d-flex justify-content-between line order-list">
                                                                        <div><p class="mb-0"><b>{{ __('store-admin.sub_total') }}</b></p></div>
                                                                        <div><p class="mb-0"><b class="cart-sub-total"></b></p></div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between line order-list">
                                                                        <div><p class="mb-0"><b>{{ __('store-admin.tax') }}</b></p></div>
                                                                        <div><p class="mb-0"><b class="cart-total-tax"></b></p></div>
                                                                    </div>
                                                                    <!-- <div class="d-flex justify-content-between line order-list">
                                                                        <div>
                                                                            <p class="mb-0"><b>Discount</b></p>
                                                                        </div>
                                                                        <div>
                                                                            <p class="mb-0 cart-total-discount invoice-cart-total-discount"><b>SAR 0.00</b></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between coupon-discount-field dnone line order-list">
                                                                        <div>
                                                                            <p class="mb-0"><b>Coupon Discount</b></p>
                                                                        </div>
                                                                        <div>
                                                                            <p class="mb-0 cart-total-discount invoice-coupon-total-discount"><b>SAR 0.00</b></p>
                                                                        </div>
                                                                    </div> -->
                                                                    <hr class="pos-invc line">
                                                                    <div class="d-flex justify-content-between line order-list">
                                                                        <div>
                                                                            <h4 style="font-size:17px;color:#000;">{{ __('store-admin.grand_total') }}</h4>
                                                                        </div>
                                                                        <div>
                                                                            <h4 style="font-size:17px;color:#000;" class="total-cart-amount"></h4>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between order-cash payment-cash-field order-list line">
                                                                        <div>
                                                                            <p class="mb-0">{{ __('store-admin.cash_received') }}</p>
                                                                        </div>
                                                                        <div>
                                                                            <p class="mb-0 order-cash-amount">0.00</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex justify-content-between order-change payment-cash-field order-list line">
                                                                        <div>
                                                                            <p class="mb-0">{{ __('store-admin.balance_returned') }}</p>
                                                                        </div>
                                                                        <div>
                                                                            <p class="mb-0 order-change-amount">0.00</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr class="pos-invc">
                                                                <p class="text-center thanks-message line order-list">{{ __('store-admin.thank_you') }}</p>
                                                                <center class="line order-list">
                                                                    <div id="qrcodeCanvas"></div>
                                                                </center>
                                                                <!-- <div class="print_add_order">
                                                                    <hr/>
                                                                    <div class="text-right">
                                                                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.index') }}"><button type="button" class="btn btn-success btn-sm rounded">{{ __('store-admin.add_new_order') }}</button></a>
                                                                        <a href="#" ><button type="button" class="btn btn-primary btn-sm rounded print-order-invoice">Print</button></a>
                                                                    </div>
                                                                </div> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer text-right">
                                            <button type="button" class="btn btn-danger btn-sm rounded" data-dismiss="modal">{{ __('store-admin.close') }}</button>
                                            <a href="#"><button class="btn btn-primary btn-sm rounded print-order-invoice">{{ __('store-admin.print') }}</button></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>	  
        @include('common.cashier_admin.copyright')
        @include('common.cashier_admin.footer')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"></script>
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script type="text/javascript" src="https://jeromeetienne.github.io/jquery-qrcode/src/qrcode.js"></script>
        <script>
            discount_details = [];
            function validateQuantity(_this) {
                quantity = _this.closest(".product-cart-list").find(".quantity").val(); 
                type_of_product = _this.closest(".product-cart-list").find(".type-of-product").val();
                if(type_of_product == "single")
                    product_unit = _this.closest(".product-cart-list").find(".product-unit").val(); 
                else if(type_of_product == "variant")
                    product_unit = _this.closest(".product-cart-list").find("option:selected", ".select-variants").attr('data-quantity');
                trackable = _this.closest(".product-cart-list").find(".trackable").val(); 
                if((type_of_product == "single" && trackable == 1 || type_of_product == "variant") && product_unit != "" && product_unit != undefined && (parseInt(quantity) > parseInt(product_unit))) {
                    _this.closest(".product-cart-list").find(".quantity").val(product_unit); 
                    toastr.options =
                    {
                        "closeButton" : true,
                        "progressBar" : true
                    }
                    toastr.error("Maximum quantity is "+product_unit);
                }
            }
            function calculateDiscount(discount_value,discount_type,product_price) {
                if(discount_type == "percent") {
                    return (product_price * discount_value / 100);
                }
            }
            function calculatePrice(_type = '') {
                total_price = 0; sub_total_amount = 0; total_tax_price = 0; total_discount_amount = 0;
                $("#card-details-table").find(".total-product-price").each(function() {
                    variants = $(this).closest("tr").find(".select-variants").val();
                    discount_type = $(this).closest("tr").find('.discount-dropdown option:selected').data('discount-type');
                    discount_value = discount_amount = $(this).closest("tr").find(".discount-dropdown").val();
                    taxPercentage = !isNaN($(".tax-percentage").val()) ? parseFloat($(".tax-percentage").val()) : 0;
                    if(variants == undefined || variants != "") {
                        price = $(this).val();
                        tax_type = $(this).closest("tr").find(".tax-type").val();
                        tax_amount = $(this).closest("tr").find(".tax-amount").val();
                        quantity = $(this).closest("tr").find(".product-quantity").val();
                        productPrice = parseFloat($(this).closest("tr").find(".product-unit-price").val());
                        product_id = $(this).closest("tr").find(".product-id").val();
                        type_of_product = $(this).closest(".product-cart-list").find(".type-of-product").val();
                        if(tax_type == "flat" && tax_amount != "") {
                            tax_amount = quantity * parseFloat(tax_amount);
                            taxAmount = parseFloat(tax_amount);
                        }
                        else if(tax_type == "percent") {
                            tax_amount = price * (tax_amount / 100);
                            taxAmount = productPrice * (tax_amount / 100);
                        }
                        else if(tax_type == "incl_of_tax" && taxPercentage != "" && taxPercentage > 0 && _type != "calculated") {
                            if (!isNaN(productPrice) && !isNaN(taxPercentage)) {
                                var taxAmount = productPrice - (productPrice * (100 / (100 + taxPercentage)));
                                var unit_price = productPrice - taxAmount;
                                $(this).closest("tr").find(".product-unit-price").val(unit_price.toFixed(2));
                                $(this).closest("tr").find(".product-price").val(unit_price.toFixed(2)); 
                                $(this).closest("tr").find(".single-product-price").text("SAR "+unit_price.toFixed(2));
                                $(this).closest("tr").find(".tax-amount").val(taxAmount != undefined ? taxAmount.toFixed(2) : 0); 
                                tax_amount = taxAmount * quantity;
                                price = unit_price * quantity;
                                $(this).closest("tr").find(".total-product-price").val(price.toFixed(2)); 
                            }
                        } else if(_type == "calculated") {
                            tax_amount = tax_amount * quantity;
                        }
                        if(tax_type == "incl_of_tax" && taxPercentage != "" && taxPercentage > 0) {
                            product_price = (parseFloat($(this).closest("tr").find(".product-unit-price").val()) + parseFloat($(this).closest("tr").find(".tax-amount").val()));
                        } else 
                            product_price = parseFloat($(this).closest("tr").find(".product-unit-price").val());
                        if(discount_type == "percent" && discount_value > 0) 
                            discount_amount = calculateDiscount(discount_value,discount_type,product_price);
                        discount_amount = (discount_amount > 0) ? parseFloat(discount_amount).toFixed(2) : 0;
                        $(this).closest("tr").find(".single-discount-price").val((discount_amount > 0) ? parseFloat(discount_amount).toFixed(2) : 0);
                        total_discount_amount = parseFloat(total_discount_amount)+parseFloat(discount_amount * quantity);
                        $(this).closest("tr").find(".total-product-discount").text(parseFloat(discount_amount * quantity).toFixed(2));
                        $(this).closest("tr").find(".total-discount").val(parseFloat(discount_amount * quantity).toFixed(2));
                        total_tax_amount = ((tax_amount != "" && tax_type != "incl_of_tax") || (tax_type == "incl_of_tax" && taxPercentage > 0)) ? parseFloat(tax_amount) : 0;
                        tax_amount = ((tax_amount != "" && tax_type != "incl_of_tax") || (tax_type == "incl_of_tax" && taxPercentage > 0)) ? parseFloat(tax_amount) : 0;
                        total_amount = parseFloat(price)+parseFloat(total_tax_amount);
                        total_discount = ($(this).closest("tr").find(".total-discount").val() != undefined && $(this).closest("tr").find(".total-discount").val() != "") ? $(this).closest("tr").find(".total-discount").val() : 0;
                        total_product_sum = parseFloat(total_amount) - parseFloat(total_discount);
                        total_product_sum = (total_product_sum > 0) ? total_product_sum : 0;
                        if(_type != "calculated") {
                            $(this).closest("tr").find(".single-product-tax").text(taxAmount != undefined ? taxAmount.toFixed(2) : 0);
                            $(this).closest("tr").find(".product-tax-amount").val(tax_amount.toFixed(2));
                            $(this).closest("tr").find(".product-item-amount").text("SAR "+total_amount.toFixed(2));
                        }
                        $(this).closest("tr").find(".total-product-item-val").val(total_product_sum.toFixed(1));
                        $(this).closest("tr").find(".total-product-item-amount").text("SAR "+total_product_sum.toFixed(1));
                        total_tax_price = (parseFloat(total_tax_price) + parseFloat(total_tax_amount));
                        sub_total_amount = (parseFloat(sub_total_amount) + parseFloat(price));
                        // total_price = parseFloat(total_price)+parseFloat(price)+parseFloat(total_tax_amount); 
                        total_price = parseFloat(total_price)+parseFloat(total_product_sum); 
                    }
                });
                $(".total-cart-amount").text("SAR "+total_price.toFixed(1));
                $(".total_cart_amount").val(total_price.toFixed(1));

                $(".cart-total-discount").text("SAR "+total_discount_amount.toFixed(1));
                $(".invoice-cart-total-discount").css("font-weight","bold");
                $(".cart_total_discount").val(total_discount_amount.toFixed(1));

                $(".cart-sub-total").text("SAR "+sub_total_amount.toFixed(2));
                $(".total_cart_sub_total").val(sub_total_amount.toFixed(2));
                $(".cart-total-tax").text("SAR "+total_tax_price.toFixed(2));
                $(".total_cart_tax_amount").val(total_tax_price.toFixed(2));
                calculateCoupon($(".apply-coupon-code"));
            }
            $(document).ready(function() {
                $(".select-variants").each(function() {
                    get_variant_name = ($(this).find("option:selected").text() != "Select") ? $(this).find("option:selected").text() : "";
                    $(this).closest("td").find(".variant-item-name").val(get_variant_name); 
                });
                product_discount = $(".specific-discount-data").val();
                if(product_discount != "") 
                    product_discount = $.parseJSON(product_discount);
                variant_combinations = $(".variant-combinations").val();
                if(variant_combinations != "") 
                    variant_combinations = $.parseJSON(variant_combinations);
                
                specific_discount_data = $(".specific-discount-data").val();
                if(specific_discount_data != "") 
                    specific_discount_data = $.parseJSON(specific_discount_data);

                all_discount_data = $(".all-discount-data").val();
                if(all_discount_data != "") 
                    all_discount_data = $.parseJSON(all_discount_data);

                get_discount_data = $(".get-discount-data").val();
                if(get_discount_data != "") 
                    get_discount_data = $.parseJSON(get_discount_data);
            });
            $('.minus').click(function () {
                var input_quantity = $(this).closest(".product-item").find(".quantity");
                quantity = parseFloat(input_quantity.val()) - 1;
                input_quantity.val((quantity > 0) ? quantity : 1);
                input_quantity.change();
                return false;
            });
            $('.plus').click(function () {
                var input_quantity = $(this).closest(".product-item").find(".quantity");
                input_quantity.val(parseFloat(input_quantity.val()) + 1);
                input_quantity.change();
                return false;
            });

            $(document).on("change",".select-variants",function() {
                variant_id = $(this).val();
                get_variant_name = ($(this).find("option:selected").text() != "Select") ? $(this).find("option:selected").text() : "";
                produt_id = $(this).closest("tr").find(".product-id").val();
                $(this).closest("td").find(".variant-item-name").val(get_variant_name); 
                taxPercentage = !isNaN($(".tax-percentage").val()) ? parseInt($(".tax-percentage").val()) : 0;
                showDisplay($(this).closest("tr"));
                if(variant_combinations[variant_id]) {
                    variation_combination_data = variant_combinations[variant_id];
                    variant_price = variation_combination_data.variant_price != "" ? variation_combination_data.variant_price : $(this).closest("tr").find(".product-unit-price").val();
                    tax_type = $(this).closest("tr").find(".tax-type").val();
                    quantity = $(this).closest("tr").find(".product-quantity").val();
                    /*if(tax_type != "incl_of_tax") {
                        $(this).closest("tr").find(".single-product-price").text("SAR "+Number(variant_price).toFixed(2));
                        $(this).closest("tr").find(".product-price").val(variant_price); 
                        $(this).closest("tr").find(".product-unit-price").val(variant_price); 
                        total_product_price = quantity * variant_price;
                    }
                    tax_amount = $(this).closest("tr").find(".tax-amount").val();
                    if(tax_type == "flat" && tax_amount != "") {
                        tax_amount = quantity * parseFloat(tax_amount);
                        taxAmount = parseFloat(tax_amount);
                    }
                    else if(tax_type == "percent") {
                        tax_amount = variant_price * quantity * (tax_amount / 100);
                        taxAmount = variant_price * (tax_amount / 100);
                    }*/
                    if(tax_type == "incl_of_tax" && taxPercentage != "" && taxPercentage > 0) {
                        if (!isNaN(variant_price) && !isNaN(taxPercentage)) {
                            var taxAmount = variant_price - (variant_price * (100 / (100 + taxPercentage)));
                            var unit_price = variant_price - taxAmount;
                            $(this).closest("tr").find(".product-unit-price").val(unit_price.toFixed(2));
                            $(this).closest("tr").find(".product-price").val(unit_price.toFixed(2)); 
                            $(this).closest("tr").find(".single-product-price").text("SAR "+unit_price.toFixed(2));
                            tax_amount = parseFloat(taxAmount) * quantity;
                            total_product_price = unit_price * quantity;
                        }
                    }
                    $(this).closest("tr").find(".tax-amount").val(taxAmount.toFixed(2)); 
                    $(this).closest("tr").find(".single-product-tax").text(taxAmount.toFixed(2));
                    $(this).closest("tr").find(".product-tax-amount").val(tax_amount.toFixed(2));
                    $(this).closest("tr").find(".total-product-price").val(total_product_price);
                    total_amount = parseFloat(total_product_price)+parseFloat(tax_amount);
                    $(this).closest("tr").find(".product-item-amount").text("SAR "+ total_amount.toFixed(2));
                    validateQuantity($(this));
                    addToCart($(this));
                }  else {
                    $(this).closest("tr").find(".single-product-tax").text(0.00); 
                    $(this).closest("tr").find(".product-tax-amount").val(0.00); 
                    $(this).closest("tr").find(".product-item-amount").text("SAR "+ "0.00");
                    $(this).closest("tr").find(".single-product-price").text("SAR "+ "0.00");
                    $(this).closest("tr").find(".product-price").val(0.00); 
                    $(this).closest("tr").find(".product-unit-price").val(0.00);
                    $(this).closest("tr").find(".tax-amount").val(0.00); 
                    $(this).closest("tr").find(".total-product-price").val(0.00); 
                    $(this).closest("tr").find(".total-product-discount").text(0.00);
                    $(this).closest("tr").find(".total-product-item-val").val(0.00);
                    $(this).closest("tr").find(".total-product-item-amount").text("SAR "+ "0.00");
                }
                calculatePrice('calculated');
            });
            $('#addNewOrder').hide();
            $(document).on("click",".save-store-order",function() {
                _this = $(this);
                $('#addNewOrder').show();
                is_form_submitted = _this.closest("form").find(".is-form-submitted").val();
                if(is_form_submitted == 0) {
                    error = 0;
                    $(this).closest("form").find(".card-details-tbody").find(".select-variants").css("border","1px solid #86a4c3");
                    $(this).closest("form").find(".card-details-tbody").find(".select-variants").each(function() {
                        if($(this).val() == "") {
                            $(this).css("border","2px solid #F30000");
                            error++;
                        }
                    });
                    if(error > 0)
                        return false;
                    else {
                        coupon_discount_value = _this.closest("form").find(".coupon-discount-value").val();
                        if(coupon_discount_value > 0) {
                            total_cart_amount = _this.closest("form").find(".total_cart_amount").val();
                            cart_amount = parseFloat(total_cart_amount) - parseFloat(coupon_discount_value);
                            total_cart_amount = _this.closest("form").find(".total_cart_amount").val((cart_amount > 0) ? cart_amount : 0);
                        }
                        billing_tr = ''; tax_amount_tr = ''; total_tax_amount = 0; cart_tax_amount = 0; total_product_cart_price = 0;
                        $(this).closest("form").find("#card-details-table").find("tbody tr").each(function() {
                            quantity = $(this).find(".product-quantity").val();
                            product_name = $(this).find(".product-name").val();
                            variants = ($(this).find(".select-variants").val() != "") ? ($(this).find(".select-variants option:selected").text()) : "";
                            product_price =  $(this).find(".product-price").val();
                            total_product_price = $(this).find(".total-product-price").val();
                            total_cart_price = $(this).closest("form").find(".total_cart_amount").val();                    
                            tax_type = $(this).find(".tax-type").val(); 
                            tax_amount = $(this).find(".tax-amount").val();
                            if(tax_type == "flat" && tax_amount != "") {
                                tax_amount = quantity * parseFloat(tax_amount);
                            } else if(tax_type == "percent" && tax_amount != "") {
                                tax_amount = total_product_price * (tax_amount / 100);
                            } else if(tax_type == "incl_of_tax" && tax_amount != "") {
                                tax_amount = quantity * parseFloat(tax_amount);
                            }
                            if(tax_amount != "")  
                                total_tax_amount += parseFloat(tax_amount);
                            tax_amount = (tax_amount != "") ? parseFloat(tax_amount) : 0;
                            total_product_price = (parseFloat(total_product_price) + parseFloat(tax_amount)).toFixed(2); 
                            total_product_cart_price = (parseFloat(total_product_cart_price) + parseFloat(quantity * product_price)).toFixed(2);
                            cart_tax_amount = (parseFloat(cart_tax_amount) + parseFloat(tax_amount)).toFixed(2);
                            // billing_tr += '<div class="d-flex justify-content-between"><div class="col-3 p-0"><p class="mb-0">'+product_name+'</p></div><div class="col-3 p-0"><p class="mb-0">'+variants+'</p></div><div class="col-2 p-0 text-center"><p class="mb-0">'+quantity+' x '+product_price+'</p></div><div class="col-2 p-0 text-center"><p class="mb-0">'+tax_amount.toFixed(2)+'</p></div><div class="col-2 p-0 text-right"><p class="mb-0">'+total_product_price+'</p></div></div></div>';
                            billing_tr += '<div class="d-flex justify-content-between line order-list"><div class="w-50 col-7 p-0"><p class="mb-0">'+product_name+'</p>';
                            if(variants != ""){
                                billing_tr += '<p class="mb-0 mt-0">( '+variants+' )</p>';
                            }
                            billing_tr += '</div><div class="col-1 p-0 text-center"><p class="mb-0 text-center">'+quantity+'</p></div><div class="col-2 p-0 text-center"><p class="mb-0 text-center">'+product_price+'</p></div><div class="col-2 p-0 text-right"><p class="mb-0 text-right">'+total_product_price+'</p></div></div></div>';
                        });
                        total_amount = parseFloat(total_cart_price).toFixed(2);
                        $(this).closest("form").find(".billing-items").html(billing_tr);
                        $(this).closest("form").find(".no-of-products").val($("#card-details-table").find(".card-details-tbody").find(".product-cart-list").length);
                        if($(this).closest("form").find(".balance-amount").text() == "SAR 0.00") 
                            $(this).closest("form").find(".order-cash-amount").text("SAR "+total_amount);
                        else 
                            $(this).closest("form").find(".order-cash-amount").text($(this).closest("form").find(".cash-in-hand").val());  
                        $(this).closest("form").find(".order-change-amount").text($(this).closest("form").find(".balance-amount").text());
                        order_type = $(this).closest("form").find('input[name="pickup"]:checked').val() != undefined ? $(this).closest("form").find('input[name="pickup"]:checked').next('label').text() : "-";
                        $(this).closest("form").find(".order-type-name").text("Order: " +order_type);
                        $(this).closest("form").find(".cart-sub-total").text("SAR "+total_product_cart_price);
                        $(this).closest("form").find(".cart-total-tax").text("SAR "+cart_tax_amount);
                        $(this).closest("form").find(".total_cart_tax_amount").val(cart_tax_amount);
                        $(this).closest("form").find(".total_cart_sub_total").val(total_product_cart_price);
                        customer_name = $(this).closest("form").find(".customer-name").val();
                        customer_name = (customer_name != "" && customer_name != undefined) ? customer_name : "-";
                        customer_phone_no = $(this).closest("form").find(".customer-phone-number").val();
                        customer_phone_no = (customer_phone_no != "" && customer_phone_no != undefined) ? customer_phone_no : "-"
                        $(this).closest("form").find(".inv-customer-name").text(customer_name);
                        $(this).closest("form").find(".inv-customer-phoneno").text(customer_phone_no);
                        $.ajax({
                            data: $('#msform').serialize(),
                            url: "{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.store') }}",
                            type: "POST",
                            dataType: 'json',
                            success: function (response) {
                                _this.text(translations.preview_print); 
                                // _this.closest("form").find(".coupon-code, .cash-in-hand, .quantity").prop("readonly",true);
                                _this.closest("form").find(".apply-coupon-code,.select-variants,.discount-dropdown,.quantity, .coupon-code, .cash-in-hand").prop("disabled",true);
                                _this.closest("form").find(".plus,.minus").css("pointer-events","none");
                                // _this.closest("form").find(".invoice-preview-btn").removeClass("dnone");
                                _this.closest("form").find(".receipt-order-no").text(response.order_number);
                                _this.closest("form").find(".billing-date").text(translations.order_date+' : '+response.ordered_date_time); 
                                (function( $ ){
                                    $.fn.qrcode = function(options) {
                                        // if options is string, 
                                        if( typeof options === 'string' ){
                                            options	= { text: options };
                                        }
                                        // set default values
                                        // typeNumber < 1 for automatic calculation
                                        options	= $.extend( {}, {
                                            render		: "canvas",
                                            width		: 150,
                                            height		: 150,
                                            typeNumber	: -1,
                                            correctLevel	: QRErrorCorrectLevel.H,
                                            background      : "#ffffff",
                                            foreground      : "#000000"
                                        }, options);
                                        var createCanvas	= function(){
                                            // create the qrcode itself
                                            var qrcode	= new QRCode(options.typeNumber, options.correctLevel);
                                            qrcode.addData(options.text);
                                            qrcode.make();

                                            // create canvas element
                                            var canvas	= document.createElement('canvas');
                                            canvas.width	= options.width;
                                            canvas.height	= options.height;
                                            var ctx		= canvas.getContext('2d');

                                            // compute tileW/tileH based on options.width/options.height
                                            var tileW	= options.width  / qrcode.getModuleCount();
                                            var tileH	= options.height / qrcode.getModuleCount();

                                            // draw in the canvas
                                            for( var row = 0; row < qrcode.getModuleCount(); row++ ){
                                                for( var col = 0; col < qrcode.getModuleCount(); col++ ){
                                                    ctx.fillStyle = qrcode.isDark(row, col) ? options.foreground : options.background;
                                                    var w = (Math.ceil((col+1)*tileW) - Math.floor(col*tileW));
                                                    var h = (Math.ceil((row+1)*tileW) - Math.floor(row*tileW));
                                                    ctx.fillRect(Math.round(col*tileW),Math.round(row*tileH), w, h);  
                                                }	
                                            }
                                            // return just built canvas
                                            return canvas;
                                        }

                                        // from Jon-Carlos Rivera (https://github.com/imbcmdth)
                                        var createTable	= function(){
                                            // create the qrcode itself
                                            var qrcode	= new QRCode(options.typeNumber, options.correctLevel);
                                            qrcode.addData(options.text);
                                            qrcode.make();
                                            
                                            // create table element
                                            var $table	= $('<table></table>')
                                                .css("width", options.width+"px")
                                                .css("height", options.height+"px")
                                                .css("border", "0px")
                                                .css("border-collapse", "collapse")
                                                .css('background-color', options.background);
                                        
                                            // compute tileS percentage
                                            var tileW	= options.width / qrcode.getModuleCount();
                                            var tileH	= options.height / qrcode.getModuleCount();

                                            // draw in the table
                                            for(var row = 0; row < qrcode.getModuleCount(); row++ ){
                                                var $row = $('<tr></tr>').css('height', tileH+"px").appendTo($table);
                                                
                                                for(var col = 0; col < qrcode.getModuleCount(); col++ ){
                                                    $('<td></td>')
                                                        .css('width', tileW+"px")
                                                        .css('background-color', qrcode.isDark(row, col) ? options.foreground : options.background)
                                                        .appendTo($row);
                                                }	
                                            }
                                            // return just built canvas
                                            return $table;
                                        }
                                

                                        return this.each(function(){
                                            var element	= options.render == "canvas" ? createCanvas() : createTable();
                                            jQuery(element).appendTo(this);
                                        });
                                    };
                                })( jQuery );
                                jQuery('#qrcodeCanvas').qrcode({
                                    text	: response.qr_url
                                });
                                jQuery('#qrcodeCanvas1').qrcode({
                                    text	: response.qr_url
                                });	
                                toastr.options =
                                {
                                    "closeButton" : true,
                                    "progressBar" : true
                                }
                                toastr.success(response.message);
                                _this.closest("form").find(".is-form-submitted").val(1);
                                _this.closest("form").find(".hide-after-submit").addClass("dnone");
                                window.print();
                                $("#invoicePreview").modal("hide");
                            },
                            error: function (data) {
                                console.log(data);
                            }
                        });
                        $("#invoicePreview").modal("hide");
                        return true;
                    }
                }
                 window.print();
                $("#invoicePreview").modal("hide");
            });
            $(document).ready(function(){
                hideShowCashField();
                $(".card-details-tbody").find("tr").each(function() {
                    showDisplay($(this));
                });
                calculatePrice();
            });
            hideShowButton();
            function hideShowButton() {
                if($("#card-details-table").find(".card-details-tbody").find(".product-cart-list").length > 0) 
                    $(".save-store-order").css("display","inline-flex");
                else
                    $(".save-store-order").css("display","none");
            }
            $(document).on("click",".add-more-items",function() {
                addToCart($(this),'list');
            });

            function addToCart(_this,type = null) {
                variants_combination_array = []; product_ids = []; variant_ids = [];  total_cart_quantity = 0;
                _this.closest("table").find(".card-details-tbody tr").each(function() {
                    variants = $(this).find(".select-variants").val();
                    quantity = $(this).find(".product-quantity").val();
                    product_id = $(this).find(".product-id").val();
                    discount_id = $(this).find('.discount-dropdown option:selected').data('discount-id') != undefined ? $(this).find('.discount-dropdown option:selected').data('discount-id') : 0;
                    total_cart_quantity = parseInt(total_cart_quantity) + parseInt(quantity);
                    variant_array = {}; 
                    if(variants != undefined) {
                        variant_array[variants] = {};
                        variants_details = {};
                        variants_details.variants_id = variants;
                        variants_details.discount_id = discount_id;
                        variants_details.quantity = quantity;
                        variant_array[variants] = variants_details;
                        if((variants_combination_array.length > 0) && (product_id in variants_combination_array)) {
                            variants_combination_array[product_id][variants] = variant_array[variants];
                            variant_ids.push(variants);
                        } else {
                            variants_combination_array[product_id] = variant_array;
                            product_ids.push(product_id);
                            variant_ids.push(variants);
                        }
                    } else {
                        variants_details = {};
                        variants_details.quantity = quantity;
                        variants_details.discount_id = discount_id;
                        variants_combination_array[product_id] = variants_details;
                        product_ids.push(product_id);
                    }
                });
                view_cart_url = _this.closest("form").find(".view-cart-url").val();
                customer_name = _this.closest("form").find(".customer-name").val();
                customer_phone_number = _this.closest("form").find(".customer-phone-number").val();
                $.ajax({
                    data: {_token: CSRF_TOKEN,cart_data: variants_combination_array, product_ids : product_ids, variant_ids : variant_ids, total_cart_quantity : total_cart_quantity, customer_name : customer_name, customer_phone_number : customer_phone_number},
                    url: "{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (response) {
                        if((type == "list") || (type == "clear")) 
                            $(location).attr('href',view_cart_url);                  
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });      
            }
            $(document).on("change keyup",".quantity",function() {
                validateQuantity($(this));
                total_amount = 0;
                $(".quantity").each(function() {
                    variants = $(this).closest("tr").find(".select-variants").val();
                    taxPercentage = !isNaN($(".tax-percentage").val()) ? parseFloat($(".tax-percentage").val()) : 0;
                    if(variants == undefined || variants != "") {
                        quantity = $(this).val();
                        $(this).closest("tr").find(".product-quantity").val(quantity);
                        item_price = $(this).closest(".product-item").find(".product-price").val();
                        quantity_price = quantity * item_price;
                        tax_type = $(this).closest("tr").find(".tax-type").val();
                        tax_amount = $(this).closest("tr").find(".tax-amount").val();
                        if((tax_type == "flat" && tax_amount != "") || (tax_type == "incl_of_tax" && taxPercentage != "" && taxPercentage > 0)) {
                            tax_amount = quantity * parseFloat(tax_amount);
                        }
                        else if(tax_type == "percent") {
                            tax_amount = quantity_price * (tax_amount / 100); 
                        }
                        total_tax_amount = ((tax_amount != "" && tax_type != "incl_of_tax") || (tax_type == "incl_of_tax" && taxPercentage > 0)) ? parseFloat(tax_amount) : 0;
                        tax_amount = ((tax_amount != "" && tax_type != "incl_of_tax") || (tax_type == "incl_of_tax" && taxPercentage > 0)) ? parseFloat(tax_amount) : 0;
                        $(this).closest("tr").find(".product-tax-amount").val(tax_amount.toFixed(2));
                        total_amount = parseFloat(total_amount) + parseFloat(quantity_price)+parseFloat(total_tax_amount);
                        total_price = parseFloat(quantity_price)+parseFloat(total_tax_amount);
                        $(this).closest("tr").find(".product-item-amount").text("SAR "+total_price.toFixed(2));
                        $(this).closest("tr").find(".total-product-price").val(quantity_price.toFixed(2));
                    } else if(variants == "") {
                        $(this).closest("tr").find(".single-product-tax").text("0.00"); 
                        $(this).closest("tr").find(".product-tax-amount").val("0.00"); 
                        $(this).closest("tr").find(".product-item-amount").text("SAR "+ "0.00");
                    }
                });
                calculatePrice('calculated');
                addToCart($(this));
            });

            $(document).on("click",".delete-product-item",function(event) {
                event.stopImmediatePropagation();
                $(this).closest("tr").remove();
                addToCart($(this));
                calculatePrice('calculated');
                hideShowButton();
            });
            $(document).on("click",".remove-all-item",function(event) {
                event.stopImmediatePropagation();
                $(this).closest("table").find("tbody").find("tr").remove();
                $(this).closest("table").find("tbody").html('').html('<tr><td colspan="10" class="text-center">'+translations.cart_empty+'</td></tr>');
                addToCart($(this));
                calculatePrice('calculated');
                hideShowButton();
            });
            $(document).on("keyup",".cash-in-hand",function() {
                total_cart_amount = $(this).closest("form").find(".total_cart_amount").val();
                coupon_discount_value = $(this).closest("form").find(".coupon-discount-value").val();
                if(coupon_discount_value > 0) {
                    total_cart_amount = _this.closest("form").find(".total_cart_amount").val();
                    cart_amount = parseFloat(total_cart_amount) - parseFloat(coupon_discount_value);
                    total_cart_amount = (cart_amount > 0) ? cart_amount : 0;
                }
                cash_in_hand = ($(this).val() != "") ? $(this).val() : 0;
                balance_amount = 0;
                $(this).closest("form").find(".order-cash-amount").text("SAR "+parseFloat(cash_in_hand).toFixed(2));
                if(parseInt(total_cart_amount) <= parseInt(cash_in_hand)) {
                    balance_amount = parseInt(cash_in_hand) - parseInt(total_cart_amount);
                    // $(this).closest("form").find(".balance-button").prop("disabled",false);
                } else {
                    // $(this).closest("form").find(".balance-button").prop("disabled",true);
                }
                $(this).closest("form").find(".balance-amount").text("SAR "+balance_amount.toFixed(2));
            }); 
            $(document).on("keyup",".customer-phone-number",function() {
                _this = $(this);
                customer_phone_number = $(this).val();
                _this.closest("form").find(".customer-id").val("");
                if(customer_phone_number != "") {
                    $.ajax({
                        data: {_token: CSRF_TOKEN,customer_phone_number: customer_phone_number},
                        url: "{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.phone-number-exist') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function (response) {
                            if(response.customer_data != "[]") {
                                if(response.customer_data.length > 0) {
                                    _this.closest("form").find(".customer-name").val(response.customer_data[0].customer_name);
                                    _this.closest("form").find(".customer-id").val(response.customer_data[0].customer_id);
                                }    
                            }       
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });   
                }
            });
            function hideShowCashField() {
                var payment_method = $("input[name='payment_method']:checked").val();
                if(payment_method == "cash") {
                    $(".payment-cash-field").removeClass("dnone");
                } else {
                    $(".cash-in-hand").val("");
                    $(".order-change-amount").text("SAR 0.00");
                    $(".payment-cash-field").addClass("dnone");
                }
            }
            $(document).on("change",".payment-method-field",function() {
                hideShowCashField();
            }); 
            $(document).on("change",".discount-dropdown",function() {
                calculatePrice('calculated');
                addToCart($(this));
            });
            function showDisplay(_this) {
                produt_id = _this.find(".product-id").val();
                type_of_product = _this.find(".type-of-product").val();
                variant_id = (type_of_product == "variant") ? _this.find(".select-variants").val() : 0;
                product_price = _this.find(".product-price").val();
                discount_option = '<select class="form-control discount-dropdown"><option value="">Select Discount</option>';
                isDiscountExist = 0;
                if(all_discount_data.length > 0) {
                    $(all_discount_data).each(function(key,val) {
                        if(parseFloat(val.max_discount_value) > 0) {
                            discount_option += '<option data-discount-id="'+val.discount_id+'" data-discount-type="'+val.discount_type+'" value="'+val.max_discount_value+'">'+(val.discount_type == "flat" ? "FLAT "+val.max_discount_value : val.max_discount_value+" %")+'</option>';
                            isDiscountExist++;
                        }
                    });
                }
                if(specific_discount_data.hasOwnProperty(produt_id)) {
                    if(type_of_product == "variant") {
                        if(specific_discount_data[produt_id].hasOwnProperty(variant_id)) {
                            $(specific_discount_data[produt_id][variant_id]).each(function(key,val) {
                            if(parseFloat(val.discount_value) > 0) {
                                isDiscountExist++;
                                discount_option += '<option data-discount-id="'+val.discount_id+'" data-discount-type="'+val.discount_type+'" value="'+val.discount_value+'">'+(val.discount_type == "flat" ? "FLAT "+val.discount_value : val.discount_value+" %")+'</option>';
                            }
                            });
                        }
                    } else {
                        $(specific_discount_data[produt_id][0]).each(function(key,val) {
                            if(parseFloat(val.discount_value) > 0) {
                                isDiscountExist++;
                                discount_option += '<option data-discount-id="'+val.discount_id+'" data-discount-type="'+val.discount_type+'" value="'+val.discount_value+'">'+(val.discount_type == "flat" ? "FLAT "+val.discount_value : val.discount_value+" %")+'</option>';
                            }
                        });
                    }
                }
                discount_option += '</select>';
                if(isDiscountExist > 0) {
                    _this.find(".single-product-discount").html(discount_option);
                    // _this.find(".single-product-discount .discount-dropdown").find('option').eq(1).prop('selected', true);
                    if(get_discount_data.hasOwnProperty(produt_id)) {
                        discount_id = 0;
                        if(get_discount_data[produt_id].hasOwnProperty(variant_id)) {
                            discount_id = get_discount_data[produt_id][variant_id];
                        } else {
                            discount_id = get_discount_data[produt_id];
                        }
                        if(discount_id > 0)
                            _this.find('.single-product-discount .discount-dropdown option[data-discount-id="' + discount_id + '"]').prop('selected', true);
                    }
                }
            }
            $(document).on("click",".apply-coupon-code",function() {
                _this = $(this);
                _this.closest(".row").find(".invalid-coupon-msg").text("");
                coupon_code = _this.closest(".row").find(".coupon-code").val();
                _this.closest("form").find(".coupon-discount-field").addClass("dnone");
                total_cart_amount = _this.closest("form").find(".total_cart_amount").val();
                if(coupon_code != "") {
                    $.ajax({
                        data: {_token: CSRF_TOKEN,coupon_code: coupon_code},
                        url: "{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.get-coupon-code-details') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function (response) {
                            product_discount_data = {};
                            discount_details = response.store_discount;
                            if(discount_details.length > 0)  {
                                calculateCoupon(_this);
                            }
                            else {
                                _this.closest("form").find(".coupon-discount-value").val(0.00);
                                _this.closest("form").find(".total-cart-amount").text("SAR "+(parseFloat(total_cart_amount).toFixed(1)));
                                _this.closest("form").find(".invalid-coupon-msg").text(translations.invalid_coupon_code).css("color", "#F30000"); 
                            }
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });     
                } else {
                    _this.closest(".row").find(".invalid-coupon-msg").text(translations.enter_coupon).css("color", "#F30000"); 
                }
            });
            function calculateCoupon(_this) {
                _this.closest(".row").find(".invalid-coupon-msg").text("");
                _this.closest("form").find(".coupon-discount-field").addClass("dnone");
                if(discount_details.length > 0) {
                    min_require_type = discount_details[0].min_require_type;
                    min_value = discount_details[0].min_value;
                    max_discount_uses = discount_details[0].max_discount_uses;
                    max_value = discount_details[0].max_value;
                    once_per_order = discount_details[0].once_per_order;
                    coupon_discount_type = discount_details[0].discount_type;
                    coupon_discount_value = discount_details[0].discount_value;                    
                    if(discount_details[0].product_discount_type == "specific") {
                        $(discount_details).each(function(key,val) {
                            discount_data = discount_details[key];
                            if(discount_data.variant_id > 0) {
                                if (!product_discount_data[discount_data.product_id]) {
                                    product_discount_data[discount_data.product_id] = {};
                                }
                                if (!product_discount_data[discount_data.product_id][discount_data.variant_id]) {
                                    product_discount_data[discount_data.product_id][discount_data.variant_id] = {};
                                }
                                product_discount_data[discount_data.product_id][discount_data.variant_id]['discount_type'] = discount_data.discount_type;
                                product_discount_data[discount_data.product_id][discount_data.variant_id]['discount_value'] = discount_data.discount_value;
                            } else {
                                if (!product_discount_data[discount_data.product_id]) {
                                    product_discount_data[discount_data.product_id] = {};
                                }
                                product_discount_data[discount_data.product_id]['discount_type'] = discount_data.discount_type;
                                product_discount_data[discount_data.product_id]['discount_value'] = discount_data.discount_value;
                            }
                        });
                    }
                    coupon_discount = 0; discount_product_price = 0; discount_product_quantity = 0;
                    _this.closest("form").find(".card-details-tbody tr").each(function() {
                        product_id = $(this).find(".product-id").val(); 
                        type_of_product = $(this).find(".type-of-product").val(); 
                        variant_id =$(this).find(".select-variants").val(); 
                        discount_type = '',discount_value = 0;
                        if(discount_details[0].product_discount_type == "specific") {
                            if (product_discount_data.hasOwnProperty(product_id)) {
                                if(type_of_product == "single") {
                                    discount_type = product_discount_data[product_id]['discount_type'];
                                    discount_value = product_discount_data[product_id]['discount_value'];
                                } else if(type_of_product == "variant") {
                                    if(variant_id > 0) {
                                        if (product_discount_data[product_id].hasOwnProperty(variant_id)) {
                                            discount_type = product_discount_data[product_id][variant_id]['discount_type'];
                                            discount_value = product_discount_data[product_id][variant_id]['discount_value'];
                                        }
                                    }
                                }
                            }
                        } else if(discount_details[0].product_discount_type == "all") {
                            discount_type = discount_details[0].discount_type;
                            discount_value = discount_details[0].discount_value;
                        }
                        if(discount_type != "" && discount_value > 0) {                                            
                            total_product_price = $(this).closest("tr").find(".total-product-item-val").val();
                            quantity = $(this).closest("tr").find(".quantity").val();
                            discount_product_price += parseFloat(total_product_price);
                            discount_product_quantity += parseFloat(quantity);
                            if(discount_type == "percent")
                                discount_value = calculateDiscount(discount_value,discount_type,product_price);
                            if(max_discount_uses == "max_discount" && max_value > 0) {
                                discount_value = (discount_value <= max_value) ? discount_value : max_value;
                            }
                            coupon_discount += discount_value;
                        }
                    });
                    min_satisfied = 0;
                    if(min_require_type == "no" || (min_require_type == "amount" && min_value <= discount_product_price) || (min_require_type == "quantity" && min_value <= discount_product_quantity))
                        min_satisfied = 1;
                    if(min_satisfied == 1) {
                        if(once_per_order == 1) {
                            if(coupon_discount_type == "percent") {
                                coupon_discount_value = calculateDiscount(coupon_discount_value,coupon_discount_type,discount_product_price);
                            }
                            if(max_discount_uses == "max_discount" && max_value > 0) {
                                coupon_discount_value = (coupon_discount_value <= max_value) ? coupon_discount_value : max_value;
                            }
                        } else if(once_per_order == 0) {
                            coupon_discount_value = coupon_discount;
                        }
                        if(max_discount_uses )
                        _this.closest("form").find(".coupon-discount-field").removeClass("dnone");
                        _this.closest("form").find(".coupon-discount-value").val(parseFloat(coupon_discount_value).toFixed(2));
                        _this.closest("form").find(".coupon-total-discount").text("SAR "+parseFloat(coupon_discount_value).toFixed(2));
                        _this.closest("form").find(".invoice-coupon-total-discount").html("<b>SAR "+parseFloat(coupon_discount_value).toFixed(2)+"</b>");
                        _this.closest("form").find(".total-cart-amount").text("SAR "+(parseFloat(total_cart_amount) - parseFloat(coupon_discount_value)).toFixed(1));
                    }
                }
            }
        </script>
    </body>
</html>