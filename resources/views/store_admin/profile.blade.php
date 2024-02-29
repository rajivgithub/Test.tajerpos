<!DOCTYPE html>
<html lang="en">
    <head>
        @include('common.store_admin.header')
    </head>
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.store_admin.navbar')
            @include('common.store_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <section class="content ">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="mb-0">Profile</h4>
                            </div>
                            <div class="card-body">
                                <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.profile') }}"  enctype="multipart/form-data">
                                @csrf
                                    <input type="hidden" class="state-list-url" value="{{ route('state-list')}}">
                                    <input type="hidden" class="city-list-url" value="{{ route('city-list')}}">
                                    <input type="hidden" name="user_id" class="user-id" value="{{!empty($admin_details) && !empty($admin_details[0]->id) ? Crypt::encrypt($admin_details[0]->id) : '' }}">
                                    <input type="hidden" class="email-path" value="{{ route('email-exist') }}">
                                    <input type="hidden" name="is_admin" value="2">
                                    <input type="hidden" name="store_id" class="store-id" value="{{!empty($admin_details) && !empty($admin_details[0]->store_id) ? Crypt::encrypt($admin_details[0]->store_id) : '' }}">
                                    @php
                                        $profile_image = !empty($admin_details) && !empty($admin_details[0]->profile_image) ? $admin_details[0]->profile_image : '';
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Name</label>
                                                <input type="text" data-label = "Name" placeholder="Mr. Customer" name="name" value = "{{!empty($admin_details) && !empty($admin_details[0]->name) ? $admin_details[0]->name : '' }}" class="form-control required-field">
                                                @if ($errors->has('name'))
                                                    <span class="text-danger error-message">{{ $errors->first('name') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Phone Number</label>
                                                <input type="text" data-label = "Phone Number" placeholder="00000000" name="phone_number" value = "{{!empty($admin_details) && !empty($admin_details[0]->phone_number) ? $admin_details[0]->phone_number : '' }}" onkeypress="return isNumber(event)" class="form-control required-field">
                                                @if ($errors->has('phone_number'))
                                                    <span class="text-danger error-message">{{ $errors->first('phone_number') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Email Address</label>
                                                <input type="email" data-label = "Email Address" data-type="store-admin" placeholder="admin@gmail.com" name="email" value = "{{!empty($admin_details) && !empty($admin_details[0]->email) ? $admin_details[0]->email : '' }}" class="form-control required-field email-field" >
                                                @if ($errors->has('email'))
                                                    <span class="text-danger error-message">{{ $errors->first('email') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>

                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Street Name</label>
                                                <input type="text" data-label = "Street Name" placeholder="Street Name" name="street_name" value = "{{!empty($admin_details) && !empty($admin_details[0]->street_name) ? $admin_details[0]->street_name : '' }}" class="form-control required-field">
                                                @if ($errors->has('street_name'))
                                                    <span class="text-danger error-message">{{ $errors->first('street_name') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Building Name/No.</label>
                                                <input type="text" data-label = "Building Name/No." placeholder="Building Name/No." name="building_name" value = "{{!empty($admin_details) && !empty($admin_details[0]->building_name) ? $admin_details[0]->building_name : '' }}" class="form-control required-field">
                                                @if ($errors->has('building_name'))
                                                    <span class="text-danger error-message">{{ $errors->first('building_name') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>


                                            <!-- <div class="mb-4 input-field-div">
                                                <label class="form-label">Address</label>
                                                <textarea  type="text" data-label = "Address" placeholder="your address" name="address" class="form-control required-field">{{!empty($admin_details) && !empty($admin_details[0]->address) ? $admin_details[0]->address : '' }}</textarea>
                                                @if ($errors->has('address'))
                                                    <span class="text-danger error-message">{{ $errors->first('address') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div> -->
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Company Name</label>
                                                <input type="text" placeholder="Company Name" data-label = "Company Name" name="company_name" value = "{{!empty($admin_details) && !empty($admin_details[0]->company_name) ? $admin_details[0]->company_name : '' }}" class="form-control required-field">
                                                @if ($errors->has('company_name'))
                                                    <span class="text-danger error-message">{{ $errors->first('company_name') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-4 input-field-div">
                                                        <label class="form-label">Country</label>
                                                        <select class="form-control required-field country-list dropdown-search" data-label = "Country" name="country_id">
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
                                                </div>
                                                <input type="hidden" class="state-id" value="{{!empty($admin_details) && !empty($admin_details[0]->state_id) ? $admin_details[0]->state_id : ''}}">
                                                <input type="hidden" class="city-id" value="{{!empty($admin_details) && !empty($admin_details[0]->city_id) ? $admin_details[0]->city_id : ''}}">
                                                <div class="col-md-4">
                                                    <div class="mb-4 input-field-div">
                                                        <label class="form-label">State</label>
                                                        <select class="form-control required-field state-list dropdown-search" data-label = "State" name="state_id">
                                                            <option value="">--Select State--</option>    
                                                        </select>
                                                        @if ($errors->has('state_id'))
                                                            <span class="text-danger error-message">{{ $errors->first('state_id') }}</span>
                                                        @endif
                                                        <span class="error error-message"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-4 input-field-div">
                                                        <label class="form-label">City</label>
                                                        <select class="form-control required-field city-list dropdown-search" data-label = "City" name="city_id">
                                                            <option value="">--Select City--</option>  
                                                        </select>
                                                        @if ($errors->has('city_id'))
                                                            <span class="text-danger error-message">{{ $errors->first('city_id') }}</span>
                                                        @endif
                                                        <span class="error error-message"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-4 input-field-div">
                                                        <label class="form-label">Postal Code</label>
                                                        <input type="text" data-label = "Postal Code" name="postal_code" placeholder="Postal Code" value = "{{!empty($admin_details) && !empty($admin_details[0]->postal_code) ? $admin_details[0]->postal_code : '' }}" onkeypress="return isNumber(event)" class="form-control required-field" >
                                                        @if ($errors->has('postal_code'))
                                                            <span class="text-danger error-message">{{ $errors->first('postal_code') }}</span>
                                                        @endif
                                                        <span class="error error-message"></span>
                                                    </div>
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
                                            <!-- <div class="mb-4 input-field-div">
                                                <label class="form-label">Avatar (90x90)</label>
                                                <div class="input-upload">                                    
                                                    <input class="form-control image-field" data-type="image" type="file" data-label = "Avatar" name="profile_image">
                                                    <div class="file-preview row">
                                                        <div class="d-flex mt-2 ms-2 file-preview-item">
                                                            <div class="align-items-center thumb">
                                                                <img src="{{ $profile_image }}" class="img-fit image-preview" alt="Item">
                                                            </div>
                                                            <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> -->

                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Profile (100 x 100)</label>
                                                <div class="input-upload my-file">             
                                                    <input type="hidden" name="remove_image" class="remove-image" value="0">                       
                                                    <input class="upload form-control image-field mb-2" data-type="image" type="file" data-label = "Profile" name="profile_image">
                                                    <div class="file-preview row">
                                                        <div class="d-flex mt-2 ms-2 file-preview-item">
                                                            <div class="align-items-center thumb">
                                                                <img src="{{ $profile_image }}" class="img-fit image-preview" data-type="Profile" alt="Item">
                                                            </div>
                                                            <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                                                        </div>
                                                    </div>
                                                    <div class="profile-image-preview dnone"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="text-right">
                                                <button class="btn btn-primary" id="save-profile-info">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            @include('common.store_admin.copyright')
        </div>
        @include('common.store_admin.footer')
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
                $('.dropdown-search').select2();
            });
        </script>
   </body>
</html>