<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ __('store-admin.profile_title',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header')
        <style>
            .img-md {
                width: 112px;
                height: 112px;
            }
        </style>
    </head>
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <section class="content ">
                        <div class="card mb-4">
                            <div class="card-header">
                                <ul class="nav nav-pills mb-0">
                                    <li class=" nav-item"> <a href="#navpills-1" class="nav-link active" data-toggle="tab" aria-expanded="false">{{ __('store-admin.your_profile_details') }}</a> </li>
                                    <li class="nav-item"> <a href="#navpills-2" class="nav-link" data-toggle="tab" aria-expanded="false">{{ __('store-admin.store_details') }}</a> </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="box-body">
                                    <div class="tab-content">
                                        <div id="navpills-1" class="tab-pane active">
                                            <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.profile') }}" class="form-element-data" enctype="multipart/form-data">
                                            @csrf
                                                @php
                                                    $profile_image = !empty($cashier_admin_details) && isset($cashier_admin_details['profile_image']) ? $cashier_admin_details['profile_image'] : '';
                                                @endphp
                                                <input type="hidden" name="user_id" class="user-id" value="{{!empty($cashier_admin_details) && isset($cashier_admin_details['id']) ? Crypt::encrypt($cashier_admin_details['id']) : '' }}">
                                                <input type="hidden" class="email-path" value="{{ route('email-exist') }}">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ __('store-admin.name') }}<span>*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                                </div>
                                                                <input type="text" data-label = "{{ __('store-admin.name') }}" data-error-msg="{{ __('validation.invalid_name_err') }}" data-pattern="^[A-Za-z\u0600-\u06FF. ]+$" onkeypress="return restrictCharacters(event)" data-max="100" name="name" value = "{{!empty($cashier_admin_details) && isset($cashier_admin_details['name']) ? $cashier_admin_details['name'] : '' }}" class="form-control required-field form-input-field auth-user-name">
                                                            </div>
                                                            @if ($errors->has('name'))
                                                                <span class="text-danger error-message">{{ $errors->first('name') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ __('store-admin.email_address') }}</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                                                </div>
                                                                <input type="email" data-label = "{{ __('store-admin.email_address') }}" data-type="cashier-admin" name="email" value = "{{!empty($cashier_admin_details) && isset($cashier_admin_details['email']) ? $cashier_admin_details['email'] : '' }}" class="form-control required-field form-input-field email-field" disabled>
                                                            </div>
                                                            @if ($errors->has('email'))
                                                                <span class="text-danger error-message">{{ $errors->first('email') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ __('store-admin.phone_number') }}<span>*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                                                </div>
                                                                <input type="text" data-label = "{{ __('store-admin.phone_number') }}" data-min="10" data-max="12" name="phone_number" value = "{{!empty($cashier_admin_details) && isset($cashier_admin_details['phone_number']) ? $cashier_admin_details['phone_number'] : '' }}" data-pattern="^[0-9]+$" data-error-msg="{{ __('validation.invalid_numeric_err') }}" onkeypress="return restrictCharacters(event)" class="form-control required-field form-input-field">
                                                            </div>
                                                            @if ($errors->has('phone_number'))
                                                                <span class="text-danger error-message">{{ $errors->first('phone_number') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ __('store-admin.profile') }} (100 x 100)</label>
                                                            <div class="input-upload my-file">             
                                                                <input type="hidden" name="remove_image" class="remove-image" value="0">                      
                                                                <input class="upload form-control image-field mb-2 form-input-field" data-type="image" type="file" data-label = "Avatar" name="profile_image">
                                                                <div class="file-preview row ml-0">
                                                                    <div class="d-flex mt-2 ms-2 file-preview-item">
                                                                        <div class="align-items-center thumb">
                                                                            <img src="{{ $profile_image }}" class="img-fit image-preview img-md" data-type="{{ __('store-admin.profile') }}" alt="Item">
                                                                        </div>
                                                                        <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                                                                    </div>
                                                                </div>
                                                                @if ($errors->has('profile_image'))
                                                                    <span class="text-danger error-message">{{ $errors->first('profile_image') }}</span>
                                                                @endif
                                                                <span class="error error-message"></span>
                                                                <div class="profile-image-preview dnone"></div>
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-primary" id="save-profile-info">{{ __('store-admin.save') }}</button>
                                                    </div>
                                                    <div class="col-md-6">&nbsp;</div>
                                                </div>
                                            </form>
                                        </div>
                                        <div id="navpills-2" class="tab-pane">
                                            <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.update-store-details') }}" class="form-element-data">
                                            @csrf
                                                <input type="hidden" name="user_id" class="user-id" value="{{!empty($store_details) && isset($store_details['id']) ? Crypt::encrypt($store_details['id']) : '' }}">
                                                <input type="hidden" class="state-list-url" value="{{ route('state-list')}}">
                                                <input type="hidden" class="city-list-url" value="{{ route('city-list')}}">
                                                <input type="hidden" class="state-id" value="{{!empty($store_details) && isset($store_details['state_id']) ? $store_details['state_id'] : ''}}">
                                                <input type="hidden" class="city-id" value="{{!empty($store_details) && isset($store_details['city_id']) ? $store_details['city_id'] : ''}}">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ __('store-admin.owner_name') }}<span>*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                                </div>
                                                                <input type="text" data-label = "{{ __('store-admin.owner_name') }}" data-error-msg="{{ __('validation.invalid_name_err') }}" data-pattern="^[A-Za-z\u0600-\u06FF. ]+$" onkeypress="return restrictCharacters(event)" data-max="100" name="name" value = "{{!empty($cashier_admin_details) && isset($cashier_admin_details['name']) ? $cashier_admin_details['name'] : '' }}" class="form-control required-field form-input-field auth-user-name">
                                                            </div>
                                                            @if ($errors->has('name'))
                                                                <span class="text-danger error-message">{{ $errors->first('name') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ __('store-admin.store_name') }}<span>*</span></label>
                                                            <input type="text" data-label = "{{ trans('store-admin.store_name') }}" data-error-msg="{{ __('validation.invalid_company_name_err') }}" data-max="100" data-pattern="^[',\-A-Za-z\u0600-\u06FF0-9 .&()]+$" onkeypress="return restrictCharacters(event)" name="store_name" value = "{{!empty($store_details) && isset($store_details['company_name']) ? $store_details['company_name'] : '' }}" class="form-control required-field form-input-field" >
                                                            @if ($errors->has('store_name'))
                                                                <span class="text-danger error-message">{{ $errors->first('store_name') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ __('store-admin.email_address') }}</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                                                </div>
                                                                <input type="email" data-label = "{{ __('store-admin.email_address') }}" data-type="cashier-admin" name="email" value = "{{!empty($store_details) && isset($store_details['email']) ? $store_details['email'] : '' }}" class="form-control required-field form-input-field email-field" disabled>
                                                            </div>
                                                            @if ($errors->has('email'))
                                                                <span class="text-danger error-message">{{ $errors->first('email') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ __('store-admin.phone_number') }}<span>*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                                                </div>
                                                                <input type="text" data-label = "{{ __('store-admin.phone_number') }}" data-min="10" data-max="12" name="phone_number" value = "{{!empty($store_details) && isset($store_details['phone_number']) ? $store_details['phone_number'] : '' }}" data-pattern="^[0-9]+$" data-error-msg="{{ __('validation.invalid_numeric_err') }}" onkeypress="return restrictCharacters(event)" class="form-control required-field form-input-field">
                                                            </div>
                                                            @if ($errors->has('phone_number'))
                                                                <span class="text-danger error-message">{{ $errors->first('phone_number') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ trans('store-admin.building_name') }}<span>*</span></label>
                                                            <input type="text" data-label = "{{ trans('store-admin.building_name') }}" data-max="100" data-error-msg="{{ __('validation.invalid_address_err') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF ',./&()+-]+$" onkeypress="return restrictCharacters(event)" name="building_name" value = "{{!empty($store_details) && isset($store_details['building_name']) ? $store_details['building_name'] : '' }}" class="form-control required-field form-input-field">
                                                            @if ($errors->has('building_name'))
                                                                <span class="text-danger error-message">{{ $errors->first('building_name') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ trans('store-admin.street_name') }}<span>*</span></label>
                                                            <input type="text" data-label = "{{ trans('store-admin.street_name') }}" data-max="100" name="street_name" data-error-msg="{{ __('validation.invalid_address_err') }}" data-pattern="^[A-Za-z0-9\u0600-\u06FF ',./&()+-]+$" onkeypress="return restrictCharacters(event)" value = "{{!empty($store_details) && isset($store_details['street_name']) ? $store_details['street_name'] : '' }}" class="form-control required-field form-input-field">
                                                            @if ($errors->has('street_name'))
                                                                <span class="text-danger error-message">{{ $errors->first('street_name') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ trans('admin.country') }}<span>*</span></label>
                                                            <select class="form-control required-field form-input-field country-list dropdown-search" data-label = "{{ trans('admin.country') }}" name="store_country">
                                                                <option value="">--Select Country--</option> 
                                                                @if(isset($countries) && !empty($countries))
                                                                    @foreach ($countries as $country_id => $country)
                                                                        <option value="{{ $country_id }}" {{!empty($store_details) && isset($store_details['country_id']) && ($store_details['country_id'] == $country_id) ? "selected" : '' }}>{{ $country }}</option> 
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            @if ($errors->has('store_country'))
                                                                <span class="text-danger error-message">{{ $errors->first('store_country') }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ trans('admin.state') }}<span>*</span></label>
                                                            <select class="form-control required-field form-input-field state-list dropdown-search" data-label = "{{ trans('admin.state') }}" name="store_state">
                                                                <option value="">--Select State--</option>    
                                                            </select>
                                                            @if ($errors->has('store_state'))
                                                                <span class="text-danger error-message">{{ $errors->first('store_state') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ trans('admin.city') }}<span>*</span></label>
                                                            <select class="form-control required-field form-input-field city-list dropdown-search" data-label = "{{ trans('admin.city') }}" name="store_city">
                                                                <option value="">--Select City--</option>  
                                                            </select>
                                                            @if ($errors->has('store_city'))
                                                                <span class="text-danger error-message">{{ $errors->first('store_city') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                        <div class="mb-4 input-field-div">
                                                            <label class="form-label">{{ trans('admin.postal_code') }}<span>*</span></label>
                                                            <input type="text" data-label = "{{ trans('admin.postal_code') }}" data-error-msg="{{ __('validation.invalid_numeric_err') }}" data-min="5" data-max="11" data-pattern="^[0-9]+$" onkeypress="return restrictCharacters(event)" value = "{{!empty($store_details) && isset($store_details['postal_code']) ? $store_details['postal_code'] : '' }}" name="store_postal_code" class="form-control required-field form-input-field" >
                                                            @if ($errors->has('store_postal_code'))
                                                                <span class="text-danger error-message">{{ $errors->first('store_postal_code') }}</span>
                                                            @endif
                                                            <span class="error error-message"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="text-right">
                                                        <button class="btn btn-primary" id="save-profile-info">{{ __('store-admin.save') }}</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            @include('common.cashier_admin.copyright')
        </div>
        @include('common.cashier_admin.footer')
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script src="{{ URL::asset('assets/js/select2.min.js') }}"></script>
        <script>
            $(document).on("click","#save-profile-info",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else 
                    return true;     
            });
            $(document).ready(function() {
                // $('.dropdown-search').select2();
            });
        </script>
    </body>
</html>