<!DOCTYPE html>
<html lang="en">
    <head>
<!-- Vendors Style-->
<link rel="stylesheet" href="{{ URL::asset('assets/cashier-admin/css/vendors_css.css') }}">
<!-- Style-->  
<link rel="stylesheet" href="{{ URL::asset('assets/cashier-admin/css/style.css') }}">
        <style>
            @media print {
                .progressbar-wrapper,.print-order-invoice,.main-footer {
                    display: none !important; 
                }
            }
        </style>
    </head>
 
    <body class="">
        <div class="">
            <div class="" >
                <div class="container-full">
                    <section class="content ">
                        <div class="row ">
                            <div class="col-md-6 offset-md-3 col-xs-12">
                                <div class="card mb-4 mt-45">
                                    <div class="card-body  p-4">
                                        <div id="invoice-POS">
                                            <center>
                                                <img src="{{$store_logo}}" class="logo" alt="eMonta">
                                            </center>
                                            <hr/>
                                            <div id="mid">
                                                <div class="info">
                                                    <div class="row justify-content-between">
                                                        <?php 
                                                            $address = '';
                                                            if(isset($address_details) && !empty($address_details)) {
                                                                if(!empty($address_details[0]->store_address))
                                                                    $address .= $address_details[0]->store_address.',';
                                                                if(!empty($address_details[0]->city_name))
                                                                    $address .= $address_details[0]->city_name.',';
                                                                if(!empty($address_details[0]->state_name))
                                                                    $address .= $address_details[0]->state_name.',';
                                                                if(!empty($address_details[0]->country_name))
                                                                    $address .= $address_details[0]->country_name;
                                                            }
                                                        ?>
                                                        <div class="col-md-8">
                                                            <h6>{{ isset($address_details) && !empty($address_details) && !empty($address_details[0]['store_name']) ? $address_details[0]['store_name'] : 'eMonta' }}</h6>
                                                            <p>{{$address}}</p>
                                                            <p>Cashier: </p>
                                                            <p class="order-type-name">Order: {{!empty($store_order_details) && !empty($store_order_details[0]->status_name) ? $store_order_details[0]->status_name : '-' }}</p>
                                                            <p>Welcome</p>
                                                            <p class="mb-0">Simplified tax invoice</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p>Receipt #: {{!empty($store_order_details) && !empty($store_order_details[0]->order_number) ? $store_order_details[0]->order_number : '-' }}</p>
                                                            <p class="billing-date">Date: {{!empty($store_order_details) && !empty($store_order_details[0]->created_at) ? date('d M Y H:i:s', strtotime(trim($store_order_details[0]->created_at))) : '-' }}</p>
                                                            <p>Customer Name: {{!empty($store_order_details) && !empty($store_order_details[0]->customer_name) ? $store_order_details[0]->customer_name : '-' }}</p>
                                                            <p>Phone Number: {{!empty($store_order_details) && !empty($store_order_details[0]->phone_number) ? $store_order_details[0]->phone_number : '-' }}</p>
                                                        </div>
                                                        <br/>
                                                    </div>
                                                </div>
                                                <hr class="pos-invc">
                                                <div class="d-flex justify-content-between">
                                                    <div class="col-3 p-0">
                                                        <p class="mb-0">Items</p>
                                                    </div>
                                                    <div class="col-3 p-0">
                                                        <p class="mb-0">Variants</p>
                                                    </div>
                                                    <div class="col-2 p-0 text-center">
                                                        <p class="mb-0">Quantity x Price</p>
                                                    </div>
                                                    <div class="col-2 p-0 text-center">
                                                        <p class="mb-0">Tax</p>
                                                    </div>
                                                    <div class="col-2 p-0 text-right">
                                                        <p class="mb-0">Total Amount</p>
                                                    </div>
                                                </div>
                                                @if(isset($store_order_details) && !empty($store_order_details))
                                                    @php $i=0; @endphp
                                                    @foreach ($store_order_details as $order)
                                                        <div class="d-flex justify-content-between">
                                                            <div class="col-3 p-0">
                                                                <p class="mb-0">{{$order->product_name}}</a></p>
                                                            </div>
                                                            <div class="col-3 p-0">
                                                                <p class="mb-0">{{ $order->product_variants }}</p> 
                                                            </div>
                                                            <div class="col-2 p-0 text-center">
                                                                <p class="mb-0">{{ $order->quantity.' x '.number_format((float)($order->product_price/$order->quantity), 2, '.', '')}}</p>
                                                            </div>
                                                            <div class="col-2 p-0 text-center">
                                                                <p class="mb-0">{{ $order->product_tax }}</p>
                                                            </div>
                                                            <div class="col-2 p-0 text-right">
                                                                <p class="mb-0">{{ number_format((float)($order->product_price + $order->product_tax), 2, '.', '') }}</p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                <hr class="pos-invc">
                                                <div class="d-flex justify-content-between">
                                                    <div><p class="mb-0"><b>Sub Total</b></p></div> 
                                                    <div><p class="mb-0"><b class="cart-sub-total">SAR {{!empty($store_order_details) && !empty($store_order_details[0]->sub_total_amount) ? $store_order_details[0]->sub_total_amount : '0.00' }}</b></p></div>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <div><p class="mb-0"><b>Tax</b></p></div>
                                                    <div><p class="mb-0"><b class="cart-total-tax">SAR {{!empty($store_order_details) && !empty($store_order_details[0]->total_tax_amount) ? $store_order_details[0]->total_tax_amount : '0.00' }}</b></p></div> 
                                                </div>
                                                <hr class="pos-invc">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <h4 style="font-size:17px;color:#000;">Total with tax</h4>
                                                    </div>
                                                    <div>
                                                        <h4 style="font-size:17px;color:#000;" class="total-cart-amount">SAR {{!empty($store_order_details) && !empty($store_order_details[0]->total_amount) ? $store_order_details[0]->total_amount : '0.00' }}</h4> 
                                                    </div>
                                                </div>
                                                @if(!empty($store_order_details) && !empty($store_order_details[0]->paid_amount))
                                                    <div class="d-flex justify-content-between order-cash">
                                                        <div>
                                                            <p class="mb-0">Cash</p>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 order-cash-amount">SAR {{!empty($store_order_details) && !empty($store_order_details[0]->paid_amount) ? number_format((float)($store_order_details[0]->paid_amount), 2, '.', '') : '0.00' }}</p> 
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between order-change">
                                                        <div>
                                                            <p class="mb-0">Change</p>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 order-change-amount">SAR {{ number_format((float)($store_order_details[0]->paid_amount - $store_order_details[0]->total_amount), 2, '.', '') }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <hr class="pos-invc">
                                            <p class="text-center">Thank's for your choice</p>
                                       
                                            <hr/>
                                            <div class="text-right">
                                                <a href="#" ><button class="btn btn-primary btn-sm rounded print-order-invoice" >Download</button></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

        </div>


    </body>
</html>
<script src="{{ URL::asset('assets/cashier-admin/js/vendors.min.js') }}"></script>
<script>
            $(document).on("click",".print-order-invoice",function() {
                window.print(); 
            });
    </script>