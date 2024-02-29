<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ ($mode == "add") ? trans('store-admin.explore_dashboard_banner_module',['company' => Auth::user()->company_name]) : trans('store-admin.edit_customer_module_dashboard_banner',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header')
        <link rel="stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"/> 
        <style>
            .image-preview {
                width: 250px;
                height: 95px;
                margin-left: 15px;
            }
        </style>
    </head>
    @php
        $prefix_url = config('app.module_prefix_url');
    @endphp
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <section class="content">
                        <div class="card mb-4">
                            <div class="content-header">
                                <div class="d-flex align-items-center">
                                    <div class="mr-auto">
                                        @php
                                            $page_title = ($mode == "add") ? __('store-admin.add_dashboard_banner') : __('store-admin.edit_dashboard_banner');
                                        @endphp
                                        <h3 class="page-title">{{$page_title}}</h3>
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="card-body">
                                <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.customer-banners.store') }}" enctype="multipart/form-data">
                                @csrf
                                    @php
                                        $banner_image_path = !empty($banner_details) && !empty($banner_details[0]->banner_image) ? $banner_details[0]->banner_image : '';
                                        $banner_image_validation = !empty($banner_details) && !empty($banner_details[0]->banner_image) ? 'optional-field' : 'required-field';
                                    @endphp
                                    <input type="hidden" name="mode" value={{$mode}}> 
                                    <input type="hidden" name="status" value="{{!empty($banner_details) && !empty($banner_details[0]->status) ? $banner_details[0]->status : '' }}"> 
                                    <input type="hidden" name="banner_id" class="banner-id" value="{{!empty($banner_details) && !empty($banner_details[0]->banner_id) ? Crypt::encrypt($banner_details[0]->banner_id) : '' }}">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.banner_image') }} (1440 x 470)<span>*</span></label>
                                                <div class="input-upload my-file">  
                                                    <input type="hidden" name="remove_banner_image" class="remove-image" value="0">                                  
                                                    <input class="form-control {{ $banner_image_validation }} image-field" data-type="image" data-label = "{{ __('store-admin.banner_image') }}" name="banner_image" type="file">									
                                                </div>
                                                <div class="file-preview row">
                                                    <div class="d-flex mt-2 ms-2 file-preview-item">
                                                        <div class="align-items-center thumb">
                                                            <img src="{{ $banner_image_path }}" class="img-fit image-preview" alt="Item">
                                                        </div>
                                                        <div class="remove"><button class="btn btn-md btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                                                    </div>
                                                </div>
                                                @if ($errors->has('banner_image'))
                                                    <span class="text-danger error-message">{{ $errors->first('banner_image') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <!-- <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.redirect_url') }}<span>*</span></label>
                                                <input type="url" class="form-control required-field" id="exampleUrl" data-label = "{{ __('store-admin.redirect_url') }}" placeholder="https://example.com" name="banner_url"  value="{{!empty($banner_details) && !empty($banner_details[0]->banner_url) ? $banner_details[0]->banner_url : '' }}">
                                                @if ($errors->has('banner_url'))
                                                    <span class="text-danger error-message">{{ $errors->first('banner_url') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div> -->
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.start_date') }}</label>
                                                <input class="form-control date-time-picker" name="start_date" type="text"  value="{{!empty($banner_details) && !empty($banner_details[0]->start_date) ? $banner_details[0]->start_date : '' }}" />
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.end_date') }}</label>
                                                <input type="text" class="form-control date-time-picker" name="end_date"  value="{{!empty($banner_details) && !empty($banner_details[0]->end_date) ? $banner_details[0]->end_date : '' }}">
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.sales_channels') }}<span>*</span></label>
                                                <div class="form-group">
                                                    <div class="radio-list">
                                                        <label class="radio-inline p-0 mr-10">
                                                            <div class="radio radio-info">
                                                                <input type="radio" name="banner_type" id="channel1" value="web" {{!empty($banner_details) && !empty($banner_details[0]->banner_type) && ($banner_details[0]->banner_type == "web") ? "checked" : '' }}>
                                                                <label for="channel1">{{ __('store-admin.web') }}</label>
                                                            </div>
                                                        </label>
                                                        <label class="radio-inline p-0 mr-10">
                                                            <div class="radio radio-info">
                                                                <input type="radio" name="banner_type" id="channel2" value="app" {{!empty($banner_details) && !empty($banner_details[0]->banner_type) && ($banner_details[0]->banner_type == "app") ? "checked" : '' }}>
                                                                <label for="channel2">{{ __('store-admin.app') }}</label>
                                                            </div>
                                                        </label>
                                                        <label class="radio-inline">
                                                            <div class="radio radio-info">
                                                                <input type="radio" name="banner_type" id="channel3" value="both" {{(!empty($banner_details) && !empty($banner_details[0]->banner_type) && ($banner_details[0]->banner_type == "both") || $mode == "add") ? "checked" : '' }}>
                                                                <label for="channel3">{{ __('store-admin.both') }}</label>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <span class="error error-message"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <button class="btn btn-primary" id="save-banner-settings">{{ __('store-admin.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            @include('common.cashier_admin.copyright')
        </div>
        @include('common.cashier_admin.footer')
        <script src= "https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script> 
        <script>
            $('.date-time-picker').datetimepicker({
                format: 'd-m-Y H:i'
            });
            $(document).on("click","#save-banner-settings",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
        </script>
    </body>
</html>