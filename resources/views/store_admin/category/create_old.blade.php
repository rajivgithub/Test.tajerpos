<!DOCTYPE html>
<html lang="en">
    <head>
        @include('common.cashier_admin.header')
        <style>
            .img-md {
                width: 112px;
                height: 112px;
            }
            .img-sm {
                width: 60px !important;
                height: 60px !important;
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
                                            $page_title = ($mode == "add") ? "Add New Category" : "Edit Category";
                                        @endphp
                                        <h3 class="page-title">{{$page_title}}</h3>
                                    </div>
                                </div>
                                <hr/>
                            </div>
                            <div class="card-body">
                                <form  method="POST" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.category.store') }}" enctype="multipart/form-data">
                                @csrf
                                    <!-- $banner_image_validation = !empty($banner_image_path) ? 'optional-field' : 'required-field';
                                    $icon_image_validation = !empty($icon_image_path) ? 'optional-field' : 'required-field';
                                    $category_image_validation = !empty($category_image_path) ? 'optional-field' : 'required-field';  -->
                                    @php
                                        $banner_image_path = !empty($category_details) && !empty($category_details[0]->banner) ? $category_details[0]->banner : '';
                                        $icon_image_path = !empty($category_details) && !empty($category_details[0]->icon) ? $category_details[0]->icon : '';
                                        $category_image_path = !empty($category_details) && !empty($category_details[0]->category_image) ? $category_details[0]->category_image : '';
                                        $banner_image_validation = 'optional-field';
                                        $icon_image_validation = 'optional-field';
                                        $category_image_validation = 'optional-field';
                                    @endphp
                                    <input type="hidden" name="mode" value={{$mode}}> 
                                    <input type="hidden" name="category_id" class="category-id" value="{{!empty($category_details) && !empty($category_details[0]->category_id) ? $category_details[0]->category_id : '' }}">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Category<span>*</span></label>
                                                <input type="text" placeholder="Type here" data-label = "Category" name="category_name" class="form-control required-field" value="{{!empty($category_details) && !empty($category_details[0]->category_name) ? $category_details[0]->category_name : '' }}">
                                                @if ($errors->has('category_name'))
                                                    <span class="text-danger error-message">{{ $errors->first('category_name') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div" hidden>
                                                <label>Category Image <small>(640x512)</small></label>
                                                <div class="input-upload my-file">         
                                                    <input type="hidden" name="remove_category_image" class="remove-image" value="0">                           
                                                    <input class="form-control {{ $category_image_validation }} image-field" data-type="image" data-label = "Category Image" name="category_image" type="file">									
                                                </div>
                                                <div class="file-preview row">
                                                    <div class="d-flex mt-2 ms-2 file-preview-item">
                                                        <div class="align-items-center thumb">
                                                            <img src="{{ $category_image_path }}" class="img-fit image-preview img-md" alt="Item">
                                                        </div>
                                                        <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                                                    </div>
                                                </div>
                                                @if ($errors->has('category_image'))
                                                    <span class="text-danger error-message">{{ $errors->first('category_image') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div dnone">
                                                <label>Banner <small>(200x200)</small></label>
                                                <div class="input-upload my-file">  
                                                    <input type="hidden" name="remove_banner_image" class="remove-image" value="0">                                  
                                                    <input class="form-control {{ $banner_image_validation }} image-field" data-type="image" data-label = "Banner" name="banner_image" type="file">									
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
                                            <div class="mb-4 input-field-div">
                                                <label>Icon <small>(32x32)</small></label>
                                                <div class="input-upload my-file">          
                                                    <input type="hidden" name="remove_icon_image" class="remove-image" value="0">                          
                                                    <input class="form-control {{ $icon_image_validation }} image-field" data-type="image" data-label = "Icon" name="icon_image" type="file">									
                                                </div>
                                                <div class="file-preview row">
                                                    <div class="d-flex mt-2 ms-2 file-preview-item">
                                                        <div class="align-items-center thumb">
                                                            <img src="{{ $icon_image_path }}" class="img-fit image-preview img-sm" alt="Item">
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
                                                <input type="text" placeholder="Type here" data-label = "Ordering Number" name="order_number" class="form-control" value="{{!empty($category_details) && !empty($category_details[0]->order_number) ? $category_details[0]->order_number : '' }}">
                                                @if ($errors->has('order_number'))
                                                    <span class="text-danger error-message">{{ $errors->first('order_number') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div> -->
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Meta Title</label>
                                                <input type="text" placeholder="Type here" data-label = "Meta Title" name="meta_title" class="form-control" value="{{!empty($category_details) && !empty($category_details[0]->meta_title) ? $category_details[0]->meta_title : '' }}">
                                                @if ($errors->has('meta_title'))
                                                    <span class="text-danger error-message">{{ $errors->first('meta_title') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Meta description</label>
                                                <textarea placeholder="Type here" class="form-control" name="meta_description" data-label = "Meta description" rows="4">{{!empty($category_details) && !empty($category_details[0]->meta_description) ? $category_details[0]->meta_description : '' }}</textarea>
                                                @if ($errors->has('meta_description'))
                                                    <span class="text-danger error-message">{{ $errors->first('meta_description') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                            <div class="mb-4 input-field-div">
                                                <label class="form-label">Slug</label>
                                                <input type="text" placeholder="Type here" data-label = "Slug" name="slug" value="{{!empty($category_details) && !empty($category_details[0]->slug) ? $category_details[0]->slug : '' }}" class="form-control" >
                                                @if ($errors->has('slug'))
                                                    <span class="text-danger error-message">{{ $errors->first('slug') }}</span>
                                                @endif
                                                <span class="error error-message"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <button class="btn btn-primary" id="save-category-info">Save</button>
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
        <script>
            $(document).on("click","#save-category-info",function() {
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else
                    return true;
            });
        </script>
    </body>
</html>