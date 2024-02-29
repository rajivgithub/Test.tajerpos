<!DOCTYPE html>
<html>
<head>
    <title>Transaction Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-bordered {
            border: 1px solid #000000; 
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #000000; 
        }
        .table-bordered thead th, .table-bordered thead td {
            border-bottom-width: 2px; 
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-md-4">
            <h4>Transaction Report</h4>
        </div>
        <div class="col-md-4">&nbsp;</div>
        <div class="col-md-4 mb-4">
            <span class="order-id"><b>Report Date : {{!empty($report_date) ? $report_date : '-' }} </b></span> 
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-hover nowrap table-bordered display table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th scope="col">Order ID</th>
                            <th scope="col">Ordered At</th>
                            <th scope="col">Order Type</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Variants</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Price</th>
                            <th scope="col">Tax</th>
                            <th scope="col">Sub Total</th>
                            <th scope="col">Total Tax</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody> <?php
                            $rowspanCounts = [];
                            foreach ($transaction_report_data as $report) {
                                $orderNumber = $report->order_number;
                                if (!isset($rowspanCounts[$orderNumber])) {
                                    $rowspanCounts[$orderNumber] = 0;
                                }
                                $rowspanCounts[$orderNumber]++;
                            }
                            // Update the rowspan count in the $transaction_report_data array
                            foreach ($transaction_report_data as $report) {
                                $orderNumber = $report->order_number;
                                $report->rowspan = $rowspanCounts[$orderNumber];
                            }
                        ?>
                        @if(isset($transaction_report_data) && !empty($transaction_report_data))
                            @php 
                                $i = 1; 
                                $prevOrderNumber = null;
                            @endphp
                            @foreach($transaction_report_data as $report)
                                @php
                                    $orderNumber = $report->order_number;
                                    $rowspan = ($orderNumber === $prevOrderNumber) ? 0 : $transaction_report_data->where('order_number', $orderNumber)->count();
                                    $prevOrderNumber = $orderNumber;
                                @endphp
                                <tr>
                                    @if ($rowspan > 0)
                                        <td rowspan="{{ $rowspan }}">{{ $i }}</td>
                                        <td class="order-number" rowspan="{{ $rowspan }}">{{ $orderNumber }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $report->order_created_at }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $report->order_type }}</td>
                                    @endif
                                    <td>{{ $report->product_name}}</td>
                                    <td>{{ !empty($report->product_variants) ? $report->product_variants : "-"}}</td>
                                    <td>{{ $report->product_price}}</td>
                                    <td>{{ $report->quantity}}</td>
                                    <td>{{ $report->product_tax}}</td>
                                    @if ($rowspan > 0)
                                        <td rowspan="{{ $rowspan }}">{{ $report->sub_total_amount}}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $report->total_tax_amount}}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $report->total_amount}}</td>
                                        @php $i++; @endphp
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="12">Data not found..!</td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                                <td style="display: none;"></td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="10">&nbsp;</td>
                            <td class="text-right">Total Amount</td>
                            <td>SAR {{!empty($total_sum_amount) ? number_format((float)($total_sum_amount), 2, '.', '') : '-' }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            var topMatchTd,ordered_at_td,sub_total_td,total_tax_td,total_td,order_type_td;
            var previousValue = "";
            var rowSpan = 1;
            $('.order-number').each(function(){
                if($(this).text() == previousValue)
                {
                    rowSpan++;
                    $(topMatchTd).attr('rowspan',rowSpan);
                    $(ordered_at_td).attr('rowspan',rowSpan);
                    $(sub_total_td).attr('rowspan',rowSpan);
                    $(total_tax_td).attr('rowspan',rowSpan); 
                    $(total_td).attr('rowspan',rowSpan);
                    $(order_type_td).attr('rowspan',rowSpan);
                    $(this).closest("tr").find("td:eq(11)").remove();
                    $(this).closest("tr").find("td:eq(10)").remove();
                    $(this).closest("tr").find("td:eq(9)").remove();
                    $(this).closest("tr").find("td:eq(3)").remove();
                    $(this).closest("tr").find("td:eq(2)").remove();
                    $(this).remove();
                }
                else
                {
                    topMatchTd = $(this);
                    ordered_at_td = $(this).closest("tr").find("td:eq(2)");
                    order_type_td = $(this).closest("tr").find("td:eq(3)");
                    sub_total_td = $(this).closest("tr").find("td:eq(9)");
                    total_tax_td = $(this).closest("tr").find("td:eq(10)");
                    total_td = $(this).closest("tr").find("td:eq(11)");
                    rowSpan = 1;
                }
                previousValue = $(this).text();
            });
        });
    </script>
</body>
</html>