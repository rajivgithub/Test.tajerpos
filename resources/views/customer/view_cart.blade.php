<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        @include('common.customer.header')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
        <style>
            .cart-variants {
                cursor : pointer;
            }
            .mb-15 {
                margin-bottom: 15px !important;
            }
            .mt-20 {
                margin-top: 20px !important;
            }
        </style>
    </head>
    <body>
        @php
            function variantCombinationTitle($variants_name) {
                if(!empty($variants_name)) {
                    $getVariantCount = explode("***", $variants_name);
                    if(count($getVariantCount) > 1) {
                        $variantTitle = implode(', ', array_slice($getVariantCount, 0, -1)) . ' and ' . end($getVariantCount);
                    } else {
                        $variantTitle = $variants_name;
                    }
                    return $variantTitle;
                }
            }
            
        @endphp
        
        <div class="body_overlay"></div>
        @include('common.customer.mobile_navbar')
        @include('common.customer.navbar')
        @include('common.customer.mini_cart')
        @include('common.customer.breadcrumbs')
        <input type="hidden" class="page-type" value = "view-cart">
        <input type="hidden" class="translation-key" value="view_cart_page_title">
        <div class="cart-area">
            <div class="container">
                <div class="cko-progress-tracker">
                    <div class="step-1" id="checkout-progress" data-current-step="1">
                        <div class="progress-bar">
                            <div class="step step-1 current">
                                <a href="{{ route($store_url.'.customer.view-cart') }}">
                                <span> 1</span>
                                <div class="step-label">{{ __('customer.bag') }}</div>
                                </a>
                            </div>
                            <div class="step step-2">
                                <span> 2</span>
                                <div class="step-label">{{ __('customer.sign_in') }}</div>
                            </div>
                            <div class="step step-3">
                                <span> 3</span>
                                <div class="step-label">{{ __('customer.delivery_and_payment') }}</div>
                            </div>
                            <div class="step step-4">
                                <span> 4</span>
                                <div class="step-label">{{ __('customer.confirmation') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-5 shopping-bag-list">
                    <input type="hidden" class="global-tax-percentage" value="{{ isset($tax_details) && count($tax_details) > 0 ? $tax_details[0]['tax_percentage'] : 0 }}">
                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="shopping-cart-count item_count shopping-bag-details">{{ __('customer.my_bag') }}</h5>
                            </div>
                            <div class="col-md-4 text-end">
                                @if((auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $store_id))
                                    <a href="{{ route($store_url.'.customer.wishlist.index') }}"><i class='fa fa-plus'></i> {{ __('customer.add_from_wishlist') }}</a>
                                @endif
                            </div>
                        </div>
                        <div class="shopping-cart-body">
                            @if(isset($product_details) && !empty($product_details) && count($product_details) > 0)
                                @foreach ($product_details as $key => $product)
                                    @php $cartID = $productQuantity = ""; @endphp
                                    <hr/>
                                    @if((auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $store_id))
                                        @if($product->type_of_product == "single" && !empty($cartData) && isset($cartData[$product->product_id]))
                                            @php
                                                $productQuantity = $cartData[$product->product_id]['quantity'];
                                                $cartID = $cartData[$product->product_id]['cart_id'];
                                            @endphp
                                        @elseif($product->type_of_product == "variant" && !empty($cartData) && isset($cartData[$product->product_id]) && isset($cartData[$product->product_id][$product->variants_id]))
                                            @php
                                                $productQuantity = $cartData[$product->product_id][$product->variants_id]['quantity'];
                                                $cartID = $cartData[$product->product_id][$product->variants_id]['cart_id'];
                                            @endphp
                                        @endif
                                    @else
                                        @if($product->type_of_product == "single" && !empty($get_quantity) && isset($get_quantity[$product->product_id]))
                                            @php $productQuantity = $get_quantity[$product->product_id]; @endphp
                                        @elseif($product->type_of_product == "variant" && !empty($get_quantity) && isset($get_quantity[$product->product_id]) && isset($get_quantity[$product->product_id][$product->variants_id]))
                                            @php $productQuantity = $get_quantity[$product->product_id][$product->variants_id]; @endphp
                                        @endif
                                    @endif
                                    @php 
                                        $onHand = ($product->type_of_product == "variant") ? $product->on_hand : $product->unit; 
                                        $productPrice = ($product->type_of_product == "variant") ? $product->variant_price : $product->price;  
                                        $productImages = !empty($product->image_path) ? htmlspecialchars(json_encode(explode('***', $product->image_path), JSON_HEX_QUOT | JSON_HEX_TAG), ENT_QUOTES, 'UTF-8') : "";
                                        $variantID = (!empty($product->variants_id)) ? str_replace(' / ','-',$product->variants_id) : "";  
                                        $singleProductURL = url($store_url . '/customer/single-product/category/' . $product->category_id . '/' . strtolower(str_replace(' ', '-', $product->category_name)) . '/product/' . $product->product_id . '/' . strtolower(str_replace(' ', '-', $product->product_name)) .'/product_page');
                                    @endphp
                                    <div class="{{ (($product->type_of_product == 'variant' && $product->product_available == 'out-of-stock') || ($product->type_of_product == 'single' && $product->trackable == 1 && $product->unit <= 0)) ? 'out-of-stock-cart' : '' }} product-cart-list product-cart-list-{{ $product->product_id }}-{{ $variantID }} single_product" data-element="product-cart-list-{{ $product->product_id }}-{{ $variantID }}">
                                        @if(($product->type_of_product == "variant" && $product->product_available == "out-of-stock") || ($product->type_of_product == "single" && $product->trackable == 1 && $product->unit <= 0)) 
                                            <div class="mb-15">
                                                <span class="text-danger"><b><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{ __('customer.out_of_stock') }}</b></span><br/>
                                                <span>{{ __('customer.unavailable_products') }}</span>
                                            </div>
                                        @elseif($product->type_of_product == "variant" && $product->on_hand != "" && ($product->on_hand < $productQuantity) || ($product->type_of_product == "single" && $product->trackable == 1 && $product->unit < $productQuantity))
                                            <div class="mb-15 unavailable-error-message"> 
                                                <span class="text-danger">{{ __('customer.quantity_adjusted',['on_hand' => $onHand]) }}</span>
                                            </div>
                                        @endif
                                        <div class="error-message"></div>
                                        <div class="d-flex gap-3 justify-content-between mb-3">
                                            <span class="dnone product-name">{{ $product->product_name }}</span>
                                            <input type="hidden" class="product-price" value="{{ $productPrice }}">
                                            <input type="hidden" class="product-unit-price" value="{{ $productPrice }}">
                                            <input type="hidden" class="product-quantity" value="{{ $productQuantity }}"> 
                                            <input type="hidden" class="product-id single-product-id" value="{{ $product->product_id }}">
                                            <input type="hidden" class="tax-type" value="{{ $product->tax_type }}"> 
                                            <input type="hidden" class="tax-amount" value="{{ $product->tax_amount }}">  
                                            <input type="hidden" class="trackable single-product-trackable" value="{{ $product->trackable }}">
                                            <input type="hidden" class="product-category-name" value="{{ $product->category_name }}">
                                            <input type="hidden" class="product-subcategory-name" value="{{ $product->sub_category_name }}">
                                            <input type="hidden" class="product-unit" value="{{ $onHand }}">  
                                            <input type="hidden" class="modal-product-unit" value=""> 
                                            <input type="hidden" class="single-product-description" value="{{ $product->product_description }}"> 
                                            <!-- <input type="hidden" class="single-product-url" value="{{ url($store_url . '/customer/single-product/category/' . $product->category_id . '/' . strtolower(str_replace(' ', '-', $product->category_name)) . '/product/' . $product->product_id . '/' . strtolower(str_replace(' ', '-', $product->product_name))) }}">  -->
                                            <input type="hidden" class="type-of-product single-product-type" value="{{ $product->type_of_product }}">
                                            <input type="hidden" name="product_amount[{{$product->category_id}}][{{$product->product_id}}][]" class="total-product-price" value="{{ $productQuantity * $productPrice }}">
                                            <input type="hidden" name="product_tax_amount[{{$product->category_id}}][{{$product->product_id}}][]" class="product-tax-amount" value="">
                                            <input type="hidden" name="no_of_products" class="no-of-products" value="">
                                            <input type="hidden" class="variant-combinations-{{ $product->product_id }}" value=""> 
                                            <input type="hidden" class="product-category-images variant-combinations-images-{{ $product->product_id }}" value=""> 
                                            @if($product->type_of_product == "variant")
                                                <input type="hidden" class="select-variants single-product-variants-combination" value="{{ $product->variants_id }}"> 
                                                <input type="hidden" class="variants-quantity" value="{{ $product->on_hand }}">  
                                                <input type="hidden" class="selected-combination-name" value="{{ $product->variants_combination_name }}">
                                            @endif
                                            <div class="d-flex gap-3">
                                                <div>
                                                    @if(!empty($product->image_path))
                                                        <a href="{{ $singleProductURL }}" class="single-product-url" target="_blank"><img style="width: 70px;" class="single-product-img" src="{{ $product->image_path }}" alt="Product Image"></a>
                                                    @endif
                                                </div> 
                                                <div>
                                                    <p class="mb-2 fw-bold"><a href="{{ $singleProductURL }}" target="_blank">{{ $product->product_name }}</a></p>
                                                    <p class="fw-bolder mb-2 single-product-price">SAR {{ number_format((float)($productPrice), 2, '.', '') }}</p>                                                 
                                                    @if($product->type_of_product == "variant" && !empty($product->variants_name))
                                                        <!-- <p class="mb-0 cart-variants" data-bs-toggle="modal" data-bs-target="#cart-variants-modal"> -->
                                                        <a href="#" data-product-type="{{ $product->type_of_product }}" data-variants-title="{{ variantCombinationTitle($product->variants_name) }}" data-page-type="view-cart" class="mb-0 cart-variants product-quick-view">{{ variantCombinationTitle($product->variants_name) }}  : {{ $product->variants_combination_name }} <i class="fa fa-chevron-down" aria-hidden="true"></i></a>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="product_pro_button quantity1 mb-3">
                                                    @if($product->product_available != "out-of-stock")
                                                        <div class="pro-qty border number product-item">
                                                            @if($product->type_of_product == "variant")
                                                                <input type="text" name="product_item[{{$product->category_id}}][{{$product->product_id}}][]" value="{{ ($product->on_hand != '' && $product->on_hand < $productQuantity) ? $product->on_hand :  $productQuantity }}" class="quantity add-product-quantity" onkeypress="return isNumber(event)" style="height: 35px;">
                                                            @else
                                                                <input type="text" name="product_item[{{$product->category_id}}][{{$product->product_id}}][]" value="{{ ($product->trackable == 1 && $product->unit < $productQuantity) ? $product->unit : $productQuantity }}" class="quantity add-product-quantity" onkeypress="return isNumber(event)" style="height: 35px;">
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                <ul class="d-flex justify-content-center mb-0"> 
                                                    <li class="delete-product-item mr-30" data-cart-id="{{ $cartID }}">
                                                        <a href="#01" title="{{ __('customer.delete') }}" class="text-danger mb-0">
                                                            {!! (($product->type_of_product == "variant" && $product->product_available == "out-of-stock") || ($product->type_of_product == "single" && $product->trackable == 1 && $product->unit <= 0)) ? __('customer.remove_from_bag') : '<i class="fa fa-trash-o"></i> '.__('customer.delete') !!}
                                                        </a>
                                                    </li>
                                                    <li class="wishlist move-to-wishlist" data-type="view-cart"><a href="#01" title="{{ __('customer.move_to_wishlist') }}" class="text-primary mb-0"><i data-wishlist-type="add" class="wishlist-icon fa fa-heart-o"></i> {{ __('customer.move_to_wishlist') }}</a></li> 
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class='empty-row text-center'>{{ __('customer.empty_bag_desc') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-1">
                    </div>
                    <div class="col-md-4">
                        <!-- <h5 class="mb-5">have a promo code?</h5>
                        <div class="coupon-all w-100 ">
                            <div class="coupon w-100 d-flex mb-4">
                                <input id="coupon_code" class="input-text w-75" name="coupon_code" value="" placeholder="Coupon code" type="text">
                                <input class="button mt-xxs-30 w-25" name="apply_coupon" value="Apply"type="submit">
                            </div>
                        </div> -->
                        <h5 class="mb-3">{{ __('customer.order_summary') }}</h5>
                        <div class="cart-page-total">
                            <table class="table table-borderless align-middle mb-0">
                                <tbody>
                                <tr>
                                    <td class="fw-semibold" colspan="2">{{ __('customer.sub_total') }} :</td>
                                    <td class="fw-semibold text-end sub-total-amount"></td>
                                </tr>
                                <!-- <tr>
                                    <td colspan="2">Discount <span class="text-muted">(STEEX30)</span> : </td>
                                    <td class="text-end">- ₹681.89</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Shipping Charge :</td>
                                    <td class="text-end">₹49.99</td>
                                </tr> -->
                                <tr>
                                    <td colspan="2">{{ __('customer.estimated_tax') }} ({{ isset($tax_details) && count($tax_details) > 0 && isset($tax_details[0]['tax_percentage']) && !empty($tax_details[0]['tax_percentage']) ? preg_replace('/\.?0+%?$/', '%', $tax_details[0]['tax_percentage']) : "0" }}): </td>
                                    <td class="text-end total-tax-amount"></td>
                                </tr>
                                <tr class="border-top border-dashed">
                                    <th colspan="2">{{ __('customer.grand_total') }} :</th>
                                    <td class="text-end">
                                        <span class="fw-semibold total-cart-amount"></span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="w-100 proceed-to-checkout-btn">
                                @php 
                                    $route_url = (auth()->guard('customer')->check() && session()->has('authenticate_user') && session('authenticate_user')->store_id == $store_id) ? route($store_url.'.customer.checkout') : url($store_url.'/customer-login/'.Crypt::encrypt("placeorder"));
                                @endphp
                                <a class="w-100 text-center" href="{{ $route_url }}">{{ __('customer.proceed_to_checkout') }}</a>
                            </div>
                        </div>
                        <hr/>
                        <div class="text-center">
                            <img src="{{ URL::asset('assets/customer/images/others/paypal.png') }}" alt="payments">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="delete-confirmation-popup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="min-width: auto;">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h6 class="modal-title delete-product-name" id="exampleModalLabel"></h6>
                            <span class="delete-product-variant-name"></span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="delete-product-row">
                        <div class="d-flex gap-3">
                            <div class="">
                                <img style="width: 150px;height: 100px;" class="delete-product-image" src="" alt="Product Image">
                            </div>
                            <div class="">
                                <b>{{ __('customer.delete_from_bag') }}</b>
                                <p>{{ __('customer.delete_confirmation_description') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-0" style="flex-wrap: inherit;">
                        <button type="button" data-type="confirmation-popup" class="btn btn-warning w-50 move-to-wishlist" >{{ __('customer.move_to_wishlist') }}</button>
                        <button type="button" class="btn btn-danger w-50 delete-product">{{ __('customer.delete') }}</button>
                    </div>
                </div>
            </div>
        </div>
        @include('common.customer.footer')
        @include('common.customer.view_popup')
        @include('common.customer.script')
        <script>
            function validateQuantity(_this) {
                quantity = _this.closest(".product-cart-list").find(".quantity").val(); 
                type_of_product = _this.closest(".product-cart-list").find(".type-of-product").val();
                if(type_of_product == "single")
                    product_unit = _this.closest(".product-cart-list").find(".product-unit").val(); 
                else if(type_of_product == "variant")
                    product_unit = _this.closest(".product-cart-list").find(".variants-quantity").val();
                trackable = _this.closest(".product-cart-list").find(".trackable").val(); 
                if((type_of_product == "single" && trackable == 1 || type_of_product == "variant") && product_unit != "" && product_unit != undefined && (parseInt(quantity) > parseInt(product_unit))) {
                    _this.closest(".product-cart-list").find(".quantity").val(product_unit); 
                    error_message = customerTranslations['quantity_exceeds_stock'].replace(":unit",product_unit);
                    var errorMessageElement = _this.closest(".product-cart-list").find(".error-message");
                    errorMessageElement.text(error_message).css("color", "#F30000");
                    if (errorMessageElement.data("timeoutId")) {
                        clearTimeout(errorMessageElement.data("timeoutId"));
                    }
                    errorMessageElement.show();
                    errorMessageElement.data("timeoutId", setTimeout(function() {
                        errorMessageElement.fadeOut();
                    }, 5000));
                    // toastr.options =
                    // {
                    //     "closeButton" : true,
                    //     "progressBar" : true
                    // } 
                    // toastr.error(customerTranslations['quantity_exceeds_stock'].replace(":unit",product_unit));
                }
            }
            function addProductToCart(_this,product_variants_combination = '',quantity = '',_type = '',remove_variant_id) { 
                variants_combination_array = []; product_ids = []; variant_ids = [];  total_cart_quantity = 0; var totalQuantity = 0;
                var isAuthenticated = {{ auth()->guard('customer')->check() ? 'true' : 'false' }};
                if(isAuthenticated) {
                    _type = (_type == 'delete') ? "delete" : "add_to_cart_page";
                    product_id = _this.closest(".product-cart-list").find(".product-id").val();
                    product_variants_combination = _this.closest(".product-cart-list").find(".select-variants").val();
                    quantity = parseInt(_this.closest(".product-cart-list").find(".quantity").val());
                    cartData = {_token: CSRF_TOKEN, product_id : product_id, product_variants_combination : product_variants_combination, quantity : quantity,type : _type,isAuthenticated : isAuthenticated, remove_variant_id : remove_variant_id};
                } else {
                    _this.closest("body").find(".shopping-bag-list").find(".shopping-cart-body .product-cart-list").each(function() {
                        variants = $(this).find(".select-variants").val();
                        if (variants == product_variants_combination) {
                            totalQuantity = parseInt(totalQuantity) + parseInt($(this).find(".quantity").val());
                            quantity = totalQuantity;
                        }
                        else {
                            quantity = parseInt($(this).find(".quantity").val());
                        }
                        product_id = $(this).find(".product-id").val();
                        total_cart_quantity = parseInt(total_cart_quantity) + quantity;
                        variant_array = {}; 
                        if(variants != undefined) {
                            variant_array[variants] = {};
                            variants_details = {};
                            variants_details.variants_combination_id = variants;
                            variants_details.quantity = quantity;
                            variants_details.product_id = product_id;
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
                            variants_combination_array[product_id] = variants_details;
                            product_ids.push(product_id);
                        }
                    });
                    cartData = {_token: CSRF_TOKEN,cart_data: variants_combination_array, product_id : product_ids, variant_ids : variant_ids, total_cart_quantity : total_cart_quantity,type : "add_to_cart_page",isAuthenticated : isAuthenticated};
                }
                add_to_cart_url = $(".add-to-cart-url").val();
                $.ajax({
                    url: add_to_cart_url,
                    type: "POST",
                    data: cartData,
                    dataType: 'json',
                    success: function (response) {
                        /*toastr.options =
                        {
                            "closeButton" : true,
                            "progressBar" : true
                        }
                        toastr.success("Success! Your changes have been updated.");*/
                        console.log(response);                
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });      
            }

            $(document).ready(function() {
                calculateAmount();
                hideShowBtn();
                if($(".unavailable-error-message").length > 0) {
                    var errorMessageElement = $(".unavailable-error-message");
                    errorMessageElement.data("timeoutId", setTimeout(function() {
                        errorMessageElement.fadeOut();
                    }, 5000));
                }
                
            });
            function calculateAmount() {
                total_price = 0; product_cart_price = 0; total_tax_price = 0; sub_total_amount = 0;
                $(".product-cart-list").filter(function() {
                    return !($(this).closest(".quick-view-modal").length || $(this).closest(".out-of-stock-cart").length);
                }).each(function() {
                    variants = $(this).find(".select-variants").val();
                    if(variants == undefined || variants != "") {
                        price = $(this).find(".total-product-price").val();
                        tax_amount = $(this).closest("body").find(".global-tax-percentage").val();
                        quantity = $(this).find(".product-quantity").val();
                        tax_amount = (tax_amount != "") ? price * (tax_amount / 100) : 0;
                        sub_total = price - tax_amount;
                        sub_total_amount = (parseFloat(sub_total_amount) + parseFloat(sub_total));
                        total_tax_amount = parseFloat(tax_amount);
                        tax_amount = parseFloat(tax_amount);
                        total_amount = parseFloat(price);
                        $(this).find(".product-tax-amount").val(tax_amount.toFixed(2));
                        product_cart_price = (parseFloat(product_cart_price) + parseFloat(price));
                        total_price = total_price+parseFloat(price); 
                        total_tax_price = total_tax_price + parseFloat(total_tax_amount);
                    }
                });
                $(".total-cart-amount").text("SAR "+total_price.toFixed(2));
                $(".total-tax-amount").text("SAR "+total_tax_price.toFixed(2));
                $(".sub-total-amount").text("SAR "+sub_total_amount.toFixed(2));
            }
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
            $(document).on("change keyup",".quantity:not(.modal_add_to_cart .quantity)",function(event) {
                event.preventDefault();
                event.stopImmediatePropagation();
                validateQuantity($(this));
                _type = $(this).data("type");
                $(this).closest(".product-cart-list").find(".unavailable-error-message").html("");
                if(_type == "product-in-popup") {
                    product_id = $(this).closest(".single-product-details").find(".product-id").val();
                    variant_combinations_data = $(this).closest("body").find(".variant-combinations-"+product_id).val();
                    cartUpdateQuantity($(this),variant_combinations_data);
                } else {
                    variant_id = $(this).closest(".product-cart-list").find(".select-variants").val();
                    total_quantity = $(this).val();
                    $(this).closest(".shopping-cart-body").find(".quantity").each(function() {
                        variants = $(this).closest(".product-cart-list").find(".select-variants").val();
                        if(variants == undefined || variants != "") {
                            quantity = $(this).val();
                            $(this).closest(".product-cart-list").find(".product-quantity").val(quantity);
                            item_price = $(this).closest(".product-cart-list").find(".product-price").val();
                            total_price = parseFloat(quantity * item_price);
                            $(this).closest(".product-cart-list").find(".single-product-price").text("SAR "+parseFloat(item_price).toFixed(2));
                            $(this).closest(".product-cart-list").find(".total-product-price").val(total_price);
                        }
                    });
                    calculateAmount();
                    var totalQuantity = 0;
                    $(this).closest(".shopping-cart-body").find('.product-cart-list').each(function() {
                        var variant = $(this).find('.select-variants').val();
                        var quantity = parseInt($(this).find('.quantity').val());
                        if (variant == variant_id) 
                            totalQuantity += quantity;
                    });
                    if(totalQuantity > 0)
                        total_quantity = totalQuantity;
                    addProductToCart($(this),variant_id,total_quantity);
                    cart_product_count();
                }
            });
            $(document).on("click",".delete-product-item",function(event) {
                event.preventDefault();
                confirmationModal = $(this).closest("body").find("#delete-confirmation-popup");
                productName = $(this).closest(".product-cart-list").find(".product-name").text();
                productImage = $(this).closest(".product-cart-list").find(".single-product-img").attr('src');
                productType = $(this).closest(".product-cart-list").find(".type-of-product").val();
                deleteElement = $(this).closest(".product-cart-list").data("element");
                confirmationModal.find(".delete-product-name").text(productName);
                confirmationModal.find(".delete-product-row").val(deleteElement);
                confirmationModal.find(".delete-product-variant-name").text("");
                confirmationModal.find(".delete-product-image").attr("src",productImage);
                if(productType == 'variant') {
                    productVariantName = $(this).closest(".product-cart-list").find(".product-quick-view").data('variants-title');
                    productVariantOption = $(this).closest(".product-cart-list").find(".selected-combination-name").val();
                    $(this).closest("body").find(".delete-product-variant-name").text(productVariantName+" : "+productVariantOption);
                }
                confirmationModal.modal("show");
            });
            $(document).on("click",".delete-product",function(event) {
                event.preventDefault();
                deleteElement = $(this).closest("#delete-confirmation-popup").find(".delete-product-row").val();
                cartID = $("."+deleteElement).find(".delete-product-item").data("cart-id");
                $("."+deleteElement).prev().remove();
                $("."+deleteElement).remove(); 
                calculateAmount();
                addProductToCart($(".delete-product-item"),'','','delete',cartID);
                cart_product_count();
                cart_list = $(".shopping-bag-list").find(".product-cart-list").length; 
                if(cart_list <= 0) {
                    $(".shopping-bag-list").find(".shopping-cart-body").html("<p class='empty-row text-center'>"+customerTranslations['empty_bag_desc']+"</p>");
                }
                hideShowBtn();
                $(this).closest("#delete-confirmation-popup").modal("hide");
            });
            function hideShowBtn() {
                cart_list = $(".shopping-bag-list").find(".product-cart-list").filter(function() {
                    return !($(this).closest(".quick-view-modal").length || $(this).closest(".out-of-stock-cart").length)
                }).length;
                if(cart_list <= 0)
                    $(".proceed-to-checkout-btn").addClass("dnone");
                else
                    $(".proceed-to-checkout-btn").removeClass("dnone");
            }
            $(document).on("click",".move-to-wishlist",function(event) {
                event.preventDefault();
                _type = $(this).data("type");
                if(_type == 'confirmation-popup') {
                    deleteElementID = $(this).closest("#delete-confirmation-popup").find(".delete-product-row").val();
                    selector = $("."+deleteElementID);
                }
                else 
                    selector = $(this).closest(".product-cart-list");
                product_id = selector.find(".product-id").val();
                addWishlist(product_id,"add",'','wishlist');
                selector.find('.delete-product-item').click(); 
            }); 
            function selectedVariantsName(_this) {
                var selectedVariantsName = [];
                _this.closest(".single-product-details").find('.selected-variant-id').each(function() {
                    selector = $(this).closest(".variants-group");
                    img_exist = $(this).closest(".variants-group").find("li:first img").length;
                    selectedVariantID = $(this).val();
                    variantName = "";
                    if(selector.find("li:first img").length > 0)
                        variantName = selector.find("#variant-combination-"+selectedVariantID).next().attr("title");
                    else if(selector.find("li:first select").length > 0) 
                        variantName = selector.find(".variant-combination option[value='" + selectedVariantID + "']").text();
                    else if(selector.find("li:first input[type='radio']").length > 0)
                        variantName = selector.find("#variant-combination-"+selectedVariantID).next().text();
                    if(variantName != "")
                        selectedVariantsName.push(variantName);
                });
                variantsCombinationName = Object.values(selectedVariantsName).join(' / ');
                return variantsCombinationName;
            }
            $(document).on("click",".cart-update",function(event) {
                event.preventDefault();
                event.stopImmediatePropagation();
                _this = $(this);
                validateQuantity($(this).closest(".product-cart-list").find(".quantity"));
                variant_id = $(this).closest(".product-cart-list").find(".single-product-variants-combination").val();
                variantID = variant_id.replace(' / ','-');
                product_id = $(this).closest(".product-cart-list").find(".product-id").val();
                total_quantity = parseFloat($(this).closest(".product-cart-list").find(".quantity").val());
                product_price = parseFloat($(this).closest(".product-cart-list").find(".product-price").val());
                selected_element_class = $(this).closest(".product-cart-list").find(".selected-element-class").val();
                variant_combination = selectedVariantsName($(this));
                remove_variant_id = "";
                product_unit = $(this).closest(".product-cart-list").find(".variants-quantity").val();
                if(($(this).closest("body").find(".product-cart-list-"+product_id+"-"+variantID).length > 0) && (selected_element_class != "product-cart-list-"+product_id+"-"+variantID)) {
                    remove_variant_id = $("."+selected_element_class).find(".select-variants").val();
                    _element = $(".product-cart-list-"+product_id+"-"+variantID);
                    _element.find(".select-variants").val(variant_id);
                    _element.find(".selected-combination-name").val(variant_combination);
                    _element.find(".single-product-price").text("SAR "+(product_price).toFixed(2));
                    _element.find(".variants-quantity").val(product_unit);
                    if(product_unit != "" && product_unit != undefined && (parseInt(total_quantity) > parseInt(product_unit))) {
                        _element.find(".quantity").val(product_unit); 
                        _element.find(".product-quantity").val(product_unit);
                        _element.find(".total-product-price").val(parseFloat(product_unit) * parseFloat(product_price));
                        toastr.options =
                        {
                            "closeButton" : true,
                            "progressBar" : true
                        }
                        toastr.error(customerTranslations['quantity_exceeds_stock'].replace(":unit",product_unit)); 
                    } else {
                        _element.find(".quantity").val(total_quantity); 
                        _element.find(".product-quantity").val(total_quantity); 
                        _element.find(".total-product-price").val(parseFloat(total_quantity) * parseFloat(product_price));
                        _element.find(".variants-quantity").val(product_unit);
                    }
                    $("."+selected_element_class).prev().remove();
                    $("."+selected_element_class).remove();
                } else {
                    _element = $("."+selected_element_class); 
                    variantImage = _this.closest(".product-modal-popup").find(".product-images-modal-tab .modal_tab_img:first img").attr("src");
                    _element.find(".single-product-img").attr("src",variantImage);
                    _element.find(".select-variants").val(variant_id);
                    _element.find(".selected-combination-name").val(variant_combination);
                    _element.find(".single-product-price").text("SAR "+(product_price).toFixed(2));
                    _element.find(".quantity").val(total_quantity);
                    _element.find(".product-quantity").val(total_quantity);
                    _element.find(".total-product-price").val(parseFloat(total_quantity) * parseFloat(product_price));
                    variants_title = _element.find(".cart-variants").data("variants-title");
                    _element.find(".cart-variants").html(variants_title +" : "+variant_combination+'<i class="fa fa-chevron-down" aria-hidden="true"></i>');
                    _element.data("element","product-cart-list-"+product_id+"-"+variantID);
                    _element.addClass("product-cart-list-"+product_id+"-"+variantID).removeClass(selected_element_class);
                    _element.find(".variants-quantity").val(product_unit);
                }
                calculateAmount();
                addProductToCart($(this),variant_id,total_quantity,'update-cart',remove_variant_id);
                cart_product_count();
                $('#modal_box').modal('hide');
            });
        </script>
    </body>
</html>