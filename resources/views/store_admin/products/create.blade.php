<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ ($mode == "add") ? trans('store-admin.add_product_title',['company' => Auth::user()->company_name]) : trans('store-admin.edit_product_title',['company' => Auth::user()->company_name]) }}</title>
        @include('common.cashier_admin.header')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ URL::asset('assets/cashier-admin/css/image-uploader.css') }}">
        <style>
            .note-toolbar-wrapper .card-header{display: inherit;}
            .note-editor.note-frame {border: 1px solid #86a4c3;}
            .note-editor.note-frame .note-statusbar{  border-bottom-right-radius: 30px; border-bottom-left-radius: 30px;}
            .invest-edit-prifile .note-btn-group i, .invest-edit-prifile .note-current-fontname { color: #50a5f8; }
            .table > tbody > tr > td, .table-bordered > thead > tr > th{    padding: 0.547rem !important;}
            .note-btn-group .btn-light{background-color: inherit;    color: #006aa9;}
            .table-bordered > thead > tr > th{background: #006aa9 !important; color: #fff;}
            .bg-light {
                background-color: #d5e5f4 !important;
                color: #2d9bda;
            }
            .img-md {
                width: 112px;
                height: 112px;
            }
            .tooltip--triangle {
                position: relative;
                display: inline-block;
                cursor: pointer;
            }

            .tooltip--triangle::after {
                content: attr(data-tooltip);
                position: absolute;
                top: 100%;
                left: 50%;
                transform: translateX(-50%);
                padding: 8px;
                background-color: #000;
                color: #fff;
                font-size: 14px;
                border-radius: 4px;
                white-space: nowrap;
                visibility: hidden;
                opacity: 0;
                transition: visibility 0s, opacity 0.3s;
                z-index: 9;
                margin-left: 78px;
            }

            .tooltip--triangle:hover::after {
                visibility: visible;
                opacity: 1;
            }
        </style>
    </head>
    @php
        $prefix_url = config('app.module_prefix_url');
        $page_title = ($mode == "add") ? __('store-admin.product_card_title') : __('store-admin.edit_product');
        $category_img_validation = !empty($product_details) && !empty($product_details[0]->category_image) ? 'optional-field' : 'required-field';
        $category_img_path = !empty($product_details) && !empty($product_details[0]->category_image) ? $product_details[0]->category_image : '';
    @endphp
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <section class="content">
                        <form  method="POST" id="product-form" name="product_form" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.product.store') }}" enctype="multipart/form-data">
                        @csrf
                            <input type="hidden" class="sub-category-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.sub-category-list') }}">
                            <input type="hidden" class="barcode-unique-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.check-unique-barcode') }}">
                            <input type="hidden" class="sub-category-id" value="{{ !empty($product_details) && !empty($product_details[0]->sub_category_id) ? $product_details[0]->sub_category_id : '' }}">
                            <input type="hidden" name="get_product_images" class="get-product-images" value="{{ !empty($product_details) && !empty($product_details[0]->category_image) ? $product_details[0]->category_image : '' }}">
                            <input type="hidden" name="mode" class="mode" value={{$mode}}> 
                            <input type="hidden" name="products[status_type]" class="status" value="">
                            <input type="hidden" name="product_id" class="product-id" value="{{!empty($product_details) && !empty($product_details[0]->product_id) ? Crypt::encrypt($product_details[0]->product_id) : '' }}">
                            <input type="hidden" name="product_tax_id" value="{{!empty($product_details) && !empty($product_details[0]->product_tax_id) ? Crypt::encrypt($product_details[0]->product_tax_id) : '' }}">
                            <input type="hidden" name="price_id" value="{{!empty($product_details) && !empty($product_details[0]->price_id) ? Crypt::encrypt($product_details[0]->price_id) : '' }}">
                            <input type="hidden" name="variants_combination_details" class="save-variants-combination-details" value="">
                            <input type="hidden" name="variants_details" class="save-variants-details" value="">
                            <input type="hidden" name="variants_option_details" class="save-variants-options-details" value="">
                            <input type="hidden" class="variant-combinations" value="{{!empty($variant_combinations) ? json_encode($variant_combinations) : '' }}"> 
                            <div class="row">
                                <div class="col-12">
                                    <div class="box">
                                        <div class="content-header">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-auto">
                                                    <h3 class="page-title">{{$page_title}}</h3>
                                                </div>
                                            </div>
                                            <hr/>
                                        </div>
                                        <div class="box-body add-product-info">
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <div class="card mb-4">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                <div class="form-group mr-4 form-check pl-0">
                                                                    <input type="radio" class="form-check-input product-type" name="products[type_of_product]" value="single" id="exampleCheck1" {{((!empty($product_details) && !empty($product_details[0]->type_of_product) && $product_details[0]->type_of_product == 'single') || ($mode == "add")) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="exampleCheck1"><b>{{ __('store-admin.standard_product') }}</b></label>
                                                                </div>
                                                                <div class="form-group form-check pl-0">
                                                                    <input type="radio" class="form-check-input product-type" name="products[type_of_product]" value="variant" id="exampleCheck2" {{!empty($product_details) && !empty($product_details[0]->type_of_product) && $product_details[0]->type_of_product == 'variant' ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="exampleCheck2"><b>{{ __('store-admin.variant_product') }}</b></label>
                                                                </div>
                                                                @if ($errors->has('products.type_of_product'))
                                                                    <span class="text-danger error-message">{{ $errors->first('products.type_of_product') }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="mb-4 input-field-div">
                                                                <label class="form-label">{{ __('store-admin.product') }}<span>*</span></label>
                                                                <input type="text" data-max="150" class="form-control required-field form-input-field product-name" name="products[product_name]" data-label = "{{ __('store-admin.product') }}" data-pattern="^[A-Za-z\u0600-\u06FF0-9,._&\/+()\-\s|]*$" data-error-msg="{{ __('validation.invalid_category_err') }}" onkeypress="return restrictCharacters(event)" value="{{!empty($product_details) && !empty($product_details[0]->product_name) ? $product_details[0]->product_name : '' }}">
                                                                @if ($errors->has('products.product_name'))
                                                                    <span class="text-danger error-message">{{ $errors->first('products.product_name') }}</span>
                                                                @endif
                                                                <span class="error error-message"></span>
                                                            </div>
                                                            <div class="mb-4 input-field-div">
                                                                <label class="form-label">{{ __('store-admin.description') }}</label>
                                                                <textarea id="summernote" class="form-control form-input-field" rows="4" data-label = "{{ __('store-admin.description') }}" name="products[product_description]">{{!empty($product_details) && !empty($product_details[0]->product_description) ? $product_details[0]->product_description : '' }}</textarea>
                                                                <span class="error error-message"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card mb-4">
                                                        <div class="card-header">
                                                            <h4 class="form-label">{{ __('store-admin.media') }}(250x250)<span>*</span></h4>
                                                        </div>
                                                        <div class="card-body input-field-div">
                                                            <div class="input-images-2"></div>
                                                            <span class="error error-message"></span>
                                                            @if ($errors->has('category_image'))
                                                                <span class="text-danger error-message">{{ $errors->first('category_image') }}</span>
                                                            @endif
                                                            <input type="hidden" class="product-image-validation" value="{{ $category_img_validation }}">
                                                            <!-- <div class="input-field">
                                                                <div class="input-images-2" style="padding-top: .5rem;"></div>
                                                            </div> -->
                                                            <!-- <div class="input-upload input-field-div">    
                                                                <input type="hidden" name="remove_product_image" class="remove-image" value="0">                                  
                                                                <input class="form-control {{$category_img_validation}} image-field" data-type="image" type="file" data-label = "Category Image" name="category_image" multiple>
                                                                <div class="file-preview row">
                                                                    <div class="d-flex mt-2 ms-2 file-preview-item">
                                                                        <div class="align-items-center thumb">
                                                                            <img src="{{ $category_img_path }}" class="img-fit image-preview img-md" alt="Item">
                                                                        </div>
                                                                        <div class="remove"><button class="btn btn-sm btn-link remove-attachment" type="button"><i class="fa fa-close"></i></button></div>
                                                                    </div>
                                                                </div>
                                                                <span class="error error-message"></span>
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                    <div class="card mb-4 single-product-field">
                                                        <div class="card-header">
                                                            @php
                                                                if (!empty($tax_details) && !empty($tax_details[0]->tax_percentage)) {
                                                                    $tax_percentage = (int)$tax_details[0]->tax_percentage ? number_format($tax_details[0]->tax_percentage, 0)."%" : number_format($tax_details[0]->tax_percentage, 2)."%";
                                                                } else {
                                                                    $tax_percentage = "0%";
                                                                }
                                                            @endphp
                                                            <h4 class="mb-0">{{ __('store-admin.price') }} ({{ __('store-admin.tax_inclusive') }}) <i class="tooltip--triangle" data-tooltip="{{ trans('store-admin.tax_tooltip',['tax' => $tax_percentage]) }}">?</i></h4>
                                                            <h4>{{ __('store-admin.current_tax') }} {{ $tax_percentage }}</h4>
                                                        </div>
                                                        <div class="card-body price-info">
                                                            <div class="single-product-field">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <div class="mb-4 input-field-div">
                                                                            <label class="form-label">{{ __('store-admin.price') }}</label>
                                                                            <input type="text" data-max="12" class="form-control form-input-field amount sell-price product-price"  data-label = "{{ __('store-admin.price') }}" name="price_details[price]" value="{{!empty($product_details) && !empty($product_details[0]->price) ? $product_details[0]->price : '' }}">
                                                                            @if ($errors->has('price_details.price'))
                                                                                <span class="text-danger error-message">{{ $errors->first('price_details.price') }}</span>
                                                                            @endif
                                                                            <span class="error error-message"></span>
                                                                        </div>
                                                                        <div class="mb-4 input-field-div single-product-tax-field">
                                                                            <label class="form-label">{{ __('store-admin.unit_price') }}</label>
                                                                            <input type="text" class="form-control amount product-unit-price"  data-label = "{{ __('store-admin.unit_price') }}" name="price_details[price]" value="{{!empty($product_details) && !empty($product_details[0]->price) ? $product_details[0]->price : '' }}" disabled>
                                                                            <span class="error error-message"></span>
                                                                        </div>
                                                                        <div class="mb-4 input-field-div single-product-tax-field">
                                                                            <label class="form-label">{{ __('store-admin.tax_price') }}</label>
                                                                            <input type="text" class="form-control amount product-tax-price"  data-label = "{{ __('store-admin.tax_price') }}" name="price_details[price]" value="{{!empty($product_details) && !empty($product_details[0]->price) ? $product_details[0]->price : '' }}" disabled>
                                                                            <span class="error error-message"></span>
                                                                        </div>
                                                                    </div>
                                                                    <!-- <div class="col-lg-6">
                                                                        <div class="mb-4 input-field-div">
                                                                            <label class="form-label">Compare-at price <i class="fa fa-question-circle" data-toggle="tooltip" title="To display a markdown, enter a value higher than your price. Often shown with a strikethrough." ></i></label>
                                                                            <input type="text" class="form-control amount" name="price_details[compare_price]" value="{{!empty($product_details) && !empty($product_details[0]->compare_price) ? $product_details[0]->compare_price : '' }}">
                                                                        </div>
                                                                    </div> -->
                                                                </div>
                                                                <!-- <hr/> -->
                                                            </div>
                                                            <!-- <div class="row">
                                                                <div class="col-lg-4">
                                                                    <div class="mb-4">
                                                                        <label class="form-label">Cost per item <i class="fa fa-question-circle" data-toggle="tooltip" title="Customers won't see this." ></i></label>
                                                                        <input type="text" name="price_details[cost_per_item]" class="form-control amount buy-price product-price" value="{{!empty($product_details) && !empty($product_details[0]->cost_per_item) ? $product_details[0]->cost_per_item : '' }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="mb-4">
                                                                        <label class="form-label">Profit</label>  
                                                                        <input type="text" class="form-control profit-price" name="price_details[profit]" readonly value="{{!empty($product_details) && !empty($product_details[0]->profit) ? $product_details[0]->profit : '' }}">										
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="mb-4">
                                                                        <label class="form-label">Margin</label> 
                                                                        <input type="text" class="form-control margin-percentage" name="price_details[margin]" readonly value="{{!empty($product_details) && !empty($product_details[0]->margin) ? $product_details[0]->margin : '' }}">	 										
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                            <hr/>-->
                                                            <!-- <div class="checkbox">
                                                                <input type="checkbox" class="filled-in taxable" name="products[taxable]" value="1" id="basic_checkbox_2" {{!empty($product_details) && !empty($product_details[0]->taxable) && $product_details[0]->taxable == '1' ? 'checked' : '' }}>
                                                                <label for="Remember">Charge tax on this product</label> 
                                                            </div>
                                                            <div class="row tax-details-row dnone"> 
                                                                <div class="col-lg-6">
                                                                    <div class="mb-4 input-field-div">  
                                                                        <label class="form-label">GAZT</label>                                   
                                                                        <input type="text" class="form-control amount tax-amount" data-label = "GAZT" name="tax[tax_amount]" value="{{!empty($product_details) && !empty($product_details[0]->tax_amount) ? $product_details[0]->tax_amount : '' }}">
                                                                        <span class="error error-message"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-4 input-field-div">
                                                                        <label class="form-label">Tax Type</label>  
                                                                        <select class="form-control" name="tax[tax_type]">
                                                                            <option value="flat" {{!empty($product_details) && !empty($product_details[0]->tax_type) && $product_details[0]->tax_type == 'flat' ? 'selected' : '' }}>Flat</option>
                                                                            <option value="percent" {{!empty($product_details) && !empty($product_details[0]->tax_type) && $product_details[0]->tax_type == 'percent' ? 'selected' : '' }}>Percent</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="checkbox">
                                                                <input type="checkbox" class="filled-in" name="products[taxable]" value="0" id="basic_checkbox_2" {{!empty($product_details) && !empty($product_details[0]->taxable) && $product_details[0]->taxable == '0' ? 'checked' : '' }}>
                                                                <label for="Remember">Tax included in the price</label> 
                                                            </div> -->
                                                            <div class="form-group mr-4 form-check pl-0 dnone">
                                                                <input type="radio" class="form-check-input filled-in taxable tax-input-field" name="products[taxable]" value="1" id="charge-tax" {{((!empty($product_details) && !empty($product_details[0]->taxable) && $product_details[0]->taxable == '1')) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="charge-tax">Charge tax on this product</label>
                                                            </div>
                                                            <div class="row tax-details-row dnone"> 
                                                                <div class="col-lg-6">
                                                                    <div class="mb-4 input-field-div">  
                                                                        <label class="form-label">GAZT</label>                                   
                                                                        <input type="text" class="form-control form-input-field amount tax-amount" data-label = "GAZT" name="tax_amount" value="{{!empty($product_details) && !empty($product_details[0]->tax_amount) ? $product_details[0]->tax_amount : '' }}">
                                                                        <span class="error error-message"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-4 input-field-div">
                                                                        <label class="form-label">Tax Type</label>  
                                                                        <select class="form-control form-input-field" name="tax[tax_type]">
                                                                            <option value="flat" {{!empty($product_details) && !empty($product_details[0]->tax_type) && $product_details[0]->tax_type == 'flat' ? 'selected' : '' }}>Flat</option>
                                                                            <option value="percent" {{!empty($product_details) && !empty($product_details[0]->tax_type) && $product_details[0]->tax_type == 'percent' ? 'selected' : '' }}>Percent</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group form-check pl-0 dnone">
                                                                <input type="radio" class="form-check-input tax-incl-price tax-input-field" name="products[taxable]" value="0" id="incl-tax" {{ ((!empty($product_details) && $product_details[0]->taxable == '0') || ($mode == "add")) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="incl-tax">Tax included in the price</label>
                                                            </div> 
                                                            <div class="row tax-incl-details-row dnone"> 
                                                                <div class="col-lg-6">
                                                                    <div class="mb-4 input-field-div dnone">  
                                                                        <label class="form-label">Tax Percentage</label>       
                                                                        @php
                                                                            if (!empty($tax_details) && !empty($tax_details[0]->tax_percentage)) {
                                                                                $tax_amount = $tax_details[0]->tax_percentage;
                                                                            } else {
                                                                                $tax_amount = '';
                                                                            }
                                                                        @endphp
                                                                        <input type="text" class="form-control form-input-field amount tax-percentage" data-label = "Tax Percentage" name="tax_percentage" value="{{ $tax_amount }}" readonly>
                                                                        <span class="error error-message"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">&nbsp;</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card mb-4 single-product-field">
                                                        <div class="card-header">
                                                            <h4 class="mb-0">{{ __('store-admin.inventory') }}</h4>
                                                        </div>
                                                        <div class="card-body inventory-details">
                                                            <div class="row">
                                                                <div class="col-lg-8">
                                                                    <div class="checkbox mb-3">
                                                                        <input type="checkbox" class="enable-track-quantity" name="products[trackable]" value="1" id="enable-track-quantity" {{!empty($product_details) && !empty($product_details[0]->trackable) && $product_details[0]->trackable == '1' ? 'checked' : '' }}>
                                                                        <label for="enable-track-quantity">{{ __('store-admin.track_qty') }}</label> 
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4 input-field-div">
                                                                    <input type="number" placeholder="0" data-max="12" data-label = "{{ __('store-admin.quantity') }}" onkeypress="return isNumber(event)" name="products[unit]" value="{{!empty($product_details) && !empty($product_details[0]->unit) ? $product_details[0]->unit : '' }}" class="form-control form-input-field track-quantity dnone">
                                                                    <span class="error error-message"></span>
                                                                </div>
                                                                @if ($errors->has('products.unit'))
                                                                    <span class="text-danger error-message">{{ $errors->first('products.unit') }}</span>
                                                                @endif
                                                            </div>
                                                            <hr/>
                                                            <!-- <div class="checkbox mb-3">
                                                                <input type="checkbox" name="products[sell_out_of_stock]" value="1" id="sell-out-of-stock" {{!empty($product_details) && !empty($product_details[0]->sell_out_of_stock) && $product_details[0]->sell_out_of_stock == '1' ? 'checked' : '' }}>
                                                                <label for="Remember">Continue selling when out of stock</label> 
                                                            </div> 
                                                            <hr/> --> 
                                                            <div class="checkbox mb-3">
                                                                <input type="checkbox" name="products[is_sku_barcode]" value="1" class="product-sku-info" id="product-sku-info" {{!empty($product_details) && !empty($product_details[0]->is_sku_barcode) && $product_details[0]->is_sku_barcode == '1' ? 'checked' : '' }}>
                                                                <label for="product-sku-info">{{ __('store-admin.barcode_title') }}</label>  
                                                            </div>
                                                            <div class="row sku-barcode-details input-field-div dnone">
                                                                <!-- <div class="col-lg-12">
                                                                    <div class="mb-4">
                                                                        <label for="product-sku" class="form-label">{{ __('store-admin.sku') }} (Stock Keeping Unit)</label>
                                                                        <input type="text" data-label = "{{ __('store-admin.sku_or_barcode') }}" data-type="sku-barcode" class="form-control product-sku sku-barcode-field" id="product-sku" name="products[sku]" value="{{!empty($product_details) && !empty($product_details[0]->sku) ? $product_details[0]->sku : '' }}">
                                                                    </div>
                                                                    <span class="error error-message"></span>
                                                                </div> -->
                                                                <div class="col-lg-6">
                                                                    <div class="mb-4 barcode-section">
                                                                        <canvas id="barcodeCanvas" class="barcodeCanvas dnone"></canvas>
                                                                        <button class="btn btn-danger generateButton" id="generateButton" type="button"><span>{{ __('store-admin.generate_barcode') }}</span></button>
                                                                        <!-- <label for="product-barcode" class="form-label">Barcode (ISBN, UPC, GTIN, etc.)</label> -->
                                                                        <input type="text" data-label = "{{ __('store-admin.sku_or_barcode') }}" data-type="sku-barcode" class="form-control form-input-field product-barcode sku-barcode-field dnone" id="product-barcode" name="products[barcode]" value="{{!empty($product_details) && !empty($product_details[0]->barcode) ? $product_details[0]->barcode : '' }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('products.barcode'))
                                                                <span class="text-danger error-message">{{ $errors->first('products.barcode') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="card mb-4 variant-product-fields">
                                                        <div class="card-header">
                                                            <h4 class="mb-0">{{ __('store-admin.variants') }} <i class="tooltip--triangle" data-tooltip="{{ trans('store-admin.tax_tooltip',['tax' => $tax_percentage]) }}">?</i></h4>
                                                            <h4>{{ __('store-admin.current_tax') }} {{ $tax_percentage }}</h4>
                                                        </div>
                                                        <div class="card-body add-variants">                                  
                                                            <div class="col-lg-2 col-md-2 col-sm-2 col-2 pr-0 clone-option-image dnone">
                                                                <div class="mb-0 bg-light bg-opacity-25 border border-light-subtle shadow-none text-center rounded-lg">
                                                                    <div class="d-flex justify-content-center align-items-center file-up p-2">
                                                                        <div>
                                                                            <input type="file" class="variant-option-img"> 
                                                                            <label class="form-label fw-bold"><img class="variant-option-img-preview" src="{{ URL::asset('assets/cashier-admin/images/upload-img.png') }}" alt="logo"></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-9 col-md-9 col-sm-9 col-9 input-field-div clone-option-values dnone">                                      
                                                                <input type="text" value="" class="form-control option-values-fields form-input-field">
                                                                <input type="hidden" value="" class="form-control variant-options-id">
                                                                <input type="hidden" value="" class="form-control variant-options-name">
                                                                <span class="error error-message"></span>
                                                            </div>
                                                            <div class="col-lg-1 col-md-1 col-sm-2 col-2 clone-option-remove dnone">
                                                                <a href="#" class="btn btn-circle btn-primary btn-xs remove-option-value"><i class="fa fa-trash"></i></a>
                                                            </div>
                                                            <!-- <div class="col-lg-11 col-md-11 col-sm-10 col-10 input-field-div clone-option-values dnone">                                        
                                                                <input type="text" data-label="{{ __('store-admin.option_values') }}" class="form-control option-values-fields form-input-field">
                                                                <span class="error error-message"></span>
                                                            </div>
                                                            <div class="col-lg-1 col-md-1 col-sm-2 col-2 clone-option-remove dnone">
                                                                <a href="#" class="btn btn-circle btn-primary btn-xs remove-option-value"><i class="fa fa-trash"></i></a>
                                                            </div> -->
                                                            <div class="clone-variants-options dnone">
                                                                <label class="form-label">{{ __('store-admin.option_name') }}</label>
                                                                <div class="row mb-4">
                                                                    <div class="col-lg-11 col-md-11 col-sm-10 col-10 input-field-div">
                                                                        <input type="text" data-label="{{ __('store-admin.option_name') }}" class="form-control option-name form-input-field" id="option-name">
                                                                        <span class="error error-message"></span>
                                                                    </div>
                                                                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                                                                        <a href="#" class="btn btn-circle btn-primary btn-xs remove-option-row"><i class="fa fa-trash"></i></a>
                                                                    </div>
                                                                </div>
                                                                <label class="form-label">{{ __('store-admin.option_values') }}</label>
                                                                <div class="option-values-row">
                                                                    <div class="row mb-2 align-items-center option-values variants-options-fields-row">                                   
                                                                        <!-- <div class="col-lg-11 col-md-11 col-sm-10 col-10 input-field-div">   
                                                                            <label class="form-label">{{ __('store-admin.option_values') }}</label>                                     
                                                                            <input type="text" data-label="{{ __('store-admin.option_values') }}" class="form-control option-values-fields form-input-field">
                                                                            <span class="error error-message"></span>
                                                                        </div> -->                                
                                                                        <div class="col-lg-2 col-md-2 col-sm-2 col-2 pr-0">
                                                                            <div class="mb-0 bg-light bg-opacity-25 border border-light-subtle shadow-none text-center rounded-lg">
                                                                                <div class="d-flex justify-content-center align-items-center file-up p-2">
                                                                                    <div>
                                                                                        <input type="file" class="variant-option-img" name="variant_option_image[]"> 
                                                                                        <label class="form-label fw-bold"><img class="variant-option-img-preview" src="{{ URL::asset('assets/cashier-admin/images/upload-img.png') }}" alt="logo"></label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-9 col-md-9 col-sm-9 col-9">                                      
                                                                            <input type="text" value="" name="option_fields_value[]" class="form-control option-values-fields form-input-field">
                                                                            <input type="hidden" value="" name="option_fields_id[]" class="form-control variant-options-id">
                                                                            <input type="hidden" value="" name="option_names[]" class="form-control variant-options-name">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-danger add-option-values mb-2">{{ __('store-admin.done') }}</button>
                                                                <hr/>
                                                            </div>
                                                            @if($mode == "edit" && !empty($variants) && count($variants) > 0)
                                                                @foreach($variants as $key=>$variant)
                                                                    <div data-row="{{$key+1}}" class="variants-options-row variants-options-row-{{$key+1}}">
                                                                        <div class="save-option-fields">
                                                                            <label class="form-label">{{ __('store-admin.option_name') }}</label>
                                                                            <div class="row mb-4">
                                                                                <div class="col-lg-11 col-md-11 col-sm-10 col-10">
                                                                                    <input type="text" value="{{ $variant->variants_name }}" class="form-control option-name form-input-field" id="option-name">
                                                                                    <input type="hidden" class="variants-id" value="{{ $variant->variants_id }}">
                                                                                </div>
                                                                                <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                                                                                    <a href="#" class="btn btn-circle btn-primary btn-xs remove-option-row"><i class="fa fa-trash"></i></a>
                                                                                </div>
                                                                            </div>
                                                                            <label class="form-label">{{ __('store-admin.option_values') }}</label>
                                                                            <div class="option-values-row">
                                                                                @if(!empty($variants_options_array) && count($variants_options_array) > 0 && array_key_exists($variant->variants_id,$variants_options_array))
                                                                                    @foreach($variants_options_array[$variant->variants_id] as $k=>$variant_options)
                                                                                        <div class="row mb-2 align-items-center option-values variants-options-fields-row variants-options-fields-row-{{$k+1}}" data-row="{{$k+1}}">                                   
                                                                                            <div class="col-lg-2 col-md-2 col-sm-2 col-2 pr-0">
                                                                                                <div class="mb-0 bg-light bg-opacity-25 border border-light-subtle shadow-none text-center rounded-lg">
                                                                                                    <div class="d-flex justify-content-center align-items-center file-up p-2">
                                                                                                        <div>
                                                                                                            <input type="file" class="variant-option-img" name="variant_option_image[]"> 
                                                                                                            <label class="form-label fw-bold"><img class="variant-option-img-preview" src="{{ ($variant_options['variants_option_image']) ? $variant_options['variants_option_image'] : URL::asset('assets/cashier-admin/images/upload-img.png') }}" alt="logo"></label>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-9 col-md-9 col-sm-9 col-9">                                      
                                                                                                <input type="text" value="{{ $variant_options['variant_options_name'] }}" name="option_fields_value[]" class="form-control option-values-fields form-input-field">
                                                                                                <input type="hidden" value="{{ $variant_options['variant_options_id'] }}" name="option_fields_id[]" class="form-control variant-options-id">
                                                                                                <input type="hidden" value="{{ $variant->variants_name }}" name="option_names[]" class="form-control variant-options-name">
                                                                                            </div>
                                                                                            <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                                                                                                <a href="#" class="btn btn-circle btn-primary btn-xs remove-option-value"><i class="fa fa-trash"></i></a>
                                                                                            </div>
                                                                                        </div>
                                                                                        @if(($k+1) == count($variants_options_array[$variant->variants_id]))
                                                                                            <div class="row mb-2 align-items-center option-values variants-options-fields-row variants-options-fields-row-{{$k+2}}" data-row="{{$k+2}}">                                   
                                                                                                <div class="col-lg-2 col-md-2 col-sm-2 col-2 pr-0">
                                                                                                    <div class="mb-0 bg-light bg-opacity-25 border border-light-subtle shadow-none text-center rounded-lg">
                                                                                                        <div class="d-flex justify-content-center align-items-center file-up p-2">
                                                                                                        <div>
                                                                                                            <input type="file" class="variant-option-img" name="variant_option_image[]"> 
                                                                                                            <label class="form-label fw-bold"><img class="variant-option-img-preview" src="{{ URL::asset('assets/cashier-admin/images/upload-img.png') }}" alt="logo"></label>
                                                                                                        </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-lg-9 col-md-9 col-sm-9 col-9">                                      
                                                                                                    <input type="text" value="" name="option_fields_value[]" class="form-control option-values-fields form-input-field">
                                                                                                    <input type="hidden" value="" name="option_fields_id[]" class="form-control variant-options-id">
                                                                                                    <input type="hidden" value="" name="option_names[]" class="form-control variant-options-name">
                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
                                                                                    @endforeach
                                                                                @else
                                                                                    <div class="row mb-2 align-items-center option-values variants-options-fields-row variants-options-fields-row-1" data-row="1">                                   
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 col-2 pr-0">
                                                                                            <div class="mb-0 bg-light bg-opacity-25 border border-light-subtle shadow-none text-center rounded-lg">
                                                                                                <div class="d-flex justify-content-center align-items-center file-up p-2">
                                                                                                    <div>
                                                                                                        <input type="file" class="variant-option-img" name="variant_option_image[]"> 
                                                                                                        <label class="form-label fw-bold"><img class="variant-option-img-preview" src="{{ URL::asset('assets/cashier-admin/images/upload-img.png') }}" alt="logo"></label>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-lg-9 col-md-9 col-sm-9 col-9">                                      
                                                                                            <input type="text" value="" name="option_fields_value[]" class="form-control option-values-fields form-input-field">
                                                                                            <input type="hidden" value="" name="option_fields_id[]" class="form-control variant-options-id">
                                                                                            <input type="hidden" value="" name="option_names[]" class="form-control variant-options-name">
                                                                                        </div>
                                                                                        <div class="col-lg-1 col-md-1 col-sm-2 col-2">&nbsp;</div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            <button type="button" class="btn btn-danger add-option-values mb-2">{{ __('store-admin.done') }}</button>
                                                                            <hr>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                            <div class="clone-save-variants dnone">
                                                                <div class="row">
                                                                    <div class="col-md-9 col-sm-9 col-8">
                                                                        <label class="form-label save-option-name">Color</label>
                                                                        <div class="d-flex save-option-values">
                                                                            <p class="bg-light p-2 rounded-pill mr-2 mb-1 save-option-values-fields">#000</p>
                                                                            <p class="bg-light p-2 rounded-pill mr-2 mb-1 save-option-values-fields">#000</p>
                                                                            <p class="bg-light p-2 rounded-pill mr-2 mb-1 save-option-values-fields">#000</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 col-sm-3 col-4 text-right">
                                                                        <button type="button" class="btn btn-success btn-outline p-2 edit-option-fields"><i class="fa fa-edit"></i> {{ __('store-admin.edit') }}</button>
                                                                    </div>
                                                                </div>
                                                                <hr/> 
                                                            </div>
                                                            <a href="#0" class="add-variants-info"><i class="fa fa-plus"></i><span class="add-variants-text">{{ __('store-admin.add_variants_text') }}</span></a>
                                                            <hr/>
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered display variants-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">{{ __('store-admin.variant') }}</th>
                                                                            <th scope="col">{{ __('store-admin.price') }}</th>
                                                                            <th scope="col" class="variant-tax-fields">{{ __('store-admin.unit_price') }}</th>
                                                                            <th scope="col" class="variant-tax-fields">{{ __('store-admin.tax_price') }}</th>
                                                                            <!-- <th scope="col">Quantity</th> -->
                                                                            <th scope="col">{{ __('store-admin.on_hand') }}</th>
                                                                            <!-- <th scope="col">Available</th> -->
                                                                            <th scope="col">{{ __('store-admin.sku') }}</th>
                                                                            <th scope="col">{{ __('store-admin.barcode') }}</th>
                                                                            <th scope="col" class="text-right">{{ __('store-admin.action') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="variants-tbody">
                                                                        <tr class="variants-tbody-empty">
                                                                            <td colspan="9" class="text-center">{{ trans('datatables.sEmptyTable') }}</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="card mb-4">
                                                        <div class="card-header">
                                                            <h4 class="mb-0">{{ __('store-admin.status') }}</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            <select class="form-control" name="products[status_type]">
                                                                <option value="publish" {{!empty($product_details) && !empty($product_details[0]->status_type) && ($product_details[0]->status_type == "publish") ? "selected" : '' }}>Publish</option>
                                                                <option value="unpublish" {{!empty($product_details) && !empty($product_details[0]->status_type) && ($product_details[0]->status_type == "unpublish") ? "selected" : '' }}>Draft</option>
                                                            </select>
                                                            @if ($errors->has('products.status_type'))
                                                                <span class="text-danger error-message">{{ $errors->first('products.status_type') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="card mb-4">
                                                        <div class="card-header">
                                                            <h4 class="mb-0">{{ __('store-admin.sales_channels') }}</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- <select class="form-control" name="products[status_type]">
                                                                <option value="publish" {{!empty($product_details) && !empty($product_details[0]->status_type) && ($product_details[0]->status_type == "publish") ? "selected" : '' }}>Publish</option>
                                                                <option value="unpublish" {{!empty($product_details) && !empty($product_details[0]->status_type) && ($product_details[0]->status_type == "unpublish") ? "selected" : '' }}>Draft</option>
                                                            </select> -->
                                                            <div class="form-group">
                                                                <div class="radio-list">
                                                                    <label class="radio-inline p-0 mr-10">
                                                                        <div class="radio radio-info">
                                                                            <input type="radio" name="products[product_type]" id="channel1" value="online" {{!empty($product_details) && !empty($product_details[0]->product_type) && ($product_details[0]->product_type == "online") ? "checked" : '' }}>
                                                                            <label for="channel1">{{ __('store-admin.online') }}</label>
                                                                        </div>
                                                                    </label>
                                                                    <label class="radio-inline">
                                                                        <div class="radio radio-info">
                                                                            <input type="radio" name="products[product_type]" id="channel2" value="in_store" {{!empty($product_details) && !empty($product_details[0]->product_type) && ($product_details[0]->product_type == "instore") ? "checked" : '' }}>
                                                                            <label for="channel2">{{ __('store-admin.in-store') }}</label>
                                                                        </div>
                                                                    </label>
                                                                    <label class="radio-inline">
                                                                        <div class="radio radio-info">
                                                                            <input type="radio" name="products[product_type]" id="channel3" value="both" {{(!empty($product_details) && !empty($product_details[0]->product_type) && ($product_details[0]->product_type == "both") || $mode == "add") ? "checked" : '' }}>
                                                                            <label for="channel3">{{ __('store-admin.both') }}</label>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                                @if ($errors->has('products.product_type'))
                                                                    <span class="text-danger error-message">{{ $errors->first('products.product_type') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card mb-4">
                                                        <div class="card-header">
                                                            <h4 class="mb-0">{{ __('store-admin.product_org') }}</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="mb-4 input-field-div">
                                                                <label class="form-label">{{ __('store-admin.category') }}<span>*</span></label>
                                                                <select class="form-control required-field form-input-field category-id" data-label = "{{ __('store-admin.category') }}" name="products[category_id]">
                                                                    <option value="">--Select Category--</option>
                                                                    @if(isset($category_details) && !empty($category_details))
                                                                        @foreach ($category_details as $category)
                                                                            <option value="{{ $category->category_id }}" {{isset($product_details) && !empty($product_details) && !empty($product_details[0]->category_id) && ($product_details[0]->category_id == $category->category_id) ? "selected" : '' }}>{{ $category->category_name }}</option> 
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('products.category_id'))
                                                                    <span class="text-danger error-message">{{ $errors->first('products.category_id') }}</span>
                                                                @endif
                                                                <span class="error error-message"></span>
                                                            </div>
                                                            <div class="mb-4 input-field-div">
                                                                <label class="form-label">{{ __('store-admin.sub_category') }}</label>
                                                                <select class="form-control form-input-field sub-category-list" data-label = "{{ __('store-admin.sub_category') }}" name="products[sub_category_id]">
                                                                    <option value="">--Select Sub Category--</option>
                                                                </select>
                                                            </div>
                                                            <!-- <div class="mb-4">
                                                                <label class="form-label">Tags</label>
                                                                <div class="tags-default"> 
                                                                    <input type="text" data-role="tagsinput" name="products[tags]" class="form-control" value="{{!empty($product_details) && !empty($product_details[0]->tags) ? $product_details[0]->tags : '' }}">									
                                                                </div> 
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                    <!-- <div class="card mb-4">
                                                        <div class="card-header">
                                                            <h4 class="mb-0">Search engine listing</h4>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="mb-4">
                                                                <label class="form-label">Meta Title</label>
                                                                <input type="text" class="form-control" data-label = "Meta Title" name="products[meta_title]" value="{{!empty($product_details) && !empty($product_details[0]->meta_title) ? $product_details[0]->meta_title : '' }}">
                                                            </div>
                                                            <div class="mb-4">
                                                                <label class="form-label">Meta description</label>
                                                                <textarea class="form-control" rows="4" data-label = "Full description" name="products[meta_description]">{{!empty($product_details) && !empty($product_details[0]->meta_description) ? $product_details[0]->meta_description : '' }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                </div>
                                                <div class="col-12 form-actions mt-10" style="text-align: right; width: 100%;">
                                                    <button class="btn btn-primary mb-2 save-products-info"> {{ __('store-admin.save') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
            @include('common.cashier_admin.copyright') 
        </div>
        @include('common.cashier_admin.footer')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.3/dist/JsBarcode.all.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.3.3/purify.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/he/1.2.0/he.min.js"></script>
        <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
        <script src="{{ URL::asset('assets/cashier-admin/js/image-uploader.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#summernote').summernote({
                    height: 120,   //set editable area's height
                    callbacks: {
                        onInit: function () {
                            $('.note-editable').addClass('form-input-field');
                            $('.note-editable').attr('data-label', translations.description);
                            $('.note-editable').attr('data-type', 'codeeditor');
                        }
                    }
                });
                track_quantity($(".enable-track-quantity"));
                if($(".taxable").is(":checked")) {
                    $(".taxable").closest(".price-info").find(".tax-details-row").removeClass("dnone");
                    // $(".tax-amount").addClass("required-field");
                }  
                if($(".tax-incl-price").is(":checked")) {
                    $(".tax-incl-price").closest(".price-info").find(".tax-incl-details-row").removeClass("dnone");
                    // $(".tax-percentage").addClass("required-field");
                }  
                if($(".product-sku-info").is(":checked")) {
                    $(".product-sku-info").closest(".inventory-details").find(".sku-barcode-details").removeClass("dnone"); 
                    // $(".product-sku-info").closest(".inventory-details").find(".sku-barcode-field").addClass("required-field");
                }
                $(".add-option-values").trigger("click");
                product_type = $('input[name="products[type_of_product]"]:checked').val();
                showProductFields(product_type,$(".product-type"));
                // tax_input_field = $('input[name="products[taxable]"]:checked').val();
                // if(tax_input_field == 1) 
                //     $(".price-info").find(".tax-percentage").val("");
                // else
                //     $(".price-info").find(".tax-amount").val("");
                hideShowTaxField();
                img_validation = $(".product-image-validation").val();
                $('.input-images-2').imageUploader({
                    preloaded: preloaded,
                    imagesInputName: 'category_image',
                    dataLabel: @json(__('store-admin.product_image')),
                    dataType : 'image',
                    preloadedInputName: 'old',
                    imagesInputClass : 'form-control form-input-field '+img_validation+' image-field product-image',
                });
            });
            $(document).on("keyup",".product-price",function() {
                hideShowTaxField();
            });

            function calculateTax(_this = '') {
                var taxPercentage = parseFloat($(".tax-percentage").val());
                product_type = $('input[name="products[type_of_product]"]:checked').val();
                if(product_type == "single") {
                    productPrice = parseFloat($(".product-price").val());
                    if (!isNaN(productPrice) && !isNaN(taxPercentage)) {
                        var taxAmount = productPrice - (productPrice * (100 / (100 + taxPercentage)));
                        var unitPrice = productPrice - taxAmount;
                        $(".product-tax-price").val(taxAmount.toFixed(2));
                        $(".product-unit-price").val(unitPrice.toFixed(2));
                    } else {
                        $(".product-tax-price").val(0);
                        $(".product-unit-price").val(productPrice);
                    }
                } else {
                    productPrice = parseFloat(_this.val());
                    if (!isNaN(productPrice) && !isNaN(taxPercentage)) {
                        var taxAmount = productPrice - (productPrice * (100 / (100 + taxPercentage ) ) );
                        var unitPrice = productPrice - taxAmount;
                        _this.closest("tr").find(".tax-price-data").val(taxAmount.toFixed(2));
                        _this.closest("tr").find(".unit-price-data").val(unitPrice.toFixed(2));
                    } else {
                        _this.closest("tr").find(".tax-price-data").val(0);
                        _this.closest("tr").find(".unit-price-data").val(productPrice);
                    }
                }
            }
            function hideShowTaxField() {
                product_type = $('input[name="products[type_of_product]"]:checked').val();
                tax_input_field = $('input[name="products[taxable]"]:checked').val();
                if(product_type == "single") {
                    if($(".product-price").val() == "" || tax_input_field == 1) {
                        $(".single-product-tax-field").addClass("dnone");
                    } else {
                        $(".single-product-tax-field").removeClass("dnone");
                    }
                    if(tax_input_field != 1)
                        calculateTax();
                } else {
                    if(tax_input_field == 1) {
                        $(".variant-tax-fields").addClass("dnone");
                    } else {
                        $(".variant-tax-fields").removeClass("dnone");
                    }
                }
            }

            $(document).on("change",".product-type",function() {
                product_type = $('input[name="products[type_of_product]"]:checked').val();
                showProductFields(product_type,$(this));
                hideShowTaxField();
                if(product_type == "single") {
                    calculateTax();
                } else {
                    $(".variants-tbody").find("tr").each(function() {
                        calculateTax($(this).find(".price-data"));
                    });
                }
                
            });

            function showProductFields(_type,_this) {
                if(_type == "single") {
                    _this.closest("form").find(".product-price").addClass("required-field");
                    _this.closest("form").find(".variant-product-fields").addClass("dnone");
                    _this.closest("form").find(".single-product-field").removeClass("dnone");
                } else {
                    _this.closest("form").find(".product-price").removeClass("required-field");
                    _this.closest("form").find(".variant-product-fields").removeClass("dnone");
                    _this.closest("form").find(".single-product-field").addClass("dnone");
                }
            }

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('.image-upload-wrap').hide();
                        $('.file-upload-image').attr('src', e.target.result);
                        $('.file-upload-content').show();
                        $('.image-title').html(input.files[0].name);
                    };
                    reader.readAsDataURL(input.files[0]);
                } else {
                    removeUpload();
                }
            }
            function removeUpload() {
                $('.file-upload-input').replaceWith($('.file-upload-input').clone());
                $('.file-upload-content').hide();
                $('.image-upload-wrap').show();
            }
            $('.image-upload-wrap').bind('dragover', function () {
                $('.image-upload-wrap').addClass('image-dropping');
            });
            $('.image-upload-wrap').bind('dragleave', function () {
                $('.image-upload-wrap').removeClass('image-dropping');
            });
            $(document).on("change",".enable-track-quantity",function() {
                track_quantity($(this));
            });  
            function track_quantity(_this) {
                if(_this.is(":checked")) { 
                    _this.closest(".inventory-details").find(".track-quantity").removeClass("dnone");
                    _this.closest(".inventory-details").find(".track-quantity").addClass("required-field");
                } else {
                    _this.closest(".inventory-details").find(".track-quantity").addClass("dnone");
                    _this.closest(".inventory-details").find(".track-quantity").removeClass("required-field");
                }
            }
            $(document).on("keyup",".product-name",function() {
                product_name = ($(this).val() != "") ? $(this).val() : "Product Name";
                $(this).closest(".add-product-info").find(".inventory-product-name").text(product_name);
            }); 
            $(document).on("change",".product-sku-info",function() {
                if($(this).is(":checked")) {
                    $(this).closest(".inventory-details").find(".sku-barcode-details").removeClass("dnone");
                    // $(this).closest(".inventory-details").find(".sku-barcode-field").addClass("required-field");
                }
                else  {
                    $(this).closest(".inventory-details").find(".sku-barcode-details").addClass("dnone");
                    $(this).closest(".inventory-details").find(".sku-barcode-field").removeClass("required-field");
                }
            });  
            variants_details = {}; variants_index_details = []; delete_variants = [];
            if($(".mode").val() == "edit") {
                variants_option_fields_row = parseInt($(".variants-options-fields-row").find(".option-values-fields").length) + 1;
                variants_table($(".add-variants"));
                variant_combinations = $(".variant-combinations").val();
                if(variant_combinations != "") {
                    variant_combinations = $.parseJSON(variant_combinations);
                    $(".variants-table").find(".variants-tbody").find("tr").each(function() { 
                        variant_combination_name = $(this).closest("tr").find("td:eq(1)").text();
                        if(variant_combinations[variant_combination_name]) {
                            variation_combination_data = variant_combinations[variant_combination_name];
                            $(this).closest("tr").find(".price-data").val(variation_combination_data.variant_price);
                            // $(this).closest("tr").find(".quantity-data").val(variation_combination_data.quantity);
                            $(this).closest("tr").find(".onhand-data").val(variation_combination_data.on_hand);
                            // $(this).closest("tr").find(".available-data").val(variation_combination_data.available);
                            $(this).closest("tr").find(".sku-data").val(variation_combination_data.sku);
                            $(this).closest("tr").find(".barcode-data").val(variation_combination_data.barcode);
                            $(this).closest("tr").find(".variants-combination-id-data").val(variation_combination_data.variants_combination_id);
                            index = $(this).attr("data-index"); 
                            variants_data = [];
                            variants_data["price"] = variation_combination_data.variant_price;
                            // variants_data["quantity"] = variation_combination_data.quantity;
                            variants_data["onhand"] = variation_combination_data.on_hand;
                            // variants_data["available"] = variation_combination_data.available;
                            variants_data["sku"] = variation_combination_data.sku;
                            variants_data["barcode"] = variation_combination_data.barcode;
                            variants_data["variants_combination_id"] = variation_combination_data.variants_combination_id;
                            variants_details[index] = variants_data;
                        } else {
                            $(this).closest("tr").remove();
                            variantRowAndReindex();
                        }
                        calculateTax($(this).find(".price-data"));
                    });
                }
            }
            else
                variants_option_fields_row = 1;
            function variantRowAndReindex() {
                $('.variants-table .variants-tbody tr').each(function(index) {
                    $(this).find("td:eq(0)").text(index + 1); // Update index column
                });
            }
            $(document).on("change",".variants-details",function() {
                index = $(this).closest("tr").attr("data-index");       
                variants_data = [];
                variants_data["price"] = $(this).closest("tr").find(".price-data").val();
                variants_data["onhand"] = $(this).closest("tr").find(".onhand-data").val(); 
                // variants_data["quantity"] = $(this).closest("tr").find(".quantity-data").val(); 
                // variants_data["available"] = $(this).closest("tr").find(".available-data").val();
                variants_data["sku"] = $(this).closest("tr").find(".sku-data").val();
                variants_data["barcode"] = $(this).closest("tr").find(".barcode-data").val();
                variants_data["variants_combination_id"] = $(this).closest("tr").find(".variants-combination-id").val();
                variants_details[index] = variants_data;
            });

            $(document).on("click",".add-variants-info",function() {
                variants_fields = $(this).closest(".add-variants").find(".clone-variants-options").clone();
                row_count = ($(".variants-options-row").length > 0) ? (parseInt($(".variants-options-row:last").attr("data-row")) + 1) : 1;
                add_variants_fields = "<div data-row='"+row_count+"' class='variants-options-row variants-options-row-"+row_count+"'></div>";
                $(add_variants_fields).insertBefore($(this));
                $(".variants-options-row-"+row_count).html(variants_fields);
                $(".variants-options-row-"+row_count).find(".clone-variants-options").removeClass("clone-variants-options dnone").addClass("save-option-fields"); 
                $(".variants-options-row-"+row_count).find(".save-option-fields").find(".option-name").addClass("required-field");
                values_row_count = variants_option_fields_row;
                $(".variants-options-row-"+row_count).find(".option-values").addClass("variants-options-fields-row variants-options-fields-row-"+values_row_count);
                $(".variants-options-row-"+row_count).find(".option-values").attr("data-row",values_row_count);
                $(".variants-options-row-"+row_count).find(".option-values").find(".option-values-fields").addClass("required-field");
                $(this).find(".add-variants-text").text(@json(__('store-admin.add_another_option')));
                variants_option_fields_row++;
            }); 

            $(document).on("click",".remove-option-row",function() {
                variants_btn_element = $(this).closest(".add-variants").find(".add-variants-text");
                variants_details = {};               
                add_variants = $(this).closest(".add-variants");
                $(this).closest(".variants-options-row").remove();
                variants_btn_text = ($(".variants-options-row").length > 0) ? @json(__('store-admin.add_another_option')) : @json(__('store-admin.add_variants_text'));
                variants_btn_element.text(variants_btn_text);
                if(add_variants.find(".variants-options-row").length > 0)
                    variants_table(add_variants);
                else {
                    variants_tbody_message = '<td colspan="8" class="text-center">Data not found..!</td>';
                    add_variants.find(".variants-tbody").html(variants_tbody_message);
                }
            });  
            $(document).on("click",".remove-option-value",function() {
                _this = $(this);
                data_row = _this.closest(".variants-options-row").attr("data-row") - 1; 
                data_option_row = _this.closest(".variants-options-fields-row").attr("data-row") - 1;
                variant_key = []; variant_index_key = [];
                if(variants_index_details.length > 0) {
                    $(variants_index_details).each(function(key,value) {
                        if(value.toString().search("-") > -1) {
                            split_key = value.split("-");
                            if(split_key[data_row] == data_option_row) {
                                variant_key.push(key);
                                variant_index_key.push(value);
                            }
                        } else {
                            if(value == data_option_row) {
                                variant_key.push(key);
                                variant_index_key.push(value);
                            }
                        }
                    });
                }
                if(variant_key.length > 0) {
                    $(variant_key).each(function(key,val) {
                        _this.closest(".add-variants").find(".variants-tbody").find(".variants-row-"+variant_index_key[key]).remove();
                        if(variant_index_key[key] in variants_details)
                            delete variants_details[variant_index_key[key]];
                        delete variants_index_details[val];
                    });
                    index = 0;
                    _this.closest(".add-variants").find(".variants-tbody").find(".variants-row").each(function() {
                        row_index = index + 1; 
                        $(this).find("td:first").text(row_index);
                        index++;
                    });
                    variants_index_details = variants_index_details.filter(function(val){
                        return val
                    });
                }
                add_variants = _this.closest(".add-variants");
                option_values_row = _this.closest(".option-values-row");
                _this.closest(".variants-options-fields-row").remove();
                option_values_row.find(".variants-options-fields-row:eq(0)").find(".option-values-fields").addClass("required-field");
                if(variants_index_details.length == 0) 
                    variants_table(add_variants);
            }); 

            $(document).on("keyup",".option-values-fields",function(event) {
                event.stopPropagation();
                if(!($(this).closest(".variants-options-fields-row").next().hasClass('variants-options-fields-row'))) {
                    variants_option_image = $(this).closest(".add-variants").find(".clone-option-image").clone();
                    variants_option_fields = $(this).closest(".add-variants").find(".clone-option-values").clone();
                    row_count = variants_option_fields_row;
                    add_variants_fields = "<div data-row='"+row_count+"' class='row mb-2 align-items-center variants-options-fields-row variants-options-fields-row-"+row_count+"'></div>";
                    $(add_variants_fields).appendTo($(this).closest(".option-values-row"));
                    $(".variants-options-fields-row-"+row_count).html(variants_option_image);
                    $(".variants-options-fields-row-"+row_count).find(".variant-option-img").attr("name","variant_option_image[]");
                    $(".variants-options-fields-row-"+row_count).find(".clone-option-image").removeClass("clone-option-image dnone"); 
                    $(".variants-options-fields-row-"+row_count).append(variants_option_fields);
                    $(".variants-options-fields-row-"+row_count).find(".clone-option-values").removeClass("clone-option-values dnone"); 
                    $(".variants-options-fields-row-"+row_count).find(".option-values-fields").attr("name","option_fields_value[]");
                    $(".variants-options-fields-row-"+row_count).find(".variant-options-id").attr("name","option_fields_id[]");
                    $(".variants-options-fields-row-"+row_count).find(".variant-options-name").attr("name","option_names[]");
                    variants_option_remove = $(this).closest(".add-variants").find(".clone-option-remove").clone();
                    $(".variants-options-fields-row-"+row_count).prev().append(variants_option_remove);
                    remove_row_count = $(this).closest(".option-values-row").find(".remove-option-value").length;
                    $(".variants-options-fields-row-"+row_count).prev().find(".clone-option-remove").removeClass("clone-option-remove dnone"); 
                    $(".variants-options-fields-row-"+row_count).prev().find(".remove-option-value").attr("data-row",remove_row_count);
                    row_count++; variants_option_fields_row++;
                }
                variants_table($(this).closest(".add-variants"));
            });  

            function variants_table(_this) {
                variant_mode = $(".mode").val();
                let attributes = [];
                _this.find(".variants-options-row").each(function() {
                    variants_array = []; variants_options_ids = [];
                    $(this).find(".save-option-fields").find(".option-values-fields").each(function() {
                        if($(this).val() != "") {
                            variants_array.push($(this).val());
                            variants_options_ids.push($(this).closest(".variants-options-fields-row").find(".variant-options-id").val());
                        }
                    });
                    if(variants_array.length > 0)
                        attributes.push(variants_array);
                });
                var variants_tbody = "";
                if(attributes.length > 0) {
                    var variations = [];
                    variants_index_details = [];
                    function generateCombinations(index, currentIndexCombination, currentValueCombination) {
                        if (index === attributes.length) {
                            variants_index_details.push(currentIndexCombination.join("-"));
                            variations.push(currentValueCombination.join(" / "));
                            return;
                        }
                        for (var i = 0; i < attributes[index].length; i++) {
                            generateCombinations(
                                index + 1,
                                [...currentIndexCombination, i],
                                [...currentValueCombination, attributes[index][i]]
                            );
                        }
                    }
                    generateCombinations(0, [], []);
                    if(variations.length > 0) {
                        $(variations).each(function(key,val) {
                            index = key+1;
                            var price = onhand = available = quantity = sku = barcode = variants_combination_id = ''; 
                            if(Object.keys(variants_details).length > 0) {
                                if(variants_index_details[key] in variants_details){
                                    price = variants_details[variants_index_details[key]]['price']; 
                                    // quantity = variants_details[variants_index[key]]['quantity'];
                                    onhand = variants_details[variants_index_details[key]]['onhand'];
                                    // available = variants_details[variants_index[key]]['available'];
                                    sku = variants_details[variants_index_details[key]]['sku'];
                                    barcode = variants_details[variants_index_details[key]]['barcode'];
                                    variants_combination_id = variants_details[variants_index_details[key]]['variants_combination_id'];
                                }
                            }
                            if (delete_variants.indexOf(variants_index_details[key]) > -1)  {
                                text_decoration = "text-decoration: line-through";
                                disabled = "disabled";
                                remove_icon = "fa-repeat";
                            } else {
                                text_decoration = disabled = "";
                                remove_icon = "fa-trash";
                            }
                            variants_tbody += "<tr class='variants-row variants-row-"+variants_index_details[key]+"' data-variants-combination-id='"+variants_combination_id+"' data-index='"+variants_index_details[key]+"'><td>"+index+"</td>";
                            variants_tbody += '<td style=" '+text_decoration+' ">'+val+'</td>';
                            variants_tbody += '<td class="input-field-div"><input type="text" data-max="12" data-label="Price" data-type = "show-border-error" data-min = "1" class="form-control form-input-field variants-details price-data price-'+variants_index_details[key]+'" value="'+price+'" '+disabled+'><span class="error error-message"></span></td>';
                            variants_tbody += '<td><input type="number" data-max="12" class="form-control variants-details variant-tax-fields unit-price-data unit-price-'+variants_index_details[key]+'" '+disabled+' disabled></td>';
                            variants_tbody += '<td><input type="number" data-max="12" class="form-control variants-details tax-price-data variant-tax-fields tax-price-'+variants_index_details[key]+'" '+disabled+' disabled></td>';
                            // variants_tbody += '<td><input type="text" class="form-control variants-details quantity-data quantity-'+variants_index_details[key]+'" value="'+quantity+'"></td>';
                            variants_tbody += '<td class="input-field-div"><input type="text" data-label="On Hand" data-max="10" class="form-control form-input-field variants-details onhand-data onhand-'+variants_index_details[key]+'" value="'+onhand+'" '+disabled+'><span class="error error-message"></span></td>';
                            // variants_tbody += '<td><input type="text" class="form-control variants-details available-data available-'+variants_index_details[key]+'" value="'+available+'" '+disabled+'></td>';
                            variants_tbody += '<input type="hidden" class="form-control variants-details variants-combination-id-data variants-combination-id-'+variants_index_details[key]+'" data-value="'+variants_combination_id+'" value="'+variants_combination_id+'">';
                            variants_tbody += '<td class="input-field-div"><input type="text" data-max="15" data-label="SKU" class="form-control form-input-field variants-details sku-data sku-'+variants_index_details[key]+'" value="'+sku+'" '+disabled+'><span class="error error-message"></span></td>';
                            variants_tbody += '<td class="barcode-section text-center"><canvas id="barcodeCanvas" class="barcodeCanvas dnone"></canvas><button class="btn btn-circle btn-danger btn-xs generateButton" id="generateButton" type="button"><i class="fa fa-barcode"></i></button><input type="text" class="form-control variants-details product-barcode dnone barcode-data barcode-'+variants_index_details[key]+'" value="'+barcode+'" '+disabled+'></td>';
                            variants_tbody += '<td class="text-right"><div class="d-flex"><a href="#" class="btn btn-circle btn-primary btn-xs remove-variants"><i class="fa '+remove_icon+'"></i></a></div></td>';
                            variants_tbody += "</tr>";
                        });
                    } 
                } else 
                    variants_tbody = '<td colspan="8" class="text-center">Data not found..!</td>';
                _this.closest(".add-variants").find(".variants-tbody").html(variants_tbody);
                $(".variants-tbody").find("tr").each(function() {
                    calculateTax($(this).find(".price-data"));
                });
                return true;
            }

            function add_variations_to_array(base, variations){
                let ret = []; let combination_index = [];
                for(i=0;i<base.length;i++) {
                    for(j=0;j<variations.length;j++) {
                        ret.push(base[i]+" / "+variations[j]);
                        combination_index.push(i+"-"+j);
                    }
                }
                return [ret,combination_index];
            }
            
            $(document).on("click",".add-option-values",function(event) {
                event.stopPropagation();
                error = 0;
                $(this).closest(".save-option-fields").find(".error-message").text("");
                $(this).closest(".save-option-fields").find(".required-field").each(function() {
                    field_value = $(this).val();
                    field_label = $(this).attr("data-label");
                    if((field_value == "")) {
                        $(this).closest(".input-field-div").find(".error.error-message").text(field_label+" is required").css("color", "#F30000");
                        error++;
                    } 
                });
                if(error > 0)
                    return false;
                else {
                    save_variants_fields = $(this).closest(".add-variants").find(".clone-save-variants").clone();  
                    if($(this).closest(".variants-options-row").find(".save-variants-fields").length == 0) {
                        $(this).closest(".variants-options-row").append(save_variants_fields); 
                        $(this).closest(".variants-options-row").find(".clone-save-variants").removeClass("clone-save-variants dnone").addClass("save-variants-fields"); 
                    }
                    option_name = $(this).closest(".variants-options-row").find(".save-option-fields").find(".option-name").val();
                    $(this).closest(".variants-options-row").find(".save-option-name").text(option_name);
                    save_option_values = "";
                    $(this).closest(".variants-options-row").find(".save-option-fields").find(".option-values-fields").each(function() {
                        if($(this).val() != "") 
                            save_option_values += '<p class="bg-light p-2 rounded-pill mr-2 mb-1 save-option-values-fields">'+$(this).val()+'</p>';
                    });
                    $(this).closest(".variants-options-row").find(".save-option-values").html(save_option_values);
                    $(this).closest(".variants-options-row").find(".save-option-fields").addClass("dnone");
                    $(this).closest(".variants-options-row").find(".save-variants-fields").removeClass("dnone");
                }
            }); 

            $(document).on("click",".edit-option-fields",function(event) {
                event.stopPropagation();
                $(this).closest(".variants-options-row").find(".save-option-fields").removeClass("dnone");
                $(this).closest(".variants-options-row").find(".save-variants-fields").addClass("dnone");
            }); 

            $(document).on("click",".remove-variants",function(event) {
                event.stopPropagation();
                index = $(this).closest("tr").attr("data-index");
                if(index.search("-") > -1) {
                    if($(this).find(".fa-trash").length > 0) {
                        delete_variants.push(index);
                        $(this).closest("tr").find("td:nth-child(2)").css("textDecoration",'line-through');
                        $(this).closest("tr").find("input").prop('disabled',true);
                        $(this).find(".fa-trash").removeClass("fa-trash").addClass("fa-repeat");
                        $(this).closest("tr").addClass("remove-variant-row");
                    } else if($(this).find(".fa-repeat").length > 0) {
                        const remove_index = delete_variants.indexOf(index);
                        if (remove_index > -1)  
                            delete_variants.splice(remove_index, 1); 
                        $(this).closest("tr").find("td:nth-child(2)").css("textDecoration",'');
                        $(this).closest("tr").find("input").prop('disabled',false);
                        $(this).find(".fa-repeat").removeClass("fa-repeat").addClass("fa-trash");
                        $(this).closest("tr").removeClass("remove-variant-row");
                    }
                    
                }  else {
                    if(index in variants_details)
                        delete variants_details[index];
                    $(this).closest(".add-variants").find(".variants-options-fields-row").eq(index).remove();
                    $(this).closest(".add-variants").find(".save-option-values-fields").eq(index).remove();
                    add_variants = $(this).closest(".add-variants");
                    $(this).closest("tr").remove();
                    var k = 1;
                    add_variants.find(".variants-tbody tr").each(function() {
                        $(this).find("td:eq(0)").text(k);
                        k++;
                    });
                }
            });  

            $(document).on("change",".tax-input-field",function() {
                tax_input_field = $('input[name="products[taxable]"]:checked').val();
                if(tax_input_field == 1) {
                    $(this).closest(".price-info").find(".tax-details-row").removeClass("dnone");
                    // $(this).closest(".price-info").find(".tax-amount").addClass("required-field");
                    $(this).closest(".price-info").find(".tax-incl-details-row").addClass("dnone");
                    // $(this).closest(".price-info").find(".tax-percentage").removeClass("required-field");
                    // $(this).closest(".price-info").find(".tax-percentage").val("");
                }
                else {
                    $(this).closest(".price-info").find(".tax-details-row").addClass("dnone");
                    $(this).closest(".price-info").find(".tax-amount").removeClass("required-field");
                    $(this).closest(".price-info").find(".tax-incl-details-row").removeClass("dnone");
                    // $(this).closest(".price-info").find(".tax-percentage").addClass("required-field");
                    $(this).closest(".price-info").find(".tax-amount").val("");
                }
                hideShowTaxField();
            });   
            //calculate the profit and margin
            /*$(document).on("change",".product-price",function() {
                sell_price = $(this).closest(".price-info").find(".sell-price").val();
                buy_price = $(this).closest(".price-info").find(".buy-price").val();
                profit = ($.trim(sell_price) != "" && $.trim(buy_price) != "") ? (sell_price - buy_price).toFixed(2) : '--';  
                margin = (profit != "--" && $.trim(buy_price) != "") ? (100 * (profit) / buy_price)  : '--';
                gross_margin = (margin != "--") ? (margin >= 100) ? 100 + "%" : margin.toFixed(2) + "%" : "--";
                $(this).closest(".price-info").find(".profit-price").val(profit);
                $(this).closest(".price-info").find(".margin-percentage").val(gross_margin);
            });*/
            $(document).on("keyup change",".price-data",function() {
                product_price = $(this).val();
                calculateTax($(this));
            });
            $(document).on("keypress",".price-data,.onhand-data",function(evt) {
                return isNumber(evt);
            });
            $(document).on("click",".save-products-info",function() {
                _this = $(this);
                variant_fields_length = _this.closest("form").find(".variants-tbody tr:not(.remove-variant-row,.variants-tbody-empty)").length;
                _this.closest("form").find(".required-field").css("border","1px solid #86a4c3");
                _this.closest("form").find(".variants-tbody").find(".remove-variant-row").find(".price-data").removeClass("required-field");
                product_type = $('input[name="products[type_of_product]"]:checked').val();
                if(variant_fields_length > 0 && product_type == "variant") {
                    _this.closest("form").find(".variants-tbody tr:not(.remove-variant-row)").find(".price-data").addClass("required-field");
                    _this.closest("form").find(".sell-price").removeClass("required-field");
                } else {
                    _this.closest("form").find(".variants-tbody tr:not(.remove-variant-row) .price-data").removeClass("required-field");
                    _this.closest("form").find(".sell-price").addClass("required-field");
                }
                check_fields = validateFields($(this));
                if(check_fields > 0)
                    return false;
                else {
                    variants_combination_details = {}; save_variants_details = {}; variants_option_details = {}; variants_combination_array = []; variants_array = []; variants_options_array = []; 
                    $(this).closest("form").find(".variants-options-row").each(function() { 
                        if($(this).find(".option-name").val() != "") {
                            variants_data = {};
                            variants_data.variants_name = $(this).find(".option-name").val();
                            variants_data.variants_id = $(this).find(".variants-id").val();
                            variants_array.push(variants_data);
                        }
                    });
                    save_variants_details = JSON.stringify(variants_array)
                    $(this).closest("form").find(".save-variants-details").val(JSON.stringify(save_variants_details));
                    $(this).closest("form").find(".variants-options-row").find(".option-values-fields").each(function() { 
                        if($(this).val() != "") {
                            variants_name = $(this).closest(".variants-options-row").find(".option-name").val()
                            $(this).closest(".variants-options-fields-row").find(".variant-options-name").val(variants_name);
                        }
                    });
                    $(this).closest("form").find(".variants-tbody tr:not(.remove-variant-row)").each(function() { 
                        if($(this).closest("tr").find("td:eq(1)").text() != "") {
                            variants_combination_data = {};
                            variants_combination_data.variants_name = $(this).closest("tr").find("td:eq(1)").text();
                            variants_combination_data.price = $(this).closest("tr").find(".price-data").val();
                            // variants_combination_data.quantity = $(this).closest("tr").find(".quantity-data").val();
                            variants_combination_data.onhand = $(this).closest("tr").find(".onhand-data").val();
                            // variants_combination_data.available = $(this).closest("tr").find(".available-data").val();
                            variants_combination_data.sku = $(this).closest("tr").find(".sku-data").val();
                            variants_combination_data.barcode = $(this).closest("tr").find(".barcode-data").val();
                            variants_combination_data.variants_combination_id = $(this).closest("tr").find(".variants-combination-id-data").val();
                            variants_combination_array.push(variants_combination_data);
                        }
                    });
                    variants_combination_details = JSON.stringify(variants_combination_array);
                    $(this).closest("form").find(".save-variants-combination-details").val(JSON.stringify(variants_combination_details));
                    return true;   
                }
            });
            $(document).ready(function() {
                if($(".category-id").val() != "") {
                    sub_category_id = $(".category-id").closest("form").find(".sub-category-id").val();
                    subCategoryList($(".category-id").val(),$(".category-id"),sub_category_id);
                }
            });
            $(document).on("change",".category-id",function(event) {
                event.preventDefault();
                _this = $(this);
                category_id = _this.val();
                subCategoryList(category_id,_this);
            });
            function subCategoryList(category_id,_this,sub_category_id = '') {
                sub_category_url = _this.closest("form").find(".sub-category-url").val();
                mode = _this.closest("form").find(".mode").val();
                $.ajax({
                    url: sub_category_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,category_id: category_id,mode:mode},
                    success: function(response){
                        sub_category_details = response.sub_category_details;
                        sub_category_list = '<option value="">--Select Sub Category--</option>';
                        if(sub_category_details.length > 0) {
                            $(sub_category_details).each(function(key,val) {
                                selected = (sub_category_id == val.sub_category_id) ? 'selected' : '';
                                sub_category_list += '<option value="'+val.sub_category_id+'" '+selected+'>'+val.sub_category_name+'</option>';
                            });
                        }
                        _this.closest('form').find(".sub-category-list").html('').html(sub_category_list);
                    }
                });
            }
            $(document).on("change",".sku-barcode-field",function(event) {
                event.preventDefault();
                sku_barcode_field = $(this).val();
                if(sku_barcode_field != "") 
                    $(this).closest(".sku-barcode-details").find(".sku-barcode-field").removeClass("required-field");
                // else
                //     $(this).closest(".sku-barcode-details").find(".sku-barcode-field").addClass("required-field");
            });
            function generateRandomBarcode(barcodeValue = '',_this = '') {
                barcode_unique_url = $(".barcode-unique-url").val();
                barcodeValue = '';
                // Generate the first 12 digits randomly
                for (var i = 0; i < 12; i++) {
                    barcodeValue += Math.floor(Math.random() * 10);
                }
                // Calculate the checksum digit
                var sum = 0;
                for (var i = 0; i < 12; i++) {
                    var digit = parseInt(barcodeValue.charAt(i));
                    sum += (i % 2 === 0) ? digit : digit * 3;
                }
                var checksumDigit = (10 - (sum % 10)) % 10;
                // Set the last digit as the checksum digit
                barcodeValue += checksumDigit;
                $.ajax({
                    url: barcode_unique_url,
                    type: 'post',
                    data: {_token: CSRF_TOKEN,barcode: barcodeValue},
                    dataType: 'json',
                    success: function(response){
                        if(response.message == 1) {
                            generateRandomBarcode('',_this);
                        } else {
                            showBarcode(barcodeValue,_this);
                        }
                    }
                });
            }
            function showBarcode(barcodeValue,_this = '') {
                var canvas = _this.closest(".barcode-section").find(".barcodeCanvas")[0];
                JsBarcode(canvas, barcodeValue, {
                    format: "ean13",
                    displayValue: true
                });
                _this.closest(".barcode-section").find(".product-barcode").val(barcodeValue);
                _this.closest(".barcode-section").find(".barcodeCanvas").removeClass("dnone");
            }
            if($(".mode").val() == "edit") {
                product_type = $('input[name="products[type_of_product]"]:checked').val();
                if(product_type == "single" && $(".product-barcode").val() != "") {
                    showBarcode($(".product-barcode").val(),$(".product-barcode"));
                } else {
                    $(".variants-table").find(".variants-tbody").find(".barcode-data").each(function() {
                        barcodeValue = $(this).val();
                        if(barcodeValue != "")
                            showBarcode(barcodeValue,$(this));
                    });
                }
            }

            $(document).on("click",".generateButton",function() {
                _this = $(this);
                generateRandomBarcode('',_this);
            });
            get_product_images = $(".get-product-images").val();
            let product_images =  (get_product_images != "") ? get_product_images.split('***') : [];
            preloaded = [];
            if(product_images.length > 0) {
                $(product_images).each(function(key,val) {
                    product_image = {};
                    product_image.id = key;
                    product_image.src = val;
                    product_image.mode = 'add';
                    preloaded.push(product_image);
                });
            }
            function removeProductImg(remove_img_path,_this) {
                product_id = _this.closest("body").find(".product-id").val();
                $.ajax({
                    url: "{{ route(config('app.prefix_url').'.'.$store_url.'.'.$prefix_url.'.product.remove-image') }}",
                    type: 'post',
                    data: {_token: CSRF_TOKEN,product_id: product_id,remove_img_path: remove_img_path},
                    success: function(response){
                        toastr.options =
                        {
                            "closeButton" : true,
                            "progressBar" : true
                        }
                        toastr.success(response.message);
                    }
                });
            }
            $(document).on("change",".variant-option-img",function() {
                readURL($(this));
            });

            function readURL(_this) {
                var preview = _this.closest(".variants-options-fields-row").find(".variant-option-img-preview")[0];
                input = _this[0];
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
        <script src="{{ URL::asset('assets/cashier-admin/vendor_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.js') }}"></script>
    </body>
</html>