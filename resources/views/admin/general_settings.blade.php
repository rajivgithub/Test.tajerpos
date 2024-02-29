<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ trans('admin.general_settings_title') }}</title>
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
                            <h4>{{ trans('admin.general_settings') }}</h4>
                        </div>
                        <div class="card-body">
                            <form  method="POST" action="{{ route(config('app.prefix_url').'.general-settings') }}" class="form-element-data" enctype="multipart/form-data">
                            @csrf
                                @php
                                    $sidebar_logo = !empty($moduleLogos) && isset($moduleLogos['sidebar_logo']) ? $moduleLogos['sidebar_logo'] : '';
                                    $company_logo = !empty($moduleLogos) && isset($moduleLogos['company_logo']) ? $moduleLogos['company_logo'] : '';
                                    $favicon = !empty($moduleLogos) && isset($moduleLogos['favicon']) ? $moduleLogos['favicon'] : '';
                                    $company_logo_validation = !empty($moduleLogos) && !empty($moduleLogos['company_logo']) ? 'optional-field' : 'required-field';
                                    $favicon_validation = !empty($moduleLogos) && !empty($moduleLogos['favicon']) ? 'optional-field' : 'required-field';
                                @endphp
                                <input type="hidden" name="module_name" value="admin"> 
                                <input type="hidden" id="logo-id" name="logo_id" value="{{ !empty($moduleLogos) && isset($moduleLogos['logo_id']) ? Crypt::encrypt($moduleLogos['logo_id']) : '' }}"> 
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label input-field-div">{{ trans('admin.company_logo') }}<span>*</span></label>
                                            <input type="hidden" name="remove_login_logo" class="remove-login-logo remove-image" value="0">     
                                            <input type="file" data-type="image" data-label = "{{ trans('admin.company_logo') }}" id="login-page-logo" name="company_logo" class="form-control form-input-field image-field {{ $company_logo_validation }}" value="">
                                            <div class="file-preview row">
                                                <div class="d-flex mt-2 ms-2 file-preview-item">
                                                    <div class="align-items-center thumb">
                                                        <input type="hidden" class="image-path-data login-logo-path" value="{{ $company_logo }}"> 
                                                        <img src="{{ $company_logo }}" class="img-fit image-preview" alt="Item">
                                                    </div>
                                                    <div class="remove"><button class="btn btn-sm btn-link remove-attachment" data-image-type = "required" data-type="logo" type="button"><i class="fa fa-close"></i></button></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('company_logo'))
                                                <span class="text-danger error-message">{{ $errors->first('company_logo') }}</span> 
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                        <!-- <div class="mb-4 input-field-div">
                                            <label class="form-label input-field-div">{{ trans('admin.sidebar_logo') }}</label>
                                            <input type="hidden" name="remove_sidebar_logo" class="remove-image remove-sidebar-logo" value="0">   
                                            <input type="file" data-type="image" data-label = "{{ trans('admin.sidebar_logo') }}" id="sidebar-logo" name="sidebar_logo" class="form-control form-input-field image-field" value="">
                                            <div class="file-preview row">
                                                <div class="d-flex mt-2 ms-2 file-preview-item">
                                                    <div class="align-items-center thumb">
                                                        <input type="hidden" class="image-path-data sidebar-logo-path" value="{{ $sidebar_logo }}"> 
                                                        <img src="{{ $sidebar_logo }}" class="img-fit image-preview" alt="Item">
                                                    </div>
                                                    <div class="remove"><button class="btn btn-sm btn-link remove-attachment" data-type="logo" type="button"><i class="fa fa-close"></i></button></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('sidebar_logo'))
                                                <span class="text-danger error-message">{{ $errors->first('sidebar_logo') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div> -->
                                        <div class="mb-4 input-field-div">
                                            <label class="form-label input-field-div">{{ trans('admin.favicon') }}<span>*</span></label>
                                            <input type="hidden" name="remove_favicon" class="remove-image remove-favicon" value="0">   
                                            <input type="file" data-type="image" data-label = "{{ trans('admin.favicon') }}" id="favicon" name="favicon" class="form-control form-input-field image-field {{ $favicon_validation }}" value="">
                                            <div class="file-preview row">
                                                <div class="d-flex mt-2 ms-2 file-preview-item">
                                                    <div class="align-items-center thumb">
                                                        <input type="hidden" class="image-path-data favicon-path" value="{{ $favicon }}"> 
                                                        <img src="{{ $favicon }}" class="img-fit image-preview" alt="Item">
                                                    </div>
                                                    <div class="remove"><button class="btn btn-sm btn-link remove-attachment" data-image-type = "required" data-type="logo" type="button"><i class="fa fa-close"></i></button></div>
                                                </div>
                                            </div>
                                            @if ($errors->has('favicon'))
                                                <span class="text-danger error-message">{{ $errors->first('favicon') }}</span>
                                            @endif
                                            <span class="error error-message"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">&nbsp;</div>
                                    <div class="text-end">
                                        <button class="btn btn-md rounded font-sm hover-up" id="save-general-settings">{{ trans('admin.save') }}</button>
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
            $(document).on("click","#save-general-settings",function() { 
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true; 
            });
        </script>
    </body>
</html>