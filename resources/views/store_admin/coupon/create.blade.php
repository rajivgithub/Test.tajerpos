@include('common.store_admin.header')
<section class="content-main">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Coupon Information Update</h4>
            </div>
            <div class="card-body">
                <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.coupon.store') }}">
                @csrf
                    <input type="hidden" name="mode" value={{$mode}}> 
                    <input type="hidden" name="coupon_id" class="coupon-id " value="{{!empty($coupon_details) && !empty($coupon_details[0]->coupon_id) ? Crypt::encrypt($coupon_details[0]->coupon_id) : '' }}">
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Coupon Code</label>
                        <input type="text" placeholder="Type here" data-label = "Coupon Code" name="coupon_code" class="form-control required-field" value="{{!empty($coupon_details) && !empty($coupon_details[0]->coupon_code) ? $coupon_details[0]->coupon_code : '' }}">
                        @if ($errors->has('coupon_code'))
                            <span class="text-danger error-message">{{ $errors->first('coupon_code') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Coupon Type</label>
                        <select class="form-select required-field" data-label = "Coupon Type" name="coupon_type">
                            <option value="">--Select Coupon Type--</option>
                            <option value="products" {{!empty($coupon_details) && !empty($coupon_details[0]->coupon_type) && ($coupon_details[0]->coupon_type == "products") ? "selected" : '' }}>For products</option>
                            <option value="total_orders" {{!empty($coupon_details) && !empty($coupon_details[0]->coupon_type) && ($coupon_details[0]->coupon_type == "total_orders") ? "selected" : '' }}>For Total Orders</option>
                        </select>
                        @if ($errors->has('coupon_type'))
                            <span class="text-danger error-message">{{ $errors->first('coupon_type') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Product</label>
                        <select class="form-select product-list" data-label = "Product" name="product_id">
                            <option value="">--Select Product--</option> 
                            @if(isset($product_details) && !empty($product_details))
                                @foreach ($product_details as $product)
                                    <option value="{{ $product->product_id }}" {{!empty($coupon_details) && !empty($coupon_details[0]->product_id) && ($coupon_details[0]->product_id == $product->product_id) ? "selected" : '' }}>{{ $product->product_name }}</option> 
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('product_id'))
                            <span class="text-danger error-message">{{ $errors->first('product_id') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="mb-4 input-field-div">
                                <label class="form-label">Discount</label>
                                <input type="text" placeholder="Type here" onkeypress="return isNumber(event)" class="form-control required-field"  data-label = "Discount" name="discount" value="{{!empty($coupon_details) && !empty($coupon_details[0]->discount) ? $coupon_details[0]->discount : '' }}">
                                @if ($errors->has('discount'))
                                    <span class="text-danger error-message">{{ $errors->first('discount') }}</span>
                                @endif
                                <span class="error error-message"></span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-4 input-field-div">
                                <label class="form-label"></label>
                                <select class="form-select" name="discount_type">
                                    <option value="flat" {{!empty($coupon_details) && !empty($coupon_details[0]->discount_type) && $coupon_details[0]->discount_type == 'flat' ? 'selected' : '' }}>Flat</option>
                                    <option value="percent" {{!empty($coupon_details) && !empty($coupon_details[0]->discount_type) && $coupon_details[0]->discount_type == 'percent' ? 'selected' : '' }}>Percent</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Start date</label>
                        <input type="date" data-label = "Start date" placeholder="Type here"  value = "{{!empty($coupon_details) && !empty($coupon_details[0]->start_up_date) ? $coupon_details[0]->start_up_date : '' }}" name="start_up_date" class="form-control required-field validity-date" >
                        @if ($errors->has('start_up_date'))
                            <span class="text-danger error-message">{{ $errors->first('start_up_date') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">End Date</label>
                        <input type="date" data-label = "End Date" placeholder="Type here"  value = "{{!empty($coupon_details) && !empty($coupon_details[0]->expiration_date) ? $coupon_details[0]->expiration_date : '' }}" name="expiration_date" class="form-control required-field validity-date" >
                        @if ($errors->has('expiration_date'))
                            <span class="text-danger error-message">{{ $errors->first('expiration_date') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-md rounded font-sm hover-up save-coupon-info">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@include('common.store_admin.footer')
<script>
    $(document).on("click",".save-coupon-info",function() {
        check_fields = validateFields($(this));
        if(check_fields > 0)
            return false;
        else
            return true;
    });
    //Minimum validation for Date field
    var today = new Date();
    var month = today.getMonth()+1;
    var date = today.getDate();
    var min_date = today.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (date <10 ? '0' : '') + date;
    $(".validity-date").attr("min",min_date);
</script>