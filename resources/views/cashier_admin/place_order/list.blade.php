<!DOCTYPE html>
<html lang="en">
   <head>
      <title>{{ trans('store-admin.place_order_page_title',['company' => Auth::user()->company_name]) }}</title>
      @include('common.cashier_admin.header')
      <link href="{{ URL::asset('assets/cashier-admin/css/swiper.min.css') }}" rel="stylesheet" type="text/css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css">
      <style>
         small {
            margin-top: 0.7142857143rem;
            margin-bottom: 0.7142857143rem;
         }
      </style>
   </head>
   <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
      <div class="wrapper">
         @include('common.cashier_admin.navbar')
         @include('common.cashier_admin.sidebar')
         <div class="content-wrapper" >
            <div class="container-full">
               <section class="content">
                  <div class="card mb-4 place-order-cart">
                     <input type="hidden" class="mode" value="{{$type}}">
                     <input type="hidden" class="get-cart-data" value="{{!empty($get_cart_data) ? json_encode($get_cart_data[0]) : '' }}">
                     <input type="hidden" class="product-ids" value="{{!empty($product_ids) ? json_encode($product_ids[0]) : '' }}">
                     <input type="hidden" class="variant-ids" value="{{!empty($variant_ids) ? json_encode($variant_ids[0]) : '' }}">
                     <input type="hidden" class="total-cart-quantity" value='{{!empty($total_cart_quantity) ? $total_cart_quantity[0] : 0 }}'>
                     <input type="hidden" class="variant-combinations" value="{{!empty($variant_combination_data) ? json_encode($variant_combination_data) : '' }}">
                     <input type="hidden" class="specific-discount-data" value="{{!empty($productDiscounts) ? json_encode($productDiscounts) : '' }}">
                     <input type="hidden" class="all-discount-data" value="{{!empty($all_discount) ? json_encode($all_discount) : '' }}">
                     <input type="hidden" class="get-product-by-barcode" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.index') }}"> 
                     <header class="row card-header">
                        <div class="col-lg-3 col-md-6 me-auto">
                           <h4>{{ __('store-admin.placeorder') }}</h4>
                        </div>
                        <div class="col-lg-6 col-md-12">  
                           <div class="text-right">
                              <input type="hidden" class="view-cart-url" value="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.view-cart') }}">
                              <button type="button" class="btn btn-rounded btn-warning rounded scan-barcode" data-toggle="modal" data-target="#scan-the-barcode">{{ __('store-admin.scan_barcode') }}</button>
                              <button type="button" class="btn btn-rounded btn-dark rounded clear-cart-btn dnone" style="" data-toggle="modal" data-target="#confirm-modal">{{ __('store-admin.clear_cart') }}</button>
                              <button class="btn btn-rounded btn-primary rounded view-cart" style=""><span class="card-total-amount" style="font-size:16px;">{{ __('store-admin.view_cart') }}</span><span class="badge2 total-item-count dnone"></span><i class="fa fa-arrow-right ms-3"></i></button>
                           </div>
                        </div>
                     </header>
                     <div class="card-body">
                        @if(isset($category_details) && !empty($category_details) && count($category_details) > 0 && count($product_details) > 0)
                           <div class="swiper-container swiper-tabs-nav">
                              <div class="swiper-wrapper">
                                 @if(count($product_details) > 0)
                                    <div class="swiper-slide">
                                       <div class="d-flex align-items-center">
                                          <div>{{ __('store-admin.all') }} <br/> <span>{{ count($product_details) }}</span></div>
                                       </div>
                                    </div>
                                 @endif
                                 @foreach ($category_details as $category)
                                    @if(!empty($category_count) && array_key_exists($category->category_id,$category_count) && ($category_count[$category->category_id] > 0))
                                       <div class="swiper-slide">
                                          <div class="d-flex align-items-center">
                                             <div>
                                                @if(!empty($category->icon) && (strpos($category->icon, "assets/placeholder_icon.jpg") != true))
                                                   <img src="{{ $category->icon }}" class="img-fluid" style="width:32px;height:32px;">
                                                @endif
                                             </div>
                                             <div>{{ $category->category_name }} 
                                                <br/>
                                                <span>{{ $category_count[$category->category_id] }}</span>
                                             </div>
                                          </div>
                                       </div>
                                    @endif
                                 @endforeach
                              </div>
                           </div>
                        @elseif($search_type == "search" && count($product_details) <= 0)
                           <p class="text-center">No results for {{ !empty($search_text) && isset($search_text[0]) ? $search_text[0] : "" }}. Search instead for {{ !empty($search_text) && isset($search_text[0]) ? $search_text[0] : "" }}.</p>
                        @else
                           <p class="text-center">Please add the category and product.</p>
                        @endif
                        <div class="swiper-container swiper-tabs-content">
                           <div class="swiper-wrapper">
                              @if(isset($category_details) && !empty($category_details))
                                 <div class="swiper-slide">
                                    <div class="row">
                                       @if(isset($product_details) && !empty($product_details))
                                          @foreach ($product_details as $product)
                                             <div class="col-lg-6 product-card-details product-card-details-{{$product['product_id']}}">
                                                <div class="card-box">
                                                   <div class="row align-items-center">
                                                      <div class="col-md-4">
                                                         @php $product_image = ""; @endphp
                                                         @if($product['category_image'] != "") 
                                                               @php
                                                                  $product_images = explode("***",$product['category_image']);
                                                                  $product_image = $product_images[0];
                                                               @endphp
                                                         @endif
                                                         <div class="text-center"><img src="{{ $product_image }}" class="img-fluid rounded-circle"  style="width: 310px;height: 110px;" alt=""></div>
                                                      </div>
                                                      <div class="col-md-8">
                                                         <div class="p-2">
                                                            <h4 style="min-height: 10px;">{{ $product['product_name'] }}</h4>
                                                            <!-- <p>{{ $product['category_name'] }}</p> -->
                                                            <div class="py-2">
                                                               @if(!empty($variant_combinations) && array_key_exists($product['product_id'],$variant_combinations))
                                                                  <select class="form-control select-variants-type" name="variants_item[{{$category->category_id}}][{{$product['product_id']}}][]" data-live-search="true">
                                                                     @foreach($variant_combinations[$product['product_id']] as $variants)
                                                                        <option data-quantity="{{$variants['on_hand']}}" value="{{$variants['variants_combination_id']}}">{{$variants['variants_combination_name']}}</option>
                                                                     @endforeach
                                                                  </select>
                                                               @endif
                                                            </div>
                                                            <div class="product-discount-part"></div>
                                                            @if(!empty($variant_combinations) && array_key_exists($product['product_id'],$variant_combinations))
                                                               <h3 class="product-variant-price d-flex"></h3>
                                                            @else
                                                               <div class="d-flex">
                                                                  <h3>
                                                                     <h3 class="product-original-price">SAR {{ number_format((float)($product['price']), 2, '.', '') }}</h3>
                                                                     <!-- <small>(Incl of Tax)</small> -->
                                                                  </h3>
                                                               </div>
                                                            @endif
                                                            <div class="d-flex justify-content-between">
                                                               <div class="number product-item d-flex">
                                                                  <span class="minus">-</span>
                                                                  <input type="hidden" class="product-price" value="{{ $product['price'] }}">
                                                                  <input type="hidden" class="product-id" value="{{ $product['product_id'] }}">
                                                                  <input type="hidden" class="trackable" value="{{ $product['trackable'] }}">
                                                                  <input type="hidden" class="product-unit" value="{{ $product['unit'] }}">
                                                                  <input type="hidden" class="type-of-product" value="{{ $product['type_of_product'] }}">
                                                                  <input type="text" name="product_item[{{$category->category_id}}][{{$product['product_id']}}]" class="quantity quantity-<?php echo $product['product_id']; ?>" onkeypress="return isNumber(event)" value="1"/>
                                                                  <span class="plus">+</span>
                                                               </div>
                                                               <button type="button" class="btn btn-rounded btn-primary rounded add-to-cart" style="padding: 2px 12px;font-size: 12px;">{{ __('store-admin.add_to_cart') }}</button>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          @endforeach
                                       @endif
                                    </div>
                                 </div>
                                 @foreach ($category_details as $category_id => $category)
                                    @if(array_key_exists($category->category_id,$all_sub_category_details))
                                       @php
                                          $sub_category_data = $all_sub_category_details[$category->category_id];
                                       @endphp
                                       @if(!empty($category_count) && array_key_exists($category->category_id,$category_count) && ($category_count[$category->category_id] > 0))
                                          <div class="swiper-slide all-product-data">
                                             @if(isset($sub_category_data) && !empty($sub_category_data)) 
                                                <div class="swiper-container swiper-custom-nav swiper-tabs-nav-{{$category_id}}" data-id="{{$category_id}}">
                                                   <div class="swiper-wrapper">
                                                      <div class="swiper-slide">{{ __('store-admin.all') }} <span class="all-product-count"></span></div>
                                                      @foreach ($sub_category_data as $sub_category)
                                                         @if(!empty($sub_category_count) && array_key_exists($category->category_id,$sub_category_count) && array_key_exists($sub_category['sub_category_id'],$sub_category_count[$category->category_id]) && ($sub_category_count[$category->category_id][$sub_category['sub_category_id']] > 0))
                                                            <div class="swiper-slide">  
                                                               @php
                                                                  $count_sub_category = !empty($sub_category_product_details) ? count($sub_category_product_details[$category->category_id][$sub_category['sub_category_id']]) : 0;
                                                               @endphp
                                                               {{ $sub_category['sub_category_name'] }} ({{ $count_sub_category }})
                                                            </div> 
                                                         @endif  
                                                      @endforeach
                                                   </div>
                                                </div>
                                                <div class="swiper-container swiper-tabs-content-{{$category_id}}">
                                                   <div class="swiper-wrapper">
                                                      @php
                                                         $sub_category_data = $all_sub_category_details[$category->category_id];
                                                      @endphp
                                                      <div class="swiper-slide all-product-details">
                                                         <div class="row">
                                                            @if(array_key_exists($category->category_id,$all_product_details))
                                                               @php
                                                                  $product_data = $all_product_details[$category->category_id];
                                                               @endphp
                                                               @if(isset($product_data) && !empty($product_data))
                                                                  @foreach ($product_data as $product)
                                                                     <div class="col-lg-6 product-card-details product-card-details-{{$product['product_id']}}">
                                                                        <div class="card-box">
                                                                           <div class="row align-items-center">
                                                                              <div class="col-md-4">
                                                                                 @php $product_image = ""; @endphp
                                                                                 @if($product['category_image'] != "") 
                                                                                       @php
                                                                                          $product_images = explode("***",$product['category_image']);
                                                                                          $product_image = $product_images[0];
                                                                                       @endphp
                                                                                 @endif
                                                                                 <div class="text-center"><img src="{{ $product_image }}" class="img-fluid rounded-circle"  style="width: 310px;height: 110px;" alt=""></div>
                                                                              </div>
                                                                              <div class="col-md-8">
                                                                                 <div class="p-2">
                                                                                    <h4 style="min-height: 10px;">{{ $product['product_name'] }}</h4>
                                                                                    <!-- <p>{{ $product['category_name'] }}</p> -->
                                                                                    <div class="py-2">
                                                                                       @if(!empty($variant_combinations) && array_key_exists($product['product_id'],$variant_combinations))
                                                                                          <select class="form-control select-variants-type" name="variants_item[{{$category->category_id}}][{{$product['product_id']}}][]" data-live-search="true">
                                                                                             <!-- <option value="">Select</option> -->
                                                                                             @foreach($variant_combinations[$product['product_id']] as $variants)
                                                                                                <option data-quantity="{{$variants['on_hand']}}" value="{{$variants['variants_combination_id']}}">{{$variants['variants_combination_name']}}</option>
                                                                                             @endforeach
                                                                                          </select>
                                                                                       @endif
                                                                                    </div>
                                                                                    <div class="product-discount-part"></div>
                                                                                    @if(!empty($variant_combinations) && array_key_exists($product['product_id'],$variant_combinations))
                                                                                       <h3 class="product-variant-price d-flex"></h3>
                                                                                    @else
                                                                                       <div class="d-flex">
                                                                                          <h3>
                                                                                             <h3 class="product-original-price">SAR {{ number_format((float)($product['price']), 2, '.', '') }} </h3>
                                                                                             <!-- <small>(Incl of Tax)</small> -->
                                                                                          </h3>
                                                                                       </div>
                                                                                    @endif
                                                                                    <div class="d-flex justify-content-between">
                                                                                       <div class="number product-item d-flex">
                                                                                          <span class="minus">-</span>
                                                                                          <input type="hidden" class="product-price" value="{{ $product['price'] }}">
                                                                                          <input type="hidden" class="product-id" value="{{ $product['product_id'] }}">
                                                                                          <input type="hidden" class="trackable" value="{{ $product['trackable'] }}">
                                                                                          <input type="hidden" class="product-unit" value="{{ $product['unit'] }}"> 
                                                                                          <input type="hidden" class="type-of-product" value="{{ $product['type_of_product'] }}">
                                                                                          <input type="text" name="product_item[{{$category->category_id}}][{{$product['product_id']}}]" class="quantity quantity-<?php echo $product['product_id']; ?>" onkeypress="return isNumber(event)" value="1"/>
                                                                                          <span class="plus">+</span>
                                                                                       </div>
                                                                                       <button type="button" class="btn btn-rounded btn-primary rounded add-to-cart" style="padding: 2px 12px;font-size: 12px;">{{ __('store-admin.add_to_cart') }}</button>
                                                                                    </div>
                                                                                 </div>
                                                                              </div>
                                                                           </div>
                                                                        </div>
                                                                     </div>
                                                                  @endforeach
                                                               @endif
                                                            @endif
                                                            @if(!empty($category_count) && array_key_exists($category->category_id,$category_count) && ($category_count[$category->category_id] > 0) && isset($sub_category_data) && !empty($sub_category_data) && !empty($sub_category_product_details))    
                                                               @foreach ($sub_category_data as $sub_category)
                                                                  @php
                                                                     $product_data = $sub_category_product_details[$category->category_id][$sub_category['sub_category_id']];
                                                                  @endphp
                                                                  @foreach ($product_data as $product)
                                                                     <div class="col-lg-6 product-card-details product-card-details-{{$product['product_id']}}">
                                                                        <div class="card-box">
                                                                           <div class="row align-items-center">
                                                                              <div class="col-md-4">
                                                                                 @php $product_image = ""; @endphp
                                                                                 @if($product['category_image'] != "") 
                                                                                       @php
                                                                                          $product_images = explode("***",$product['category_image']);
                                                                                          $product_image = $product_images[0];
                                                                                       @endphp
                                                                                 @endif
                                                                                 <div class="text-center"><img src="{{ $product_image }}" class="img-fluid rounded-circle"  style="width: 310px;height: 110px;" alt=""></div>
                                                                              </div>
                                                                              <div class="col-md-8">
                                                                                 <div class="p-2">
                                                                                    <h4 style="min-height: 10px;">{{ $product['product_name'] }}</h4>
                                                                                    <!-- <p>{{ $product['category_name'] }}</p> -->
                                                                                    <div class="py-2">
                                                                                       @if(!empty($variant_combinations) && array_key_exists($product['product_id'],$variant_combinations))
                                                                                          <select class="form-control select-variants-type" name="variants_item[{{$category->category_id}}][{{$product['product_id']}}][]" data-live-search="true">
                                                                                             <!-- <option value="">Select</option> -->
                                                                                             @foreach($variant_combinations[$product['product_id']] as $variants)
                                                                                                <option data-quantity="{{$variants['on_hand']}}" value="{{$variants['variants_combination_id']}}">{{$variants['variants_combination_name']}}</option>
                                                                                             @endforeach
                                                                                          </select>
                                                                                       @endif
                                                                                    </div>
                                                                                    <div class="product-discount-part"></div>
                                                                                    @if(!empty($variant_combinations) && array_key_exists($product['product_id'],$variant_combinations))
                                                                                       <h3 class="product-variant-price d-flex"></h3>
                                                                                    @else
                                                                                       <div class="d-flex">
                                                                                          <h3>
                                                                                             <h3 class="product-original-price">SAR {{ number_format((float)($product['price']), 2, '.', '') }} </h3>
                                                                                             <!-- <small>(Incl of Tax)</small> -->
                                                                                          </h3>
                                                                                       </div>
                                                                                    @endif
                                                                                    <div class="d-flex justify-content-between">
                                                                                       <div class="number product-item d-flex">
                                                                                          <span class="minus">-</span>
                                                                                          <input type="hidden" class="product-price" value="{{ $product['price'] }}">
                                                                                          <input type="hidden" class="product-id" value="{{ $product['product_id'] }}">
                                                                                          <input type="hidden" class="trackable" value="{{ $product['trackable'] }}">
                                                                                          <input type="hidden" class="product-unit" value="{{ $product['unit'] }}">
                                                                                          <input type="hidden" class="type-of-product" value="{{ $product['type_of_product'] }}">
                                                                                          <input type="text" name="product_item[{{$category->category_id}}][{{$product['product_id']}}]" class="quantity quantity-<?php echo $product['product_id']; ?>" onkeypress="return isNumber(event)" value="1"/>
                                                                                          <span class="plus">+</span>
                                                                                       </div>
                                                                                       <button type="button" class="btn btn-rounded btn-primary rounded add-to-cart" style="padding: 2px 12px;font-size: 12px;">{{ __('store-admin.add_to_cart') }}</button>
                                                                                    </div>
                                                                                 </div>
                                                                              </div>
                                                                           </div>
                                                                        </div>
                                                                     </div>
                                                                  @endforeach 
                                                               @endforeach
                                                            @endif
                                                         </div>
                                                      </div>
                                                      @if(!empty($category_count) && array_key_exists($category->category_id,$category_count) && ($category_count[$category->category_id] > 0) && isset($sub_category_data) && !empty($sub_category_data))    
                                                         @foreach ($sub_category_data as $sub_category)
                                                            @if(!empty($sub_category_count) && array_key_exists($category->category_id,$sub_category_count) && array_key_exists($sub_category['sub_category_id'],$sub_category_count[$category->category_id]) && ($sub_category_count[$category->category_id][$sub_category['sub_category_id']] > 0) && !empty($sub_category_product_details))
                                                               <div class="swiper-slide"> 
                                                                  @php
                                                                     $product_data = $sub_category_product_details[$category->category_id][$sub_category['sub_category_id']];
                                                                  @endphp
                                                                  <div class="row">
                                                                     <input type="hidden" class="product-count" value="1">
                                                                     @foreach ($product_data as $product)
                                                                        <div class="col-lg-6 product-card-details product-card-details-{{$product['product_id']}}">
                                                                           <div class="card-box">
                                                                              <div class="row align-items-center">
                                                                                 <div class="col-md-4">
                                                                                    @php $product_image = ""; @endphp
                                                                                    @if($product['category_image'] != "") 
                                                                                       @php
                                                                                          $product_images = explode("***",$product['category_image']);
                                                                                          $product_image = $product_images[0];
                                                                                       @endphp
                                                                                    @endif
                                                                                    <div class="text-center"><img src="{{ $product_image }}" class="img-fluid rounded-circle"  style="width: 310px;height: 110px;" alt=""></div>
                                                                                 </div>
                                                                                 <div class="col-md-8">
                                                                                    <div class="p-2">
                                                                                       <h4 style="min-height: 10px;">{{ $product['product_name'] }}</h4>
                                                                                       <!-- <p>{{ $product['category_name'] }}</p> -->
                                                                                       <div class="py-2">
                                                                                          @if(!empty($variant_combinations) && array_key_exists($product['product_id'],$variant_combinations))
                                                                                             <select class="form-control select-variants-type" name="variants_item[{{$category->category_id}}][{{$product['product_id']}}][]" data-live-search="true">
                                                                                                <!-- <option value="">Select</option> -->
                                                                                                @foreach($variant_combinations[$product['product_id']] as $variants)
                                                                                                   <option data-quantity="{{$variants['on_hand']}}" value="{{$variants['variants_combination_id']}}">{{$variants['variants_combination_name']}}</option>
                                                                                                @endforeach
                                                                                             </select>
                                                                                          @endif
                                                                                       </div>
                                                                                       <div class="product-discount-part"></div>
                                                                                       @if(!empty($variant_combinations) && array_key_exists($product['product_id'],$variant_combinations))
                                                                                          <h3 class="product-variant-price d-flex"></h3>
                                                                                       @else
                                                                                          <div class="d-flex">
                                                                                             <h3>
                                                                                                <h3 class="product-original-price">SAR {{ number_format((float)($product['price']), 2, '.', '') }} </h3>
                                                                                                <!-- <small>(Incl of Tax)</small> -->
                                                                                             </h3>
                                                                                          </div>
                                                                                       @endif
                                                                                       <div class="d-flex justify-content-between">
                                                                                          <div class="number product-item d-flex">
                                                                                             <span class="minus">-</span>
                                                                                             <input type="hidden" class="product-price" value="{{ $product['price'] }}">
                                                                                             <input type="hidden" class="product-id" value="{{ $product['product_id'] }}">
                                                                                             <input type="hidden" class="trackable" value="{{ $product['trackable'] }}">
                                                                                             <input type="hidden" class="product-unit" value="{{ $product['unit'] }}">
                                                                                             <input type="hidden" class="type-of-product" value="{{ $product['type_of_product'] }}">
                                                                                             <input type="text" name="product_item[{{$category->category_id}}][{{$product['product_id']}}]" class="quantity quantity-<?php echo $product['product_id']; ?>" onkeypress="return isNumber(event)" value="1"/>
                                                                                             <span class="plus">+</span>
                                                                                          </div>
                                                                                          <button type="button" class="btn btn-rounded btn-primary rounded add-to-cart" style="padding: 2px 12px;font-size: 12px;">{{ __('store-admin.add_to_cart') }}</button>
                                                                                       </div>
                                                                                    </div>
                                                                                 </div>
                                                                              </div>
                                                                           </div>
                                                                        </div>
                                                                     @endforeach 
                                                                  </div>
                                                               </div>
                                                            @endif  
                                                         @endforeach
                                                      @endif
                                                   </div>
                                                </div>
                                             @endif
                                          </div>
                                       @endif
                                    @else
                                       @if(array_key_exists($category->category_id,$all_product_details))
                                          <div class="swiper-slide">
                                             @php
                                                $product_data = $all_product_details[$category->category_id];
                                             @endphp
                                             <div class="row">
                                                @if(isset($product_data) && !empty($product_data))
                                                   <input type="hidden" class="product-count" value="1">
                                                   @foreach ($product_data as $product)
                                                      <div class="col-lg-6 product-card-details product-card-details-{{$product['product_id']}}">
                                                         <div class="card-box">
                                                            <div class="row align-items-center">
                                                               <div class="col-md-4">
                                                                  @php $product_image = ""; @endphp
                                                                  @if($product['category_image'] != "") 
                                                                     @php
                                                                        $product_images = explode("***",$product['category_image']);
                                                                        $product_image = $product_images[0];
                                                                     @endphp
                                                                  @endif
                                                                  <div class="text-center"><img src="{{ $product_image }}" class="img-fluid rounded-circle"  style="width: 310px;height: 110px;" alt=""></div>
                                                               </div>
                                                               <div class="col-md-8">
                                                                  <div class="p-2">
                                                                     <h4 style="min-height: 10px;">{{ $product['product_name'] }}</h4>
                                                                     <!-- <p>{{ $product['category_name'] }}</p> -->
                                                                     <div class="py-2">
                                                                        @if(!empty($variant_combinations) && array_key_exists($product['product_id'],$variant_combinations))
                                                                           <select class="form-control select-variants-type" name="variants_item[{{$category->category_id}}][{{$product['product_id']}}][]" data-live-search="true">
                                                                              <!-- <option value="">Select</option> -->
                                                                              @foreach($variant_combinations[$product['product_id']] as $variants)
                                                                                 <option data-quantity="{{$variants['on_hand']}}" value="{{$variants['variants_combination_id']}}">{{$variants['variants_combination_name']}}</option>
                                                                              @endforeach
                                                                           </select>
                                                                        @endif
                                                                     </div>
                                                                     <div class="product-discount-part"></div>
                                                                     @if(!empty($variant_combinations) && array_key_exists($product['product_id'],$variant_combinations))
                                                                        <h3 class="product-variant-price d-flex"></h3>
                                                                     @else
                                                                        <div class="d-flex">
                                                                           <h3>
                                                                              <h3 class="product-original-price">SAR {{ number_format((float)($product['price']), 2, '.', '') }} </h3>
                                                                              <!-- <small>(Incl of Tax)</small> -->
                                                                           </h3>
                                                                        </div>
                                                                     @endif
                                                                     <div class="d-flex justify-content-between">
                                                                        <div class="number product-item d-flex">
                                                                           <span class="minus">-</span>
                                                                           <input type="hidden" class="product-price" value="{{ $product['price'] }}">
                                                                           <input type="hidden" class="product-id" value="{{ $product['product_id'] }}">
                                                                           <input type="hidden" class="trackable" value="{{ $product['trackable'] }}">
                                                                           <input type="hidden" class="product-unit" value="{{ $product['unit'] }}">
                                                                           <input type="hidden" class="type-of-product" value="{{ $product['type_of_product'] }}">
                                                                           <input type="text" name="product_item[{{$category->category_id}}][{{$product['product_id']}}]" class="quantity quantity-<?php echo $product['product_id']; ?>" onkeypress="return isNumber(event)" value="1"/>
                                                                           <span class="plus">+</span>
                                                                        </div>
                                                                        <button type="button" class="btn btn-rounded btn-primary rounded add-to-cart" style="padding: 2px 12px;font-size: 12px;">{{ __('store-admin.add_to_cart') }}</button>
                                                                     </div>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   @endforeach
                                                @endif
                                             </div>
                                          </div>
                                       @endif
                                    @endif
                                 @endforeach
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>
               </section>
               <div class="modal center-modal fade scan-the-barcode" id="scan-the-barcode" tabindex="-1">
                  <div class="modal-dialog modal-dialog-scrollable barcode-modal-body">
                        <div class="modal-content">
                           <div class="modal-header">
                           <h5 class="modal-title">{{ __('store-admin.scan_barcode') }}</h5>
                           <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                           </div>
                           <div class="modal-body">
                           <div class="form-group">
                              <div class="radio-list">
                                    <label class="radio-inline p-0 mr-10">
                                       <div class="radio radio-info">
                                       <input type="radio" name="scanner_type" class="scanner-type" id="channel1" value="scanner" checked>
                                       <label for="channel1">{{ __('store-admin.scan_by_scanner') }}</label>
                                       </div>
                                    </label>
                                    <label class="radio-inline">
                                       <div class="radio radio-info">
                                       <input type="radio" name="scanner_type" class="scanner-type" id="channel2" value="camera">
                                       <label for="channel2">{{ __('store-admin.scan_by_camera') }}</label>
                                       </div>
                                    </label>
                              </div>
                              @if ($errors->has('products.product_type'))
                                    <span class="text-danger error-message">{{ $errors->first('products.product_type') }}</span>
                              @endif
                           </div>
                           <div class="mb-4 input-field-div barcode-scanner scanner-field">
                              <label class="form-label">{{ __('store-admin.barcode_value') }}<span>*</span></label>
                              <input type="text" class="form-control barcode-value">
                              <span class="error error-message"></span>
                           </div>
                           <div style="width: 470px;" class="barcode-reader scanner-field" id="reader"></div> 
                           <div class="card mb-4 barcode-product-details dnone" style="margin-top:20px;">
                              <div class="card-body">
                                    <div class="row">
                                       <div class="col-md-12 product-card-details">
                                       <div class="row align-items-center">
                                          <div class="col-md-4">
                                                <div class="text-center"><img src="" class="img-fluid rounded-circle product-category-image"  style="width: 310px;height: 110px;" alt=""></div>
                                          </div>
                                          <div class="col-md-8">
                                                <div class="p-2">
                                                   <h4 class="barcode-product-name" style="min-height: 10px;"></h4>
                                                   <p class="barcode-category-name"></p>
                                                   <div class="py-2 product-variants-type-section dnone">
                                                   <p class="product-variant-combination-name"></p>
                                                   <select class="form-control product-variants-type select-variants-type dnone" name="">
                                                      <option data-quantity="" value=""></option>
                                                   </select>
                                                   <div class="product-discount-part"></div>
                                                   </div>
                                                   <h4 class="product-variant-price d-flex"></h4> 
                                                   <div class="d-flex justify-content-between">
                                                   <div class="number product-item d-flex">
                                                      <span class="minus">-</span>
                                                      <input type="hidden" class="product-price" value="">
                                                      <input type="hidden" class="product-id" value="">
                                                      <input type="hidden" class="trackable" value="">
                                                      <input type="hidden" class="product-unit" value=""> 
                                                      <input type="hidden" class="type-of-product" value="">
                                                      <input type="text" name="" class="quantity" onkeypress="return isNumber(event)" value="1"/>
                                                      <span class="plus">+</span>
                                                   </div>
                                                   <button type="button" class="btn btn-rounded btn-primary rounded add-to-cart" data-type="barcode" style="padding: 2px 12px;font-size: 12px;">{{ __('store-admin.add_to_cart') }}</button>
                                                   </div>
                                                </div>
                                          </div>
                                       </div>
                                       </div>
                                    </div>
                              </div>
                           </div>
                           </div>
                           <div class="modal-footer modal-footer-uniform text-center">
                           <button type="button" class="btn btn-primary" data-dismiss="modal">{{ __('store-admin.close') }}</button>
                           </div>
                        </div>
                  </div>
               </div>

               <div class="modal center-modal fade" id="confirm-modal" tabindex="-1">
                  <div class="modal-dialog">
                     <div class="modal-content">
                     <!-- <div class="modal-header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div> -->
                     <div class="modal-body">
                        <h3 class="text-center">{{ __('store-admin.are_you_sure') }}</h3>
                     </div>
                     <div class="modal-footer modal-footer-uniform">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('store-admin.close') }}</button>
                        <button type="button" class="btn btn-primary float-right clear-cart">{{ __('store-admin.yes_clear_it') }}</button>
                     </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         @include('common.cashier_admin.copyright')
      </div>
      <script src="{{ URL::asset('assets/cashier-admin/js/swiper.min.js') }}"></script>
      @include('common.cashier_admin.footer')      
      <script src="{{ URL::asset('assets/js/validation.js') }}"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"></script>
      <script src="{{ URL::asset('assets/store-admin/js/html5-qrcode.min.js') }}"></script>
      <script>
         function validateQuantity(_this) {
            quantity = _this.closest(".product-card-details").find(".quantity").val(); 
            type_of_product = _this.closest(".product-card-details").find(".type-of-product").val();
            if(type_of_product == "single")
               product_unit = _this.closest(".product-card-details").find(".product-unit").val(); 
            else if(type_of_product == "variant") 
               product_unit = _this.closest(".product-card-details").find("option:selected", ".select-variants-type").attr('data-quantity');
            trackable = _this.closest(".product-card-details").find(".trackable").val(); 
            if((type_of_product == "single" && trackable == 1 || type_of_product == "variant") && product_unit !== "" && product_unit != undefined && (parseInt(quantity) > parseInt(product_unit))) {
               _this.closest(".product-card-details").find(".add-to-cart").prop("disabled",true);
               toastr.options =
               {
                  "closeButton" : true,
                  "progressBar" : true
               }
               toastr.error("Maximum quantity is "+product_unit);
            } else {
               if(type_of_product == "variant" && product_unit !== "" && product_unit <= 0)
                  _this.closest(".product-card-details").find(".add-to-cart").prop("disabled",true);
               else
                  _this.closest(".product-card-details").find(".add-to-cart").prop("disabled",false);
            }
         }
         if($(".product-count").val() == undefined)
            $(".empty-cart").removeClass("dnone");
         swiperTabsNav = new Swiper('.swiper-tabs-nav', {
            spaceBetween: 0,
            slidesPerView: 5,
            loop: false,
            loopedSlides: 5,
            autoHeight: false,
            observer: true,	
            observeParents: true,
            resistanceRatio: 0,
            watchOverflow: true,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
            breakpoints: {
               280: {
                  slidesPerView: 1,
               },
               768: {
                  slidesPerView: 3, 
               },
               980: {
                  slidesPerView: 4,
               },
               1280: {
                  slidesPerView: 5,
               }
            }
		   });
         // Swiper Content
         swiperTabsContent = new Swiper('.swiper-tabs-content', {
            spaceBetween: 0,
            loop:false,
            autoHeight: true,
            observer: true,	
            observeParents: true,
            longSwipes: true,
            resistanceRatio: 0, // Disable First and Last Swiper
            watchOverflow: true,
            loopedSlides: 5,
            allowTouchMove: false,
            thumbs: {
               swiper: swiperTabsNav,
            },
         });
         $(".swiper-custom-nav").each(function() {
            data_id = $(this).attr("data-id");
            swiperTabsNav1 = new Swiper('.swiper-tabs-nav-'+data_id, {
               spaceBetween: 0,
               slidesPerView: 10,
               loop: false,
               observer: true,	
               observeParents: true,
               loopedSlides: 5,
               autoHeight: false,
               resistanceRatio: 0,
               watchOverflow: true,
               watchSlidesVisibility: true,
               watchSlidesProgress: true,
               breakpoints: {
                  280: {
                     slidesPerView: 1,
                  },
                  768: {
                     slidesPerView: 3,
                  },
                  980: {
                     slidesPerView: 4,
                  },
                  1280: {
                     slidesPerView: 10,
                  }
               }
		      });
            // Swiper Content
            swiperTabsContent1 = new Swiper('.swiper-tabs-content-'+data_id, {
               spaceBetween: 0,
               loop:false,
               autoHeight: true,
               observer: true,	
               observeParents: true,
               longSwipes: false,
               resistanceRatio: 0, // Disable First and Last Swiper
               watchOverflow: true,
               loopedSlides: 5,
               // allowTouchMove: false,			
               thumbs: {
                  swiper: swiperTabsNav1,
               },
            });
         });
         variant_combinations = $(".variant-combinations").val();
         if(variant_combinations != "") 
            variant_combinations = $.parseJSON(variant_combinations);

         specific_discount_data = $(".specific-discount-data").val();
         if(specific_discount_data != "") 
            specific_discount_data = $.parseJSON(specific_discount_data);

         all_discount_data = $(".all-discount-data").val();
         if(all_discount_data != "") 
            all_discount_data = $.parseJSON(all_discount_data);

         function getBarcodeProduct(qrCodeMessage) {
            product_by_barcode = $(".get-product-by-barcode").val();
            $(".product-variants-type").html("");
            if(qrCodeMessage != "") {
               $.ajax({
                  url: product_by_barcode,
                  type: 'get',
                  data: {barcode: qrCodeMessage,type:"barcode"},
                  dataType: 'json',
                  success: function(response){
                     if(response.product_variant_details.length > 0) {
                        product_details = response.product_variant_details[0];
                        // product_image = product_details.category_image != "" ? product_details.category_image.split("***") : [];
                        // if(product_image.length > 0)
                        //    $(".product-category-image").attr("src",product_image[0]);
                        // else 
                        $(".product-category-image").attr("src","/assets/placeholder.jpg");
                        $(".barcode-product-name").text(product_details.product_name);
                        $(".barcode-category-name").text(product_details.category_name);
                        $(".product-variant-combination-name").text("").addClass("dnone");
                        $(".barcode-product-details").find(".product-id").val(product_details.product_id);
                        $(".barcode-product-details").find(".type-of-product").val(product_details.type_of_product);
                        quantity = (product_details.type_of_product == "single") ? product_details.unit : product_details.on_hand;
                        $(".barcode-product-details").find(".product-unit").val(quantity);
                        $(".barcode-product-details").find(".trackable").val(product_details.trackable);
                        if(product_details.type_of_product == "single") {
                           $(".product-variants-type-section").addClass("dnone");
                           // $(".product-variant-price").html("SAR "+product_details.price+ " <small>(Incl of Tax)</small>");
                           $(".product-variant-price").html("SAR "+product_details.price);
                        } else {
                           $(".product-variants-type-section").removeClass("dnone");
                           // $(".product-variant-price").html("<h3 class='product-original-price'> SAR "+product_details.variant_price+ " </h3><small>(Incl of Tax)</small>");
                           $(".product-variant-price").html("<h3 class='product-original-price'> SAR "+product_details.variant_price);
                           $(".product-variant-combination-name").text(product_details.variants_combination_name).removeClass("dnone");
                           $(".product-variants-type").val(product_details.variants_combination_name);
                           variants_option = '<option data-quantity="'+product_details.on_hand+'" value="'+product_details.variants_combination_id+'" selected>'+product_details.variants_combination_name+'</option>';
                           $(".product-variants-type").html(variants_option);
                           if(variants_combination_array.length > 0) {
                              $.each(variants_combination_array, function(index, subArray) {
                                 if(subArray != null) {
                                    if(index == product_details.product_id) {
                                       $.each(subArray, function(innerIndex, innerValue) {
                                          if (typeof innerValue === 'object' && innerValue.hasOwnProperty('variants_id')) 
                                             decreaseQuantity($(".product-card-details-"+index),innerValue.quantity);
                                          else 
                                             decreaseQuantity($(".product-category-image"),subArray.quantity);
                                       });
                                    }
                                 }
                              });
                           }
                        }
                        $(".barcode-product-details").removeClass("dnone");
                     }
                  }
               });
            }
         }
         $(document).on("input",".barcode-value",function() {
            barcodeValue = $(this).val();
            $(".barcode-product-details").addClass("dnone");
            getBarcodeProduct(barcodeValue);            
         });

         function barcodeField(scannerType) {
            $(".scanner-field").addClass("dnone");
            if(scannerType == "scanner")
               $(".barcode-scanner").removeClass("dnone");
            else
               $(".barcode-reader").removeClass("dnone");
         }
         $(document).ready(function() {
            $('.scan-barcode').click(function() {
               $('input[name="scanner_type"][value="scanner"]').prop('checked', true);
               barcodeField("scanner");
               $(".barcode-value").val("");
               $(".barcode-product-details").addClass("dnone");
               $('#scan-the-barcode').modal('show');
            });
            $('#scan-the-barcode').on('shown.bs.modal', function() {
               let scanner = new Html5QrcodeScanner(
                  "reader", // The ID of the element to render the scanner
                  { fps: 10, qrbox: 250 } // Optional configurations
               );
               scanner.render(onScanSuccess);
               function onScanSuccess(qrCodeMessage) {
                  $("#html5-qrcode-button-camera-stop").trigger("click");
                  $(".barcode-product-details").addClass("dnone");
                  getBarcodeProduct(qrCodeMessage);
               }
            });
            $(".all-product-details").each(function() {
               product_count = $(this).find(".product-card-details").length;
               $(this).closest(".all-product-data").find(".all-product-count").text("("+product_count+")");
            });
            variants_combination_array = ($(".get-cart-data").val() != "") ? $.parseJSON($(".get-cart-data").val()) : {}; 
            product_ids = ($(".product-ids").val() != "") ? $.parseJSON($(".product-ids").val()) : []; 
            variant_ids = ($(".variant-ids").val() != "") ? $.parseJSON($(".variant-ids").val()) : []; 
            total_cart_quantity = (($(".total-cart-quantity").val() != "") && (!isNaN($(".total-cart-quantity").val()))) ? $(".total-cart-quantity").val() : 0; 
            if(total_cart_quantity > 0)  {
               $(".total-item-count").text(total_cart_quantity).removeClass("dnone");
               $(".clear-cart-btn").removeClass("dnone");
            }
            else {
               $(".clear-cart-btn").addClass("dnone");
               $(".total-item-count").addClass("dnone");
            }     
            $(".select-variants-type").trigger("change");   
            updateQuantity(variants_combination_array);
         });
         $(document).on("change",".scanner-type",function() {
            var scannerType = $('input[name="scanner_type"]:checked').val();
            barcodeField(scannerType);
         }); 
         function updateQuantity(variants_combination_array,type=null) {
            if(Object.keys(variants_combination_array).length > 0) {
               $.each(variants_combination_array, function(index, subArray) {
                  if(subArray != null) {
                     $.each(subArray, function(innerIndex, innerValue) {
                        if (typeof innerValue === 'object' && innerValue.hasOwnProperty('variants_id')) 
                           decreaseQuantity($(".product-card-details-"+index),innerValue.quantity,type);
                        else 
                           decreaseQuantity($(".product-card-details-"+index),subArray.quantity,type);
                     });
                  }
               });
               return true;
            }
         }
         $('.minus').click(function () {
            var input_quantity = $(this).closest(".product-item").find(".quantity");
            quantity = parseFloat(input_quantity.val()) - 1;
            input_quantity.val((quantity > 0) ? quantity : 0);
            input_quantity.change();
            return false;
         });
         $('.plus').click(function () {
            var input_quantity = $(this).closest(".product-item").find(".quantity");
            input_quantity.val(parseFloat(input_quantity.val()) + 1);
            input_quantity.change();
            return false;
         });
         $(document).on("change",".quantity",function() {
            validateQuantity($(this));
         });
         $(document).on("change",".select-variants-type",function() {
            variant_id = $(this).val();
            if(variant_combinations[variant_id]) {
               variation_combination_data = variant_combinations[variant_id];
               variant_price = variation_combination_data.variant_price;
               $(this).closest(".product-card-details").find(".product-price").val(variant_price);
               // $(this).closest(".product-card-details").find(".product-variant-price").html("<h3 class='product-original-price'> SAR "+Number(variant_price).toFixed(2)+ " </h3><small>(Incl of Tax)</small>");
               $(this).closest(".product-card-details").find(".product-variant-price").html("<h3 class='product-original-price'> SAR "+Number(variant_price).toFixed(2)+ " </h3>");
               in_stock = $(this).closest(".product-card-details").find("option:selected", ".select-variants-type").attr('data-quantity');
               if(in_stock == "" || in_stock > 0) {
                  $(this).closest(".product-card-details").find(".quantity").val(1);
                  $(this).closest(".product-card-details").find(".add-to-cart").prop("disabled",false);
               } else if(in_stock <= 0) {
                  $(this).closest(".product-card-details").find(".quantity").val(0);
                  $(this).closest(".product-card-details").find(".add-to-cart").prop("disabled",true);
               }
               validateQuantity($(this));
            } else {
               $(this).closest(".product-card-details").find(".product-variant-price").text("");
               $(this).closest(".product-card-details").find(".product-price").val(0);
            }
         });
         $(document).on("click",".add-to-cart",function(event) {
            event.stopImmediatePropagation();
            _type = $(this).attr("data-type");
            variants = $(this).closest(".product-card-details").find(".select-variants-type").val();
            variants_name = $(this).closest(".product-card-details").find(".select-variants-type option:selected").text();
            quantity = $(this).closest(".product-card-details").find(".quantity").val();
            product_id = $(this).closest(".product-card-details").find(".product-id").val();
            discount_id = $(this).closest(".product-card-details").find(".discount-dropdown option:selected").data('discount-id');
            type_of_product = $(this).closest(".product-card-details").find(".type-of-product").val();
            $(this).closest(".product-card-details").find(".select-variants-type").css("border","1px solid #86a4c3");
            $(this).closest(".product-card-details").find(".quantity").css("border","1px solid #86a4c3");
            error = 0;
            if(type_of_product != "single" && variants != undefined && variants == "") {
               $(this).closest(".product-card-details").find(".select-variants-type").css("border","2px solid #F30000");
               error++;
            }
            if(quantity <= 0) {
               $(this).closest(".product-card-details").find(".quantity").css("border","2px solid #F30000");
               error++;
            }
            if(error > 0)
               return false;
            else {
               total_cart_quantity = parseInt(total_cart_quantity) + parseInt(quantity);
               variant_array = {}; 
               if(variants != undefined) {
                  variant_array[variants] = {};
                  variants_details = {};
                  variants_details.variants_id = variants;
                  variants_details.variants_name = variants_name;
                  variants_details.discount_id = discount_id;
                  variants_details.quantity = quantity;
                  variant_array[variants] = variants_details;
                  if((Object.keys(variants_combination_array).length > 0) && (product_id in variants_combination_array) && (variants_combination_array[product_id] != null)) {
                     if(variants_combination_array[product_id] != null && variants in variants_combination_array[product_id]) {
                        variant_quantity = variants_combination_array[product_id][variants].quantity;
                        total_variant_quantity = parseInt(variant_quantity) + parseInt(quantity);
                        variants_combination_array[product_id][variants].quantity = total_variant_quantity;
                     } else {
                        variants_combination_array[product_id][variants] = variant_array[variants];
                        variant_ids.push(variants);
                     }
                  } else {
                     variants_combination_array[product_id] = variant_array;
                     product_ids.push(product_id);
                     variant_ids.push(variants);
                  }
               } else {
                  if((Object.keys(variants_combination_array).length > 0) && (product_id in variants_combination_array) && (variants_combination_array[product_id] != null)) {
                     variant_quantity = variants_combination_array[product_id].quantity;
                     total_variant_quantity = parseInt(variant_quantity) + parseInt(quantity);
                     variants_combination_array[product_id].quantity = total_variant_quantity;
                  } else {
                     variants_details = {};
                     variants_details.quantity = quantity;
                     variants_details.discount_id = discount_id;
                     variants_combination_array[product_id] = variants_details;
                     product_ids.push(product_id);
                  }
               }
               if(total_cart_quantity > 0) {
                  $(this).closest("body").find(".place-order-cart").find(".total-item-count").text(total_cart_quantity).removeClass("dnone");
                  $(this).closest("body").find(".place-order-cart").find(".clear-cart-btn").removeClass("dnone");
               }  
               else {
                  $(this).closest("body").find(".place-order-cart").find(".total-item-count").addClass("dnone");
                  $(this).closest("body").find(".place-order-cart").find(".clear-cart-btn").addClass("dnone");
               }
               addToCart($(this),'add',quantity,_type,product_id);
               if(_type == "barcode") {
                  $('#scan-the-barcode').modal('hide');
               }
            }
         });  
         $(document).on("click",".clear-cart",function(event) {
            event.stopImmediatePropagation();
            _this = $(this);
            updateQuantity(variants_combination_array,'clear');
            variants_combination_array = {}; product_ids = []; variant_ids = [];  total_cart_quantity = 0;
            addToCart(_this,'clear');
         });

         $(document).on("click",".view-cart",function(event) {
            event.stopImmediatePropagation();
            _this = $(this);
            view_cart_url = _this.closest(".place-order-cart").find(".view-cart-url").val();
            if(Object.keys(variants_combination_array).length > 0) {
               $(location).attr('href',view_cart_url);       
            } else {
               toastr.options =
               {
                  "closeButton" : true,
                  "progressBar" : true
               }
               toastr.error("Your Cart is Empty!");
            }
         });
         function decreaseQuantity(_this,quantity,type=null) {
            type_of_product = _this.closest(".product-card-details").find(".type-of-product").val();
            if(type_of_product == "single")
               product_unit = _this.closest(".product-card-details").find(".product-unit").val(); 
            else if(type_of_product == "variant")
               product_unit = _this.closest(".product-card-details").find("option:selected", ".select-variants-type").attr('data-quantity');
            trackable = _this.closest(".product-card-details").find(".trackable").val(); 
            if((type_of_product == "single" && trackable == 1 || type_of_product == "variant") && product_unit != "" && product_unit != undefined) {   
               if(type == 'clear')           
                  in_stock = product_unit + quantity;
               else
                  in_stock = product_unit - quantity;
               if(type_of_product == "single") {
                  product_unit = _this.closest(".product-card-details").find(".product-unit").val(in_stock); 
               }
               else if(type_of_product == "variant") {
                  product_unit = _this.closest(".product-card-details").find("option:selected", ".select-variants-type").attr('data-quantity',in_stock);
                  if(_this.closest(".product-card-details").find(".select-variants-type").length > 0)
                     _this.closest(".product-card-details").find(".select-variants-type")[0].selectedIndex = 0;
               }
               if(in_stock > 0 && type_of_product == "single") {
                  _this.closest(".product-card-details").find(".quantity").val(1);
                  _this.closest(".product-card-details").find(".add-to-cart").prop("disabled",false);
               } else {
                  if((type_of_product == "single" && in_stock !== "" && in_stock <= 0) || (type_of_product == "variant" && _this.closest(".product-card-details").find("option:selected", ".select-variants-type").attr('data-quantity') != "" && _this.closest(".product-card-details").find("option:selected", ".select-variants-type").attr('data-quantity') <= 0)){
                     _this.closest(".product-card-details").find(".quantity").val(0);
                     _this.closest(".product-card-details").find(".add-to-cart").prop("disabled",true);
                  }
                  if(type_of_product == "variant" && _this.closest(".product-card-details").find("option:selected", ".select-variants-type").attr('data-quantity') > 0) {
                     _this.closest(".product-card-details").find(".quantity").val(1);
                     _this.closest(".product-card-details").find(".add-to-cart").prop("disabled",false);
                  }
               }
            } else {
               _this.closest(".product-card-details").find(".quantity").val(1);
               if(_this.closest(".product-card-details").find(".select-variants-type").length > 0)
                  _this.closest(".product-card-details").find(".select-variants-type")[0].selectedIndex = 0;
            }
         }
         function addToCart(_this = null,_type,quantity = null,type = null,product_id = null) {
            $.ajax({
               data: {_token: CSRF_TOKEN,cart_data: variants_combination_array, product_ids : product_ids, variant_ids : variant_ids, total_cart_quantity : total_cart_quantity},
               url: "{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.place-order.store') }}",
               type: "POST",
               dataType: 'json',
               success: function (response) {
                  if(_type == "add") {
                     toastr.options =
                     {
                        "closeButton" : true,
                        "progressBar" : true
                     }
                     toastr.success(translations.add_cart_msg);
                     decreaseQuantity(_this,quantity);
                     if(type == "barcode") {
                        decreaseQuantity($(".product-card-details-"+product_id),quantity);
                     }
                  } else if(_type == "clear") {
                     $('#confirm-modal').modal('toggle'); 
                     _this.closest("body").find(".total-item-count").addClass("dnone");
                     _this.closest("body").find(".clear-cart-btn").addClass("dnone");
                  }
               },
               error: function (data) {
                  console.log(data);
               }
            });     
         }
         $(document).ready(function() {
            $('.scan-barcode').click(function() {
               $(".barcode-product-details").addClass("dnone");
               $('#scan-the-barcode').modal('show');
            });
            $('#scan-the-barcode').on('shown.bs.modal', function() {
               let scanner = new Html5QrcodeScanner(
                  "reader", // The ID of the element to render the scanner
                  { fps: 10, qrbox: 250 } // Optional configurations
               );
               scanner.render(onScanSuccess);
               function onScanSuccess(qrCodeMessage) {
                  $("#html5-qrcode-button-camera-stop").trigger("click");
                  $(".barcode-product-details").addClass("dnone");
                  product_by_barcode = $(".get-product-by-barcode").val();
                  $.ajax({
                     url: product_by_barcode,
                     type: 'get',
                     data: {barcode: qrCodeMessage,type:"barcode"},
                     dataType: 'json',
                     success: function(response){
                        if(response.product_variant_details.length > 0) {
                           product_details = response.product_variant_details[0];
                           product_image = product_details.category_image != "" ? product_details.category_image.split("***") : [];
                           if(product_image.length > 0)
                              $(".product-category-image").attr("src",product_image[0]);
                           else 
                              $(".product-category-image").attr("src","/assets/placeholder.jpg");
                           $(".barcode-product-name").text(product_details.product_name);
                           $(".barcode-category-name").text(product_details.category_name);
                           $(".product-variant-combination-name").text("").addClass("dnone");
                           $(".barcode-product-details").find(".product-id").val(product_details.product_id);
                           $(".barcode-product-details").find(".type-of-product").val(product_details.type_of_product);
                           quantity = (product_details.type_of_product == "single") ? product_details.unit : product_details.on_hand;
                           $(".barcode-product-details").find(".product-unit").val(quantity);
                           $(".barcode-product-details").find(".trackable").val(product_details.trackable);
                           $(".product-variants-type").html("");
                           if(product_details.type_of_product == "single") {
                              $(".product-variants-type-section").addClass("dnone");
                              // $(".product-variant-price").html("SAR "+product_details.price+ " <small>(Incl of Tax)</small>");
                              $(".product-variant-price").html("SAR "+product_details.price);
                           } else {
                              $(".product-variants-type-section").removeClass("dnone");
                              // $(".product-variant-price").html("<h3 class='product-original-price'> SAR "+product_details.variant_price+ " </h3><small>(Incl of Tax)</small>");
                              $(".product-variant-price").html("<h3 class='product-original-price'> SAR "+product_details.variant_price);
                              $(".product-variant-combination-name").text(product_details.variants_combination_name).removeClass("dnone");
                              $(".product-variants-type").val(product_details.variants_combination_name);
                              variants_option = '<option data-quantity="'+product_details.on_hand+'" value="'+product_details.variants_combination_id+'" selected>'+product_details.variants_combination_name+'</option>';
                              $(".product-variants-type").html(variants_option);
                           }
                           if(variants_combination_array.length > 0) {
                              $.each(variants_combination_array, function(index, subArray) {
                                 if(subArray != null) {
                                    if(index == product_details.product_id) {
                                       $.each(subArray, function(innerIndex, innerValue) {
                                          if (typeof innerValue === 'object' && innerValue.hasOwnProperty('variants_id')) 
                                             decreaseQuantity($(".product-card-details-"+index),innerValue.quantity);
                                          else 
                                             decreaseQuantity($(".product-category-image"),subArray.quantity);
                                       });
                                    }
                                 }
                              });
                           }
                           $(".barcode-product-details").removeClass("dnone");
                        }
                     }
                  });
               }
            });
            $(".product-card-details").each(function() {
               produt_id = $(this).find(".product-id").val();
               type_of_product = $(this).find(".type-of-product").val();
               variant_id = (type_of_product == "variant") ? $(this).find(".select-variants-type").val() : 0;
               product_price = $(this).find(".product-price").val();
               discount_option = '<select class="form-control discount-dropdown"><option value="">Select Discount</option>';
               isDiscountExist = 0;
               if(all_discount_data.length > 0) {
                  $(all_discount_data).each(function(key,val) {
                     if(parseFloat(val.max_discount_value) > 0) {
                        discount_option += '<option data-discount-id="'+val.discount_id+'" data-discount-type="'+val.discount_type+'" value="'+val.max_discount_value+'">'+(val.discount_type == "flat" ? "FLAT "+val.max_discount_value : val.max_discount_value+" %")+'</option>';
                        isDiscountExist++;
                     }
                  });
               }
               if(specific_discount_data.hasOwnProperty(produt_id)) {
                  if(type_of_product == "variant") {
                     if(specific_discount_data[produt_id].hasOwnProperty(variant_id)) {
                        $(specific_discount_data[produt_id][variant_id]).each(function(key,val) {
                           if(parseFloat(val.discount_value) > 0) {
                              isDiscountExist++;
                              discount_option += '<option data-discount-id="'+val.discount_id+'" data-discount-type="'+val.discount_type+'" value="'+val.discount_value+'">'+(val.discount_type == "flat" ? "FLAT "+val.discount_value : val.discount_value+" %")+'</option>';
                           }
                        });
                     }
                  } else {
                     $(specific_discount_data[produt_id][0]).each(function(key,val) {
                        if(parseFloat(val.discount_value) > 0) {
                           isDiscountExist++;
                           discount_option += '<option data-discount-id="'+val.discount_id+'" data-discount-type="'+val.discount_type+'" value="'+val.discount_value+'">'+(val.discount_type == "flat" ? "FLAT "+val.discount_value : val.discount_value+" %")+'</option>';
                        }
                     });
                  }
               }
               discount_option += '</select>';
               if(isDiscountExist > 0) {
                  $(this).find(".product-discount-part").html(discount_option);
                  $(this).find(".product-discount-part .discount-dropdown").find('option').eq(1).prop('selected', true);
                  discount_value = $(this).find(".discount-dropdown").val();
                  discount_type = $(this).find(".discount-dropdown option:selected").data('discount-type');
                  showDiscount(discount_type,discount_value,product_price,$(this));
               }
            });
         });
         $(document).on("change",".discount-dropdown",function() {
            discount_value = $(this).val();
            _this = $(this).closest(".product-card-details");
            discount_type = _this.find(".discount-dropdown option:selected").data('discount-type');
            product_price = _this.find(".product-price").val();
            if(discount_value > 0)
               showDiscount(discount_type,discount_value,product_price,_this);
            else {
               priceElement  = _this.find(".product-original-price del");
               priceElement.contents().unwrap();
               _this.find(".discount-price").remove();
            }
         });
         function showDiscount(discount_type,discount_value,product_price,_this) {
            discount_amount = 0;
            if(discount_type == "flat") {
               discount_amount = parseFloat(product_price) - parseFloat(discount_value);
            } else if(discount_type == "percent") {
               discount_amount = parseFloat(product_price) - parseFloat(product_price * discount_value / 100);
            }
            if(discount_amount > 0) {
               _this.find(".discount-price").remove();
               priceElement  = _this.find(".product-original-price");
               priceElement.wrapInner("<del></del>");
               priceElement.after("<h3 class='discount-price'>"+"&nbsp;&nbsp;SAR "+discount_amount.toFixed(2)+"</h3>&nbsp;&nbsp;");
            }
         }
      </script>
   </body>
</html>
