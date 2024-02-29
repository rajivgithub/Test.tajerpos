<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ __('store-admin.order_details_title',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header')            
    </head>
    @php
        $prefix_url = config('app.module_prefix_url');
    @endphp
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <section class="content new-order">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-2">
                                    <h4 class="content-title mr-2 mb-0">{{ __('store-admin.order_details') }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">&nbsp;</div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-2">
                                    <div class="card-header">
                                        <div>
                                            <p class="font-weight-bold mb-2">{{ __('store-admin.order_id') }} #{{ (!empty($store_order_details) && (count($store_order_details) > 0) && !empty($store_order_details[0]['order_number'])) ? $store_order_details[0]['order_number']: ""; }}</p>
                                            @if(!empty($store_order_details) && count($store_order_details) > 0 && !empty($store_order_details[0]['ordered_at']))
                                                @php
                                                    $dateTime = DateTime::createFromFormat('d-m-Y H:i', $store_order_details[0]['ordered_at']);
                                                    $formattedDate = $dateTime !== false ? $dateTime->format('F j, Y \a\t g:i a') : 'Invalid Date/Time';
                                                @endphp
                                                <p class="font-weight-bold mb-2">{{ $formattedDate }}</p> 
                                            @endif
                                        </div>
                                        @if(!empty($store_order_details) && count($store_order_details) > 0 && !empty($store_order_details[0]['order_methods']))
                                            <div class="alert alert-warning">
                                                <i class="mr-1"></i>{{  $store_order_details[0]['order_methods'] }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <p class="font-weight-bold mb-2">{{ __('store-admin.products_details') }}</p>
                                        <div class="table-responsive">
                                            <table class="table my-cart-tab">
                                                <tbody>
                                                    @if(!empty($store_order_details) && count($store_order_details) > 0)
                                                        @foreach($store_order_details as $order)
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        @php $product_image = ""; @endphp
                                                                        @if($order['category_image'] != "") 
                                                                            @php
                                                                                $product_images = explode("***",$order['category_image']);
                                                                                $product_image = $product_images[0];
                                                                            @endphp
                                                                        @endif
                                                                        <div><img src="{{ $product_image }}" class="img-fluid mr-2" alt=""></div>
                                                                        <div>
                                                                            <a href="{{ route(config('app.prefix_url') . '.' . $store_url . '.' . config('app.module_prefix_url') . '.product.create', Crypt::encrypt($order['product_id'])) }}" class="mb-1">{{ $order['product_name'] }}</a>
                                                                            @if($order['product_variants'] != "")
                                                                                <div class="alert alert-secondary d-inline-block">{{ $order['product_variants'] }}</div> 
                                                                            @endif
                                                                            <div>Quantity: {{ $order['quantity'] }}</div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>SAR {{ $order['sub_total'] / $order['quantity'] }} x {{ $order['quantity'] }}</td>
                                                                <td class="text-right">
                                                                    SAR {{ $order['sub_total'] }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-3">
                                    <!-- <div class="box-header">
                                        <div class="d-flex">
                                            <div class="alert alert-secondary">
                                                <i class="fa fa-check-square-o mr-1"></i> Paid
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="card-body">
                                        <p class="font-weight-bold mb-2">{{ __('store-admin.payment_details') }}</p>
                                        <div class="table-responsive">
                                            <table class="table my-cart-tab">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-right">{{ __('store-admin.sub_total') }}</td>
                                                        <td class="text-right font-weight-bold">
                                                            SAR {{ (!empty($store_order_details) && (count($store_order_details) > 0) && !empty($store_order_details[0]['sub_total_amount'])) ? $store_order_details[0]['sub_total_amount']: "0.00" }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-right">{{ __('store-admin.tax') }}</td>
                                                        <td class="text-right font-weight-bold">
                                                            SAR {{ (!empty($store_order_details) && (count($store_order_details) > 0) && !empty($store_order_details[0]['total_tax_amount'])) ? $store_order_details[0]['total_tax_amount']: "0.00" }}
                                                        </td>
                                                    </tr>
                                                    <!-- <tr>
                                                        <td colspan="3" class="font-weight-bold text-right">Discounts</td>
                                                        <td class="text-right font-weight-bold">
                                                            SAR {{ (!empty($store_order_details) && (count($store_order_details) > 0) && !empty($store_order_details[0]['discount_amount'])) ? $store_order_details[0]['discount_amount']: "0.00" }}
                                                        </td>
                                                    </tr> -->
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-right">{{ __('store-admin.total') }}</td>
                                                        <td class="text-right font-weight-bold">
                                                            @php
                                                                $total_amount = (!empty($store_order_details) && (count($store_order_details) > 0) && $store_order_details[0]['total_amount'] > 0) ? $store_order_details[0]['total_amount']: 0;
                                                            @endphp
                                                            SAR {{ $total_amount }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h4 class="mb-0">{{ __('store-admin.customer_details') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        @if(!empty($store_order_details) && count($store_order_details) > 0 && !empty($store_order_details[0]['customer_name']))
                                            <p class="mb-2">{{ ucwords($store_order_details[0]['customer_name']) }}</p>
                                        @endif
                                        <p class="font-weight-bold mb-2">{{ __('store-admin.contact_info') }}</p>
                                        <p class="mb-2">{{ (!empty($store_order_details) && count($store_order_details) > 0 && !empty($store_order_details[0]['email'])) ? $store_order_details[0]['email'] : 'No email provided'; }}</p>
                                        <p class="mb-2">{{ (!empty($store_order_details) && count($store_order_details) > 0 && !empty($store_order_details[0]['phone_number'])) ? $store_order_details[0]['phone_number'] : 'No phone number'; }}</p>
                                        <!-- <p class="font-weight-bold mb-0">Billing address</p>
                                        <p class="mb-2">No billing address provided</p> -->
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
