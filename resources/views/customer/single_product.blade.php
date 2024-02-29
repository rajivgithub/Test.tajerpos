<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        @include('common.customer.header')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
        <style>
            .slick-slide {
                width : 575px;
            }
            .slick-prev-1, .slick-next-1,.slick-prev-1:hover, .slick-prev-1:focus, .slick-next-1:hover, .slick-next-1:focus {
                border: none;
                background: none; 
                font-size: 24px; 
                color: #000000; 
                padding: 0; 
                margin: 0; 
                top: 2px;
                position:absolute;
            }
            .slick-next-1 {
                right: -14px;
            }
            .slick-prev-1 {
                left:-14px;
            }
            .single-product-img {
                width : 400px;
            }
            .truncate-text {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
                display: inline-block;
            }
            .product-add-to-cart:disabled {
                background-color: #000000;
                border-color: #000000 !important;
                color: #ffffff;
                opacity: 1;
            }
        </style>
    </head>
    <body>
        <div class="body_overlay"></div>
        @include('common.customer.mobile_navbar')
        @include('common.customer.navbar')
        @include('common.customer.mini_cart')
        @include('common.customer.breadcrumbs')
        <input type="hidden" class="page-type" value = "single-product">
        <input type="hidden" class="translation-key" value="single_product_page_title">
        <input type="hidden" class="products_by_category_url" value="{{ route($store_url.'.customer.products-by-category') }}">
        <div class="single_product_section mb-80">
            <div class="container">
                @if(isset($product_details) && !empty($product_details))
                    @if($product_details['type_of_product'] == "variant" && !empty($product_variants_combinations))
                        @php
                            $variants_id = 0;
                            if (!empty($variant_option_id)) {
                                $keys = array_keys(
                                    array_filter(
                                        $product_variants_combinations,
                                        function ($key) use ($variant_option_id) {
                                            return strpos($key, $variant_option_id) !== false;
                                        },
                                        ARRAY_FILTER_USE_KEY
                                    )
                                );
                                if(!empty($keys) && isset($keys[0]))
                                    $variants_id = $keys[0];
                            } else {
                                $variants_id = key($product_variants_combinations);
                            }
                        @endphp
                    @else
                        @php $variants_id = 0; @endphp
                    @endif
                    @php
                        $category_img_path = !empty($product_details) && !empty($product_details['category_image']) ? explode("***",$product_details['category_image']) : [];
                        $product_unit = $available_quantity = ($product_details['type_of_product'] == "variant" && !empty($product_variants_combinations)) ? $product_variants_combinations[$variants_id]['on_hand'] : $product_details['unit'];
                        $product_price = ($product_details['type_of_product'] == "variant" && !empty($product_variants_combinations)) ? $product_variants_combinations[$variants_id]['variant_price'] : $product_details['price'];
                        $getProductImages = [];
                    @endphp
                    @if(!empty($cart_data) && isset($cart_data[$product_details['product_id']])) 
                        @if($product_details['type_of_product'] == "variant" && isset($cart_data[$product_details['product_id']][$variants_id])) 
                            @php 
                                $quantity = $cart_data[$product_details['product_id']][$variants_id]['quantity']; 
                            @endphp
                            @if(!empty($product_unit) && is_numeric($product_unit) && $product_unit >= 0) 
                                @php $variants_on_hand = $available_quantity = ($product_unit - $quantity); @endphp
                            @endif
                        @elseif($product_details['type_of_product'] == "single") 
                            @php 
                                $quantity = $cart_data[$product_details['product_id']]['quantity']; 
                                $product_unit = $available_quantity = $product_details['unit'] - $quantity; 
                            @endphp
                        @endif
                    @endif
                    @if($product_details['type_of_product'] == "variant" && $variants_id > 0 && !empty($variantImages) && isset($variantImages[$variants_id])) 
                        @php $getProductImages = $variantImages[$variants_id]; @endphp
                    @elseif($product_details['type_of_product'] == "single" && !empty($product_details['image_path']))
                        @php $getProductImages = explode("***",$product_details['image_path']); @endphp
                    @endif
                    <div class="row single_product"  style="border:none;">
                        <div class="col-lg-6 col-md-6">
                            <div class="single_product_gallery">
                                @if(!empty($getProductImages))
                                    <div class="product_gallery_inner d-flex product-images">
                                        @if(count($getProductImages) > 1)
                                            <div class="product_gallery_btn_img">
                                                @foreach($getProductImages as $images)
                                                    @php $imagePath = ($product_details['type_of_product'] == "single") ? $images :  $images['image_path']; @endphp
                                                    <a class="gallery_btn_img_list" href="javascript:void(0)"><img src="{{ $imagePath }}" alt="tab-thumb"></a>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="product_gallery_main_img">
                                            @foreach($getProductImages as $images)
                                                @php $imagePath = ($product_details['type_of_product'] == "single") ? $images :  $images['image_path']; @endphp
                                                <div class="gallery_img_list">
                                                    <img data-image="{{ $imagePath }}" src="{{ $imagePath }}" alt="">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="product_details_sidebar single-product-details single_product_details">
                                <input type="hidden" class="single-product-name" value="{{ !empty($product_details) && !empty($product_details['product_name']) ? $product_details['product_name'] : '' }}">
                                <h2 class="product__title">{{ !empty($product_details) && !empty($product_details['product_name']) ? $product_details['product_name'] : '' }}</h2>
                                @if(!empty($product_details) && !empty($product_details['category_name']))
                                    <p class="category_title">{{ $product_details['category_name'] }}</p>
                                @endif
                                @if(!empty($product_details) && !empty($product_details['sub_category_name']))
                                    <p class="sub_category_title">{{ $product_details['sub_category_name'] }}</p>
                                @endif
                                <div class="price_box">
                                    <span class="current_price product-price modal-product-price">
                                        {{ ($product_price > 0) ? "SAR ".number_format($product_price, 2, '.', '') : "" }}
                                    </span>
                                </div>
                                <input type="hidden" class="product-id single-product-id single-page-product-id" value="{{ $product_details['product_id'] }}">
                                <input type="hidden" class="single-category-id" value="{{ $product_details['category_id'] }}">
                                <input type="hidden" class="single-sub-category-id" value="{{ $product_details['sub_category_id'] }}">
                                <input type="hidden" class="single-product-trackable" value="{{ $product_details['trackable'] }}">
                                <input type="hidden" class="variant-combinations variant-combinations-{{ $product_details['product_id'] }}" value="{{ !empty($product_variants_combinations) ? json_encode($product_variants_combinations) : '' }}"> 
                                <input type="hidden" class="variant-combinations-images variant-combinations-images-{{ $product_details['product_id'] }}" value="{{ (!empty($variantImages) && $product_details['type_of_product'] == 'variant') ? json_encode($variantImages) : '' }}"> 
                                <input type="hidden" class="single-product-type" value="{{ $product_details['type_of_product'] }}">
                                <input type="hidden" class="modal-variant-on-hand" value="">
                                <input type="hidden" class="variant-on-hand" value="{{ $product_unit }}">
                                <input type="hidden" class="product-unit" value="{{ $product_details['unit'] }}">
                                <input type="hidden" class="modal-product-unit" value="{{ $product_unit }}">
                                <input type="hidden" class="single-product-variants-combination" value="{{ $variants_id }}" /> 
                                <div class='variants_size'> 
                                    @foreach ($productVariants as $index => $variants)
                                        @if (isset($variantsOptions[$variants['variants_id']]))
                                            @php
                                                $productVariantsOption = $variantsOptions[$variants['variants_id']];
                                                $variant_options_name = !empty($productVariantsOption) && isset($productVariantsOption[0]) && !empty($productVariantsOption[0]['variant_options_name']) ? " : ".$productVariantsOption[0]['variant_options_name'] : "";
                                                $variantsScrollClass = "";
                                            @endphp
                                            @if(!empty($productVariantsOption))
                                                <h2>{{ $variants['variants_name'].$variant_options_name }}</h2>
                                                <div class="variants-group" data-variant-name = "{{ $variants['variants_name'] }}">
                                                    <input type="hidden" class="selected-variant-id" value="{{ $productVariantsOption[0]['variant_options_id']}}">
                                                    <ul class='list-variants {{ $variantsScrollClass }}'>
                                                        @foreach ($productVariantsOption as $key => $option)
                                                            @if(!empty($variant_option_id))
                                                                @php
                                                                    $checked = ($option['variant_options_id'] == $variant_option_id) ? 'checked' : '';
                                                                    $checkedStyle = ($option['variant_options_id'] == $variant_option_id) ? "background-color: #000;color: #fff;" : "";
                                                                @endphp
                                                            @else
                                                                @php
                                                                    $checked = ($key == 0) ? 'checked' : '';
                                                                    $checkedStyle = ($key == 0) ? "background-color: #000;color: #fff;" : "";
                                                                @endphp
                                                            @endif
                                                            <li class='product-variant-dev'>
                                                                <input type='radio' data-variant-option-name="{{ $option['variant_options_name'] }}" class='btn-check product-variant variant-combination' data-type='single-product' name="product_variant_combination_{{ $option['variants_id'] }}" id="variant-combination-{{ $option['variant_options_id'] }}" value="{{ $option['variant_options_id'] }}" data-value="{{ $option['variant_options_id'] }}" {{ $checked }}>

                                                                @if (!is_null($option['variants_option_image']) && $option['variants_option_image'] != "")
                                                                    <img src="{{ $option['variants_option_image'] }}" style='height: 30px;' class='rounded-circle variant-option-img' title="{{ $option['variant_options_name'] }}" alt=''>
                                                                @else
                                                                    <label style="{{ $checkedStyle }}" class="btn btn-outline-secondary avatar-xs-1 rounded-4 d-flex product-variant-label variant-combination-{{ $option['variant_options_id'] }}" for="variant-combination-{{ $option['variant_options_id'] }}">{{ $option['variant_options_name'] }}</label>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                                <span class="mb-10 error error-message"></span>
                                <div class="product_pro_button quantity1 d-flex mt-4 mb-4">
                                    <div class="pro-qty border product-item">
                                        <input type="text" class="quantity add-product-quantity" value="1">
                                    </div>
                                    @php $found = false; $wishlist_class = "far"; $wishlist_type = "add"; $title = __('customer.add_to_wishlist'); @endphp
                                    @if(!empty($wishlistData) && isset($wishlistData[0]) && isset($wishlistData[0]['wishlist_id']) && $wishlistData[0]['wishlist_id'] > 0)
                                        @php $found = true; $wishlist_class = "fas"; $wishlist_type = "remove"; $title = __('customer.remove_from_wishlist'); @endphp
                                    @endif
                                    <a class="add_to_cart product-wishlist" title="{{ $title }}" style="font-size:20px;" href="#"><i data-wishlist-type="{{ $wishlist_type }}" class="wishlist-icon fa-heart {{ $wishlist_class }}"></i></a>
                                    @if(($product_details['type_of_product'] == "single" && $product_details['trackable'] == 1) || ($product_details['type_of_product'] == "variant" && is_numeric($available_quantity) && $available_quantity <= 0)) 
                                        <a class="add_to_cart product-add-to-cart add-to-cart" href="#add-to-cart"> {{ __('customer.out_of_stock') }}</a>    
                                    @else
                                        <a class="add_to_cart product-add-to-cart add-to-cart" href="#add-to-cart">{{ __('customer.add_to_cart') }}</a> 
                                    @endif
                                </div>
                                @if(!empty($product_details['product_description']))
                                    <div class="product_content">
                                        <div class="accordion" id="accordionExample">
                                            <div class="accordion-item">
                                                <p class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">{{ __('customer.description') }}</button>
                                                </p>
                                                <div id="collapse8" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body px-2">
                                                        <p>{!! html_entity_decode($product_details['product_description']) !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="product_section mb-80">
            <div class="container">
                <div class="section_title text-center mb-55">
                    <h2>{{ __('customer.related_products') }}</h2>
                    <p>{{ __('customer.related_products_desc') }}</p>
                </div>
                <div class="row product_slick slick_navigation slick__activation related-products-data">
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center">
                            <a href="{{ route($store_url.'.customer.category') }}"><button type="button" class="btn btn-link">{{ __('customer.see_all_products') }}</button></a>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
        @include('common.customer.footer')
        @include('common.customer.view_popup')
        @include('common.customer.script')
        <script>
            $(document).ready(function() {
                selector = $(".single_product_section").find(".single_product");
                productType = selector.find(".single-product-type").val();
                if(productType == "variant") {
                    $(".variants_size").find(".variants-group").each(function() {
                        if($(this).find(".product-variant:checked").length == 0) {
                            $(this).find(".product-variant:first").prop("checked",true);
                            $(this).find(".product-variant:first").css({'background-color': '#000', 'color': '#fff'});
                        }
                        if($(this).find(".product-variant:checked")) {
                            optionName = $(this).data("variant-name");
                            optionValue = $(this).find(".product-variant:checked").data("variant-option-name");
                            $(this).prev().text(optionName+" : "+optionValue);
                        }
                    });
                    productID = selector.find(".product-id").val();
                    variant_combinations_data = selector.closest("body").find(".variant-combinations-"+productID).val();
                    if(variant_combinations_data != "") {
                        variant_combinations_data = $.parseJSON(variant_combinations_data);
                        variant_combination_id = selector.find(".single-product-variants-combination").val();
                        checkProductAvailable(selector,variant_combinations_data,variant_combination_id)
                    }
                }
                var totalImages = $(".category-img-count").val();
                if (totalImages <= 1) 
                    $(".carousel-control-prev, .carousel-control-next").hide();
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
                $('.slick-carousel-1').slick({
                    infinite: true,
                    slidesToShow: 11,
                    slidesToScroll: 1,
                    prevArrow: '<button type="button" class="slick-prev-1">&#8249;</button>',
                    nextArrow: '<button type="button" class="slick-next-1">&#8250;</button>',
                    arrow: false,
                    autoplay: false,
                    adaptiveHeight: true,
                    speed: 300,
                    responsive: [
                        { breakpoint: 768, settings: { slidesToShow: 2 } },
                        { breakpoint: 500, settings: { slidesToShow: 2 } }
                    ]
                }); 
                $('.slick-variants-carousel-1').slick({
                    infinite: true,
                    slidesToShow: 7,
                    slidesToScroll: 1,
                    prevArrow: '<button type="button" class="slick-prev-1">&#8249;</button>',
                    nextArrow: '<button type="button" class="slick-next-1">&#8250;</button>',
                    arrow: false,
                    autoplay: false,
                    adaptiveHeight: true,
                    speed: 300,
                    responsive: [
                        { breakpoint: 768, settings: { slidesToShow: 2 } },
                        { breakpoint: 500, settings: { slidesToShow: 2 } }
                    ]
                }); 
                $(".single_product_details").find(".product-variant-label").each(function() {
                    var $label = $(this);
                    if ($label[0].scrollWidth > $label.innerWidth()) {
                        var text = $label.text();
                        while ($label[0].scrollWidth > $label.innerWidth() && text.length > 15) {
                            text = text.slice(0, -1);
                            $label.text(text + '...');
                        }
                    }
                });
                showProducts($(".single-category-id").val(),$(".related-products-data"),'',$(".single-sub-category-id").val());
            });
            var product_img_slick = product_img_slick1 = null;
            function productImgSlick() {
                product_img_slick = $('.slick-carousel').slick({
                    infinite: true,
                    slidesToShow: 7,
                    slidesToScroll: 1,
                    prevArrow: '<button type="button" class="slick-prev-1">&#8249;</button>',
                    nextArrow: '<button type="button" class="slick-next-1">&#8250;</button>',
                    arrow: false,
                    autoplay: false,
                    adaptiveHeight: true,
                    speed: 300,
                    responsive: [
                        { breakpoint: 768, settings: { slidesToShow: 2 } },
                        { breakpoint: 500, settings: { slidesToShow: 2 } }
                    ]
                });
                product_img_slick1 = $('.slick-variants-carousel').slick({
                    infinite: true,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    prevArrow: '<button type="button" class="slick-prev-1">&#8249;</button>',
                    nextArrow: '<button type="button" class="slick-next-1">&#8250;</button>',
                    arrow: false,
                    autoplay: false,
                    adaptiveHeight: true,
                    speed: 300,
                    responsive: [
                        { breakpoint: 768, settings: { slidesToShow: 2 } },
                        { breakpoint: 500, settings: { slidesToShow: 2 } }
                    ]
                });
            }
            function showProducts(category_id, _this, page = '', sub_category_id = '') {
                products_by_category_url = $(".products_by_category_url").val();
                // $(".page-loader").show();
                $.ajax({
                    url: products_by_category_url,
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        category_id: category_id,
                        // sub_category_id: sub_category_id,
                        search_text: "",
                        page: page,
                        _type : 'related_products',
                        perPage : 3,
                        product_id : $(".single-page-product-id").val()
                    },
                    success: function (response) {
                        if (product_img_slick) {
                            product_img_slick.slick('unslick');
                        }
                        if (product_img_slick1) {
                            product_img_slick1.slick('unslick');
                        }
                        $(".related-products-data").html(response.product_list_by_category);
                        if(response.product_list_by_category != "") {
                            $(".related-products-data").find(".selected-variant-image").each(function() {
                                productType = $(this).closest(".single-product-details").find(".single-product-type").val();
                                if(productType == "variant") {
                                    variantImage = $(this).val();
                                    $(this).closest(".single-product-details").find(".product-image-path").attr("src",variantImage);
                                }
                            });
                            productImgSlick();
                            _this.closest("body").find(".related-products-data").find(".product-variant-label").each(function() {
                                var $label = $(this);
                                if ($label[0].scrollWidth > $label.innerWidth()) {
                                    var text = $label.text();
                                    while ($label[0].scrollWidth > $label.innerWidth() && text.length > 5) {
                                        text = text.slice(0, -1);
                                        $label.text(text + '...');
                                    }
                                }
                            });
                        }
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl)
                        });
                        $(".page-loader").hide();
                    }
                });
            }
            $(document).on("click",".all-product-images",function() {
                var index = $(this).data("index");
                $("#carouselExample .carousel-item").removeClass("active");
                $("#carouselExample .carousel-item:eq(" + index + ")").addClass("active");
            });
        </script> 
    </body>
</html>