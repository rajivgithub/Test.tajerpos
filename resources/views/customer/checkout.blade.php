<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        @include('common.customer.header')
    </head>
   <body>
        <div class="body_overlay"></div>
        @include('common.customer.mobile_navbar')
        @include('common.customer.navbar')
        @include('common.customer.mini_cart')
        @include('common.customer.breadcrumbs')
        <input type="hidden" class="translation-key" value="checkout_page_title">
        <input type="hidden" class="address-list-url" value="{{ route($store_url.'.customer.address.index') }}">
        <form method="POST" action="{{ route($store_url.'.customer.placeorder') }}" novalidate> 
        @csrf
            <input type="hidden" name="address_id" class="default-address-id" value="">
            <input type="hidden" class="global-tax-percentage" value="{{ isset($tax_details) && count($tax_details) > 0 ? $tax_details[0]['tax_percentage'] : 0 }}">
            <input type="hidden" name="discount_amount" class="coupon-discount-value" value="" />
            <input type="hidden" name="discount_id" class="coupon-discount-id" value="" />
            <input type="hidden" name="cart_data" class="variants-combination-array" value="">
            <input type="hidden" name="product_ids" class="product-ids" value="">
            <input type="hidden" name="variant_ids" class="variants-ids" value="">
            <input type="hidden" name="total_cart_quantity" class="total-cart-quantity" value="">
            <div class="checkout-area">
                <div class="container">
                    <div class="cko-progress-tracker">
                        <div class="step-1" id="checkout-progress" data-current-step="1">
                            <div class="progress-bar">
                                <div class="step step-1">
                                    <a href="{{ route($store_url.'.customer.view-cart') }}">
                                        <span> 1</span>
                                        <div class="step-label">{{ __('customer.bag') }}</div>
                                    </a>
                                </div>
                                <div class="step step-2">
                                    <span> 2</span>
                                    <div class="step-label">{{ __('customer.sign_in') }}</div>
                                </div>
                                <div class="step step-3 current">
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
                    <div class="row mt-5">
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card mb-3">
                                        <div class="card-header d-flex align-items-center p-3">
                                            <h6 class="card-title flex-grow-1 mb-0 text-dark fw-bold">{{ __('customer.shipping_information') }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="flex-grow-1">
                                                    <h6 class="fs-md mb-2">{{ __('customer.choose_address') }}</h6>
                                                    <div class="text-center address-error-message"></div>
                                                </div>
                                            </div>
                                            <div class="row gy-3 address-details-container">
                                                
                                            </div>
                                            <div class="dnone clone-address-details">
                                                <div class="col-lg-4 col-sm-6">
                                                    <a href="#address" class="card bg-light bg-opacity-25 border border-light-subtle shadow-none h-100 text-center">
                                                        <div class="card-body d-flex justify-content-center align-items-center">
                                                            <div>
                                                                <div class="fs-4xl mb-2"><i class="fa fa-plus-circle"></i></div>
                                                                <div class="fw-medium mt-n1 text-primary-emphasis stretched-link address-details-info" data-type="add" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">{{ __('customer.add_address') }}</div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card mb-3">
                                        <div class="card-header p-3">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title mb-0 text-dark fw-bold">{{ __('customer.order_summary') }}</h6>
                                                </div>
                                                <!-- <div class="flex-shrink-0">
                                                    <span class="badge bg-success-subtle text-success">Valid Time: 5:00</span>
                                                </div> -->
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-borderless align-middle mb-0 product-checkout-table">
                                                    <thead class="table-active text-muted">
                                                        <tr>
                                                            <th style="width: 90px;" scope="col">{{ __('customer.image') }}</th>
                                                            <th scope="col">{{ __('customer.product_info') }}</th>
                                                            <th scope="col" class="text-end">{{ __('customer.price') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="card-details-tbody">
                                                        @if(isset($product_details) && !empty($product_details))
                                                            @foreach($product_details as $product)
                                                                @php 
                                                                    $productQuantity = ""; 
                                                                    $outOfStockRow = ($product['type_of_product'] == 'variant' && $product['product_available'] == 'out-of-stock') || ($product['type_of_product'] == 'single' && $product['trackable'] == 1 && $product['unit'] <= 0) ? 'out-of-stock-row' : '';
                                                                @endphp
                                                                @if($product['type_of_product'] == "single" && !empty($cartData) && isset($cartData[$product['product_id']]))
                                                                    @php
                                                                        $productQuantity = $cartData[$product['product_id']][0]['quantity'];
                                                                        $cartID = $cartData[$product['product_id']][0]['cart_id'];
                                                                        $productStockQuantity = ($product['unit'] < $productQuantity) ? $product['unit'] : $productQuantity;
                                                                    @endphp
                                                                @elseif($product['type_of_product'] == "variant" && !empty($cartData) && isset($cartData[$product['product_id']]) && isset($cartData[$product['product_id']][$product['variants_id']]))
                                                                    @php
                                                                        $productQuantity = $cartData[$product['product_id']][$product['variants_id']]['quantity'];
                                                                        $cartID = $cartData[$product['product_id']][$product['variants_id']]['cart_id'];
                                                                        $productStockQuantity = (!empty($product['on_hand']) && $product['on_hand'] < $productQuantity) ? $product['on_hand'] : $productQuantity;
                                                                    @endphp
                                                                @endif
                                                                <tr class="cart-item {{ $outOfStockRow }}">
                                                                    <input type="hidden" class="product-quantity quantity" value="{{ $productStockQuantity }}">
                                                                    <input type="hidden" class="select-variants" value="{{ $product['variants_id'] }}">
                                                                    <input type="hidden" class="product-price" value="{{($product['type_of_product'] == 'variant') ? $product['variant_price'] : $product['price'] }}">
                                                                    <input type="hidden" class="tax-total-amount" value="">
                                                                    <input type="hidden" class="order-total-amount" value="">
                                                                    <input type="hidden" class="product-id" value="{{ $product['product_id'] }}">
                                                                    <input type="hidden" class="type-of-product" value="{{ $product['type_of_product'] }}">
                                                                    <input type="hidden" class="total-product-item-val" value="{{$productStockQuantity * (($product['type_of_product'] == 'variant') ? $product['variant_price'] : $product['price']) }}">
                                                                    <input type="hidden" class="variant-combination-name" value="{{ ($product['type_of_product'] == 'variant') ? $product['variants_combination_name'] : '' }}" />
                                                                    <td>
                                                                        @if(!empty($product['image_path']))
                                                                            <div class="avatar-md me-2">
                                                                                <div class="avatar-title rounded-1 ">
                                                                                    <img style="width: 70px;" src="{{ $product['image_path'] }}" alt="Product Image">
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <div class="cart-descrp">
                                                                            <div class="product-name">
                                                                                <a class="fw-bold cart-product-name" href="#">{{ $product['product_name'] }}{{($product['type_of_product'] == "variant") ? " - ".$product['variants_combination_name'] : "" }}</a>  
                                                                                <p class="text-muted fw-medium mb-0">{{number_format((float) (($product['type_of_product'] == 'variant') ? $product['variant_price'] : $product['price']), 2, '.', '') }} x {{ $productStockQuantity }}</p>
                                                                                @if(($product['type_of_product'] == 'variant' && $product['on_hand'] != "" && $product['on_hand'] < $productQuantity) || ($product['type_of_product'] == 'single' && $product['trackable'] == 1 && $product['unit'] < $productQuantity))
                                                                                    @php $available_quantity = ($product['type_of_product'] == 'variant') ? $product['on_hand'] : $product['unit']; @endphp
                                                                                    <div class="mb-15 unavailable-error-message">
                                                                                        @if(($product['type_of_product'] == 'variant' && $product['product_available'] == 'out-of-stock') || ($product['type_of_product'] == 'single' && $product['trackable'] == 1 && $product['unit'] <= 0))
                                                                                            <span class="text-danger">{{ __('customer.product_not_available') }}</span>
                                                                                        @else
                                                                                            <span class="text-danger">{{ __('customer.quantity_adjusted',['on_hand' => $available_quantity]) }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-end fw-semibold">{{number_format((float) $productStockQuantity * (($product['type_of_product'] == 'variant') ? $product['variant_price'] :$product['price']), 2, '.', '')}}</td>
                                                                </tr> 
                                                            @endforeach 
                                                        @else
                                                            <tr><td colspan="3" class="text-center">{{ __('customer.cart_empty_message') }}</td></tr>
                                                        @endif
                                                    </tbody>
                                                    <tfoot>
                                                        @if(isset($product_details) && !empty($product_details))
                                                            <tr class="input-field-div text-center hide-show-field">
                                                                <td colspan="3">
                                                                    <div class="coupon-all w-100 ">
                                                                        <div class="coupon w-100 d-flex mb-4">
                                                                            <input id="coupon-code-input" class="input-text w-75 coupon-code" name="coupon_code" value="" placeholder="{{ __('customer.coupon_code') }}" type="text">
                                                                            <input class="button mt-xxs-30 w-25 apply-coupon-button" name="apply_coupon" value="{{ __('customer.apply') }}"type="button">
                                                                            <!-- <input type="text" id="coupon-code-input" class="form-control coupon-code" name="coupon_code" placeholder="Discount code"> -->
                                                                        </div>
                                                                    </div>
                                                                    <span class="invalid-coupon-msg"></span>
                                                                </td>
                                                                <!-- <td class="text-left align-middle">
                                                                    <button type="button" class="btn btn-default apply-coupon-button">{{ __('customer.apply') }}</button>
                                                                </td> -->
                                                            </tr>
                                                            <tr class="cart-subtotal">
                                                                <td class="fw-semibold">{{ __('customer.sub_total') }} :</td>
                                                                <td class="fw-semibold text-end amount cart-sub-total" colspan="2"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('customer.discount') }} : </td>
                                                                <td class="text-end amount coupon-total-discount" colspan="2">SAR 0.00</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('customer.tax') }} ({{ isset($tax_details) && count($tax_details) > 0 ? preg_replace('/\.?0+%?$/', '%', $tax_details[0]['tax_percentage']) : "" }}): </td>
                                                                <td class="text-end amount cart-tax-total" colspan="2">SAR 0.00</td>
                                                            </tr>
                                                            <tr class="order-total border-top border-dashed">
                                                                <td class="fw-semibold">{{ __('customer.grand_total') }}:</td>
                                                                <td class="text-end amount cart-total-amount" colspan="2"><strong><span class="amount cart-total-amount fw-semibold"></span></strong></td>
                                                            </tr>
                                                        @endif
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="order-button-payment">
                                <input value="{{ __('customer.place_order') }}" class="mt-0 place-the-order hide-show-field"  type="submit">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @include('common.customer.address_popup')
        @include('common.customer.footer')
        @include('common.customer.script')
        <script src="{{ URL::asset('assets/customer/js/address.js') }}"></script>
        <script>
            $(document).ready(function() {
                if($(".unavailable-error-message").length > 0) {
                    var errorMessageElement = $(".unavailable-error-message");
                    errorMessageElement.data("timeoutId", setTimeout(function() {
                        errorMessageElement.fadeOut();
                    }, 5000));
                }
                if ($(".out-of-stock-row").length > 0) {
                    var outOfStockElement = $(".out-of-stock-row");
                    outOfStockElement.data("timeoutId", setTimeout(function() {
                        outOfStockElement.fadeOut();
                        if ($(".product-checkout-table").find("tbody tr:not(.out-of-stock-row)").length <= 0) {
                            var emptyCart = '<tr class="empty-cart"><td colspan="3" class="text-center">Please add another product to the cart!</td></tr>';
                            $(".card-details-tbody").html(emptyCart);
                        }
                    }, 5000));
                }
                sub_total_amount = 0; total_price = 0; product_cart_price = 0; total_tax_price = 0;
                $(".product-checkout-table").find("tbody tr").each(function() {
                    price = $(this).find(".total-product-item-val").val();
                    tax_amount = $(this).closest("body").find(".global-tax-percentage").val();
                    quantity = $(this).find(".product-quantity").val();
                    tax_amount = (tax_amount != "") ? price * (tax_amount / 100) : 0;
                    sub_total = price - tax_amount;
                    sub_total_amount = (parseFloat(sub_total_amount) + parseFloat(sub_total));
                    total_tax_amount = parseFloat(tax_amount);
                    tax_amount = parseFloat(tax_amount);
                    total_amount = parseFloat(price);
                    $(this).find(".tax-total-amount").val(tax_amount.toFixed(2));
                    product_cart_price = (parseFloat(product_cart_price) + parseFloat(price));
                    total_price = total_price+parseFloat(price); 
                    total_tax_price = total_tax_price + parseFloat(total_tax_amount); 
                });
                $(".cart-sub-total").text("SAR "+sub_total_amount.toFixed(2));
                $(".cart-tax-total").text("SAR "+total_tax_price.toFixed(2));
                $(".order-total-amount").val(total_price.toFixed(2));
                $(".cart-total-amount").text("SAR "+total_price.toFixed(2));
                hideShowData();
            });
            function hideShowData() {
                if($(".product-checkout-table").find("tbody tr.cart-item:not(.out-of-stock-row)").length <= 0) {
                    $(".hide-show-field").addClass("dnone");
                }
                if($(".product-checkout-table").find("tbody tr.cart-item").length <= 0) {
                    $(".product-checkout-table").find("tbody").html('<tr><td colspan="3" class="text-center">'+customerTranslations['cart_empty_message']+'</td></tr>');
                }
            }

            $(document).on("click",".place-the-order",function() {  
                check_fields = validateFields($(this));
                $(this).closest("body").find(".address-error-message").text("");
                if(check_fields > 0)
                    return false;
                else {
                    default_address_id = $(this).closest("body").find(".shipping-address:checked").closest(".address-details").find(".address-id").val();
                    if(default_address_id <= 0 || default_address_id == undefined) {
                        $(this).closest("body").find(".address-error-message").text("Please provide the shipping address.").css("color", "#F30000");
                        return false;
                    }
                    $(this).closest("body").find(".default-address-id").val(default_address_id);
                    variants_combination_array = []; product_ids = []; variant_ids = [];  total_cart_quantity = 0;
                    $(".product-checkout-table").find(".card-details-tbody tr").each(function() {
                        variants = $(this).find(".select-variants").val();
                        variant_combination_name = $(this).find(".variant-combination-name").val();
                        quantity = $(this).find(".product-quantity").val();
                        product_id = $(this).find(".product-id").val();
                        total_cart_quantity = parseInt(total_cart_quantity) + parseInt(quantity);
                        tax_amount = $(this).find(".tax-total-amount").val();
                        product_price = $(this).find(".product-price").val();
                        variant_array = {}; 
                        if(variants != undefined) {
                            variant_array[variants] = {};
                            variants_details = {};
                            variants_details.variants_id = variants;
                            variants_details.variant_combination_name = variant_combination_name;
                            variants_details.quantity = quantity;
                            variants_details.tax_amount = tax_amount;
                            variants_details.product_price = product_price;
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
                    $(".variants-combination-array").val(JSON.stringify(variants_combination_array));
                    $(".product-ids").val(product_ids);
                    $(".variants-ids").val(variant_ids);
                    $(".total-cart-quantity").val(total_cart_quantity);
                    return true;
                }
            });
            function calculateDiscount(discount_value,discount_type,product_price) {
                if(discount_type == "percent") {
                    return (product_price * discount_value / 100);
                }
            }
            $(document).on("click",".apply-coupon-button",function() {
                _this = $(this);
                _this.closest(".input-field-div").find(".invalid-coupon-msg").text("");
                coupon_code = _this.closest(".input-field-div").find(".coupon-code").val();
                total_cart_amount = _this.closest("form").find(".order-total-amount").val();
                if(coupon_code != "") {
                    $.ajax({
                        data: {_token: CSRF_TOKEN,coupon_code: coupon_code},
                        url: "{{ route($store_url.'.customer.get-coupon-code-details') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function (response) {
                            product_discount_data = {};
                            discount_details = response.store_discount;
                            if(discount_details.length > 0)  {
                                discount_id = discount_details[0].discount_id;
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
                                        product_price = $(this).find(".product-price").val();
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
                                    _this.closest("form").find(".coupon-discount-id").val(discount_id);
                                    _this.closest("form").find(".coupon-discount-value").val(parseFloat(coupon_discount_value).toFixed(2));
                                    _this.closest("form").find(".coupon-total-discount").text("SAR "+parseFloat(coupon_discount_value).toFixed(2));
                                    _this.closest("form").find(".cart-total-amount").text("SAR "+(parseFloat(total_cart_amount) - parseFloat(coupon_discount_value)).toFixed(1));
                                }
                            }
                            else {
                                _this.closest("form").find(".coupon-discount-id").val("");
                                _this.closest("form").find(".coupon-discount-value").val(0.00);
                                _this.closest("form").find(".order-total-amount").val(total_cart_amount);
                                _this.closest("form").find(".coupon-total-discount").text("0.00");
                                _this.closest("form").find(".cart-total-amount").text("SAR "+(parseFloat(total_cart_amount).toFixed(1)));
                                _this.closest("form").find(".invalid-coupon-msg").text(customerTranslations['invalid_coupon_code']).css("color", "#F30000");
                            }
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });     
                } else {
                    _this.closest(".input-field-div").find(".invalid-coupon-msg").text(customerTranslations['enter_coupon_code']).css("color", "#F30000");
                }
            });
        </script>
   </body>
</html>