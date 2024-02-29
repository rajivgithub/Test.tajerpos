<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ trans('admin.super_admin') }}</title>
        @include('common.admin.header')
    </head>
    <body>
        <div class="page-loader"><div class="spinner"></div></div>  
        <div class="screen-overlay"></div>
        @include('common.admin.navbar')
        <main class="main-wrap">
            @include('common.admin.sidebar')
            <section class="content-main">                         
                <h3 class="content-title card-title mb-4">{{ trans('admin.super_admin') }}</h3> 
                @php
                    $total_store_count = (isset($data) && !empty($data) && !empty($data['total_store_count'])) ? $data['total_store_count'] : 0;
                    $active_store_count = (isset($data) && !empty($data) && !empty($data['active_store_count'])) ? $data['active_store_count'] : 0;
                    $inactive_store_count = (isset($data) && !empty($data) && !empty($data['inactive_store_count'])) ? $data['inactive_store_count'] : 0;
                    $total_revenue = (isset($data) && !empty($data) && !empty($data['total_revenue'])) ? $data['total_revenue'] : 0;
                    $month_based_revenue = (isset($data) && !empty($data) && !empty($data['month_based_revenue'])) ? $data['month_based_revenue'] : 0;
                @endphp
                <input type="hidden" class="total_store_count" value="{{$total_store_count}}">
                <input type="hidden" class="active_store_count" value="{{$active_store_count}}">
                <input type="hidden" class="inactive_store_count" value="{{$inactive_store_count}}">
                <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <div class="main-tiles border-5 border-0  card-hover card o-hidden" style="box-shadow: 0 0 10px rgb(57 139 247 / 29%);">
                            <div class="custome-1-bg b-r-4 card-body">
                                <div class="media align-items-center static-top-widget">                                       
                                    <div class="align-self-center text-center">
                                        <img src="{{ URL::asset('assets/imgs/icons/icon-3.png') }}">
                                    </div>
                                    <div class="media-body p-0">
                                        <span class="m-0">{{ trans('admin.stores') }}</span>
                                        <h4 class="mb-0 counter">{{ $total_store_count }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="main-tiles border-5 border-0  card-hover card o-hidden" style="box-shadow: 0 0 10px rgb(1 194 147 / 29%);">
                            <div class="custome-1-bg b-r-4 card-body">
                                <div class="media align-items-center static-top-widget">
                                    <div class="align-self-center text-center" style="background-color: #01c293;">
                                        <img src="{{ URL::asset('assets/imgs/icons/icon-2.png') }}">
                                    </div>
                                    <div class="media-body p-0">
                                        <span class="m-0">{{ trans('admin.active_store') }}</span>
                                        <h4 class="mb-0 counter">{{ $active_store_count }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="main-tiles border-5 border-0  card-hover card o-hidden" style="box-shadow: 0 0 10px rgb(238 83 79 / 29%);">
                            <div class="custome-1-bg b-r-4 card-body">
                                <div class="media align-items-center static-top-widget">
                                    <div class="align-self-center text-center" style="background-color: #ee534f;">
                                        <img src="{{ URL::asset('assets/imgs/icons/icon-1.png') }}">
                                    </div>
                                    <div class="media-body p-0">
                                        <span class="m-0">{{ trans('admin.inactive_store') }}</span>
                                        <h4 class="mb-0 counter">{{ $inactive_store_count }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="main-tiles border-5 border-0  card-hover card o-hidden" style="box-shadow: 0 0 10px rgb(118 48 255 / 28%);">
                            <div class="custome-1-bg b-r-4 card-body">
                                <div class="media align-items-center static-top-widget">
                                    <div class="align-self-center text-center" style="background-color:#7630ff">
                                        <img src="{{ URL::asset('assets/imgs/icons/icon-4.png') }}">
                                    </div>
                                    <div class="media-body p-0">
                                        <span class="m-0">{{ trans('admin.total_revenue') }}</span>
                                        <h4 class="mb-0 counter">SAR {{ $total_revenue }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="card mb-4 shadow23">  
                            <div class="card-body" style="height: 420px">
                                <h5 class="card-title text-center">{{ trans('admin.stores') }}</h5>
                                <hr/>
                                <canvas id="chart-line" width="299" height="200" class="chartjs-render-monitor" style="display: block; width: 299px; height: 200px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="card mb-4 shadow23">   
                            <div class="card-body" style="height: 420px">
                                <h5 class="card-title text-center">{{ trans('admin.revenue') }}</h5>
                                <hr/>
                                <canvas id="chart-bar" width="299" height="200" class="chartjs-render-monitor" style="display: block; width: 299px; height: 200px;"></canvas>
                            </div>
                        </div>     
                    </div>
                </div>
            </section>
            @include('common.admin.footer')
        </main>
        @include('common.admin.script')
        <script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.bundle.min.js'></script>
        <script>
            $(document).ready(function() {
                var ctx = $("#chart-line");
                total_store_count = $(".total_store_count").val();
                active_store_count = $(".active_store_count").val();
                inactive_store_count = $(".inactive_store_count").val();
                var myLineChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: @json([ trans('admin.total_store'), trans('admin.total_approved_store'), trans('admin.total_pending_store')]),
                        datasets: [{
                            data: [total_store_count, active_store_count, inactive_store_count],
                            backgroundColor: ["#f3cc6f", "#926dde", "#ec96a3"]
                        }]
                    },
                    options: {
                        cutoutPercentage: 80,
                        legend: {
                            labels: {
                            
                                boxWidth: 10,
                                usePointStyle: true,
                            },
                            onClick: function () {
                                return '';
                            },
                            position: 'bottom',
                        }
                    }
                });
            });
        </script>
        <script>
            var month_based_revenue = <?php echo json_encode($month_based_revenue); ?>;
            var ctx = document.getElementById("chart-bar");
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ["jan", "feb", "mar", "apr", "may", "jun","jul","agu","sep","oct","nov","dec"],
                    datasets: [{
                        label: '',
                        data: month_based_revenue,
                        backgroundColor: ["#01c293","#01c293", "#01c293", "#01c293", "#01c293", "#01c293", "#01c293", "#01c293", "#01c293", "#01c293", "#01c293", "#01c293", "#01c293"]
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem) {
                            console.log(tooltipItem)
                                return tooltipItem.yLabel;
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display:false
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                display:false
                            }   
                        }]
                    }
                }  
            });
        </script>
    </body>
</html>