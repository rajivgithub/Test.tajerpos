@include('common.store_admin.header')
<section class="content-main">
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Paypal Credential</h4>
                </div>
                <div class="card-body">
                    <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.payment-gateway.store') }}">
                    @csrf
                        <input type="hidden" name="mode" value={{$paypal_mode}}> 
                        <input type="hidden" name="payment_type" value="paypal"> 
                        <input type="hidden" name="payment_credential_id" class="payment-credential-id" value="{{!empty($paypal_details) && !empty($paypal_details[0]->payment_credential_id) ? Crypt::encrypt($paypal_details[0]->payment_credential_id) : '' }}">
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Paypal Key</label>
                            <input type="text" placeholder="AbZqxwGM87-fRHI-HnG_plBoz-Z_j2O" class="form-control required-field" data-label = "Paypal Client ID" name="client_id" value="{{!empty($paypal_details) && !empty($paypal_details[0]->client_id) ? $paypal_details[0]->client_id : '' }}">
                            @if ($errors->has('client_id'))
                                <span class="text-danger error-message">{{ $errors->first('client_id') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Paypal Client Secret</label>
                            <input type="text" placeholder="EDFYQf8itbqoWi-9BIzgzrNvGWLI62UEliT1i8f_APi" class="form-control required-field"  data-label = "Paypal Client Secret" name="client_secret" value="{{!empty($paypal_details) && !empty($paypal_details[0]->client_secret) ? $paypal_details[0]->client_secret : '' }}">
                            @if ($errors->has('client_secret'))
                                <span class="text-danger error-message">{{ $errors->first('client_secret') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Paypal Webhook Key</label>
                            <input type="text" placeholder="9MJ33817UC7257" class="form-control required-field" data-label = "Paypal Webhook Key" name="webhook_key" value="{{!empty($paypal_details) && !empty($paypal_details[0]->webhook_key) ? $paypal_details[0]->webhook_key : '' }}">
                            @if ($errors->has('webhook_key'))
                                <span class="text-danger error-message">{{ $errors->first('webhook_key') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Paypal Sandbox Mode</label>
                            <div class="form-check form-switch ps-0">
                                <input class="form-check-input ms-0" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="sandbox_mode" value="yes" {{!empty($paypal_details) && !empty($paypal_details[0]->sandbox_mode) && $paypal_details[0]->sandbox_mode == 'yes' ? 'checked' : '' }}>
                            </div>
                        </div>
						<div class="text-end">
                            <button class="btn btn-md rounded font-sm hover-up save-payment-credentials">Save</button>
                        </div> 
                    </form>
                </div>
            </div>
		</div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Stripe Credential</h4>
                </div>
                <div class="card-body">
                    <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.payment-gateway.store') }}">
                    @csrf
                        <input type="hidden" name="mode" value={{$stripe_mode}}> 
                        <input type="hidden" name="payment_type" value="stripe"> 
                        <input type="hidden" name="payment_credential_id" class="payment-credential-id" value="{{!empty($stripe_details) && !empty($stripe_details[0]->payment_credential_id) ? Crypt::encrypt($stripe_details[0]->payment_credential_id) : '' }}">
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Stripe Key</label>
                            <input type="text" placeholder="AbZqxwGM87-fRHI-HnG_plBoz-Z_j2O" class="form-control required-field" data-label = "Stripe Key" name="client_id" value="{{!empty($stripe_details) && !empty($stripe_details[0]->client_id) ? $stripe_details[0]->client_id : '' }}">
                            @if ($errors->has('client_id'))
                                <span class="text-danger error-message">{{ $errors->first('client_id') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Stripe Secret</label>
                            <input type="text" placeholder="EDFYQf8itbqoWi-9BIzgzrNvGWLI62UEliT1i8f_APi" class="form-control required-field" data-label = "Stripe Secret" name="client_secret" value="{{!empty($stripe_details) && !empty($stripe_details[0]->client_secret) ? $stripe_details[0]->client_secret : '' }}">
                            @if ($errors->has('client_secret'))
                                <span class="text-danger error-message">{{ $errors->first('client_secret') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Stripe Webhook Secret Key</label>
                            <input type="text" placeholder="9MJ33817UC7257" class="form-control required-field" data-label = "Stripe Webhook Secret Key" name="webhook_key" value="{{!empty($stripe_details) && !empty($stripe_details[0]->webhook_key) ? $stripe_details[0]->webhook_key : '' }}">
                            @if ($errors->has('webhook_key'))
                                <span class="text-danger error-message">{{ $errors->first('webhook_key') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
						<div class="text-end">
                            <button class="btn btn-md rounded font-sm hover-up save-payment-credentials">Save</button>
                        </div>
                    </form>
				</div>
			</div>			
		</div> 
    </div>
</section>
@include('common.store_admin.footer')
<script>
    $(document).on("click",".save-payment-credentials",function() {
        check_fields = validateFields($(this));
        if(check_fields > 0)
            return false;
        else
            return true;
    });
</script>