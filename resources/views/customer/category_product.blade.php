<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        @include('common.customer.header')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
        <link rel="stylesheet" href="{{ URL::asset('assets/customer/css/nice-select.css') }}">
        <style>
            .pagination ul li.current a {
                color: #ffffff;
                background: #fc7c7c;
                border-color: #fc7c7c;
            }
            .category-list-details ul li.active .category-name, .sub-category-list.active .nested-sub-category-name {
                color: #fc7c7c;
            }
            .nested-sub-category-list {
                border-bottom: none !important;
                padding-bottom: 1px !important;
                margin-bottom: 1px !important;
            }
            .nested-sub-category-name {
                margin-left: 20px;
            }
            .nested-sub-category-name span {
                float : none !important;
            }
            .nested-list li:first-child .nested-sub-category-name {
                margin-top : 10px !important;
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
            .product-add-to-cart:disabled {
                background-color: #000000;
                border-color: #000000 !important;
                color: #ffffff;
                opacity: 1;
            }
            .truncate-text {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <div class="body_overlay"></div>
        @include('common.customer.mobile_navbar')
        @include('common.customer.navbar')
        @include('common.customer.mini_cart')
        @include('common.customer.breadcrumbs')
        <input type="hidden" class="translation-key" value="all_products_page_title">
        <input type="hidden" value="category_product" class="page-name">
        <input type="hidden" class="products_by_category_url" value="{{ route($store_url.'.customer.products-by-category') }}">
        <div class="product_page_section mb-100">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 order-2 order-lg-1">
                        <div class="product_sidebar product_widget">
                            <div class="widget__list category wow fadeInUp" data-wow-delay="0.2s" data-wow-duration="1.2s">
                                <h3>{{ __('customer.category') }}</h3>
                                <div class="widget_category category-list-details">
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 order-1 order-lg-2">
                        <div class="product_page_wrapper">
                            <div class="product_sidebar_header mb-60 d-flex justify-content-between align-items-center">
                                <div class="page__amount border search-product-div">
                                    <form class="" action="#">
                                        <input class="border-0 search-text" placeholder="{{ trans('customer.search_products') }}" type="text">
                                        <button type="button" class="clear-search dnone"><i class="ion-android-close me-2" style="font-size:25px;"></i></button>
                                        <button type="button" class="search-product"><span class="pe-7s-search"></span></button>
                                    </form>
                                </div>
                                <div class="product_header_right d-flex align-items-center">
                                    <div class="sorting__by d-flex align-items-center">
                                        <span>{{ __('customer.sort_by') }} : </span>
                                        <form class="" action="#">
                                            <select name="orderby" class="products-sorting" id="short">
                                                <option selected value="">{{ __('customer.default') }}</option>
                                                <!-- <option value="2">Sort by popularity</option> -->
                                                <option data-sorting="desc" value="product_id">{{ __('customer.sort_by_newness') }}</option>
                                                <option data-sorting="asc" value="low_to_high"> {{ __('customer.low_to_high') }}</option>
                                                <option data-sorting="desc" value="high_to_low"> {{ __('customer.high_to_low') }}</option>
                                                <option data-sorting="asc" value="product_name">{{ __('customer.product_name_a_z') }}</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="product_page_gallery">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="grid">
                                        <div class="row grid__product product-list-by-category">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 pagination d-flex justify-content-center"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('common.customer.footer')
        @include('common.customer.view_popup')
        @include('common.customer.script')
        <script src="{{ URL::asset('assets/customer/js/jquery.nice-select.js') }}"></script>
        <script>
            $(document).ready(function() {
                currentURL = window.location.pathname;
                if (currentURL && currentURL.length > 0) {
                    var urlSegments = currentURL.split('/');
                    category_name = (urlSegments.length > 4) ? urlSegments[4] : '';
                    sub_category_name = (urlSegments.length > 6) ? urlSegments[6] : '';
                    showProducts('',$(".product-list-by-category"),'','',category_name,sub_category_name);
                }
                $('select:not(.site-language), .select_option:not(.site-language)').niceSelect();
            });
            $(document).on("change",".products-sorting",function(event) {
                event.preventDefault();
                sorting_column = $(this).val();
                sorting_order = $(this).find("option:selected").attr("data-sorting");
                category_id = $(this).closest("body").find(".category-list-details").find(".category-details.active").find(".category-id").val();
                sub_category_id = $(this).closest("body").find(".category-list-details").find(".category-details.active").find(".sub-category-list.active").attr("data-sub-category-id");
                showProducts(category_id,$(".product-list-by-category"),'',sub_category_id);
            }); 
            $(document).on("keyup",".search-text",function(event) {
                event.preventDefault();
                if($(this).val() != "") {
                    $(this).closest("form").find(".clear-search").removeClass("dnone");
                }
                else  {
                    $(this).closest("form").find(".clear-search").addClass("dnone");
                }
            });   
            $(document).on("click",".clear-search",function(event) {
                event.preventDefault();
                $(this).closest("form").find(".search-text").val("");
                category_id = $(this).closest("body").find(".category-list-details").find(".category-details.active").find(".category-id").val();
                sub_category_id = $(this).closest("body").find(".category-list-details").find(".category-details.active").find(".sub-category-list.active").attr("data-sub-category-id");
                showProducts(category_id,$(".product-list-by-category"),'',sub_category_id);
                $(this).addClass("dnone");
            });

            $(document).on("click",".search-product",function(event) {
                event.preventDefault();
                category_id = $(this).closest("body").find(".category-list-details").find(".category-details.active").find(".category-id").val();
                sub_category_id = $(this).closest("body").find(".category-list-details").find(".category-details.active").find(".sub-category-list.active").attr("data-sub-category-id");
                showProducts(category_id,$(".product-list-by-category"),'',sub_category_id);
            });
            var product_img_slick = null;
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
                product_img_slick = $('.slick-variants-carousel').slick({
                    infinite: true,
                    slidesToShow: 2,
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
            function showProducts(category_id = '', _this, page = '',sub_category_id = '',category_url = '',sub_category_url = '') {
                $(".page-loader").show();
                sorting_column = _this.closest("body").find(".products-sorting").find("option:selected").val(); 
                sorting_order = _this.closest("body").find(".products-sorting").find("option:selected").attr("data-sorting");
                products_by_category_url = $(".products_by_category_url").val();
                search_text = $(".search-text").val();
                $.ajax({
                    url: products_by_category_url,
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN, sub_category_id : sub_category_id, category_id: category_id, search_text: search_text, page: page, _type:"product_page", sorting_column : sorting_column, sorting_order : sorting_order,perPage : 12, category_url : category_url, sub_category_url : sub_category_url
                    },
                    success: function (response) {
                        $(".category-list-details").html(response.category_list_html);
                        $(".product-list-by-category").html(response.product_list_by_category);
                        $(".product-list-by-category").find(".selected-variant-image").each(function() {
                            productType = $(this).closest(".single-product-details").find(".single-product-type").val();
                            if(productType == "variant") {
                                variantImage = $(this).val();
                                $(this).closest(".single-product-details").find(".product-image-path").attr("src",variantImage);
                            }
                        });
                        if(response.product_list_by_category != "") {
                            productImgSlick();
                        }
                        var labels = document.querySelectorAll('.product-variant-label');
                        labels.forEach(function(label) {
                            if (label.scrollWidth > label.clientWidth) {
                                var text = label.textContent;
                                while (label.scrollWidth > label.clientWidth) {
                                    text = text.slice(0, -1);
                                    label.textContent = text + '...'; 
                                }
                            }
                        });
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl)
                        });
                        updatePagination(response.currentPage, response.totalPages,category_id);
                        $(".page-loader").hide();
                    }
                });
            }
            function updatePagination(currentPage, totalPages, category_id) {
                var paginationHtml = '<ul>';
                for (var i = 1; i <= totalPages; i++) {
                    active_class = (i == currentPage) ? "current" : "";
                    paginationHtml += '<li class="'+active_class+'" data-category-id="'+category_id+'" data-page-no="'+i+'"><a href="#" onclick="showProducts(' + "'" + category_id + "'" + ', $(\'.product-list-by-category\'), ' + i + ')">' + i + '</a></li>';
                }
                paginationHtml += '</ul>';
                $(".pagination").html(paginationHtml);
            }
        </script>
    </body>
</html>