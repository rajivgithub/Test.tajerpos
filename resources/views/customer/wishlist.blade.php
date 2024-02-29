<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        @include('common.customer.header')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
        <style>
            .pagination ul li.current a {
                color: #ffffff;
                background: #fc7c7c;
                border-color: #fc7c7c;
            }
            .out-of-stock-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(255, 255, 255, 0.8); 
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 18px;
                color: #ff0000; 
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
        <input type="hidden" class="translation-key" value="wishlist_page_title">
        <div class="wishlist-area">
            <div class="container">
                <div class="row grid__product wishlist-details">
                    
                </div>
                <div class="row mb-70">
                    <div class="col-md-12 pagination d-flex justify-content-center blog_pagination_sidebar"></div>
                </div>
            </div>
        </div>
        @include('common.customer.footer')
        @include('common.customer.view_popup')
        @include('common.customer.script')
        <script>
            $(document).on("click",".remove-wishlist",function() {
                $(this).closest(".single-product-details").remove();
                wishlist_id = $(this).closest(".single-product-details").find(".wishlist-id").val();
                product_id = $(this).closest(".single-product-details").find(".single-product-id").val();
                $(this).closest(".single-product-details").remove();
                wishlist_count = $(".wishlist-details").find(".single-product-details").length;
                if(wishlist_count <= 0)
                    $(".wishlist-details").html("<p class='text-center'>Your wishlist is empty!</p>");
                addWishlist(product_id,"remove",wishlist_id);
            });
            $(document).ready(function() {
                showWishlistProducts($(".wishlist-details"));
            });
            function showWishlistProducts(_this, page = '') {
                $(".page-loader").show();
                search_text = $(".search-text").val();
                $.ajax({
                    url: "{{ route($store_url.'.customer.wishlist.index') }}",
                    type: 'get',
                    "data":{page: page,type:"list"},
                    success: function (response) {
                        updatePagination(response.currentPage, response.totalPages);
                        _this.closest("body").find(".wishlist-details").html(response.wishlist_product_list);
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl)
                        });
                        $(".page-loader").hide();
                    }
                });
            }
            function updatePagination(currentPage, totalPages) {
                var paginationHtml = '<ul>';
                for (var i = 1; i <= totalPages; i++) {
                    active_class = (i == currentPage) ? "current" : "";
                    paginationHtml += '<li class="'+active_class+'"><a href="#page'+i+'" onclick="showWishlistProducts($(\'.product-list-by-category\'), ' + i + ')">' + i + '</a></li>';
                }
                paginationHtml += '</ul>';
                $(".pagination").html(paginationHtml);
            } 
        </script>
    </body>
</html>