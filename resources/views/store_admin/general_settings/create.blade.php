@include('common.store_admin.header')
<section class="content-main">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h4>General Settings</h4>
            </div>
            <div class="card-body">
                <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.general-settings.store') }}" enctype="multipart/form-data">
                @csrf
                    @php
                        $system_white_logo_img = !empty($genral_settings_details) && !empty($genral_settings_details[0]->system_white_logo) ? $genral_settings_details[0]->system_white_logo : '';
                        $system_black_logo_img = !empty($genral_settings_details) && !empty($genral_settings_details[0]->system_black_logo) ? $genral_settings_details[0]->system_black_logo : '';
                        $email_logo_img = !empty($genral_settings_details) && !empty($genral_settings_details[0]->email_logo) ? $genral_settings_details[0]->email_logo : '';
                        $background_image = !empty($genral_settings_details) && !empty($genral_settings_details[0]->background_image) ? $genral_settings_details[0]->background_image : '';
                        $white_logo_validation = !empty($genral_settings_details) && !empty($genral_settings_details[0]->system_white_logo) ? 'optional-field' : 'required-field';
                        $black_logo_validation = !empty($genral_settings_details) && !empty($genral_settings_details[0]->system_black_logo) ? 'optional-field' : 'required-field';
                        $email_logo_validation = !empty($genral_settings_details) && !empty($genral_settings_details[0]->email_logo) ? 'optional-field' : 'required-field';
                        $background_img_validation = !empty($genral_settings_details) && !empty($genral_settings_details[0]->background_image) ? 'optional-field' : 'required-field';
                    @endphp
                    <input type="hidden" name="mode" value={{$mode}}> 
                    <input type="hidden" name="settings_id" class="settings-id" value="{{!empty($genral_settings_details) && !empty($genral_settings_details[0]->settings_id) ? Crypt::encrypt($genral_settings_details[0]->settings_id) : '' }}">
                    <input type="hidden" class="timezone-id" value="{{!empty($genral_settings_details) && !empty($genral_settings_details[0]->system_timezone) ? $genral_settings_details[0]->system_timezone : '' }}">
                    <div class="mb-4 input-field-div">
                        <label class="form-label">System Name</label>
                        <input type="text" placeholder="Prodesk" class="form-control required-field" data-label = "System Name" name="system_name" value="{{!empty($genral_settings_details) && !empty($genral_settings_details[0]->system_name) ? $genral_settings_details[0]->system_name : '' }}">
                        @if ($errors->has('system_name'))
                            <span class="text-danger error-message">{{ $errors->first('system_name') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
					<div class="mb-4 input-field-div">
                        <label class="form-label">System Logo - White</label>
                        <div class="input-upload">                                    
                            <input class="form-control {{ $white_logo_validation }} image-field" data-type="image" type="file" data-label = "System Logo - White" name="system_white_logo_image">
                        </div>
                        <div class="file-preview row">
                            <div class="d-flex mt-2 ms-2 file-preview-item">
                                <div class="align-items-center thumb">
                                    <img src="{{ $system_white_logo_img }}" class="img-fit image-preview" alt="Item">
                                </div>
                                <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                            </div>
                        </div>
                        @if ($errors->has('system_white_logo_image'))
                            <span class="text-danger error-message">{{ $errors->first('system_white_logo_image') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
					<div class="mb-4 input-field-div">
                        <label class="form-label">System Logo - Black</label>
                        <div class="input-upload">                                    
                            <input class="form-control {{ $black_logo_validation }} image-field" data-type="image" type="file" data-label = "System Logo - Black" name="system_black_logo_image">
                        </div>
                        <div class="file-preview row">
                            <div class="d-flex mt-2 ms-2 file-preview-item">
                                <div class="align-items-center thumb">
                                    <img src="{{ $system_black_logo_img }}" class="img-fit image-preview" alt="Item">
                                </div>
                                <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                            </div>
                        </div>
                        @if ($errors->has('system_black_logo_image'))
                            <span class="text-danger error-message">{{ $errors->first('system_black_logo_image') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
					<div class="mb-4 input-field-div">
						<label class="form-label">Email Logo</label>                                                                  
                        <input class="form-control {{ $email_logo_validation }} image-field" data-type="image" type="file" data-label = "Email Logo" name="email_logo_image">
						<div class="file-preview row">
                            <div class="d-flex mt-2 ms-2 file-preview-item">
                                <div class="align-items-center thumb">
                                    <img src="{{ $email_logo_img }}" class="img-fit image-preview" alt="Item">
                                </div>
                                <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                            </div>
                        </div>
                        @if ($errors->has('email_logo_image'))
                            <span class="text-danger error-message">{{ $errors->first('email_logo_image') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">Country</label>
                        <select class="form-select required-field country-list" data-label = "Country" name="country_id">
                            <option value="">--Select Country--</option> 
                            @if(isset($countries) && !empty($countries))
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{!empty($genral_settings_details) && !empty($genral_settings_details[0]->country_id) && ($genral_settings_details[0]->country_id == $country->id) ? "selected" : '' }}>{{ $country->name }}</option> 
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('store_country'))
                            <span class="text-danger error-message">{{ $errors->first('store_country') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
                    <div class="mb-4 input-field-div">
                        <label class="form-label">System Timezone</label>
                        <select class="form-select required-field timezone-list" data-label = "System Timezone" name="system_timezone">
                            <option value="">--Select Timezone--</option>   
                        </select>
                        @if ($errors->has('system_timezone'))
                            <span class="text-danger error-message">{{ $errors->first('system_timezone') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
					<div class="mb-4 input-field-div">
						<label class="form-label">Admin login page background</label>                                                             
                        <input class="form-control {{ $background_img_validation }} image-field" data-type="image" type="file" data-label = "Admin login page background"  name="admin_login_image">
						<div class="file-preview row">
                            <div class="d-flex mt-2 ms-2 file-preview-item">
                                <div class="align-items-center thumb">
                                    <img src="{{ $background_image }}" class="img-fit image-preview" alt="Item">
                                </div>
                                <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                            </div>
                        </div>
                        @if ($errors->has('admin_login_image'))
                            <span class="text-danger error-message">{{ $errors->first('admin_login_image') }}</span>
                        @endif
                        <span class="error error-message"></span>
                    </div>
					<div class="mt-3">
                        <button class="btn btn-md rounded font-sm hover-up" id="save-general-settings">Save</button>
                    </div>
                </form>
            </div>
        </div>     
    </div>    
</section>
@include('common.store_admin.footer')
<script>
    $(document).on("click","#save-general-settings",function() {
        check_fields = validateFields($(this));
        if(check_fields > 0)
            return false;
        else
            return true;
    });
    $(document).ready(function() {
        if($(".country-list").val() != "") {
            timezone_id = $(".country-list").closest("form").find(".timezone-id").val();
            timezoneList($(".country-list").val(),timezone_id,$(".country-list"));
        }
    });
    $(document).on("change",".country-list",function(event) {
        event.preventDefault();
        country_id = $(this).val();
        timezoneList(country_id,'',$(this));
    });
    function timezoneList(country_id,timezone_id = '',_this) {
        $.ajax({
            url: "{{ route('timezone-list')}}",
            type: 'post',
            data: {_token: CSRF_TOKEN,country_id: country_id},
            success: function(response){
                timezone = response.timezone;
                timezone_list = '<option value="">--Select Timezone--</option>';
                if(timezone.length > 0) {
                    $(timezone).each(function(key,val) {
                        selected = (val.id == timezone_id) ? 'selected' : '';
                        timezone_list += '<option value="'+val.id+'" '+selected+'>'+val.zone_name+'</option>';
                    });
                }
                _this.closest('form').find(".timezone-list").html('').html(timezone_list);
            }
        });
    }
</script>