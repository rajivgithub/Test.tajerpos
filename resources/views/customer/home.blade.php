<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        @include('common.customer.header')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
        <style>
            .featured_banner_text {
                white-space: nowrap;
                overflow: hidden; 
                text-overflow: ellipsis; 
            }
            .pagination ul li.current a {
                color: #ffffff;
                background: #fc7c7c;
                border-color: #fc7c7c;
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
        @include('common.customer.mobile_navbar')
        @include('common.customer.navbar')
        @include('common.customer.mini_cart')
        <input type="hidden" class="translation-key" value="home_page_title">
        <input type="hidden" value="home_page" class="page-name">
        <input type="hidden" class="products_by_category_url" value="{{ route($store_url.'.customer.products-by-category') }}">
        <input type="hidden" class="mini-cart-url" value="{{ route($store_url.'.customer.view-cart') }}">
        <div id="demo" class="carousel slide mb-50" data-bs-ride="carousel">
            <!-- The slideshow/carousel -->
            <div class="carousel-inner">
                @if(isset($bannersDetails) && !empty($bannersDetails))
                    @foreach($bannersDetails as $key => $banner)
                        <div class="carousel-item {{ ($key == 0) ? 'active' : ''}}">
                            <a href = "{{ !empty($banner->banner_url) ? $banner->banner_url : '#' }}"><img src="{{ $banner->banner_image }}" alt="Banners" loading="lazy" class="d-block w-100"></a>
                        </div>
                    @endforeach
                @endif
            </div>
            <!-- Left and right controls/icons -->
            <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
        <div class="featured_banner_section mb-30">
            <div class="container">
                <div class="product_header">
                    <div class="section_title text-center">
                        <h2>{{ __('customer.all_products') }}</h2>
                    </div>
                </div>  
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="page__amount border  mb-4 search-product-div">
                            <form class="d-flex justify-content-between" action="#">
                                <input class="border-0 w-100 search-text" placeholder="{{ trans('customer.search_products') }}" type="text">
                                <button style="border: 0;padding: 0;background: none;font-size: 25px;" type="button" class="text-end search-product"><span class="pe-7s-search"></span></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="loader"></div>
                <div class="row featured_banner_inner category-list-data category-list-details" >
                </div>              
            </div>
        </div>
        <div class="product_section mb-80 wow fadeInUp" data-wow-delay="0.1s" data-wow-duration="1.1s">
            <div class="container product-list-by-category">
                
            </div>
            <div class="container">
                <div class="row mb-70">
                    <div class="col-md-12 pagination d-flex justify-content-center blog_pagination_sidebar"></div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <a href="{{ route($store_url.'.customer.category') }}"><button type="button" style="width:100%;" class="btn btn-link see-all-products">{{ __('customer.see_all_products') }}</button></a>
                    </div>
                </div>  
            </div>
        </div>
        @include('common.customer.footer')
        @include('common.customer.view_popup')
        @include('common.customer.script')
        <script>
            $(document).ready(function() {
                showProducts("all",$(".product-list-by-category"));
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
            var slickInstance = null;
            function initializeSlick() {
                slickInstance = $('.category-list-details').slick({
                    slidesToShow: 6,
                    slidesToScroll: 1,
                    arrows: false,
                    dots: false,
                    autoplay: false,
                    speed: 300,
                    infinite: true,
                    responsive: [
                        { breakpoint: 768, settings: { slidesToShow: 2 } },
                        { breakpoint: 500, settings: { slidesToShow: 2 } }
                    ]
                });
            }
            function showProducts(category_id, _this, page = '') {
                $("#loader").show();
                products_by_category_url = $(".products_by_category_url").val();
                search_text = $(".search-text").val();
                $.ajax({
                    url: products_by_category_url,
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        category_id: category_id,
                        search_text: search_text,
                        page: page,
                        _type : 'home_page',
                        perPage : 9
                    },
                    success: function (response) {
                        if (slickInstance) {
                            slickInstance.slick('unslick');
                        }
                        if (product_img_slick) {
                            product_img_slick.slick('unslick');
                        }
                        $(".category-list-details").html(response.category_list_html);
                        $("#loader").hide();
                        updatePagination(response.currentPage, response.totalPages,category_id);
                        if(response.category_list_html != "") {
                            initializeSlick();
                        }
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
                        if(response.totalPages > 0) {
                            _this.closest("body").find(".see-all-products").removeClass("dnone");
                            sub_category_list = _this.closest("body").find(".product-list-by-category").find(".sub-category-list");
                            if(sub_category_list.length > 0) {
                                sub_category_list.each(function() {
                                    sub_category_id = $(this).attr("data-sub-category-id");
                                    if(sub_category_id == "all") {
                                        product_count = _this.closest("body").find(".product-list-by-category").find(".sub-category-product-list").length;
                                        $(this).find(".all-sub-category-li").text("("+product_count+")");
                                    } else {
                                        product_count = _this.closest("body").find(".product-list-by-category").find(".sub-category-product-"+sub_category_id).length;
                                        $(this).find(".product-count-sub-category").text("("+product_count+")");
                                    }
                                });
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
                        } else 
                            _this.closest("body").find(".see-all-products").addClass("dnone");
                        $(".page-loader").hide();
                    }
                });
            }

            function updatePagination(currentPage, totalPages, category_id) {
                var paginationHtml = '<ul>';
                for (var i = 1; i <= totalPages; i++) {
                    active_class = (i == currentPage) ? "current" : "";
                    paginationHtml += '<li class="'+active_class+'" data-category-id="'+category_id+'" data-page-no="'+i+'"><a href="#page'+i+'" onclick="showProducts(' + "'" + category_id + "'" + ', $(\'.product-list-by-category\'), ' + i + ')">' + i + '</a></li>';
                }
                paginationHtml += '</ul>';
                $(".pagination").html(paginationHtml);
            }
            $(document).on("click",".category-details",function() {
                $(this).closest(".category-list-details").find(".single_featured_banner").removeClass("active");
                $(this).find(".single_featured_banner").addClass("active");
                category_id = $(this).find(".category-id").val();
                showProducts(category_id,$(this));
            }); 
            $(document).on("click",".sub-category-list",function() {
                $(this).closest(".sub-category-list-details").find(".sub-category-li").removeClass("active");
                $(this).find(".sub-category-li").addClass("active");
                data_sub_category_id = $(this).attr("data-sub-category-id");
                if(data_sub_category_id != "all") {
                    $(this).closest(".product-list-by-category").find(".sub-category-product-list").css("display","none");
                    $(this).closest(".product-list-by-category").find(".sub-category-product-"+data_sub_category_id).css("display","block");
                } else 
                    $(this).closest(".product-list-by-category").find(".sub-category-product-list").css("display","block");
            }); 
            $(document).on("click",".search-product",function(event) {
                event.preventDefault();
                showProducts("all",$(".product-list-by-category"));
            }); 
            $(document).on("keyup",".search-text",function(event) {
                event.preventDefault();
                showProducts("all",$(".product-list-by-category"));
            });  
        </script>
    </body>
</html>