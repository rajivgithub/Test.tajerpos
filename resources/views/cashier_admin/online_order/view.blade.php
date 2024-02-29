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
                        <input type="hidden" class="update_order_status_url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.online-orders.update') }}">
                        <input type="hidden" class="online_order_id" value="{{ (!empty($online_order_details) && (count($online_order_details) > 0) && !empty($online_order_details[0]['online_order_id'])) ? $online_order_details[0]['online_order_id']: '' }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <h4 class="content-title mr-2 mb-0">{{ __('store-admin.order_details') }}</h4>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center text-right">
                                    @if(!empty($online_order_status) && count($online_order_status) > 0)
                                        <select class='form-control change-online-order-status' data-type='single-order'>
                                            <option value=''>{{ __('store-admin.select_status') }}</option>
                                            @foreach($online_order_status as $status)
                                                <option value="{{ $status->order_status_id }}" {{ $status->order_status_id == $online_order_details[0]['online_order_status'] ? 'selected' : '' }}>{{ $status->status_name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">&nbsp;</div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-2">
                                    <div class="card-header">
                                        <div>
                                            <p class="font-weight-bold mb-2">{{ __('store-admin.order_id') }} #{{ (!empty($online_order_details) && (count($online_order_details) > 0) && !empty($online_order_details[0]['order_number'])) ? $online_order_details[0]['order_number']: ""; }}</p>
                                            @if(!empty($online_order_details) && (count($online_order_details) > 0) && ($online_order_details[0]['ordered_at']))
                                                @php
                                                    $dateTime = DateTime::createFromFormat('d-m-Y H:i', $online_order_details[0]['ordered_at']);
                                                    $formattedDate = $dateTime->format('F j, Y \a\t g:i a'); 
                                                @endphp
                                                <p class="font-weight-bold mb-2">{{ $formattedDate }}</p> 
                                            @endif
                                        </div>
                                        @if(!empty($online_order_details) && count($online_order_details) > 0 && !empty($online_order_details[0]['status_name']))
                                            <div class="alert alert-warning">
                                                <i class="mr-1"></i>{{  $online_order_details[0]['status_name'] }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <p class="font-weight-bold mb-2">{{ __('store-admin.products_details') }}</p>
                                        <div class="table-responsive">
                                            <table class="table my-cart-tab">
                                                <tbody>
                                                    @if(!empty($online_order_details) && count($online_order_details) > 0)
                                                        @foreach($online_order_details as $order)
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
                                                            SAR {{ (!empty($online_order_details) && (count($online_order_details) > 0) && !empty($online_order_details[0]['sub_total_amount'])) ? $online_order_details[0]['sub_total_amount']: "0.00" }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-right">{{ __('store-admin.tax') }}</td>
                                                        <td class="text-right font-weight-bold">
                                                            SAR {{ (!empty($online_order_details) && (count($online_order_details) > 0) && !empty($online_order_details[0]['tax_amount'])) ? $online_order_details[0]['tax_amount']: "0.00" }}
                                                        </td>
                                                    </tr>
                                                    <!-- <tr>
                                                        <td colspan="3" class="font-weight-bold text-right">Discounts</td>
                                                        <td class="text-right font-weight-bold">
                                                            SAR {{ (!empty($online_order_details) && (count($online_order_details) > 0) && !empty($online_order_details[0]['discount_amount'])) ? $online_order_details[0]['discount_amount']: "0.00" }}
                                                        </td>
                                                    </tr> -->
                                                    <tr>
                                                        <td colspan="3" class="font-weight-bold text-right">{{ __('store-admin.total') }}</td>
                                                        <td class="text-right font-weight-bold">
                                                            @php
                                                                $total_amount = (!empty($online_order_details) && (count($online_order_details) > 0) && $online_order_details[0]['total_amount'] > 0) ? $online_order_details[0]['total_amount']: 0;
                                                                $discount_amount = (!empty($online_order_details) && (count($online_order_details) > 0) && $online_order_details[0]['discount_amount'] > 0) ? $online_order_details[0]['discount_amount']: 0;
                                                                $total_amount = $total_amount - $discount_amount;
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
                                        @if(!empty($online_order_details) && count($online_order_details) > 0 && !empty($online_order_details[0]['customer_name']))
                                            <p class="mb-2">{{ ucwords($online_order_details[0]['customer_name']) }}</p>
                                        @endif
                                        <p class="font-weight-bold mb-2">{{ __('store-admin.contact_info') }}</p>
                                        <p class="mb-2">{{ (!empty($online_order_details) && count($online_order_details) > 0 && !empty($online_order_details[0]['email'])) ? $online_order_details[0]['email'] : 'No email provided'; }}</p>
                                        <p class="mb-2">{{ (!empty($online_order_details) && count($online_order_details) > 0 && !empty($online_order_details[0]['phone_number'])) ? $online_order_details[0]['phone_number'] : 'No phone number'; }}</p>
                                        <p class="font-weight-bold mb-2">{{ __('store-admin.shipping_address') }}</p>
                                        @if(!empty($online_order_details) && count($online_order_details) > 0)
                                            <p class="mb-1">@if (!empty($online_order_details[0]['shipping_customer_name'])) {{ ucwords($online_order_details[0]['shipping_customer_name']) }} @endif</p>
                                            <p class="mb-3"> 
                                                @if (!empty($online_order_details[0]['building_name']))
                                                    {{ $online_order_details[0]['building_name'] }},<br/>
                                                @endif
                                                @if (!empty($online_order_details[0]['street_name']))
                                                    {{ $online_order_details[0]['street_name'] }},<br/>
                                                @endif
                                                @if (!empty($online_order_details[0]['city_name']))
                                                    {{ $online_order_details[0]['city_name'] }},<br/>
                                                @endif
                                                @if (!empty($online_order_details[0]['state_name']))
                                                    {{ $online_order_details[0]['state_name'] }},<br/>
                                                @endif
                                                @if (!empty($online_order_details[0]['country_name']))
                                                    {{ $online_order_details[0]['country_name'] }},<br/>
                                                @endif
                                                @if (!empty($online_order_details[0]['pincode']))
                                                    {{ $online_order_details[0]['pincode'] }}<br/>
                                                @endif
                                                @if (!empty($online_order_details[0]['landmark']))
                                                    Landmark : {{ $online_order_details[0]['landmark'] }}
                                                @endif
                                            </p>
                                        @else
                                            <p>No billing address available</p>
                                        @endif
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
        <script>
            $(document).on("change",".change-online-order-status",function() {
                $(this).css("border","1px solid #86a4c3");
                _type = $(this).attr("data-type");
                update_order_status_url = $(this).closest("section").find(".update_order_status_url").val();
                status_id = $(this).val();
                var data = { "_token": CSRF_TOKEN,'order_ids[]' : [],'status_id': status_id};
                store_order_id = $(this).closest("tr").find(".online-order-checkbox").val();
                data['order_ids[]'].push(store_order_id);
                $.ajax({
                    url: update_order_status_url,
                    type: 'post',
                    data: data,
                    success: function(response){
                        toastr.options =
                        {
                            "closeButton" : true,
                            "progressBar" : true
                        }
                        toastr.success(response.message);
                    }
                });
            });
        </script>
    </body>
</html>
