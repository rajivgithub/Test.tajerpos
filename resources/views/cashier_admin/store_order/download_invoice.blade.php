<!DOCTYPE html>
<html>
<head>
    <title>eMonta - Invoice Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    @php
        $prefix_url = config('app.module_prefix_url');
    @endphp
    <div class="row">
        <div class="col-md-4">
            <h4>Invoice</h4>
        </div>
        <div class="col-md-4">&nbsp;</div>
        <div class="col-md-4">
            <span class="order-id">Order NO : {{!empty($store_order_details) && !empty($store_order_details[0]->order_number) ? $store_order_details[0]->order_number : '-' }}</span> 
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-sm-6">
            <div class="ordr-date">
                <b>Order Date :</b> {{!empty($store_order_details) && !empty($store_order_details[0]->created_at) ? date('d M Y H:i:s', strtotime(trim($store_order_details[0]->created_at))) : '-' }}
            </div>
        </div>	
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th >#</th>
                            <th>Item</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($store_order_details) && !empty($store_order_details))
                            @php $i=0; @endphp
                            @foreach ($store_order_details as $order)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>
                                        <a href="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.product.show', Crypt::encrypt($order->product_id)) }}" target="_blank">{{$order->product_name}}</a>
                                    </td>
                                    <td class="text-center">{{$order->quantity}}</td>
                                    <td class="text-center">SAR {{ number_format((float)($order->sub_total), 2, '.', '') }}</td> 
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                            <td class="text-right">Total Amount</td>
                            <td>SAR {{!empty($store_order_details) && !empty($store_order_details[0]->total_amount) ? number_format((float)($store_order_details[0]->total_amount), 2, '.', '') : '-' }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>