<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        @include('common.customer.header')
        <style>
            .order-review .order-img img {
                width: 90px;
                margin-right: 20px;
            }
            .list-group-item {
                border : 0;
            }
            .pagination ul li.current a {
                color: #ffffff;
                background: #fc7c7c;
                border-color: #fc7c7c;
            }
            .btn-link-custom {
                color: #007bff; 
                background-color: transparent; 
                border: none; 
                cursor: pointer; 
            }

            .btn-link-custom:hover {
                color: #0056b3; 
            }

        </style>
    </head>
    <body>
        <div class="body_overlay"></div>
        @include('common.customer.mobile_navbar')
        @include('common.customer.navbar')
        @include('common.customer.mini_cart')
        @include('common.customer.breadcrumbs')
        <input type="hidden" class="translation-key" value="orders_page_title">
        <div class="account-page-area">
            <div class="container">
                <div class="row">
                    @include('common.customer.account_sidebar')
                    <div class="col-sm-12 col-md-8 col-lg-8 pb-30">
                        <input type="hidden" class="orders-products" value="{{ route($store_url.'.customer.orders-product') }}">
                        <div class="account-info">
                            <h5 class="row justify-content-between">
                                <div class="col-md-6 col-sm-6 col-6 order-md-1 mb-3">
                                    <p>{{ __('customer.your_orders') }}</p>
                                </div>
                                <div class="col-md-4 order-md-2">
                                    <!-- <div class="input-group">
                                        <input class="form-control border-end-0 border search-text" type="search" placeholder="Search in Order" id="example-search-input">
                                        <span class="input-group-append">
                                            <button class="btn btn-outline-secondary bg-danger border-start-0 text-white border ms-n5 search-product" type="button"><i class="fa fa-search"></i></button>
                                        </span>
                                    </div> -->
                                </div>
                            </h5>
                            <hr/>
                            <div class="orders-list"></div>
                            <div class="row">
                                <div class="col-md-12 pagination d-flex justify-content-center blog_pagination_sidebar"></div>
                            </div>
                            <!-- <div class="order-one mb-3">
                                <div class="card order-review">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-sm-8 col-md-8 col-lg-8">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-12 d-flex">
                                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                                            <span>ORDER PLACED</span><br/>
                                                            <span>12 September 2023</span>
                                                        </div>
                                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                                            <span>TOTAL</span><br/>
                                                            <span>SAR 1500.00</span>
                                                        </div>
                                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                                            <span>SHIP TO</span><br/>
                                                            <span>Rajashree <i class="fa fa-angle-down"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 col-md-4 col-lg-4">
                                                <span>ORDER # 408-015573</span><br/>
                                                <a href="#">View order details</a>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <h6 style="margin-bottom:0px;">Delivered 26-Aug-2023</h6>
                                            <span>Package was handed to resident</span>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="order-img"> 
                                                    <img src="{{ URL::asset('assets/customer/images/product/hot-coffee.jpg') }}" alt="" class="rounded">
                                                </div>
                                                <div class="order-des">
                                                    <a href="#">Breakpoint</a>
                                                    <div class="">
                                                        <button class="btn btn-warning btn-sm">Buy it again</button>
                                                        <button class="btn btn-light btn-sm">View your item</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul> 
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <h6 style="margin-bottom:0px;">Delivered 26-Aug-2023</h6>
                                            <span>Package was handed to resident</span>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="order-img"> 
                                                    <img src="{{ URL::asset('assets/customer/images/product/16.jpg') }}" alt="" class="rounded">
                                                </div>
                                                <div class="order-des">
                                                    <a href="#">Breakpoint</a>
                                                    <div class="">
                                                        <button class="btn btn-warning btn-sm">Buy it again</button>
                                                        <button class="btn btn-light btn-sm">View your item</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul> 
                                </div>
                            </div>
                            <div class="order-one mb-3">
                                <div class="card order-review">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-sm-8 col-md-8 col-lg-8">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-12 d-flex">
                                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                                            <span>ORDER PLACED</span><br/>
                                                            <span>12 September 2023</span>
                                                        </div>
                                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                                            <span>TOTAL</span><br/>
                                                            <span>SAR 1500.00</span>
                                                        </div>
                                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                                            <span>SHIP TO</span><br/>
                                                            <span>Rajashree <i class="fa fa-angle-down"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 col-md-4 col-lg-4">
                                                <span>ORDER # 408-015573</span><br/>
                                                <a href="#">View order details</a>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <h6 style="margin-bottom:0px;">Delivered 26-Aug-2023</h6>
                                            <span>Package was handed to resident</span>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="order-img"> 
                                                    <img src="{{ URL::asset('assets/customer/images/product/17.jpg') }}" alt="" class="rounded">
                                                </div>
                                                <div class="order-des">
                                                    <a href="#">Breakpoint</a>
                                                    <div class="">
                                                        <button class="btn btn-warning btn-sm">Buy it again</button>
                                                        <button class="btn btn-light btn-sm">View your item</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul> 
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- review 
        <div class="modal fade" id="review" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title " id="exampleModalLabel">Write Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="exampleFormControlTextarea1" class="form-label fw-bold">Rate this product</label>
                    <div class="d-flex rating mb-3"><i class="fa fa-star me-2 text-danger"></i><i class="fa fa-star me-2"></i> <i class="fa fa-star me-2"></i> <i class="fa fa-star me-2"></i> <i class="fa fa-star me-2"></i></div>
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label fw-bold">Please write product review here.</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="7"></textarea>
                    </div>
                    <div class="mb-3 file-up">
                        <input type="file" name="myfile">
                        <label class="form-label fw-bold"></label>
                    </div>
                    <button class="btn btn-danger text-uppercase btn-md me-2 fw-normal" > Submit </button>
                </div>
                </div>
            </div>
        </div>-->
        @include('common.customer.footer')
        @include('common.customer.script')
        <script>
            $(document).ready(function() {
                showOrderProducts();
            });

            function showOrderProducts(page = '') {
                $(".page-loader").show();
                orders_products_url = $(".orders-products").val();
                search_text = $(".search-text").val();
                $.ajax({
                    url: orders_products_url,
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        search_text: search_text,
                        perPage : 12,
                        page: page,
                    },
                    success: function (response) {
                        $(".orders-list").html(response.orders_list);
                        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                            return new bootstrap.Popover(popoverTriggerEl)
                        });
                        updatePagination(response.currentPage, response.totalPages);
                        $(".page-loader").hide();
                    }
                });
            }

            function updatePagination(currentPage, totalPages) {
                var paginationHtml = '<ul>';
                for (var i = 1; i <= totalPages; i++) {
                    active_class = (i == currentPage) ? "current" : "";
                    paginationHtml += '<li class="'+active_class+'" data-page-no="'+i+'"><a href="#page'+i+'" onclick="showOrderProducts(' + i + ')">' + i + '</a></li>';
                }
                paginationHtml += '</ul>';
                $(".pagination").html(paginationHtml);
            }

            $(document).on("click",".search-product",function(event) {
                event.preventDefault();
                showOrderProducts();
            }); 

            // $(document).on("keyup",".search-text",function(event) {
            //     event.preventDefault();
            //     showOrderProducts();
            // });  
        </script>
    </body>
</html>