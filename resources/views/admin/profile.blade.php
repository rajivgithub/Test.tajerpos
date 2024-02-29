<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ trans('admin.profile_title') }}</title>
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
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>{{ trans('admin.profile_cart_title') }}</h4>
                        </div>
                        <div class="card-body">
                            <form  method="POST" action="{{ route(config('app.prefix_url').'.admin.profile') }}" class="form-element-data"  enctype="multipart/form-data">
                            @csrf
                                @php
                                    $profile_image = !empty($admin_details) && !empty($admin_details[0]->profile_image) ? $admin_details[0]->profile_image : '';
                                    $company_logo = !empty($admin_details) && !empty($admin_details[0]->company_logo) ? $admin_details[0]->company_logo : '';
                                    $company_logo_validation = !empty($admin_details) && !empty($admin_details[0]->company_logo) ? 'optional-field' : 'required-field';
                                @endphp
                                <input type="hidden" class="state-list-url" value="{{ route('state-list')}}">
                                <input type="hidden" class="city-list-url" value="{{ route('city-list')}}">
                                <input type="hidden" name="user_id" class="user-id" value="{{!empty($admin_details) && !empty($admin_details[0]->id) ? Crypt::encrypt($admin_details[0]->id) : '' }}">
                                <input type="hidden" class="email-path" value="{{ route('email-exist') }}">
                                <input type="hidden" name="is_admin" value="1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.name') }}<span>*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fa fa-user"></i></span>									
                                                <input type="text" data-label = "{{ trans('admin.name') }}" data-error-msg="{{ __('validation.invalid_name_err') }}" data-pattern="^[A-Za-z\u0600-\u06FF. ]+$" onkeypress="return restrictCharacters(event)" data-max="100" name="name" value = "{{!empty($admin_details) && !empty($admin_details[0]->name) ? $admin_details[0]->name : '' }}" class="form-control required-field form-input-field user-name">
                                            </div>
                                            @if ($errors->has('name'))
                                                <span class="text-danger error-message">{{ $errors->first('name') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.company_name') }}<span>*</span></label>
                                            <input type="text" data-label="{{ trans('admin.company_name') }}" data-error-msg="{{ __('validation.invalid_company_name_err') }}" data-pattern="^[',\-A-Za-z0-9 .&()\u0600-\u06FF]+$" onkeypress="return restrictCharacters(event)" data-max="150" name="company_name" value="{{ !empty($admin_details) && !empty($admin_details[0]->company_name) ? $admin_details[0]->company_name : '' }}" class="form-control required-field form-input-field">
                                            @if ($errors->has('company_name'))
                                                <span class="text-danger error-message">{{ $errors->first('company_name') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.phone_number') }}<span>*</span></label>
                                            <div class="input-group">                                        
                                                <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                                <input type="text" data-label = "{{ trans('admin.phone_number') }}" data-min="10" data-max="12" name="phone_number" value = "{{!empty($admin_details) && !empty($admin_details[0]->phone_number) ? $admin_details[0]->phone_number : '' }}" data-pattern="^[0-9]+$" data-error-msg="{{ __('validation.invalid_numeric_err') }}" onkeypress="return restrictCharacters(event)" class="form-control required-field form-input-field"> 
                                            </div>
                                            @if ($errors->has('phone_number'))
                                                <span class="text-danger error-message">{{ $errors->first('phone_number') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.email_address') }}<span>*</span></label>  
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                                <input type="email" data-label = "{{ trans('admin.email_address') }}" data-type="admin" data-error-msg="{{ __('validation.email_invalid_msg') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF._%+\-@]+$" onkeypress="return restrictCharacters(event)" data-max="100" name="email" value = "{{!empty($admin_details) && !empty($admin_details[0]->email) ? $admin_details[0]->email : '' }}" class="form-control required-field form-input-field email-field" >
                                            </div>
                                            @if ($errors->has('email'))
                                                <span class="text-danger error-message">{{ $errors->first('email') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.building_name') }}<span>*</span></label>
                                            <input type="text" data-label = "{{ trans('admin.building_name') }}" data-max="100" data-error-msg="{{ __('validation.invalid_address_err') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF ',./&()+-]+$" onkeypress="return restrictCharacters(event)" name="building_name" value = "{{!empty($admin_details) && !empty($admin_details[0]->building_name) ? $admin_details[0]->building_name : '' }}" class="form-control required-field form-input-field">
                                            @if ($errors->has('building_name'))
                                                <span class="text-danger error-message">{{ $errors->first('building_name') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label">{{ trans('admin.street_name') }}<span>*</span></label>
                                            <input type="text" data-label = "{{ trans('admin.street_name') }}" data-max="100" name="street_name" data-error-msg="{{ __('validation.invalid_address_err') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF ',./&()+-]+$" onkeypress="return restrictCharacters(event)" value = "{{!empty($admin_details) && !empty($admin_details[0]->street_name) ? $admin_details[0]->street_name : '' }}" class="form-control required-field form-input-field">
                                            @if ($errors->has('street_name'))
                                                <span class="text-danger error-message">{{ $errors->first('street_name') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <!-- <div class="mb-4 input-field-div">
                                            <label class="form-label">Address</label>
                                            <textarea  type="text" data-label = "Address" placeholder="your address" name="address" class="form-control required-field form-input-field">{{!empty($admin_details) && !empty($admin_details[0]->address) ? $admin_details[0]->address : '' }}</textarea>
                                            @if ($errors->has('address'))
                                                <span class="text-danger error-message">{{ $errors->first('address') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div> -->
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ trans('admin.country') }}<span>*</span></label>
                                                <select class="form-control required-field form-input-field country-list dropdown-search" data-label = "{{ trans('admin.country') }}" name="country_id">
                                                    <option value="">--Select Country--</option> 
                                                    @if(isset($countries) && !empty($countries))
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}" {{!empty($admin_details) && !empty($admin_details[0]->country_id) && ($admin_details[0]->country_id == $country->id) ? "selected" : '' }}>{{ $country->name }}</option> 
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if ($errors->has('country_id'))
                                                    <span class="text-danger error-message">{{ $errors->first('country_id') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <input type="hidden" class="state-id" value="{{!empty($admin_details) && !empty($admin_details[0]->state_id) ? $admin_details[0]->state_id : ''}}">
                                            <input type="hidden" class="city-id" value="{{!empty($admin_details) && !empty($admin_details[0]->city_id) ? $admin_details[0]->city_id : ''}}">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ trans('admin.state') }}<span>*</span></label>
                                                <select class="form-select required-field form-input-field state-list dropdown-search" data-label = "{{ trans('admin.state') }}" name="state_id">
                                                    <option value="">--Select State--</option>    
                                                </select>
                                                @if ($errors->has('state_id'))
                                                    <span class="text-danger error-message">{{ $errors->first('state_id') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ trans('admin.city') }}<span>*</span></label>
                                                <select class="form-select required-field form-input-field city-list dropdown-search" data-label = "{{ trans('admin.city') }}" name="city_id">
                                                    <option value="">--Select City--</option>  
                                                </select>
                                                @if ($errors->has('city_id'))
                                                    <span class="text-danger error-message">{{ $errors->first('city_id') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">  
                                                <label class="form-label">{{ trans('admin.postal_code') }}<span>*</span></label>
                                                <input type="text" data-label = "{{ trans('admin.postal_code') }}" data-error-msg="{{ __('validation.invalid_numeric_err') }}" name="postal_code"  data-min="5" data-max="11" value = "{{!empty($admin_details) && !empty($admin_details[0]->postal_code) ? $admin_details[0]->postal_code : '' }}" data-pattern="^[0-9]+$" data-error-msg="{{ __('validation.invalid_numeric_err') }}" onkeypress="return restrictCharacters(event)" class="form-control required-field form-input-field">
                                                @if ($errors->has('postal_code'))
                                                    <span class="text-danger error-message">{{ $errors->first('postal_code') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                        </div>
                                        <!-- <div class="mb-4 input-field-div">
                                            <label class="form-label">Message</label>
                                            <textarea  type="text" data-label = "Message" placeholder="Message" name="message" class="form-control">{{!empty($admin_details) && !empty($admin_details[0]->message) ? $admin_details[0]->message : '' }}</textarea>
                                            @if ($errors->has('message'))
                                                <span class="text-danger error-message">{{ $errors->first('message') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div> -->
                                        <div class="mb-4 input-field-div"> 
                                            <label class="form-label">{{ trans('admin.profile') }}</label>
                                            <div class="input-upload">      
                                                <input type="hidden" name="remove_profile_image" class="remove-image" value="0">                                
                                                <input class="form-control image-field form-input-field" data-type="image" type="file" data-label = "{{ trans('admin.profile') }}" name="profile_image">
                                                <div class="file-preview row">
                                                    <div class="d-flex mt-2 ms-2 file-preview-item">
                                                        <div class="align-items-center thumb">
                                                            <img src="{{ $profile_image }}" class="img-fit image-preview" data-type = "Profile" alt="Item"> 
                                                        </div>
                                                        <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                                                    </div>
                                                </div>
                                                <div class="profile-image-preview dnone"></div>
                                            </div>
                                            @if ($errors->has('profile'))
                                                <span class="text-danger error-message">{{ $errors->first('profile') }}</span> 
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <!-- <div class="mb-4 input-field-div"> 
                                            <label class="form-label">{{ trans('admin.company_logo') }}<span>*</span></label>
                                            <div class="input-upload">        
                                                <input type="hidden" name="remove_company_logo" class="remove-image" value="0">                                     
                                                <input class="form-control image-field form-input-field {{ $company_logo_validation }}" data-type="image" type="file" data-label = "{{ trans('admin.company_logo') }}" name="company_logo">
                                                <div class="file-preview row">
                                                    <div class="d-flex mt-2 ms-2 file-preview-item">
                                                        <div class="align-items-center thumb">
                                                            <img src="{{ $company_logo }}" class="img-fit image-preview" alt="Item"> 
                                                        </div>
                                                        <div class="remove"><button class="btn btn-sm btn-link remove-attachment" data-image-type = "required" type="button"><i class="fa fa-close"></i></button></div>
                                                    </div>
                                                </div>
                                                <div class="company-logo-preview dnone"></div>
                                            </div>
                                            @if ($errors->has('company_logo'))
                                                <span class="text-danger error-message">{{ $errors->first('company_logo') }}</span> 
                                            @endif
                                            <span class="error error-message"></span>
                                        </div> -->
                                        <div class="text-end">
                                            <button class="btn btn-md rounded font-sm hover-up" id="save-profile-info">{{ trans('admin.save') }}</button>
                                        </div>
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
        <script>
            $(document).on("click","#save-profile-info",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else 
                    return true;     
            });
            $(document).ready(function() {
                $('.dropdown-search').select2();
            });
        </script>
    </body>
</html>