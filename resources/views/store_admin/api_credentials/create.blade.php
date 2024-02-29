@include('common.store_admin.header')
<section class="content-main">
    <div class="row">
		<div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Google reCAPTCHA Setting</h4>
                </div>
                <div class="card-body">
                    <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.api-credentials.store') }}">
                    @csrf
                        <input type="hidden" name="mode" value={{$mode}}> 
                        <input type="hidden" name="api_credential_id" class="api-credential-id" value="{{!empty($api_credentials) && !empty($api_credentials[0]->api_credential_id) ? Crypt::encrypt($api_credentials[0]->api_credential_id) : '' }}">
                        <div class="mb-4">
                            <label class="form-label">Google reCAPTCHA</label>
                            <div class="form-check form-switch ps-0">
                                <input class="form-check-input ms-0" type="checkbox" role="switch" id="flexSwitchCheckDefault" value="yes" name="google_recaptcha" {{!empty($api_credentials) && !empty($api_credentials[0]->google_recaptcha) && $api_credentials[0]->google_recaptcha == 'yes' ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Site Key</label>
                            <input type="text" placeholder="" class="form-control required-field" data-label = "Site Key" name="site_key" value="{{!empty($api_credentials) && !empty($api_credentials[0]->site_key) ? $api_credentials[0]->site_key : '' }}">
                            @if ($errors->has('site_key'))
                                <span class="text-danger error-message">{{ $errors->first('site_key') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-md rounded font-sm hover-up save-api-credentials">Save </button>
                        </div>
                    </form>
                </div>
			</div>		
		</div>
    </div>
</section>
@include('common.store_admin.footer')
<script>
    $(document).on("click",".save-api-credentials",function() {
        check_fields = validateFields($(this));
        if(check_fields > 0)
            return false;
        else
            return true;
    });
</script>