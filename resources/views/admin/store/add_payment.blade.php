@include('common.admin.header')
<section class="content-main">
    <div class="card mb-4">
        <div class="card-header">
            <div class="row gx-3">
                <div class="col-lg-3 col-md-6 me-auto"><h3>Add Payment</h3></div>
                <div class="col-lg-2 col-6 col-md-2 text-end">
                    <a href="{{ url(config('app.prefix_url').'/admin/store') }}" class="btn btn-primary btn-sm rounded"><i class="fa fa-arrow-left" ></i><b>Back</b></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form  method="POST" action="{{ route(config('app.prefix_url').'.admin.store.add-payment') }}"  enctype="multipart/form-data">
            @csrf
                <input type="hidden" name="store_id" class="store_id" value="{{isset($store_id) && !empty($store_id) ? $store_id : '' }}">
                <input type="hidden" name="payment_id" class="payment-id" value="{{ (isset($payment_details) && !empty($payment_details) && count($payment_details) > 0 && $payment_details[0]->balance_amount > 0) ?  Crypt::encrypt($payment_details[0]->payment_id) : '' }}">
                <input type="hidden" name="mode" class="mode" value="{{ $mode }}">
                <input type="hidden" name="balance_exist" class="balance-exist" value="{{ (isset($payment_details) && !empty($payment_details) && count($payment_details) > 0 && $payment_details[0]->balance_amount > 0) ?  $payment_details[0]->balance_amount : '0' }}">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Payment Type<span>*</span></label>
                            <select class="form-select dropdown-search required-field payment-method" data-label = "Payment Type" name="payment_method">
                                <option value="" selected="">--Select Payment Type--</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="online">Online</option>
                                <option value="free">Free Trial</option>
                            </select>
                            @if ($errors->has('payment_method'))
                                <span class="text-danger error-message">{{ $errors->first('payment_method') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-4 input-field-div">
                                    <label class="form-label">Package Amount<span>*</span></label>
                                    <input type="text" placeholder="Type here" onkeypress="return isNumber(event)" {{ ($mode == "edit" && isset($payment_details) && !empty($payment_details) && !empty($payment_details[0]->balance_amount) && $payment_details[0]->balance_amount > 0 ) ? "readonly" : '' }} value="{{ (isset($payment_details) && !empty($payment_details) && count($payment_details) > 0 && $payment_details[0]->balance_amount > 0) ? $payment_details[0]->package_amount : '' }}" data-label = "Package Amount" name="package_amount" class="form-control required-field package-amount payment-fields">
                                    @if ($errors->has('package_amount'))
                                        <span class="text-danger error-message">{{ $errors->first('package_amount') }}</span>
                                    @endif
                                    <span class="error error-message"></span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-4 input-field-div">
                                    <label class="form-label">Paid Amount<span>*</span></label>
                                    <input type="hidden" class="already-paid-amount" value="{{  (isset($payment_details) && !empty($payment_details) && count($payment_details) > 0 && $payment_details[0]->balance_amount > 0) ?  $payment_details[0]->paid_amount : '' }}">
                                    <input type="text" placeholder="Type here" onkeypress="return isNumber(event)" data-label = "Paid Amount" class="form-control required-field amount-field paid-amount payment-fields" value="" name="paid_amount">
                                    @if ($errors->has('paid_amount'))
                                        <span class="text-danger error-message">{{ $errors->first('paid_amount') }}</span>
                                    @endif
                                    <span class="error error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 input-field-div">
                            <label class="form-label">VAT %</label>
                            <input type="number" placeholder="Type here" onkeypress="return isNumber(event)" {{ ($mode == "edit" && isset($payment_details) && !empty($payment_details) && !empty($payment_details[0]->balance_amount) && $payment_details[0]->balance_amount > 0 ) ? "readonly" : '' }} data-label = "VAT %" class="form-control vat-percentage payment-fields" value="{{ (isset($payment_details) && !empty($payment_details) && count($payment_details) > 0 && $payment_details[0]->balance_amount > 0) ?  $payment_details[0]->tax_percentage : '' }}" name="tax_percentage">
                            <input type="hidden" name="tax_amount" class="tax_amount">
                            <input type="hidden" name="discount_amount" class="discount_amount">
                            <input type="hidden" name="balance_amount" class="balance_amount">
                            <input type="hidden" name="total_amount" class="total-amount-val">
                            <input type="hidden" name="amount_payable" class="amount-payable">
                        </div>
                        <!-- <div class="mb-4 input-field-div">
                            <label class="form-label">VAT Amount</label>
                            <input type="text" placeholder="Type here" onkeypress="return isNumber(event)" data-label = "VAT Amount" readonly class="form-control tax-amount" name="tax_amount">
                        </div> -->
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-4 input-field-div">
                                    <label class="form-label ">Discount</label>
                                    <input type="text" name="discount" onkeypress="return isNumber(event)" value="{{ (isset($payment_details) && !empty($payment_details) && count($payment_details) > 0 && $payment_details[0]->balance_amount > 0) ?  $payment_details[0]->discount : '' }}" {{ ($mode == "edit" && isset($payment_details) && !empty($payment_details) && !empty($payment_details[0]->balance_amount) && $payment_details[0]->balance_amount > 0 ) ? "readonly" : '' }} data-label = "Discount" placeholder="Type here" class="form-control discount payment-fields">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-4 input-field-div">
                                    <label class="form-label ">Discount Type</label>
                                    @if($mode == "edit" && isset($payment_details) && !empty($payment_details) && !empty($payment_details[0]->balance_amount) && $payment_details[0]->balance_amount > 0 )
                                        <input type="text" class="form-control discount-type" readonly data-label = "Discount Type" name="discount-type" value="{{  (isset($payment_details) && !empty($payment_details) && count($payment_details) > 0 && $payment_details[0]->balance_amount > 0) ?  $payment_details[0]->discount_type : '' }}">
                                    @else
                                        <select class="form-control discount-type" data-label = "Discount Type" name="discount_type">
                                            <option value="flat">Flat</option>
                                            <option value="percentage">Percentage</option>
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-striped">
                            <tr>
                                <td class="text-start"><b>Subtotal</b></td>
                                <td class="sub-total-amount">0</td>
                            </tr>
                            <tr>
                                <td class="text-start"><b>Paid Amount</b></td>
                                <input type="hidden" name="total_paid_amount" class="total_paid_amount" value="">
                                <td class="total-paid-amount">0</td>
                            </tr>
                            <tr>
                                <td class="text-start"><b>VAT <span class="tax-percentage-value"></span></b></td>
                                <td class="tax-amount-value">0</td>
                            </tr>
                            <tr>
                                <td class="text-start"><b>Discount</b></td>
                                <td class="discount-amount">0</td>
                            </tr>
                            <tr>
                                <td class="text-start"><b>Balance</b></td>
                                <td class="balance-amount">0</td>
                            </tr>
                            <tr>
                                <td class="text-start"><b>Total SAR</b></td>
                                <td class="total-amount">0</td>
                            </tr>
                            <tr>
                                <td class="text-start"><b>Amount Payable SAR</b></td>
                                <td class="total-amount-payable">0</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <div class="text-end">
                            <button class="btn btn-md rounded font-sm hover-up" id="save-payment-info">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@include('common.admin.footer')
<script src="{{ URL::asset('assets/js/validation.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.dropdown-search').select2();
        if($(".mode").val() == "edit") 
            $(".discount-type").trigger("change");
    });
    $(document).on("change",".payment-method",function() {
        payment_method = $(this).val();
        if(payment_method == "free") {
            $(".amount-field").removeClass("required-field");
            $(".payment-fields").val(0);
            $(".discount-type").trigger("change");
        }
        else
            $(".amount-field").addClass("required-field");
    });
    $(document).on("click","#save-payment-info",function() {
        check_fields = validateFields($(this));
        if(check_fields > 0)
            return false;
        else
            return true;
    });
    $(document).on("keyup",".package-amount,.paid-amount,.vat-percentage,.discount",function() {
        calculateInvoice($(this));
    }); 
    $(document).on("change",".discount-type",function() {
        calculateInvoice($(this));
    }); 
    function calculateInvoice(_this) {
        package_amount = _this.closest("form").find(".package-amount").val();
        sub_total_amount = (package_amount > 0 && package_amount != '') ? package_amount : 0;
        _this.closest("form").find(".sub-total-amount").text(Number(sub_total_amount).toFixed(2));
        _mode = _this.closest("form").find(".mode").val(); 
        balance_exist = _this.closest("form").find(".balance-exist").val(); 
        if(_mode == "edit" && parseInt(balance_exist) > 0) {
            paid_amount = _this.closest("form").find(".paid-amount").val();
            already_paid_amount = _this.closest("form").find(".already-paid-amount").val();
            paid_amount = (paid_amount > 0 && paid_amount != '') ? paid_amount : 0;
            already_paid_amount = (already_paid_amount > 0 && already_paid_amount != '') ? already_paid_amount : 0;
            total_paid_amount = (paid_amount > 0 || already_paid_amount > 0 ) ? parseFloat(paid_amount) + parseFloat(already_paid_amount) : 0;
            _this.closest("form").find(".total-paid-amount").text(Number(total_paid_amount).toFixed(2));
            _this.closest("form").find(".total_paid_amount").val(Number(total_paid_amount).toFixed(2));
        } else {
            paid_amount = _this.closest("form").find(".paid-amount").val();
            total_paid_amount = (paid_amount > 0 && paid_amount != '') ? paid_amount : 0;
            _this.closest("form").find(".total-paid-amount").text(Number(total_paid_amount).toFixed(2));
        }
        vat_percentage = _this.closest("form").find(".vat-percentage").val();
        vat_percentage_text = (vat_percentage > 0 && vat_percentage != '') ? '('+vat_percentage+'%)' : "";
        _this.closest("form").find(".tax-percentage-value").text(vat_percentage_text);
        tax_amount = (package_amount > 0 && vat_percentage > 0) ? ((package_amount / 100) * vat_percentage) : 0;
        _this.closest("form").find(".tax-amount-value").text(Number(tax_amount).toFixed(2)); 
        _this.closest("form").find(".tax_amount").val(Number(tax_amount).toFixed(2)); 
        discount = _this.closest("form").find(".discount").val(); 
        discount_type = _this.closest("form").find(".discount-type").val();
        discount_price = 0;
        if(discount > 0 && discount_type == "flat" && package_amount > 0) 
            discount_price = discount;
        if(discount > 0 && discount_type == "percentage" && package_amount > 0)
            discount_price = (((package_amount / 100) * discount));
        _this.closest("form").find(".discount-amount").text(Number(discount_price).toFixed(2));  
        _this.closest("form").find(".discount_amount").val(Number(discount_price).toFixed(2));
        pay_amount = parseFloat(sub_total_amount) + parseFloat(tax_amount);
        discount_amount  = parseFloat(total_paid_amount) + parseFloat(discount_price);
        balance_amount = pay_amount - discount_amount;
        _this.closest("form").find(".balance-amount").text(Number(balance_amount).toFixed(2)); 
        _this.closest("form").find(".balance_amount").val(Number(balance_amount).toFixed(2)); 
        _this.closest("form").find(".total-amount").text(Number(pay_amount).toFixed(2)); 
        _this.closest("form").find(".total-amount-val").val(Number(pay_amount).toFixed(2)); 
        total_amount_payable = parseFloat(pay_amount) - parseFloat(discount_price);
        _this.closest("form").find(".total-amount-payable").text(Number(total_amount_payable).toFixed(2)); 
        _this.closest("form").find(".amount-payable").val(Number(total_amount_payable).toFixed(2)); 
    }
</script>
