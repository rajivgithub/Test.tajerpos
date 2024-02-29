@if(!empty(Auth::user()->is_admin) && Auth::user()->is_admin == 3) 
    @include('common.cashier_admin.header')
@else
    @include('common.store_admin.header')
@endif   
@php
    $prefix_url = config('app.module_prefix_url');
@endphp
<section class="content-main">
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Select Shipping Method</h4>
                </div>
                <div class="card-body">
                    <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.shipping.store') }}">
                    @csrf
                        <input type="hidden" name="mode" value={{$mode}}> 
                        <input type="hidden" name="shipping_id" class="shipping-id" value="{{!empty($shipping_details) && !empty($shipping_details[0]->shipping_id) ? Crypt::encrypt($shipping_details[0]->shipping_id) : '' }}">
                        <div class="input-field-div">
                            <div class="mb-4"> 
                                <div class="form-check">
                                    <input class="form-check-input required-field" type="radio" data-label = "Shipping Method" name="shipping_method" id="shipping-method" {{!empty($shipping_details) && !empty($shipping_details[0]->shipping_method) && $shipping_details[0]->shipping_method == 'product' ? 'checked' : '' }} value="product">
                                    <label class="form-check-label" for="flexRadioDefault1">Product Wise Shipping Cost</label>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input required-field" type="radio" data-label = "Shipping Method" name="shipping_method" id="shipping-method" {{!empty($shipping_details) && !empty($shipping_details[0]->shipping_method) && $shipping_details[0]->shipping_method == 'flat' ? 'checked' : '' }} value="flat">
                                    <label class="form-check-label" for="flexRadioDefault1">Flat Rate Shipping Cost</label>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input required-field" type="radio" data-label = "Shipping Method" name="shipping_method" id="shipping-method" {{!empty($shipping_details) && !empty($shipping_details[0]->shipping_method) && $shipping_details[0]->shipping_method == 'area' ? 'checked' : '' }} value="area">
                                    <label class="form-check-label" for="flexRadioDefault1">Area Wise Flat Shipping Cost</label>
                                </div>
                            </div>
                            <span class="error error-message"></span>
                        </div>
						<div class="text-end">
                            <button class="btn btn-md rounded font-sm hover-up save-shipping-info">Save </button>
                        </div>  
                    </form>
				</div>
			</div>		
		</div>
		<div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Note</h4>
                </div>
                <div class="card-body alrt">
					<p>1. Product Wise Shipping Cost calulation: Shipping cost is calculate by addition of each product shipping cost.</p>
                    <hr/>
                    <p>2. Flat Rate Shipping Cost calulation: How many products a user purchase, doesn't matter. Shipping cost is fixed.</p>
                    <hr/>
                    <p>3. Merchant Wise Flat Shipping Cost calulation: Fixed rate for each merchant. If users purchase 2 product from two merchant shipping cost is calculated by addition of each merchant flat shipping cost.</p>
                    <hr/>
                    <p>4. Area Wise Flat Shipping Cost calulation: Fixed rate for each area. If users purchase multiple products from one merchant shipping cost is calculated by the user shipping area. To configure area wise shipping cost go to <a href="shipping-cities.php">Shipping Cities</a>.</p>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Flat Rate Cost</h4>
                </div>
                <div class="card-body">
                    <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.shipping.store') }}">
                    @csrf
                        <input type="hidden" name="mode" value={{$mode}}> 
                        <input type="hidden" name="shipping_id" class="shipping-id" value="{{!empty($shipping_details) && !empty($shipping_details[0]->shipping_id) ? Crypt::encrypt($shipping_details[0]->shipping_id) : '' }}">
                        <div class="mb-4 input-field-div">										
                            <input type="text" placeholder="20" class="form-control required-field amount" data-label = "Flat Rate Cost" name="flat_rate" value="{{!empty($shipping_details) && !empty($shipping_details[0]->flat_rate) ? $shipping_details[0]->flat_rate : '' }}">
                            <span class="error error-message"></span>
                        </div>
						<div class="text-end">
                            <button class="btn btn-md rounded font-sm hover-up save-shipping-info">Save </button>
                        </div>
                    </form>
				</div>
            </div>			
		</div>
		<div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Note</h4>
                </div>
                <div class="card-body alrt">
					<p>1. Flat rate shipping cost is applicable if Flat rate shipping is enabled.</p>							
				</div>
			</div>
		</div>
		<div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Shipping Cost for Admin Products</h4>
                </div>
                <div class="card-body">
                    <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.shipping.store') }}">
                    @csrf
                        <input type="hidden" name="mode" value={{$mode}}> 
                        <input type="hidden" name="shipping_id" class="shipping-id" value="{{!empty($shipping_details) && !empty($shipping_details[0]->shipping_id) ? Crypt::encrypt($shipping_details[0]->shipping_id) : '' }}">
                        <div class="mb-4 input-field-div">										
                            <input type="text" placeholder="10" class="form-control required-field amount" data-label = "Shipping Cost for Admin Products" name="shipping_cost" value="{{!empty($shipping_details) && !empty($shipping_details[0]->shipping_cost) ? $shipping_details[0]->shipping_cost : '' }}">
                            <span class="error error-message"></span>
                        </div>
						<div class="text-end">
                            <button class="btn btn-md rounded font-sm hover-up save-shipping-info">Save </button>
                        </div>   
                    </form>
				</div>
			</div>			
		</div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Note</h4>
                </div>
                <div class="card-body alrt">
					<p>1. Shipping cost for admin is applicable if Merchant wise shipping cost is enabled.</p>							
				</div>
			</div>
		</div>
    </div>
</section>
@if(!empty(Auth::user()->is_admin) && Auth::user()->is_admin == 3) 
    @include('common.cashier_admin.footer')
@else
    @include('common.store_admin.footer')
@endif 
<script>
    $(document).on("click",".save-shipping-info",function() {
        check_fields = validateFields($(this));
        if(check_fields > 0)
            return false;
        else
            return true;
    });
</script>