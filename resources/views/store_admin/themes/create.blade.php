@include('common.store_admin.header')
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Color Information</h2>                       
        </div>
    </div>
    <div class="row">
		<div class="col-md-5">
            <div class="card mb-4">
                <div class="card-body">
                    <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.themes.store') }}">
                    @csrf
                        <input type="hidden" name="theme_id" value="{{!empty($theme_details) && !empty($theme_details[0]->theme_id) ? Crypt::encrypt($theme_details[0]->theme_id) : '' }}">
                        <input type="hidden" name="mode" value="{{ $mode }}">
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Name</label>
                            <input type="text" placeholder="AntiqueWhite" class="form-control required-field" data-label = "Name"  name="color_name" value="{{!empty($theme_details) && !empty($theme_details[0]->color_name) ? $theme_details[0]->color_name : '' }}">
                            @if ($errors->has('color_name'))
                                <span class="text-danger error-message">{{ $errors->first('color_name') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
                        <div class="mb-4 input-field-div">
                            <label class="form-label">Color Code</label>
                            <input type="text" placeholder="#FAEBD7" class="form-control required-field" data-label = "Color Code"  name="color_code" value="{{!empty($theme_details) && !empty($theme_details[0]->color_code) ? $theme_details[0]->color_code : '' }}">
                            @if ($errors->has('color_code'))
                                <span class="text-danger error-message">{{ $errors->first('color_code') }}</span>
                            @endif
                            <span class="error error-message"></span>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-md rounded font-sm hover-up" id="save-theme-info">Save</button>
                        </div>
					</form>
				</div>
			</div>
		</div>	
	</div>  
</section>          
@include('common.store_admin.footer')
<script>
    $(document).on("click","#save-theme-info",function() {
        check_fields = validateFields($(this));
        if(check_fields > 0)
            return false;
        else
            return true;
    });
</script>