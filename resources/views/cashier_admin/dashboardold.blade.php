<!DOCTYPE html>
<html lang="en">
    <head>
        @include('common.cashier_admin.header')
    </head>
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')
            <div class="content-wrapper">
                <div class="container-full">
                    <section class="content">
                        @php
                            $total_product_count = (isset($data) && !empty($data) && !empty($data['total_product_count'])) ? $data['total_product_count'] : 0;
                            $total_customer_count = (isset($data) && !empty($data) && !empty($data['total_customer_count'])) ? $data['total_customer_count'] : 0;
                            $total_revenue = (isset($data) && !empty($data) && !empty($data['total_revenue'])) ? $data['total_revenue'] : 0;
                        @endphp
                        <div class="row">
                            <div class="col-xxxl-3 col-lg-4 col-12">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="d-flex align-items-start">
                                            <div><img src="{{ URL::asset('assets/cashier-admin/images/food/online-order-4.png') }}" class="w-80 mr-20" alt="" /></div>
                                            <div>
                                                <h2 class="my-0 font-weight-700">SAR {{ number_format((float)($total_revenue), 2, '.', '') }}</h2>
                                                <p class="text-fade mb-0">Total Revenue</p>
                                                <!-- <p class="font-size-12 mb-0 text-primary"><span class="badge badge-pill badge-primary-light mr-5"><i class="fa fa-arrow-down"></i></span>12% (15 Days)</p> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxxl-3 col-lg-4 col-12">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="d-flex align-items-start">
                                            <div>
                                                <img src="{{ URL::asset('assets/cashier-admin/images/food/online-order-1.png') }}" class="w-80 mr-20" alt="" />
                                            </div>
                                            <div>
                                                <h2 class="my-0 font-weight-700">{{$total_product_count}}</h2>
                                                <p class="text-fade mb-0">Total Products</p>
                                                <!-- <p class="font-size-12 mb-0 text-success"><span class="badge badge-pill badge-success-light mr-5"><i class="fa fa-arrow-up"></i></span>3% (15 Days)</p> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxxl-3 col-lg-4 col-12">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="d-flex align-items-start">
                                            <div>
                                                <img src="{{ URL::asset('assets/cashier-admin/images/food/online-order-2.png') }}" class="w-80 mr-20" alt="" />
                                            </div>
                                            <div>
                                                <h2 class="my-0 font-weight-700">{{$total_customer_count}}</h2>
                                                <p class="text-fade mb-0">Total Customers</p>
                                                <p class="font-size-12 mb-0 text-success"><span class="badge badge-pill badge-success-light mr-5"><i class="fa fa-arrow-up"></i></span>8% (15 Days)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxxl-7 col-xl-6 col-lg-6 col-12">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="box-title mb-0">Daily Revenue</h4>
                                                <p class="mb-0 text-mute">Lorem ipsum dolor</p>
                                            </div>
                                            <div class="text-right">
                                                <h3 class="box-title mb-0 font-weight-700">SAR 154K</h3>
                                                <p class="mb-0"><span class="text-success">+ 1.5%</span> than last week</p>
                                            </div>
                                        </div>
                                        <div id="chart" class="mt-20"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxxl-5 col-xl-6 col-lg-6 col-12">
                                <div class="box">
                                    <div class="box-body">
                                        <h4 class="box-title">Customer Flow</h4>
                                        <div class="d-md-flex d-block justify-content-between">
                                            <div>
                                                <h3 class="mb-0 font-weight-700">SAR 2,780k</h3>
                                                <p class="mb-0 text-primary"><small>In Restaurant</small></p>
                                            </div>
                                            <div>
                                                <h3 class="mb-0 font-weight-700">SAR 1,410k</h3>
                                                <p class="mb-0 text-danger"><small>Online Order</small></p>
                                            </div>
                                        </div>
                                        <div id="yearly-comparison"></div>
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
    </body>
</html>