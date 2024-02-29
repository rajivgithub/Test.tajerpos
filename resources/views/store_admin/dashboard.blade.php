<!DOCTYPE html>
<html lang="en">
    <head>
        @include('common.store_admin.header')
    </head>
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.store_admin.navbar')
            @include('common.store_admin.sidebar')
            <div class="content-wrapper">
                <div class="container-full">
                    <section class="content">
                        <div class="row">
                            <div class="col-xxxl-5 col-xl-5 col-lg-5 col-12">
                                <div class="box mb-3">
                                    <div class="box-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="box-title mb-0">Total Revenue</h4>
                                            </div>
                                            <div class="text-right">
                                                <h3 class="box-title mb-0 font-weight-700">SAR 154K</h3>
                                            </div>
                                        </div>
                                        <div id="chart" class=""></div>
                                    </div>
                                </div>
                                <div class="box mb-3">
                                    <div class="box-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="box-title mb-0">Total Products</h4>
                                            </div>
                                            <div class="text-right">
                                                <h3 class="box-title mb-0 font-weight-700">800</h3>
                                            </div>
                                        </div>
                                        <div id="chart-2" class=""></div>
                                    </div>
                                    </div>
                                <div class="box">
                                    <div class="box-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="box-title mb-0">Total Customers</h4>
                                            </div>
                                            <div class="text-right">
                                                <h3 class="box-title mb-0 font-weight-700">4.5k</h3>
                                            </div>
                                        </div>
                                        <div id="chart-1" class=""></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxxl-7 col-xl-7 col-lg-7 col-12">
                                <div class="box">
                                    <div class="box-body">
                                        <h4 class="box-title">Store Customer</h4>
                                        <hr/>
                                        <div id="chart-3" class=""></div>
                                    </div>
                                </div>
                                <div class="box">
                                    <div class="box-body">
                                        <h4 class="box-title">Online Customer</h4>
                                        <hr/>
                                        <div id="chart-4" class=""></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            @include('common.store_admin.copyright')
        </div>
        @include('common.store_admin.footer')
        <script>
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
            var options = {
                series: [44, 55, 41],
                chart: {
                    width: 480,
                    type: 'donut',
                },
                plotOptions: {
                    pie: {
                        startAngle: -90,
                        endAngle: 270
                    }
                },
                dataLabels: {
                    enabled: false
                },
                fill: {
                    type: 'gradient',
                },
                legend: {
                    formatter: function(val, opts) {
                        return val + " - " + opts.w.globals.series[opts.seriesIndex]
                    }
                },
                labels: ["Total Customers", "Total Products", "Total Sales"],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                        width: 200
                        },
                        legend: {
                        position: 'bottom'
                        }
                    }
                }]
            };
            var chart = new ApexCharts(document.querySelector("#chart-3"), options);
            chart.render();
            var options = {
                series: [44, 55, 13],
                chart: {
                    width: 480,
                    type: 'pie',
                },
                labels: ["Total Customers", "Total Orders", "Total Sales"],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                        width: 200
                        },
                        legend: {
                        position: 'bottom'
                        }
                    }
                }]
            };
            var chart = new ApexCharts(document.querySelector("#chart-4"), options);
            chart.render();
        </script>
    </body>
</html>
