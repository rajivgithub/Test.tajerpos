<!DOCTYPE html>
<html lang="en">
    <head>
        @include('common.cashier_admin.header')
    </head>
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <section class="content">
                        <div class="card mb-4">
                            <div class="content-header">
                                <div class="d-flex align-items-center">
                                    <div class="mr-auto">
                                        <h3 class="page-title">Analytics</h3>
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="card-body analyst-report">
                                <div class="row">
                                    <input type="hidden" class="analyst-report-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.analyst-report') }}">
                                    <div class="col-lg-3 col-md-3 me-auto">
                                        <div class="mb-3">
                                            <label class="form-label ">Start Date</label>
                                            <input type="date" placeholder="Type here" class="form-control analyst-start-date">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 me-auto">
                                        <div class="mb-3">
                                            <label class="form-label ">End Date</label>
                                            <input type="date" placeholder="Type here" class="form-control analyst-end-date"> 
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3">
                                        <div class="mb-3">
                                            <label for="product_name" class="form-label">Filter <span>*</span></label>
                                            <select class="form-control analyst-filter-type">
                                                <!-- <option>Revenue</option> -->
                                                <option value="sales">Sales</option>
                                                <option value="top_products">Top Products</option>
                                                <!-- <option>Profit</option> -->
                                                <option value="sales_tax">Sales tax</option>
                                                <option value="payment_method">Payment Method</option>
                                                <option value="top_customer">Top Customer</option>
                                                <option value="sales_by_staff">Sales by staff</option>
                                                <!-- <option>Average Ticket Size</option> -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3" style="margin-top:20px;">
                                        <div class="mb-3">
                                            <button class="btn btn-primary analyst-report-btn" type="button"> <span>Search</span> </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4 align-items-center analyst-report-result">
                                    <!-- <div class="col-lg-4 col-12">
                                        <div class="box bg-gradient-primary">
                                            <div class="box-body">
                                                <h4 class="text-white mb-50">Revenue Overview </h4>
                                                <div class="d-flex justify-content-between align-items-end">
                                                    <div class="d-flex">
                                                        <div class="icon">
                                                            <i class="fa fa-trophy"></i>
                                                        </div>
                                                        <div>
                                                            <h3 class="font-weight-600 text-white mb-0 mt-0">34040</h3>
                                                            <p class="text-white-50">Revenue</p>
                                                            <h5 class="text-white">+34040 <span class="ml-40"><span class="text-white-50">0.036%</span></span> </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    
                                    <div class="col-lg-8 col-12">
                                        <div id="chart"></div>
                                    </div>-->
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>	  
            @include('common.cashier_admin.copyright')
        </div>
        @include('common.cashier_admin.footer')
        <script>
            $(document).on("click",".analyst-report-btn",function() { 
                _this = $(this);
                analyst_start_date = _this.closest(".analyst-report").find(".analyst-start-date").val();
                analyst_end_date = _this.closest(".analyst-report").find(".analyst-end-date").val();
                analyst_filter_type = _this.closest(".analyst-report").find(".analyst-filter-type").val();
                analyst_report_url = _this.closest(".analyst-report").find(".analyst-report-url").val();
                $.ajax({
                    url: analyst_report_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,analyst_start_date: analyst_start_date,analyst_end_date:analyst_end_date,analyst_filter_type:analyst_filter_type},
                    success: function(response){
                        analyst_report_result = '';
                        if(analyst_filter_type == "sales" || analyst_filter_type == "sales_tax") {
                            analyst_report_result += '<div class="col-lg-4 col-12"><div class="box bg-gradient-primary"><div class="box-body"><h4 class="text-white mb-50">'+response.title+'</h4><div class="d-flex justify-content-between align-items-end"><div class="d-flex"><div class="icon"><i class="fa fa-trophy"></i></div><div><h3 class="font-weight-600 text-white mb-0 mt-0">SAR '+response.total_sale_amount+'</h3><p class="text-white-50">'+response.sub_title+'</p></div></div></div></div></div></div>';
                        }
                        if(analyst_filter_type == "top_products") {
                            analyst_result = response.analyst_result;
                            if(analyst_result.length > 0) {
                                $(analyst_result).each(function(key,val) {
                                    analyst_report_result += '<div class="col-lg-4 col-12"><div class="box bg-gradient-primary"><div class="box-body"><h4 class="text-white mb-50">'+response.title+'</h4><div class="d-flex justify-content-between align-items-end"><div class="d-flex"><div class="icon"><i class="fa fa-shopping-bag"></i></div><div><a href="'+val.url+'" target="_blank"><h3 class="font-weight-600 text-white mb-0 mt-0">'+val.product_name+'</h3></a><p class="text-white-50">'+response.sub_title+'</p></div></div></div></div></div></div>';
                                });
                            } else 
                                analyst_report_result += '<div class="col-lg-4 col-12"><p>No top products found.</p></div>';
                        } 
                        if(analyst_filter_type == "payment_method") {
                            total_amount = response.total_amount;
                            title = response.title;
                            sub_title = response.sub_title;
                            if(total_amount.length > 0) {
                                $(total_amount).each(function(key,val) {
                                    analyst_report_result += '<div class="col-lg-4 col-12"><div class="box bg-gradient-primary"><div class="box-body"><h4 class="text-white mb-50">'+title[key]+'</h4><div class="d-flex justify-content-between align-items-end"><div class="d-flex"><div class="icon"><i class="fa fa-credit-card"></i></div><div><h3 class="font-weight-600 text-white mb-0 mt-0">'+val+'</h3><p class="text-white-50">'+sub_title[key]+'</p></div></div></div></div></div></div>';
                                });
                            }
                        }
                        if(analyst_filter_type == "top_customer") {
                            analyst_result = response.analyst_result;
                            if(analyst_result.length > 0) {
                                $(analyst_result).each(function(key,val) {
                                    analyst_report_result += '<div class="col-lg-4 col-12"><div class="box bg-gradient-primary"><div class="box-body"><h4 class="text-white mb-50">'+response.title+'</h4><div class="d-flex justify-content-between align-items-end"><div class="d-flex"><div class="icon"><i class="fa fa-user"></i></div><div><h3 class="font-weight-600 text-white mb-0 mt-0">'+val.customer_name+'</h3><p class="text-white-50">'+val.phone_number+'</p></div></div></div></div></div></div>';
                                });
                            } else 
                                analyst_report_result += '<div class="col-lg-4 col-12"><p>No top customers found.</p></div>';
                        } 
                        if(analyst_filter_type == "sales_by_staff") {
                            analyst_result = response.analyst_result;
                            if(analyst_result.length > 0) {
                                $(analyst_result).each(function(key,val) {
                                    analyst_report_result += '<div class="col-lg-4 col-12"><div class="box bg-gradient-primary"><div class="box-body"><h4 class="text-white mb-50">'+response.title+'</h4><div class="d-flex justify-content-between align-items-end"><div class="d-flex"><div class="icon"><i class="fa fa-user"></i></div><div><h3 class="font-weight-600 text-white mb-0 mt-0">'+val.name+'</h3><p class="text-white-50">'+val.email+'</p><p class="text-white-50">'+val.phone_number+'</p></div></div></div></div></div></div>';
                                });
                            } else 
                                analyst_report_result += '<div class="col-lg-4 col-12"><p>No top staff found.</p></div>';
                        } 
                        _this.closest(".analyst-report").find(".analyst-report-result").html(analyst_report_result);
                    }
                });
            });
        </script>
    </body>
</html>