<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ ($mode == "add") ? trans('admin.add_store_title') : trans('admin.edit_store_title') }}</title>
        @include('common.admin.header')
    </head>
    <body>
        <div class="page-loader"><div class="spinner"></div></div>  
        <div class="screen-overlay"></div>
        @include('common.admin.navbar')
        <main class="main-wrap">
            @include('common.admin.sidebar')
            <section class="content-main">  
                @include('common.admin.search')
                <div class="body-content">
                    <div class="content-header">
                        <div>
                            <h2 class="content-title card-title">{{ ($mode == "add") ? trans('admin.add_new_store_title') : trans('admin.edit_store') }}</h2>                       
                        </div> 			
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>{{ trans('admin.store_info') }}</h4>
                        </div>
                        <div class="card-body">
                            <form  method="POST" action="{{ route(config('app.prefix_url').'.admin.store.store') }}" class="form-element-data store-form" enctype="multipart/form-data">
                            @csrf
                                <input type="hidden" name="mode" class="mode" value={{$mode}}> 
                                <input type="hidden" name="store_id" class="store_id" value="{{!empty($store_details) && !empty($store_details[0]->store_id) ? Crypt::encrypt($store_details[0]->store_id) : '' }}">
                                <input type="hidden" name="user_id" class="user-id" value="{{!empty($store_details) && !empty($store_details[0]->user_id) ? Crypt::encrypt($store_details[0]->user_id) : '' }}">
                                <input type="hidden" class="state-list-url" value="{{ route('state-list')}}">
                                <input type="hidden" class="city-list-url" value="{{ route('city-list')}}">
                                <input type="hidden" class="email-path" value="{{ route('email-exist') }}"> 
                                <input type="hidden" class="is_admin" value="2">
                                <input type="hidden" class="url" value="{{ route(config('app.prefix_url').'.admin.store.url-exist')}}">
                                @php
                                    $fields_validation = !empty($store_details) && !empty($store_details[0]->store_logo) ? 'optional-field' : 'required-field';
                                    $image_path = !empty($store_details) && !empty($store_details[0]->store_logo) ? $store_details[0]->store_logo : '';
                                    $background_img_validation = !empty($store_details) && !empty($store_details[0]->store_background_image) ? 'optional-field' : 'required-field';
                                    $background_image_path = !empty($store_details) && !empty($store_details[0]->store_background_image) ? $store_details[0]->store_background_image : '';
                                @endphp
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.owner_name') }}<span>*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fa fa-user"></i></span>									
                                                <input type="text" data-max="50" data-error-msg="{{ __('validation.invalid_name_err') }}" data-label = "{{ trans('admin.owner_name') }}" data-pattern="^[A-Za-z\u0600-\u06FF. ]+$" onkeypress="return restrictCharacters(event)" name="store_user_name" value = "{{!empty($store_details) && !empty($store_details[0]->store_user_name) ? $store_details[0]->store_user_name : '' }}" class="form-control required-field form-input-field" >
                                            </div>
                                            @if ($errors->has('store_user_name'))
                                                <span class="text-danger error-message">{{ $errors->first('store_user_name') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div> 
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.shop_name') }}<span>*</span></label>
                                            <input type="text" data-label = "{{ trans('admin.shop_name') }}" data-error-msg="{{ __('validation.invalid_company_name_err') }}" data-max="100" data-pattern="^[',\-A-Za-z\u0600-\u06FF0-9 .&()]+$" onkeypress="return restrictCharacters(event)" name="store_name" value = "{{!empty($store_details) && !empty($store_details[0]->store_name) ? $store_details[0]->store_name : '' }}" class="form-control required-field form-input-field" >
                                            @if ($errors->has('store_name'))
                                                <span class="text-danger error-message">{{ $errors->first('store_name') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.url') }}<span>*</span></label>
                                            <input type="text" data-label = "{{ trans('admin.url') }}" data-error-msg="{{ __('validation.invalid_url_err') }}" data-max="150" name="store_url" value = "{{!empty($store_details) && !empty($store_details[0]->store_url) ? $store_details[0]->store_url : '' }}" data-pattern="^[0-9-A-Za-z.%\/-]" onkeypress="return restrictCharacters(event)" class="form-control required-field form-input-field store-url" >
                                            @if ($errors->has('store_url'))
                                                <span class="text-danger error-message">{{ $errors->first('store_url') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label input-field-div">{{ trans('admin.phone_number') }}<span>*</span></label>
                                            <div class="input-group">                                        
                                                <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                                <input type="text" data-label = "{{ trans('admin.phone_number') }}" data-error-msg="{{ __('validation.invalid_numeric_err') }}" data-min="10" data-max="15" value = "{{!empty($store_details) && !empty($store_details[0]->store_phone_number) ? $store_details[0]->store_phone_number : '' }}" name="store_phone_number" data-pattern="^[0-9]+$" onkeypress="return restrictCharacters(event)" class="form-control required-field form-input-field store-phone-number" >
                                            </div>
                                            @if ($errors->has('store_phone_number'))
                                                <span class="text-danger error-message">{{ $errors->first('store_phone_number') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label input-field-div">{{ trans('admin.email_address') }}<span>*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                                <input type="text" data-error-msg="{{ __('validation.email_invalid_msg') }}" data-label = "{{ trans('admin.email_address') }}" data-max="100" data-pattern="^[A-Za-z\u0600-\u06FF0-9._%+\-@]+$" onkeypress="return restrictCharacters(event)" data-type="store_admin" value = "{{!empty($store_details) && !empty($store_details[0]->email) ? $store_details[0]->email : '' }}" name="email" class="form-control required-field form-input-field email-field">
                                            </div>
                                            @if ($errors->has('email'))
                                                <span class="text-danger error-message">{{ $errors->first('email') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label input-field-div">{{ trans('admin.password') }}<span>*</span></label>
                                            <div class="input-group">
                                                @php
                                                    $decrypted_password = !empty($store_details) && !empty($store_details[0]->plain_password) ? decrypt($store_details[0]->plain_password) : '';
                                                @endphp
                                                <input type="password" data-label = "{{ trans('admin.password') }}" data-error-msg="{{ __('validation.pwd_invalid_msg') }}" data-min="8" data-max="100" data-pattern="^[A-Za-z\u0600-\u06FF0-9!@#$%^&*_=.,~/<:;?+-]+$" onkeypress="return restrictCharacters(event)" name="store_password" class="form-control required-field form-input-field password" value="{{$decrypted_password}}">
                                                <div class="input-group-text"><span id="user-password" class="fa fa-fw fa-eye field_icon"></span></div>
                                            </div>
                                            @if ($errors->has('store_password'))
                                                <span class="text-danger error-message">{{ $errors->first('store_password') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.building_name') }}<span>*</span></label>
                                            <input type="text" data-label = "{{ trans('admin.building_name') }}" data-max="100" data-error-msg="{{ __('validation.invalid_address_err') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF ',./&()+-]+$" onkeypress="return restrictCharacters(event)" name="building_name" value = "{{!empty($store_details) && !empty($store_details[0]->building_name) ? $store_details[0]->building_name : '' }}" class="form-control required-field form-input-field">
                                            @if ($errors->has('building_name'))
                                                <span class="text-danger error-message">{{ $errors->first('building_name') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <!-- <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.validity') }}<span>*</span></label>
                                            <input type="date" data-label = "{{ trans('admin.validity') }}"  value = "{{!empty($store_details) && !empty($store_details[0]->store_validity_date) ? $store_details[0]->store_validity_date : '' }}" name="store_validity_date" class="form-control required-field form-input-field validity-date" >
                                            @if ($errors->has('store_validity_date'))
                                                <span class="text-danger error-message">{{ $errors->first('store_validity_date') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div> -->
                                        <!-- <div class='form-check'>
                                            <input class="form-check-input" type="checkbox" name="add_payment_details" value="1">
                                            <label class="form-check-label">Need to add payment details</label>
                                        </div> -->
                                    </div>
                                    <div class="col-md-6">
                                        <!-- <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.address') }}<span>*</span></label>
                                            <textarea data-label = "{{ trans('admin.address') }}" data-error-msg="{{ __('validation.invalid_address_err') }}" data-max="200" style="height: 131px;" data-pattern="^[A-Za-z0-9\u0600-\u06FF ',./&()+-]+$" onkeypress="return restrictCharacters(event)" name="store_address" class="form-control required-field form-input-field">{{!empty($store_details) && !empty($store_details[0]->store_address) ? $store_details[0]->store_address : '' }}</textarea>
                                            @if ($errors->has('store_address'))
                                                <span class="text-danger error-message">{{ $errors->first('store_address') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div> -->
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.street_name') }}<span>*</span></label>
                                            <input type="text" data-label = "{{ trans('admin.street_name') }}" data-max="100" name="street_name" data-error-msg="{{ __('validation.invalid_address_err') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF ',./&()+-]+$" onkeypress="return restrictCharacters(event)" value = "{{!empty($store_details) && !empty($store_details[0]->street_name) ? $store_details[0]->street_name : '' }}" class="form-control required-field form-input-field">
                                            @if ($errors->has('street_name'))
                                                <span class="text-danger error-message">{{ $errors->first('street_name') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.country') }}<span>*</span></label>
                                            <select class="form-select required-field form-input-field country-list dropdown-search" data-label = "{{ trans('admin.country') }}" name="store_country">
                                                <option value="">--Select Country--</option> 
                                                @if(isset($countries) && !empty($countries))
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}" {{!empty($store_details) && !empty($store_details[0]->country_id) && ($store_details[0]->country_id == $country->id) ? "selected" : '' }}>{{ $country->name }}</option> 
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('store_country'))
                                                <span class="text-danger error-message">{{ $errors->first('store_country') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <input type="hidden" class="state-id" value="{{!empty($store_details) && !empty($store_details[0]->state_id) ? $store_details[0]->state_id : ''}}">
                                        <input type="hidden" class="city-id" value="{{!empty($store_details) && !empty($store_details[0]->city_id) ? $store_details[0]->city_id : ''}}">
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.state') }}<span>*</span></label>
                                            <select class="form-select required-field form-input-field state-list dropdown-search" data-label = "{{ trans('admin.state') }}" name="store_state">
                                                <option value="">--Select State--</option>    
                                            </select>
                                            @if ($errors->has('store_state'))
                                                <span class="text-danger error-message">{{ $errors->first('store_state') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.city') }}<span>*</span></label>
                                            <select class="form-select required-field form-input-field city-list dropdown-search" data-label = "{{ trans('admin.city') }}" name="store_city">
                                                <option value="">--Select City--</option>  
                                            </select>
                                            @if ($errors->has('store_city'))
                                                <span class="text-danger error-message">{{ $errors->first('store_city') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.postal_code') }}<span>*</span></label>
                                            <input type="text" data-label = "{{ trans('admin.postal_code') }}" data-error-msg="{{ __('validation.invalid_numeric_err') }}" data-min="5" data-max="11" data-pattern="^[0-9]+$" onkeypress="return restrictCharacters(event)" value = "{{!empty($store_details) && !empty($store_details[0]->postal_code) ? $store_details[0]->postal_code : '' }}" name="store_postal_code" class="form-control required-field form-input-field" >
                                            @if ($errors->has('store_postal_code'))
                                                <span class="text-danger error-message">{{ $errors->first('store_postal_code') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label input-field-div">{{ trans('admin.store_logo') }}<span>*</span></label>
                                            <input type="file" data-type="image" data-label = "{{ trans('admin.store_logo') }}" name="store_logo_image" class="form-control form-input-field {{$fields_validation}} image-field" value="">
                                            <div class="file-preview row">
                                                <div class="d-flex mt-2 ms-2 file-preview-item">
                                                    <div class="align-items-center thumb">
                                                        <input type="hidden" class="image-path-data" value="{{ $image_path }}">
                                                        <img src="{{ $image_path }}" class="img-fit image-preview" alt="Item">
                                                    </div>
                                                    <div class="remove"><button class="btn btn-sm btn-link remove-attachment" data-image-type = "required" type="button"><i class="fa fa-close"></i></button></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('store_logo_image'))
                                                <span class="text-danger error-message">{{ $errors->first('store_logo_image') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label input-field-div">{{ trans('admin.login_background_image') }}<span>*</span></label>
                                            <input type="file" data-type="image" data-label = "{{ trans('admin.login_background_image') }}" name="store_background_image" class="form-control form-input-field {{$background_img_validation}} image-field" value="">
                                            <div class="file-preview row">
                                                <div class="d-flex mt-2 ms-2 file-preview-item">
                                                    <div class="align-items-center thumb">
                                                        <input type="hidden" class="image-path-data" value="{{ $background_image_path }}">
                                                        <img src="{{ $background_image_path }}" class="img-fit image-preview" alt="Item">
                                                    </div>
                                                    <div class="remove"><button class="btn btn-sm btn-link remove-attachment" data-image-type = "required" type="button"><i class="fa fa-close"></i></button></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('store_background_image'))
                                                <span class="text-danger error-message">{{ $errors->first('store_background_image') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-md rounded font-sm hover-up" id="save-store-info">{{ trans('admin.save') }}</button>
                                    </div>
                                </div>                    
                            </form>
                        </div>
                    </div>
                </div>
            </section>
            @include('common.admin.footer')
        </main>
        @include('common.admin.script')
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script src="{{ URL::asset('assets/js/select2.min.js') }}"></script>
        <script>
            $(document).on("click","#save-store-info",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
            //Minimum validation for Date field
            // var today = new Date();
            // var month = today.getMonth()+1;
            // var date = today.getDate();
            // var min_date = today.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (date <10 ? '0' : '') + date;
            // $(".validity-date").attr("min",min_date);

            // Check URL is Unique
            $(document).on("change",".store-url",function() {
                store_url = $(this).val();
                if(store_url != "")
                    isURLExist($(this));
            });
            $(document).ready(function() {
                $('.dropdown-search').select2();
            });
        </script>
    </body>
</html>