@include('common.admin.header')
<section class="content-main">
    @include('common.admin.search')  
    <div class="body-content">
        <div class="content-header">
            <div>
                <h2 class="content-title card-title">Payment History</h2>                       
            </div> 				
        </div>
        <div class="card mb-4 all-store-list">
            <header class="card-header">
                <div class="row gx-3">
                    <div class="col-lg-4 col-md-4 me-auto">
                        <h4 class="store-name">{{!empty($store_details) && !empty($store_details[0]->store_name) ? $store_details[0]->store_name : '' }}</h4>
                    </div>  
                    <div class="col-lg-4 col-md-4 text-center">
                        <a href="{{ url(config('app.prefix_url').'/admin/store/add-payment/'.Crypt::encrypt($store_id).'/pending')}}" class="btn btn-success text-white  rounded font-sm"><i class="fa fa-money"></i></a>
                    </div>  
                    <div class="col-lg-4 col-md-4 total-payment-details dnone">
                        <!-- <select class="form-select filter-by-conditions">
                            <option value="all">Filter by </option>
                            <option value="all">All</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                        </select> -->
                        <h4>Amount Payable : <span class="amount-payable"></span></h4>
                        <h4>Paid Amount : <span class="paid-amount"></span></h4>
                        <h4>Balance Amount : <span class="balance-amount"></span></h4>
                    </div>	 
                </div>
            </header>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered pay-sel" id="payment-list-table">
                        <thead>
                            <tr>
                                <th scope="col">S. no</th>
                                <th scope="col">Payment Method</th> 
                                <th scope="col">Package Amount</th>
                                <th scope="col">TAX %</th>
                                <th scope="col">TAX Amount</th>    
                                <th scope="col">Discount</th>    
                                <th scope="col">Discount Type</th>  
                                <th scope="col">Discount Amount</th> 
                                <th scope="col">Paid Amount</th>     
                                <th scope="col">Paid Date</th>        
                                <th scope="col">Total Amount</th>
                                <th scope="col">Amount Payable</th>
                                <th scope="col">Balance Amount</th>
                            </tr>
                        </thead>
                        <tbody class = "store-list-tbody">
                            <tr>
                                <td class="text-center" colspan="13">Data not found..!</td>
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
                                <td style="display: none;"></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" class="store_id" value={{Crypt::encrypt($store_id)}}>
                </div>
            </div>   
        </div>
    </div>
</section>
@include('common.admin.footer')
<script>
    $(document).on("change",".filter-by-conditions",function(event) {
        event.preventDefault();
        _this = $(this);
        filter_condition = $(this).val();        
        PaymentList(filter_condition,_this);
    });
    $(document).ready(function() {
        PaymentList('all',$('#payment-list-table'));
    });
    function PaymentList(filter_condition,_this) {
        store_id = $(".store_id").val();
        var history_url = '{{ route(config("app.prefix_url").".admin.store.payment",":store_id") }}';
        history_url = history_url.replace(':store_id', store_id);
        if ( $.fn.dataTable.isDataTable( '#payment-list-table' ) )
            payment_list_table.destroy();
        payment_list_table = $('#payment-list-table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [[ 0, "desc" ]],
            "ajax": {
                "url": history_url,
                "dataType": "json",
                "type": "get",
                "data":{type: filter_condition}
            },
            "columns": [
                { "data": "id" },
                { "data": "payment_method" },
                { "data": "package_amount" },
                { "data": "tax_percentage" },
                { "data": "tax_amount" },
                { "data": "discount" },
                { "data": "discount_type" },
                { "data": "discount_amount"},
                { "data": "paid_amount" },
                { "data": "created_at" },
                { "data": "total_amount"},
                { "data": "amount_payable" },
                { "data": "balance_amount"}
            ],
            "drawCallback": function(settings) {
                json_data = settings.json.data;
                if(json_data.length > 0) {
                    total_payment_details = settings.json.total_payment_details;
                    if(total_payment_details.length > 0) {
                        $(_this).closest(".body-content").find(".amount-payable").text(total_payment_details[0]['amount_payable']);
                        $(_this).closest(".body-content").find(".paid-amount").text(total_payment_details[0]['paid_amount']);
                        $(_this).closest(".body-content").find(".balance-amount").text(total_payment_details[0]['balance_amount']);
                        $(_this).closest(".body-content").find(".total-payment-details").removeClass("dnone");
                    } else
                        $(_this).closest(".body-content").find(".total-payment-details").addClass("dnone");
                } else 
                    _this.closest(".all-store-list").find(".store-list-tfoot-tr").css("display","none");
            },
        });
    }
</script>

