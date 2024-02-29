<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ ($mode == "add") ? trans('store-admin.add_subcategory_title',['company' => Auth::user()->company_name]) : trans('store-admin.edit_subcategory_title',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header')
        <style>
            .img-md {
                width: 112px;
                height: 112px;
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
                                            $page_title = ($mode == "add") ? __('store-admin.add_new_subcategory') : __('store-admin.edit_sub_category');
                                        @endphp
                                        <h3 class="page-title">{{$page_title}}</h3>
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="card-body">
                                <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.sub-category.store') }}" enctype="multipart/form-data">
                                @csrf
                                    <!-- $banner_image_validation = !empty($banner_image_path) ? 'optional-field' : 'required-field';
                                    $icon_image_validation = !empty($icon_image_path) ? 'optional-field' : 'required-field';
                                    $sub_category_image_validation = !empty($sub_category_image_path) ? 'optional-field' : 'required-field'; -->
                                    @php
                                        $banner_image_path = !empty($sub_category_details) && !empty($sub_category_details[0]->banner) ? $sub_category_details[0]->banner : '';
                                        $icon_image_path = !empty($sub_category_details) && !empty($sub_category_details[0]->icon) ? $sub_category_details[0]->icon : '';
                                        $sub_category_image_path = !empty($sub_category_details) && !empty($sub_category_details[0]->sub_category_image) ? $sub_category_details[0]->sub_category_image : '';
                                        $banner_image_validation = 'optional-field';
                                        $icon_image_validation = 'optional-field';
                                        $sub_category_image_validation = 'optional-field';
                                    @endphp
                                    <input type="hidden" name="mode" value={{$mode}}> 
                                    <input type="hidden" name="sub_category_id" class="sub-category-id" value="{{!empty($sub_category_details) && !empty($sub_category_details[0]->sub_category_id) ? $sub_category_details[0]->sub_category_id : '' }}">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.category') }}<span>*</span></label>
                                                <select class="form-control required-field form-input-field dropdown-search" data-label = "{{ __('store-admin.category') }}" name="category_id">
                                                    <option value="">--Select Category--</option>
                                                    @if(isset($category_details) && !empty($category_details))
                                                        @foreach ($category_details as $category)
                                                            <option value="{{ $category->category_id }}" {{!empty($sub_category_details) && !empty($sub_category_details[0]->category_id) && ($sub_category_details[0]->category_id == $category->category_id) ? "selected" : '' }}>{{ $category->category_name }}</option> 
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if ($errors->has('category_id'))
                                                    <span class="text-danger error-message">{{ $errors->first('category_id') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.sub_category') }}<span>*</span></label>
                                                <input type="text" data-max="100" data-label = "{{ __('store-admin.sub_category') }}" name="sub_category_name" class="form-control required-field form-input-field"  data-pattern="^[A-Za-z\u0600-\u06FF0-9,._&\/+()\-\s|]*$" data-error-msg="{{ __('validation.invalid_category_err') }}" onkeypress="return restrictCharacters(event)" value="{{!empty($sub_category_details) && !empty($sub_category_details[0]->sub_category_name) ? $sub_category_details[0]->sub_category_name : '' }}">
                                                @if ($errors->has('sub_category_name'))
                                                    <span class="text-danger error-message">{{ $errors->first('sub_category_name') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">{{ __('store-admin.sub_category_url') }}<span>*</span></label>
                                                <input type="text" placeholder="{{ __('store-admin.sub_category_name_placeholder') }}"  data-max="100" data-label = "{{ __('store-admin.sub_category_url') }}" name="sub_category_url" class="form-control required-field form-input-field" data-pattern="^[A-Za-z0-9\-]*$" data-error-msg="{{ __('validation.invalid_url_err') }}" onkeypress="return restrictCharacters(event)" value="{{!empty($sub_category_details) && !empty($sub_category_details[0]->sub_category_url) ? $sub_category_details[0]->sub_category_url : '' }}">
                                                @if ($errors->has('sub_category_url'))
                                                    <span class="text-danger error-message">{{ $errors->first('sub_category_url') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div dnone">
                                                <label>Sub Category Image<small>(200x200)</small></label>
                                                <div class="input-upload my-file">         
                                                    <input type="hidden" name="remove_subcategory_image" class="remove-image" value="0">                              
                                                    <input class="form-control {{ $sub_category_image_validation }} image-field" data-type="image" data-label = "Sub Category Image" type="file" name="sub_category_image">									
                                                </div>
                                                <div class="file-preview row">
                                                    <div class="d-flex mt-2 ms-2 file-preview-item">
                                                        <div class="align-items-center thumb">
                                                            <img src="{{ $sub_category_image_path }}" class="img-fit image-preview img-md" alt="Item">
                                                        </div>
                                                        <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                                                    </div>
                                                </div>
                                                @if ($errors->has('sub_category_image'))
                                                    <span class="text-danger error-message">{{ $errors->first('sub_category_image') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div dnone">
                                                <label>Banner <small>(200x200)</small></label>
                                                <div class="input-upload my-file">
                                                    <input type="hidden" name="remove_banner_image" class="remove-image" value="0">                                       
                                                    <input class="form-control {{ $banner_image_validation }} image-field" data-type="image" data-label = "Banner" type="file" name="banner_image">									
                                                </div>
                                                <div class="file-preview row">
                                                    <div class="d-flex mt-2 ms-2 file-preview-item">
                                                        <div class="align-items-center thumb">
                                                            <img src="{{ $banner_image_path }}" class="img-fit image-preview img-md" alt="Item">
                                                        </div>
                                                        <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                                                    </div>
                                                </div>
                                                @if ($errors->has('banner_image'))
                                                    <span class="text-danger error-message">{{ $errors->first('banner_image') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div dnone">
                                                <label>Icon <small>(32x32)</small></label>
                                                <div class="input-upload my-file">      
                                                    <input type="hidden" name="remove_icon_image" class="remove-image" value="0">                                 
                                                    <input class="form-control {{ $icon_image_validation }} image-field" data-type="image" data-label = "Icon" type="file" name="icon_image">									
                                                </div>
                                                <div class="file-preview row">
                                                    <div class="d-flex mt-2 ms-2 file-preview-item">
                                                        <div class="align-items-center thumb">
                                                            <img src="{{ $icon_image_path }}" class="img-fit image-preview img-md" alt="Item">
                                                        </div>
                                                        <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                                                    </div>
                                                </div>
                                                @if ($errors->has('icon_image'))
                                                    <span class="text-danger error-message">{{ $errors->first('icon_image') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 dnone">
                                            <!-- <div class="mb-4 input-field-div">
                                                <label class="form-label">Ordering Number</label>
                                                <input type="text" data-label = "Ordering Number" name="order_number" class="form-control" value="{{!empty($sub_category_details) && !empty($sub_category_details[0]->order_number) ? $sub_category_details[0]->order_number : '' }}">
                                                @if ($errors->has('order_number'))
                                                    <span class="text-danger error-message">{{ $errors->first('order_number') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div> -->
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Meta Title</label>
                                                <input type="text" data-label = "Meta Title" class="form-control" name="meta_title" value="{{!empty($sub_category_details) && !empty($sub_category_details[0]->meta_title) ? $sub_category_details[0]->meta_title : '' }}">
                                                @if ($errors->has('meta_title'))
                                                    <span class="text-danger error-message">{{ $errors->first('meta_title') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Meta description</label>
                                                <textarea class="form-control" data-label = "Meta description" rows="4" name="meta_description">{{!empty($sub_category_details) && !empty($sub_category_details[0]->meta_description) ? $sub_category_details[0]->meta_description : '' }}</textarea>
                                                @if ($errors->has('meta_description'))
                                                    <span class="text-danger error-message">{{ $errors->first('meta_description') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Slug</label>
                                                <input type="text" data-label = "Slug" class="form-control" name="slug" value="{{!empty($sub_category_details) && !empty($sub_category_details[0]->slug) ? $sub_category_details[0]->slug : '' }}">
                                                @if ($errors->has('slug'))
                                                    <span class="text-danger error-message">{{ $errors->first('slug') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn btn-primary" id="save-sub-category-info">{{ __('store-admin.save') }}</button>
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
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script>
            $(document).on("click","#save-sub-category-info",function() {
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