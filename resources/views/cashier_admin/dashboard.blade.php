<!DOCTYPE html>
<html lang="en">
  <head>
    @php $role_name = Auth::user()->is_admin == 2 ? __('store-admin.store_admin') : __('store-admin.cashier');  @endphp
    <title>{{ __('store-admin.dashboard_page_title',['company' => Auth::user()->company_name, 'role_name' => $role_name]) }}</title>
    @include('common.cashier_admin.header')
  </head>
  <style>
    /*------------*/
    .content-wrapper {
      margin-right: 282px;
    }
    .rtl .content-wrapper {
      margin-left: 282px;
    }
    .sar_header {
      font-weight: 700;
      font-size: 21px;
    }
    .sub3 {
      font-size: 11px;
    }
    .pos-d {
      position: relative;
      top: 6px;
      width: 60px;
    }
    
    .pos-d1 {
      position: relative;
      top: 4px;
      width: 63px;
    }
    
    .sub2 {
      font-size: 13px;
    }
    
    .right2 {
        position: relative;
        margin-top: 2px;
        width: 100%;
        left: 0px;
    }
    
    .right-bar1 {
        position: fixed;
        top: 20px;
        right: -3px;
        background: #ffffff;
        width: 285px;
        overflow: hidden;
        z-index: 99;
        height: 100%;
    }
    
    .side_sub {
        font-size: 20px;
        font-weight: lighter;
        color: #cb133d;
    }
    
    .side_b {
        right: 18px;
        position: relative;
        top: 0px;
        font-weight: bolder;
        font-size: 14px;
    }
    
    .side_b1 {
        left: 20px;
        position: relative;
        font-size: 13px;
        top: 0px;
    }
    
    .box-body1 {
        padding: 16px;
        border-radius: 10px;
        margin-bottom: -17px;
    }
    
    .btn-sm1 {
        padding: .55rem .75rem;
        font-size: 8px;
        line-height: 1.35;
        position: relative;
        right: 29px;
        height: fit-content;
    }
    
    .btn-sm2 {
        padding: .55rem .75rem;
        font-size: 8px;
        line-height: 1.35;
        position: relative;
        right: 20px;
        height: fit-content;
    }
    
    /*.pos-side{
          position: relative;
          left: 25px;
          }*/
    .pos-side1 {
        position: relative;
        right: 54px;
    }
    
    .side_head {
        font-size: 14px;
        font-weight: inherit;
    }
    
    .sidebar_img {
        width: 80px;
        position: relative;
        left: 20px;
    }
    
    .apexcharts-canvas {
        position: relative;
        user-select: none;
        right: 18px;
    }
    
    .my_cust {
        background-image: linear-gradient(#e15b36, #982772);
        height: 100%;
    }
    
    .my_cust1 {
        background-image: linear-gradient(20deg, #78eefc, #36b7c7, #3497a4);
        height: 260px;
        padding: 0px;
    }
    
    .my_cust {
        font-family: "poppins" !important;
        font-size: 15px;
        color: #ffffff;
        font-weight: 500;
        padding-bottom: 0px;
    }
    
    .my_day {
        position: relative;
        top: 50px;
        color: #ededed;
    }
    
    .my_day2 {
        color: #ededed;
    }
    
    .my_amt {
        font-size: 30px;
        position: relative;
        top: 38px;
    }
    
    .un_line {
        background-color: #ededed;
        margin-top: 55px;
    }
    
    .cust_fnt {
        font-family: "poppins";
        color: white;
        font-size: 18px;
        font-weight: 500;
    }
    
    .cust_fnt_side {
        font-family: "poppins";
        color: white;
        font-size: 19px;
        font-weight: 600;
        rotate: 270deg;
        position: relative;
        left: 0px;
        top: 210px;
    }
    
    .cust1_sub {
        color: #ededed;
    }
    
    .cust2_num {
        color: #ededed;
        font-size: 43px;
        font-weight: 600;
        position: relative;
        left: 22px;
        top: 20px;
    }
    
    .my_day1 {
        position: relative;
        top: 30px;
        color: #ededed;
        left: 22px
    }
    
    .custmy_day1 {
        position: relative;
        top: 45px;
        color: #ededed;
        left: 22px;
    }
    
    .cust_count {
        font-size: 32px;
        color: #ededed;
        font-weight: 600;
        position: relative;
        top: 42px;
        left: 22px;
    }
    
    .prog_title {
        font-size: 18px;
        font-weight: 600;
    }
    
    .prog_title_side {
        font-size: 15px;
        float: right;
        position: relative;
        bottom: 30px;
    }
    
    .prog_percent {
        display: block;
        text-align: center;
        font-size: 21px;
        font-weight: 900;
        color: #af407b;
        font-family: "poppins"
    }
    
    .prog_bar {
        position: relative;
        top: 5px;
    }
    
    .slide {
        position: relative;
        left: 22px;
        top: 20px;
        width: 95px;
    }
    
    /*scrollbar*/
    header {
        /* font-family: 'Lobster', cursive; */
        text-align: center;
        font-size: 25px;
    }
    
    #info {
        font-size: 18px;
        color: #555;
        text-align: center;
        margin-bottom: 25px;
    }
    
    a {
        color: #074E8C;
    }
    
    .scrollbar {
        margin-left: 0px;
        float: left;
        height: 86%;
        overflow-y: scroll;
        margin-bottom: 25px;
        position: relative;
        right: 4px;
        top: 60px;
    }
    
    .force-overflow {
        /* min-height: 450px;*/
    }
    
    #wrapper {
        text-align: center;
        width: 500px;
        margin: auto;
    }
    
    #style-7::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        background-color: #F5F5F5;
        border-radius: 5px;
    }
    
    #style-7::-webkit-scrollbar {
        width: 5px;
        background-color: #F5F5F5;
    }
    
    #style-7::-webkit-scrollbar-thumb {
        border-radius: 5px;
        background-image: -webkit-gradient(linear,
          left bottom,
          left top,
          color-stop(0.44, rgb(122, 153, 217)),
          color-stop(0.72, rgb(73, 125, 189)),
          color-stop(0.86, rgb(28, 58, 148)));
    }
    
    span.prog-comp {
        position: relative;
        bottom: 12px;
        font-weight: 600;
    }
    
    .progress {
        margin-bottom: 18px;
        border-radius: 5px;
        -webkit-box-shadow: none;
        box-shadow: none;
        height: 9px;
        width: 100%;
    }
    
    .prog_font {
        font-size: 15px;
        font-weight: 600;
        color: #007bff;
        position: relative;
        top: 24px;
    }
    
    .prog_font1 {
        font-size: 15px;
        font-weight: 600;
        color: #00af73;
        position: relative;
        top: 30px;
    }
    
    .prog_font2 {
        font-size: 15px;
        font-weight: 600;
        color: #349ba9;
        position: relative;
        top: 25px;
    }
    
    .prog_font3 {
        font-size: 15px;
        font-weight: 600;
        color: #a73267;
        position: relative;
        top: 25px;
    }
    
    .prog_font4 {
        font-size: 15px;
        font-weight: 600;
        color: #f25127;
        position: relative;
        top: 25px;
    }
    
    .cust-progress {
        background-color: #d67c9d !important;
    }
    
    .prog_font-last {
        float: right;
        position: relative;
        top: 7px;
        left: 116px;
        font-size: medium;
        font-weight: 600;
    }
    
    .prog_font-last1 {
        float: right;
        position: relative;
        top: 0px;
        right: 21px;
        font-size: medium;
        font-weight: 600;
    }
    
    .progress_bottom {
        height: 60px;
        position: relative;
        bottom: 16px;
    }
    
    .cust_side_clr {
        background: linear-gradient(20deg, #2d929e, #36b7c7, #51becc);
        height: 260px;
        border-radius: 5px 0px 0px 5px;
        letter-spacing: 2px;
    }
    
    .cust-w {
        position: relative;
        left: 42px;
        width: 366px;
    }
    
    .cust-w1 {
        width: 366px;
    }
    
    .Cust_height {
        height: 435px;
    }
    
    .cust-progress-0 {
        background: #00af73;
    }
    
    .cust-progress-1 {
        background: #f25127;
    }
    
    .cust-progress-2 {
        background: #1ab7cb;
    }
    
    .box-hless {
        height: fit-content;
    }
    
    .ext {
        padding-bottom: 25px;
    }
    
    /*total sale page*/
    .sale_height {
        height: 87px;
        border-radius: 5px 5px 0px 0px;
        background: #ffffff;
    }
    
    button#daterange-btn {
        position: relative;
        bottom: 18px;
        background: #ffffff;
        border: 1px solid #2d9bda;
        font-family: "poppins";
    }
    
    button#action1 {
        position: relative;
        bottom: 17px;
        border: 1px solid #fd683e;
        color: black;
        font-family: sans-serif;
    }
    
    button#action1:hover {
        background: #fd683e;
        color: #fff;
    }
    
    button#daterange-btn:hover {
        background: #2d9bda;
        color: white;
    }
    
    /*.cust-func/-{
          display: block; 
          top: 241.219px; 
          right: 77px; 
          left: auto;
          padding: 12px;
          width: 14%;
          background-image: linear-gradient(45deg, #0fa3f7, #171819);
          position: absolute
          }*/
    /*mobile*/
    @media (max-width: 1199px) {
        .right-bar1 {
          right: -340px;
        }
    }
    
    @media (max-width: 991px) {
        .cust_fnt_side {
          top: 173px;
        }
    }
    
    @media (max-width: 767px) {
        .content {
          padding: 15px 10px 0px 10px;
        }
    
        .box-body {
          padding: 10px;
        }
    
        /*------*/
        .box {
          padding: 13px;
        }
    
        .sar_header {
          font-size: 21px;
        }
    
        .sub2 {
          font-size: 13px;
        }
    
        .sub3 {
          font-size: 11px;
        }
    
        .right2 {
          margin-top: 15px
        }
    
        .btn-sm2 {
          position: relative;
          right: 47px;
          padding: .55rem .75rem;
          font-size: 8px;
          line-height: 1.35;
          height: fit-content;
        }
    
        .side_b {
          font-size: 11px;
          font-weight: bolder;
        }
    
        .side_b1 {
          font-size: 12px;
        }
    
        .pos-side1 {
          position: relative;
          left: 58px;
        }
    
        .cust-w {
          position: relative;
          left: 0px;
          width: 100%;
        }
    
        .cust-w1 {
          width: 100%;
        }
    
        .cust_count {
          left: 70px;
        }
    
        .custmy_day1 {
          left: 70px;
        }
    
        .slide {
          left: 70px;
        }
    
        .cust2_num {
          left: 70px;
        }
    
        .my_day1 {
          left: 70px;
        }
    
        .cust_side_clr {
          height: 260px;
          border-radius: 7px 0px 0px 7px;
          position: relative;
          left: 6px;
          bottom: 10px;
        }
    
        .prog_title_side {
          font-size: 11px;
        }
    
        .prog_percent {
          font-size: 21px;
        }
    
        .prog_title {
          font-size: 17px;
        }
    
        .prog_font1 {
          position: relative;
          top: 23px;
        }
    
        .prog_font-last {
          position: relative;
          top: 1px;
          left: 96px;
        }
    
        .prog_font-last1 {
          position: relative;
          top: 0px;
          left: 6px;
        }
    
        .cust_fnt_side {
          position: relative;
          top: 200px;
        }
    
        .sale_height {
          height: 125px;
        }
    
        button#daterange-btn {
          font-size: 13px;
          padding: 8px;
          position: relative;
          bottom: 2px;
        }
    
        button#action1 {
          font-size: 13px;
          padding: 8px;
          position: relative;
          left: 75px;
        }
    
        /*.cust-func/- {
          width: 54%;
          display: block;
          padding: 6px;
          top: 349.291px;
          left: 87px;
          background-image: linear-gradient(45deg, #0fa3f7, #171819);
          position: absolute;
          }*/
    }
  </style>
  <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
    <div class="wrapper">
    @include('common.cashier_admin.navbar')
    @include('common.cashier_admin.sidebar')
    <div class="content-wrapper">
    <div class="container-full">
        <!-- Main content -->
        <section class="content">
          <div class=" ">
              @php
              $total_product_count = (isset($data) && !empty($data) && !empty($data['total_product_count'])) ? $data['total_product_count'] : 0;
              $total_customer_count = (isset($data) && !empty($data) && !empty($data['total_customer_count'])) ? $data['total_customer_count'] : 0;
              $total_revenue = (isset($data) && !empty($data) && !empty($data['total_revenue'])) ? $data['total_revenue'] : 0;
              $total_instoreorder_count = (isset($data) && !empty($data) && !empty($data['total_instoreorder_count'])) ? $data['total_instoreorder_count'] : 0;
              $total_instoreorder_lastmonthcount = (isset($data) && !empty($data) && !empty($data['total_instoreorder_lastmonthcount'])) ? $data['total_instoreorder_lastmonthcount'] : 0;
              $total_customer_thismonth = (isset($data) && !empty($data) && !empty($data['total_customer_thismonth'])) ? $data['total_customer_thismonth'] : 0;
              $total_revenue_today = (isset($data) && !empty($data) && !empty($data['total_revenue_today'])) ? $data['total_revenue_today'] : 0;
              $total_instoreorder_today_count = (isset($data) && !empty($data) && !empty($data['total_instoreorder_today_count'])) ? $data['total_instoreorder_today_count'] : 0;
              $total_instoreorder_process_today_count = (isset($data) && !empty($data) && !empty($data['total_instoreorder_process_today_count'])) ? $data['total_instoreorder_process_today_count'] : 0;
              $total_instoreorder_delivered_today_count = (isset($data) && !empty($data) && !empty($data['total_instoreorder_delivered_today_count'])) ? $data['total_instoreorder_delivered_today_count'] : 0;
              $total_instoreorder_cancelled_today_count = (isset($data) && !empty($data) && !empty($data['total_instoreorder_cancelled_today_count'])) ? $data['total_instoreorder_cancelled_today_count'] : 0;
              @endphp
              <div class="row">
                <div class="col-lg-4 col-12">
                    <div class="box">
                      <div class="box-body">
                          <div class="d-flex align-items-start">
                            <div>
                                <img src="{{asset('images/dashboard/Icons-07.png')}}" class="mr-20 pos-d" alt="" />
                            </div>
                            <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.product.index')}}">
                                <div>
                                  <h2 class="sar_header my-0">{{$total_product_count}}</h2>
                                  <p class="text-fade mb-0 sub2">Total Products</p>
                                  <p class="sub3 mb-0 text-success"><span class="badge badge-pill badge-success-light mr-5"><i class="fa fa-plus"></i></span>Add</p>
                                </div>
                            </a>
                          </div>
                      </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="box">
                      <div class="box-body">
                          <div class="d-flex align-items-start">
                            <div>
                                <img src="{{asset('images/dashboard/Icon-08.png')}}" class="mr-20 pos-d1" alt="" />
                            </div>
                            <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.index') }}">
                                <div>
                                  <h2 class="my-0 sar_header">{{$total_instoreorder_count}}</h2>
                                  <p class="text-fade mb-0 sub2">Instore Orders</p>
                                  <p class="sub3 mb-0 text-success"><span class="badge badge-pill badge-success-light mr-5"><i class="fa fa-plus"></i></span>Add</p>
                                </div>
                            </a>
                          </div>
                      </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="box">
                      <div class="box-body">
                          <div class="d-flex align-items-start">
                            <div>
                                <img src="{{asset('images/dashboard/Icons-09.png')}}" class="mr-20 pos-d" alt="" />
                            </div>
                            <div>
                                <h2 class="my-0 sar_header">0</h2>
                                <p class="text-fade mb-0 sub2">Online Orders	</p>
                                <p class="sub3 mb-0 text-success"><span class="badge badge-pill badge-success-light mr-5"><i class="fa fa-plus "></i></span>Add</p>
                            </div>
                          </div>
                      </div>
                    </div>
                </div>
              </div>
              <!-- task color -->
              <div class="row">
                <div class="col-lg-8 col-12">
                    <div class="mb-3">
                      <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.index') }}">
                          <div class="box-body my_cust" style="position: relative;width: 100%">
                            <div class="cust_fnt">Today's Orders</div>
                            <p class="my_day">{{date(' l j, F   Y ')}}</p>
                            <div class="my_amt">
                                ﷼{{number_format($total_revenue_today,2)}}
                            </div>
                            <hr class="un_line">
                            <div class="row d-flex justify-content-between">
                                <div class="col-md-3">
                                  <div class="my_day2">
                                      Ordered
                                  </div>
                                  <p class="my_day2">{{$total_instoreorder_today_count}}</p>
                                </div>
                                <div class="col-md-3">
                                  <div class="my_day2">
                                      Process
                                  </div>
                                  <p class="my_day2">{{$total_instoreorder_process_today_count}}</p>
                                </div>
                                <div class="col-md-3">
                                  <div class="my_day2">
                                      Delivered
                                  </div>
                                  <p class="my_day2">{{$total_instoreorder_delivered_today_count}}</p>
                                </div>
                                <div class="col-md-3">
                                  <div class="my_day2">
                                      Cancelled
                                  </div>
                                  <p class="my_day2">{{$total_instoreorder_cancelled_today_count}}</p>
                                </div>
                            </div>
                          </div>
                      </a>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="mb-3">
                      <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.reports.customer-report') }}">
                          <div class="box-body my_cust1" style="position: relative;width: 100%">
                            <div class="row">
                                <div class="col-md-3 col-3 ">
                                  <div class="cust_side_clr">
                                      <div class="cust_fnt_side">
                                        Total&nbsp;Orders
                                      </div>
                                  </div>
                                </div>
                                <div class="col-md-9 col-6">
                                  <div class="my_day1">
                                      This Month
                                  </div>
                                  <div class="cust2_num">{{$total_instoreorder_lastmonthcount}}</div>
                                  <img src="{{asset('images/dashboard/Graphs.png')}}" class="slide" alt="" />
                                  <div class="custmy_day1">
                                      Right Now
                                  </div>
                                  <div class="cust_count">
                                      {{$total_instoreorder_count}}
                                  </div>
                                </div>
                            </div>
                          </div>
                    </div>
                    </a>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                    <div class="box box-hless mb-3">
                      <div class="box-body ext" style="position: relative;width: 100%">
                          <div class="prog_title">
                            Order Status
                          </div>
                          <div class="prog_title_side">
                            Total Performance<br><span class="prog_percent">52%</span>
                          </div>
                          <div class="prog_bar">
                            <div class="progress_bottom1">
                                <div class="prog_font1">
                                  Ordered
                                </div>
                                <div class="prog_font-last">20%</div>
                                <div class="progress">
                                  <div class="progress-bar cust-progress-0" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <!-- <span class="prog-comp">20% completed</span> -->
                            </div>
                            <div class="progress_bottom">
                                <div class="prog_font">
                                  Preparing   
                                </div>
                                <div class="prog_font-last1">40%</div>
                                <div class="progress">
                                  <div class="progress-bar " role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <!-- <span class="prog-comp">40% completed</span> -->
                            </div>
                            <div class="progress_bottom">
                                <div class="prog_font2">
                                  Ready to Pickup
                                </div>
                                <div class="prog_font-last1">60%</div>
                                <div class="progress">
                                  <div class="progress-bar cust-progress-2" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <!-- <span class="prog-comp">60% completed</span> -->
                            </div>
                            <div class="progress_bottom">
                                <div class="prog_font3">
                                  Delivered
                                </div>
                                <div class="prog_font-last1">90%</div>
                                <div class="progress">
                                  <div class="progress-bar cust-progress" role="progressbar" style="width: 90%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <!-- <span class="prog-comp">90% completed</span> -->
                            </div>
                            <div class="progress_bottom">
                                <div class="prog_font4">
                                  Cancelled
                                </div>
                                <div class="prog_font-last1">100%</div>
                                <div class="progress">
                                  <div class="progress-bar cust-progress-1" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <!-- <span class="prog-comp">100% completed</span> -->
                            </div>
                          </div>
                      </div>
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="box mb-3">
                      <div class="box-body" style="position: relative;width: 100%">
                          <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="box-title mb-0">Total Revenue</h4>
                            </div>
                            <div class="text-right">
                                <h3 class="box-title mb-0 font-weight-700">﷼ {{number_format($total_revenue,2)}}</h3>
                            </div>
                          </div>
                          <div id="chart-1" class=""></div>
                      </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="box mb-3">
                      <div class="box-body" style="position: relative;width: 100%">
                          <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="box-title mb-0">Gross Profit</h4>
                            </div>
                            <div class="text-right">
                                <h3 class="box-title mb-0 font-weight-700">﷼ {{number_format(1123,2)}} </h3>
                            </div>
                          </div>
                          <div id="chart-2" class=""></div>
                      </div>
                    </div>
                </div>
              </div>
          </div>
        </section>
        <!-- right2 -->
        <section>
        <div class="container">
          <div class="row">
              <div class="right-bar1">
                <div class="scrollbar" id="style-7">
                    <div class="force-overflow">
                      <div class="col-12 col-md-12">
                          <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 100%;">
                            <div id="sidebarRight" style="overflow: hidden; width: auto; height: 100%;">
                                <div class="right-bar-inner right2">
                                  <div class="text-end position-relative">
                                      <a href="#" class="d-inline-block d-xl-none btn right-bar-btn waves-effect waves-circle btn btn-circle btn-danger float-end">
                                      <i class="fa fa-times-circle"></i>
                                      </a>
                                  </div>
                                  <div class="right-bar-content">
                                      <div class="box no-shadow box-bordered border-light">
                                        <div class="box-body1">
                                            <div class="d-flex justify-content-between align-items-center">
                                              <div>
                                                  <h5 class="mx-2 side_head">Total Sales </h5>
                                                  <h2 class="mb-0 mx-2 side_sub"> ﷼{{number_format($total_revenue,2)}}</h2>
                                              </div>
                                              <div class="">
                                                  <img src="{{asset('images/dashboard/Graphs-07.png')}}" class="sidebar_img mr-20 pos-side" alt="" />
                                              </div>
                                            </div>
                                            <div class="box-footer">
                                              <div class="d-flex align-items-center justify-content-between">
                                                  <h5 class="my-0 side_b">{{$total_instoreorder_count}} Orders</h5>
                                                  <a href="#" class="mb-0 side_b1">View Report</a>
                                              </div>
                                            </div>
                                        </div>
                                      </div>
                                      <div class="box no-shadow box-bordered border-light">
                                        <div class="box-body1">
                                            <div class="d-flex justify-content-between align-items-center">
                                              <div>
                                                  <h5 class="mx-2 side_head">Instore Sales</h5>
                                                  <h2 class="mb-0 mx-2 side_sub">﷼{{number_format($total_revenue,2)}}</h2>
                                              </div>
                                              <div class="">
                                                  <img src="{{asset('images/dashboard/Graphs-01.png')}}" class="sidebar_img mr-20 pos-side" alt="" />
                                              </div>
                                            </div>
                                            <div class="box-footer">
                                              <div class="d-flex align-items-center justify-content-between">
                                                  <h5 class="my-0 side_b">{{$total_instoreorder_count}} Orders</h5>
                                                  <a href="#" class="mb-0 side_b1">View Report</a>
                                              </div>
                                            </div>
                                        </div>
                                      </div>
                                      <div class="box no-shadow box-bordered border-light">
                                        <div class="box-body1">
                                            <div class="d-flex justify-content-between align-items-center">
                                              <div>
                                                  <h5 class="mx-2 side_head">Online Sales</h5>
                                                  <h2 class="mb-0 mx-2 side_sub">﷼0.00</h2>
                                              </div>
                                              <div class="">
                                                  <img src="{{asset('images/dashboard/Graphs-02.png')}}" class="sidebar_img mr-20 pos-side" alt="" />
                                              </div>
                                            </div>
                                            <div class="box-footer">
                                              <div class="d-flex align-items-center justify-content-between">
                                                  <h5 class="my-0 side_b">0 Orders</h5>
                                                  <a href="#" class="mb-0 side_b1">View Report</a>
                                              </div>
                                            </div>
                                        </div>
                                      </div>
                                      <div class="box no-shadow box-bordered border-light">
                                        <div class="box-body1">
                                            <div class="d-flex justify-content-between align-items-center">
                                              <div>
                                                  <h5 class="mx-2 side_head">Product Sales</h5>
                                                  <h2 class="mb-0 mx-2 side_sub">﷼0.00</h2>
                                              </div>
                                              <div class="">
                                                  <img src="{{asset('images/dashboard/Graphs-03.png')}}" class="sidebar_img mr-20 pos-side" alt="" />
                                              </div>
                                            </div>
                                            <div class="box-footer">
                                              <div class="d-flex align-items-center justify-content-between">
                                                  <h5 class="my-0 side_b">0 Orders</h5>
                                                  <a href="#" class="mb-0 side_b1">View Report</a>
                                              </div>
                                            </div>
                                        </div>
                                      </div>
                                  </div>
                                </div>
                            </div>
                          </div>
                      </div>
                    </div>
                </div>
              </div>
          </div>
        </div>
        @include('common.cashier_admin.copyright')
    </div>
    @include('common.cashier_admin.footer')
    <script>
        <!-- chart 2 -->
        
        var options = {
                series: [{
                name: 'Products',
                data: [31, 50, 28, 70, 45, 90, 140]
              }],
                chart: {
                height: 200,
                type: 'area',
          zoom: {
          enabled: false
        },
              },
        colors: ["#7c4bc9"],
              dataLabels: {
                enabled: false
              },
              stroke: {
                curve: 'smooth'
              },
              xaxis: {
                categories: ["Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", "Jan", "Feb", "Mar", "Apr", "May"]
              },
              tooltip: {
              y: {
                  formatter: function (val) {
                    return "" + val + " "
                  }
                },
              },
              };
        
              var chart = new ApexCharts(document.querySelector("#chart-2"), options);
              chart.render();
        
          // chart2 //
        
          var options = {
                series: [{
                name: 'Customers',
                data: [31, 50, 28, 70, 45, 90, 140]
              }],
                chart: {
                height: 200,
                type: 'area',
          zoom: {
          enabled: false
        },
              },
        colors: ["#e63a67"],
              dataLabels: {
                enabled: false
              },
              stroke: {
                curve: 'smooth'
              },
              xaxis: {
                categories: ["Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", "Jan", "Feb", "Mar", "Apr", "May"]
              },
              tooltip: {
              y: {
                  formatter: function (val) {
                    return "" + val + " k"
                  }
                },
              },
              };
        
              var chart = new ApexCharts(document.querySelector("#chart-1"), options);
              chart.render();
    </script>
  </body>
</html>