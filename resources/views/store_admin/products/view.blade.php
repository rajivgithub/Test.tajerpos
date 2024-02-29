<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ trans('store-admin.view_product_title',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header')  
        <style>
            .selected-variants-option {
                background-color: #000000 !important;
                color: #ffffff !important;
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
            <div class="content-wrapper">
                <div class="container-full">
                    <section class="content">
                        <input type="hidden" class="variants-product-details" value="{{ !empty($variantsCombination) ? json_encode($variantsCombination) : '' }}">
                        <input type="hidden" class="variants-images" value="{{ !empty($variantImages) ? json_encode($variantImages) : '' }}">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="row">
                                            @php 
                                                $productImages = []; 
                                                $typeOfProduct = !empty($product_details) && !empty($product_details[0]->type_of_product) ? $product_details[0]->type_of_product : "";
                                            @endphp
                                            @if($typeOfProduct == "variant" && !empty($variantImages)) 
                                                @php $variantKey = key($variantImages);  $productImages = $variantImages[$variantKey]; @endphp
                                            @elseif($typeOfProduct == "single" && !empty($product_image))
                                                @php $productImages = $product_image; @endphp
                                            @endif
                                            <input type="hidden" class="product-type" value="{{ $typeOfProduct }}">
                                            <div class="col-md-4 col-sm-6">
                                                <div class="box box-body b-1 text-center no-shadow">
                                                    @if(!empty($productImages))
                                                        <img src="{{ $productImages[0]['image_path'] }}" id="product-image" class="img-fluid" alt="" />
                                                    @endif
                                                </div>
                                                <div class="pro-photos product-photos">
                                                    @if(!empty($productImages) && count($productImages) > 1)
                                                        @foreach($productImages as $key => $images)
                                                            <div class="photos-item {{ $key == 0 ? 'item-active' : ''}}">
                                                                <img src="{{ $images['image_path'] }}" alt="" >
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="col-md-8 col-sm-6">
                                                <h2 class="box-title mt-0">{{!empty($product_details) && !empty($product_details[0]->product_name) ? $product_details[0]->product_name : '-' }}</h2>
                                                <!-- <div class="list-inline">
                                                    <a class="text-warning"><i class="mdi mdi-star"></i></a>
                                                    <a class="text-warning"><i class="mdi mdi-star"></i></a>
                                                    <a class="text-warning"><i class="mdi mdi-star"></i></a>
                                                    <a class="text-warning"><i class="mdi mdi-star"></i></a>
                                                    <a class="text-warning"><i class="mdi mdi-star"></i></a>
                                                </div> -->
                                                <h1 class="pro-price mb-0 mt-20 product-price">{{ ($typeOfProduct == "single") && (!empty($product_details) && !empty($product_details[0]->price)) ? 'SAR '.number_format($product_details[0]->price,2) : '' }}</h1>
                                                <hr>
                                                <h4 class="box-title mt-20">{{ trans('store-admin.product_details') }}</h4>
                                                @if(!empty($product_details) && !empty($product_details[0]->product_description))
                                                    <p>{!! $product_details[0]->product_description !!}</p>
                                                @endif
                                                @if(!empty($variants))
                                                    @foreach($variants as $variant)
                                                        <div class="row mt-10">
                                                            <div class="col-sm-12">
                                                                <h6>{{ $variant['variants_name'] }}</h6>
                                                                <!-- @if(!empty($variantsOptions) && isset($variantsOptions[$variant['variants_id']]))
                                                                    <p class="mb-0">
                                                                        @foreach($variantsOptions[$variant['variants_id']] as $variantOption)
                                                                            @if(!empty($variantOption['variants_option_image']))
                                                                                <img src="{{ $variantOption['variants_option_image'] }}" style="height: 30px;" class="rounded-circle" title="{{ $variantOption['variant_options_name'] }}" alt="" >
                                                                            @else
                                                                                <span class="badge badge-pill badge-lg badge-default">{{ $variantOption['variant_options_name'] }}</span>
                                                                            @endif
                                                                        @endforeach
                                                                    </p> 
                                                                @endif -->
                                                                @if(!empty($variantsOptions) && isset($variantsOptions[$variant['variants_id']]))
                                                                    <p class="mb-0 variants-group">
                                                                        @foreach($variantsOptions[$variant['variants_id']] as $index => $variantOption)
                                                                            <label class="variant-option-label">
                                                                                <input type="radio" name="variant_{{ $variant['variants_id'] }}" class="variant-radio" value="{{ $variantOption['variant_options_id'] }}" {{ $index == 0 ? 'checked' : ''}}>
                                                                                @if(!empty($variantOption['variants_option_image']))
                                                                                    <img src="{{ $variantOption['variants_option_image'] }}" style="height: 30px;" class="rounded-circle variant-option" title="{{ $variantOption['variant_options_name'] }}" alt="" >
                                                                                @else
                                                                                    <span class="badge badge-pill badge-lg badge-default variant-option {{ ($index == 0) ? 'selected-variants-option' : '' }}">{{ $variantOption['variant_options_name'] }}</span>
                                                                                @endif
                                                                            </label>
                                                                        @endforeach
                                                                    </p> 
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                <h4 class="box-title mt-20">{{ trans('store-admin.general_info') }}</h4>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <tbody> 
                                                            @if(!empty($product_details) && !empty($product_details[0]->category_name))
                                                                <tr>
                                                                    <td width="390">{{ trans('store-admin.category') }}</td> 
                                                                    <td>{{ $product_details[0]->category_name }} </td>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($product_details) && !empty($product_details[0]->sub_category_name))
                                                                <tr>
                                                                    <td>{{ trans('store-admin.sub_category') }}</td>
                                                                    <td> {{ $product_details[0]->sub_category_name }} </td>
                                                                </tr>
                                                            @endif
                                                            <tr>
                                                                <td>{{ trans('store-admin.type_of_product') }}</td> 
                                                                <td>{{ ($typeOfProduct == "variant") ? 'Variant Product' : 'Simple Product' }} </td>
                                                            </tr>
                                                            <tr class="($typeOfProduct == 'single' && $product_details[0]->trackable == 1) ? '' : 'dnone'">
                                                                <td>{{ trans('store-admin.on_hand') }}</td>  
                                                                <td class="product-unit">{{ $product_details[0]->unit }}</td>
                                                            </tr>
                                                            <tr class="dnone">
                                                                <td>{{ trans('store-admin.barcode') }}</td>
                                                                <td><input type="hidden" class="product-barcode" data-type="{{$typeOfProduct}}" data-sku-barcode="{{ $product_details[0]->is_sku_barcode }}" data-barode="{{ $product_details[0]->barcode }}" value="{{ ($typeOfProduct == 'single' && $product_details[0]->is_sku_barcode == 1 && !empty($product_details[0]->barcode)) ? $product_details[0]->barcode : '' }}"><canvas id="barcodeCanvas" class="barcodeCanvas"></canvas></td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ trans('store-admin.status') }}</td>
                                                                <td> {{ ucfirst($product_details[0]->status_type) }} </td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ trans('store-admin.sales_channels') }}</td>
                                                                <td> {{ ucfirst($product_details[0]->product_type) }} </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                        <!-- <h6>Color</h6>
                                                        <div class="input-group">
                                                            <ul class="icolors">
                                                                <li class="bg-danger rounded-circle"></li>
                                                                <li class="bg-info rounded-circle"></li>
                                                                <li class="bg-primary rounded-circle active"></li>
                                                            </ul>
                                                        </div>
                                                        <h6 class="mt-20">Available Size</h6>
                                                        <p class="mb-0">
                                                            <span class="badge badge-pill badge-lg badge-default">Small</span>
                                                            <span class="badge badge-pill badge-lg badge-default">Medium Large</span>
                                                            <span class="badge badge-pill badge-lg badge-default">1-2Y</span>
                                                        </p> -->
                                                <!-- <hr>
                                                <div class="gap-items">
                                                    <button class="btn btn-primary"><i class="mdi mdi-cart-plus"></i> Add To Cart</button>
                                                </div> -->
                                                <!-- <h4 class="box-title mt-20">Key Highlights</h4>
                                                <ul class="list-icons list-unstyled">
                                                    <li><i class="fa fa-check text-danger float-none"></i> Party Wear</li>
                                                    <li><i class="fa fa-check text-danger float-none"></i> Nam libero tempore, cum soluta nobis est</li>
                                                    <li><i class="fa fa-check text-danger float-none"></i> Omnis voluptas as placeat facere possimus omnis voluptas.</li>
                                                </ul> -->
                                            </div>
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
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.3/dist/JsBarcode.all.min.js"></script>
        <script>
            $(document).on("click",".photos-item",function() {
                var src = $(this).children().attr('src');
                $('#product-image').attr('src', src);
                $('.photos-item').removeClass('item-active');
                $(this).addClass('item-active');
            });

            variantsCombinationData = ($(".variants-product-details").val() != "") ? JSON.parse($(".variants-product-details").val()) : [];
            variantsImages = ($(".variants-images").val() != "") ? JSON.parse($(".variants-images").val()) : [];
            function getVariantsCombination(variantsCombination) {
                if (variantsCombinationData.hasOwnProperty(variantsCombination)) {
                    var variantCombinationPrice = variantsCombinationData[variantsCombination].variant_price;
                    var variantOnHand = variantsCombinationData[variantsCombination].on_hand;
                    var variantBarcode = variantsCombinationData[variantsCombination].barcode;
                    if(variantOnHand != "") {
                        $(".product-unit").text(variantOnHand);
                        $(".product-unit").closest("tr").removeClass("dnone");
                    } else 
                        $(".product-unit").closest("tr").addClass("dnone");
                    if(variantBarcode != "")
                        showBarcode(variantBarcode);
                    productPrice = "";
                    if (variantCombinationPrice != "") {
                        productPrice = "SAR " + parseFloat(variantCombinationPrice).toFixed(2);
                    }
                    $(".product-price").text(productPrice);
                    var variantCombinationID = variantsCombinationData[variantsCombination].variants_combination_id;
                    if(variantsImages.hasOwnProperty(variantCombinationID)) {
                        var getVariantsImages = variantsImages[variantCombinationID];
                        updateProductImages(getVariantsImages);
                    }
                } else {
                    console.log("Key does not exist in the object.");
                }
            }

            function updateProductImages(variantsImages) {
                // Clear existing images
                $(".product-photos").empty();

                // Update main product image
                if (variantsImages.length > 0) {
                    $("#product-image").attr("src", variantsImages[0].image_path);
                }

                // Update thumbnail images
                if (variantsImages.length > 1) {
                    for (var i = 0; i < variantsImages.length; i++) {
                        var isActiveClass = i === 0 ? 'item-active' : '';
                        var imageHtml = '<div class="photos-item ' + isActiveClass + '"><img src="' + variantsImages[i].image_path + '" alt=""></div>';
                        $(".product-photos").append(imageHtml);
                    }
                }
            }
            function showBarcode(barcodeValue) {
                var canvas = $(".barcodeCanvas")[0];
                JsBarcode(canvas, barcodeValue, {
                    format: "ean13",
                    displayValue: true
                });
                $(".product-barcode").val(barcodeValue);
                $(".barcodeCanvas").closest("tr").removeClass("dnone");
            }

            $(document).ready(function() {
                productType = $(".product-type").val();
                if(productType == "variant") {
                    selectedVariants = getSelectedVariants();
                    var variantsCombination = Object.values(selectedVariants).join(' / ');
                    getVariantsCombination(variantsCombination);
                }
                if($(".product-barcode").val() != "")
                    showBarcode($(".product-barcode").val());
                else
                    $(".barcodeCanvas").closest("tr").addClass("dnone");
            });
            $(document).on("click",".variant-option-label",function(event) {
                event.preventDefault();
                var selectedValue = $(this).find('.variant-radio').val();
                $(this).closest(".variants-group").find(".variant-radio").prop("checked", false);
                $(this).find(".variant-radio").prop("checked",true);
                $(this).closest(".variants-group").find(".selected-variants-option").removeClass("selected-variants-option");
                $(this).find(".variant-option").addClass("selected-variants-option");
                selectedVariants = getSelectedVariants();
                var variantsCombination = Object.values(selectedVariants).join(' / ');
                getVariantsCombination(variantsCombination);
            });
            function getSelectedVariants() {
                var selectedVariants = [];
                $('.variant-radio:checked').each(function() {
                    var value = $(this).val();
                    selectedVariants.push(value);
                });
                return selectedVariants;
            }

        </script>
    </body>
</html>