<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        @include('common.customer.header')
    </head>
    <body>
        <div class="body_overlay"></div>
        @include('common.customer.mobile_navbar')
        @include('common.customer.navbar')
        @include('common.customer.mini_cart')
        @include('common.customer.breadcrumbs')
        <input type="hidden" class="translation-key" value="view_order_page_title">
        <div class="checkout-area">
            <div class="container">
                <h4 class="mb-3">{{ __('customer.order_details') }}</h4>
                <div class="row">
                    <div class="col-lg-3">
                        <p class="mb-2">{{ __('customer.ordered_on') }} {{ (isset($order_details) && !empty($order_details) && isset($order_details[0]['ordered_at'])) ? DateTime::createFromFormat('d-m-Y H:i', $order_details[0]['ordered_at'])->format('d F Y') : "" }}</p>
                    </div>
                    <div class="col-lg-6">
                        <p class="mb-2">{{ __('customer.order') }} # {{ (isset($order_details) && !empty($order_details) && isset($order_details[0]['order_number'])) ? $order_details[0]['order_number'] : "" }}</p>
                    </div>
                    <!-- <div class="col-lg-3 text-end">
                        <p class="mb-2 fw-bold"><a class="text-primary" href="#"><i class="fa fa-download"></i> Invoice</a></p>
                    </div> -->
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <p class="mb-2 text-dark fw-bold">{{ __('customer.shipping_address') }}</p>
                                <?php
                                    $shipping_Address = '';
                                    if(isset($order_details) && !empty($order_details) && isset($order_details[0]['building_name']))
                                        $shipping_Address .= $order_details[0]['building_name'].",<br/>"; 
                                    if(isset($order_details) && !empty($order_details) && isset($order_details[0]['street_name']))
                                        $shipping_Address .= $order_details[0]['street_name'].",<br/>"; 
                                    if(isset($order_details) && !empty($order_details) && isset($order_details[0]['city_name']))
                                        $shipping_Address .= $order_details[0]['city_name'].",";
                                    if(isset($order_details) && !empty($order_details) && isset($order_details[0]['state_name']))
                                        $shipping_Address .= $order_details[0]['state_name'].",<br/>"; 
                                    if(isset($order_details) && !empty($order_details) && isset($order_details[0]['pincode']))
                                        $shipping_Address .= $order_details[0]['pincode'].",<br/>"; 
                                    if(isset($order_details) && !empty($order_details) && isset($order_details[0]['country_name']))
                                        $shipping_Address .= $order_details[0]['country_name'];
                                ?>
                                <p class="mb-0">{{ (isset($order_details) && !empty($order_details) && isset($order_details[0]['customer_name'])) ? $order_details[0]['customer_name'] : "" }}</p>
                                <p class="mb-0">{!! $shipping_Address !!}</p>
                            </div>
                            <!-- <div class="col-lg-4 mb-3">
                                <p class="mb-2 text-dark fw-bold">Payment Method</p>
                                <p class="mb-0">Amazon Pay balance </p>
                            </div> -->
                            <div class="col-lg-4 mb-3">
                                <p class="mb-2 text-dark fw-bold">{{ __('customer.order_summery') }}</p>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0">{{ __('customer.items_subtotal') }}: </p>
                                    <p class="mb-0">SAR {{ (isset($order_details) && !empty($order_details) && isset($order_details[0]['sub_total_amount'])) ? number_format($order_details[0]['sub_total_amount'], 2, '.', '') : "" }} </p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0">{{ __('customer.tax') }}: </p>
                                    <p class="mb-0">SAR {{ (isset($order_details) && !empty($order_details) && isset($order_details[0]['total_tax_amount'])) ? number_format($order_details[0]['total_tax_amount'], 2, '.', '') : "" }} </p>
                                </div>
                                <!-- <div class="d-flex justify-content-between"> 
                                    <p class="mb-0">Total: </p>
                                    <p class="mb-0">SAR 0.00 </p>
                                </div> -->
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0 fw-bold">{{ __('customer.grand_total') }}: </p>
                                    <p class="mb-0 fw-bold">SAR {{ (isset($order_details) && !empty($order_details) && isset($order_details[0]['total_amount'])) ? number_format($order_details[0]['total_amount'], 2, '.', '') : "" }} </p>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <p class="mb-2 text-dark fw-bold">{{ __('customer.payment_details') }}</p>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0">{{ __('customer.transaction_ref') }}: </p>
                                    <p class="mb-0">{{$payment_details->tran_ref}}</p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0">{{ __('customer.transaction_time') }}: </p>
                                    <p class="mb-0">{{$payment_details->created_at}}</p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0">{{ __('customer.card') }}: </p>
                                    <p class="mb-0">{{$payment_details->payment_method}}</p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="mb-0">{{ __('customer.account') }}: </p>
                                    <p class="mb-0">{{$payment_details->payment_desc}}</p>
                                </div>
                                <div class="d-flex justify-content-center mt-2">
                                    @if($payment_details->response_status == 'A')
                                    <p class="mb-0" style="color:green" ><strong>Payment successfully</strong> </p>

                                    @elseif($payment_details->response_status == 'C')
                                    <p class="mb-0" style="color:red" ><strong>Payment Cancelled</strong> </p>
                                    @else
                                    <p class="mb-0" style="color:orange" ><strong>Declined - {{$payment_details->response_message}}</strong> </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>{{ (isset($order_details) && !empty($order_details) && isset($order_details[0]['status_name'])) ? $order_details[0]['status_name'] : "" }}</h5> 
                        <!-- <p>Package was handed to resident</p> -->
                        @if(isset($order_details) && !empty($order_details))
                            @foreach($order_details as $order)
                                @php
                                    $singleProductURL = url("{$store_url}/customer/single-product/category/{$order['category_id']}/" . strtolower(str_replace(' ', '-', $order['category_name'])) . "/product/{$order['product_id']}/" . strtolower(str_replace(' ', '-', $order['product_name'])));
                                @endphp
                                <div class="row mb-2">
                                    <div class="col-lg-9">
                                        <div class="d-md-flex deliery-cont">
                                            <a class="mb-0" href="{{ $singleProductURL }}" target="_blank"><img style="width:120px;" src="{{ $order['image_path'] }}" ></a>
                                            <div class="ms-4">
                                                <a class="mb-0" href="{{ $singleProductURL }}" target="_blank">{{ $order['product_name'] }}</a>
                                                <p class="mb-2 fw-bold">SAR {{ number_format(($order['sub_total'] / $order['quantity']), 2, '.', '') }}</p> 
                                                @if(!empty($order['product_variants']))
                                                    <p class="mb-2">{{ $order['variants_name']." : " }} <span class="fw-bold">{{ $order['product_variants'] }}</span></p>
                                                @endif
                                                <!-- <button class="btn btn-warning text-white flex-shrink-0">Buy it again</button> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <!-- <button type="button" class="btn btn-outline-secondary mb-2 w-100">Leave seller feedback</button>
                                        <button type="button" class="btn btn-outline-secondary mb-2 w-100">Write a product review</button>
                                        <button type="button" class="btn btn-outline-secondary mb-2 w-100">Archive order</button> -->
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @include('common.customer.footer')
        @include('common.customer.script')
    </body>
</html>