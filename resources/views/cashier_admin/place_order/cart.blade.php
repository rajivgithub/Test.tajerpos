<!DOCTYPE html>
<html lang="en">
    <head>
        @include('common.cashier_admin.header')
        <style>
            #msform fieldset:not(:first-of-type) {
                display: none;
            }
            @media print {
                .progressbar-wrapper,.print-order-invoice,.main-footer {
                    display: none !important; 
                }
            }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css">
    </head>
    <body class="hold-transition light-skin sidebar-mini theme-danger fixed">
        <div class="wrapper">
            @include('common.cashier_admin.navbar')
            @include('common.cashier_admin.sidebar')
            <div class="content-wrapper" >
                <div class="container-full">
                    <section class="content ">
                        <div class="progressbar-wrapper mt-4 mb-4">
                            <ul class="progressbar">
                                <li class="active"><i class="fa fa-cart-plus"></i></li>
                                <li><i class="fa fa-spinner"></i></li>
                                <li><i class="fa fa-money"></i></li>
                                <li><i class="fa fa-file-text"></i></li>
                            </ul>
                        </div>
                        <form  method="POST" id="msform" action="{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.store') }}">
                        @csrf
                            <input type="hidden" class="variant-combinations" value="{{!empty($variant_combination_data) ? json_encode($variant_combination_data) : '' }}">  
                            <fieldset class="fieldset-1">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h4 class="mb-0">Shopping Cart</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table my-cart-tab" id="card-details-table">
                                                <thead>
                                                    <tr>
                                                        <th>Items</th>
                                                        <th>Variants</th>
                                                        <th scope="col">Unit price</th>
                                                        <th scope="col">Tax</th>
                                                        <th scope="col">Qty.</th>
                                                        <th scope="col">Total</th>
                                                        <th scope="col"></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="card-details-tbody">
                                                    @if(isset($product_details) && !empty($product_details) && count($product_details) > 0)
                                                        @foreach ($product_details as $key => $product)
                                                            @if(!empty($variant_combinations) && array_key_exists($product->product_id,$variant_combinations))
                                                                @for($i = 0; $i < ($quantity[$product->product_id]);$i++)
                                                                    <tr class="product-cart-list">
                                                                        <td>
                                                                            <div class="d-flex align-items-center">
                                                                                <input type="hidden" class="product-name" value="{{ $product->product_name }}">
                                                                                <img src="{{ $product->category_image }}" class="img-fluid me-2" alt=""> {{ $product->product_name }}
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            @if(!empty($variant_combinations) && array_key_exists($product->product_id,$variant_combinations))
                                                                                <select class="form-control select-variants" name="variants_item[{{$product->category_id}}][{{$product->product_id}}][]" data-live-search="true">
                                                                                    <option value="">Select</option>
                                                                                    @foreach($variant_combinations[$product->product_id] as $variants)
                                                                                        <option value="{{$variants['variants_combination_id']}}">{{$variants['variants_combination_name']}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @endif
                                                                        </td>
                                                                        <!-- <td class="single-product-price">SAR {{ number_format((float)($product->price), 2, '.', '') }}</td>  -->
                                                                        <td class="single-product-price">SAR 0.00</td>
                                                                        <td class="single-product-tax">&nbsp;</td>
                                                                        <td>
                                                                            <div class="number product-item">
                                                                                <span class="minus">-</span>
                                                                                <input type="hidden" class="product-price" value="{{ $product->price }}">
                                                                                <input type="hidden" class="product-unit-price" value="{{ $product->price }}">
                                                                                <input type="hidden" class="product-quantity" value="1">
                                                                                <input type="hidden" class="product-id" value="{{ $product->product_id }}">
                                                                                <input type="hidden" class="tax-type" value="{{ $product->tax_type }}"> 
                                                                                <input type="hidden" class="tax-amount" value="{{ $product->tax_amount }}">
                                                                                <input type="text" name="product_item[{{$product->category_id}}][{{$product->product_id}}]" value="1" class="quantity" onkeypress="return isNumber(event)">
                                                                                <span class="plus">+</span>
                                                                            </div>
                                                                        </td>
                                                                        <input type="hidden" name="product_amount[{{$product->category_id}}][{{$product->product_id}}]" class="total-product-price" value="{{ 1 * $product->price }}">
                                                                        <input type="hidden" name="no_of_products" class="no-of-products" value="">
                                                                        <td class="product-item-amount">SAR 0.00</td>
                                                                        <td class="text-center"><i class="fa fa-trash delete-product-item"></i></td>
                                                                    </tr>
                                                                @endfor
                                                            @else
                                                                <tr class="product-cart-list">
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <input type="hidden" class="product-name" value="{{ $product->product_name }}">
                                                                            <img src="{{ $product->category_image }}" class="img-fluid me-2" alt=""> {{ $product->product_name }}
                                                                        </div>
                                                                    </td>
                                                                    <td>--</td>
                                                                    <td class="single-product-price">SAR {{ number_format((float)($product->price), 2, '.', '') }}</td>
                                                                    <td class="single-product-tax">&nbsp;</td> 
                                                                    <td>
                                                                        <div class="number product-item">
                                                                            <span class="minus">-</span>
                                                                            <input type="hidden" class="product-price" value="{{ $product->price }}">
                                                                            <input type="hidden" class="product-unit-price" value="{{ $product->price }}">
                                                                            <input type="hidden" class="product-quantity" value="{{ $quantity[$product->product_id] }}">
                                                                            <input type="hidden" class="product-id" value="{{ $product->product_id }}">
                                                                            <input type="hidden" class="tax-type" value="{{ $product->tax_type }}"> 
                                                                            <input type="hidden" class="tax-amount" value="{{ $product->tax_amount }}">
                                                                            <input type="text" name="product_item[{{$product->category_id}}][{{$product->product_id}}]" value="{{ $quantity[$product->product_id] }}" class="quantity" onkeypress="return isNumber(event)">
                                                                            <span class="plus">+</span>
                                                                        </div>
                                                                    </td>
                                                                    <input type="hidden" name="product_amount[{{$product->category_id}}][{{$product->product_id}}]" class="total-product-price" value="{{ $quantity[$product->product_id] * $product->price }}">
                                                                    <input type="hidden" name="no_of_products" class="no-of-products" value="">
                                                                    <td class="product-item-amount">SAR {{ number_format((float)($quantity[$product->product_id] * $product->price), 2, '.', '') }}</td>
                                                                    <td class="text-center"><i class="fa fa-trash delete-product-item"></i></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <tr><td colspan="6" class="text-center">Your cart is empty..!</td></tr>
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr class="action-row">
                                                        <td colspan="6">
                                                            <div class="d-flex align-items-center justify-content-between"> 
                                                                <a href="{{ url(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/place-order/index/cart')  }}" class="add-more-items">+ Add more items</a> 
                                                                <a href="{{ url(config('app.prefix_url').'/'.$store_url.'/'.config('app.module_prefix_url').'/place-order/index/cart')  }}" class="add-more-items">+ Add more items</a> 
                                                                <a href="#0" class="remove-all-item">Remove All</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <div class="order-total-dt mb-4">
                                            <div class="order-total-left-text fsz-18">
                                                Total Amount With Tax
                                            </div>
                                            <div class="order-total-right-text fsz-18">
                                                <input type="hidden" name="total_cart_amount" class="total_cart_amount" value="">
                                                <h3 class="total-cart-amount">SAR 0.00</h3>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="text-right">
                                            <a href="#"><button type="button" class="btn btn-primary btn-sm rounded next action-button add-items-to-cart">Next</button></a>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="fieldset-2">
                                <div class="row">
                                    <div class="col-md-8 offset-md-2">
                                        <div class="card mb-4 mt-45 ">
                                            <div class="card-header">
                                                <h4 class="mb-0">Which do you prefer?</h4>
                                            </div>
                                            <div class="card-body p-4">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6 d-flex">
                                                        <div class="form-group form-check pl-0">
                                                            <input type="radio" class="form-check-input order-type" name="pickup" value="Dine-In" id="exampleCheck1" checked>
                                                            <label class="form-check-label" for="exampleCheck1"><b>Dine-In</b></label>
                                                        </div>
                                                        <div class="form-group form-check pl-0">
                                                            <input type="radio" class="form-check-input order-type" name="pickup" value="Packing" id="exampleCheck2">
                                                            <label class="form-check-label" for="exampleCheck2"><b>Packing</b></label>
                                                        </div>
                                                        <!-- <?php 
                                                            $address = '';
                                                            if(isset($address_details) && !empty($address_details)) {
                                                                if(!empty($address_details[0]->store_address))
                                                                    $address .= $address_details[0]->store_address.',';
                                                                if(!empty($address_details[0]->city_name))
                                                                    $address .= $address_details[0]->city_name.',';
                                                                if(!empty($address_details[0]->state_name))
                                                                    $address .= $address_details[0]->state_name.',';
                                                                if(!empty($address_details[0]->country_name))
                                                                    $address .= $address_details[0]->country_name;
                                                            }
                                                        ?>
                                                        <h4>{{$address}}</h4>
                                                        <p >Time: 10 minutes</p> -->
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <h3 class="total-cart-amount">SAR 0.00</h3>
                                                        <span>(Incl of all tax)</span>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="text-right">
                                                    <a href="#"><button type="button" class="btn btn-danger btn-sm rounded previous action-button-previous">Previous</button></a>
                                                    <a href="#"><button type="button" class="btn btn-primary btn-sm rounded next action-button add-items-to-cart">Next</button></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset> 
                            <fieldset class="fieldset-3">
                                <div class="row">
                                    <div class="col-md-8 offset-md-2">
                                        <div class="card mb-4 mt-45 ">
                                            <div class="card-header">
                                                <h4 >Payment Method</h4>
                                            </div>
                                            <div class="card-body p-4">
                                                <div class="row align-items-center">
                                                    <div class="col-md-7">
                                                        <p><b>PAYMENT</b></p>
                                                        <div class="d-flex">
                                                            <div class="form-group form-check pl-0">
                                                                <input type="radio" class="form-check-input" id="exampleCheck3" name="payment_method" checked value="cash">
                                                                <label class="form-check-label" for="exampleCheck3"><b>Cash</b></label>
                                                            </div>
                                                            <!-- <div class="form-group form-check pl-0">
                                                                <input type="radio" class="form-check-input" id="exampleCheck4" name="payment_method" value="online">
                                                                <label class="form-check-label" for="exampleCheck4"><b>Online</b></label>
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5 text-right">
                                                        <h3 class="total-cart-amount">SAR 0.00</h3>
                                                        <span>(Incl of all tax)</span>
                                                    </div>
                                                </div>
                                                <hr/>
                                                <div class="text-right">
                                                    <a href="#"><button type="button" class="btn btn-danger btn-sm rounded previous action-button-previous">Previous</button></a>
                                                    <a href="#" data-toggle="modal" data-target="#myModal"><button type="button" class="btn btn-primary btn-sm rounded next action-button payment-method" data-type="payment-method-btn">Next</button></a>                                               
                                                    <!-- <a href="#" data-toggle="modal" data-target="#myModal"><button type="button" class="btn btn-primary btn-sm rounded next action-button payment-method" data-type="payment-method-btn">Next</button></a>                                                -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="fieldset-4">
                                <div class="row">
                                    <div class="col-md-6 offset-md-3">
                                        <div class="card mb-4 mt-45">
                                            <div class="card-body  p-4">
                                                <div id="invoice-POS"> 
                                                    <center>
                                                        <img src="{{ URL::asset('assets/cashier-admin/images/logo.png') }}" class="logo" alt="eMonta">
                                                    </center>
                                                    <hr/>
                                                    <div id="mid">
                                                        <div class="info">
                                                            <div class="row justify-content-between">
                                                                <div class="col-md-8">
                                                                    <h6>{{ isset($address_details) && !empty($address_details) && !empty($address_details[0]['store_name']) ? $address_details[0]['store_name'] : 'eMonta' }}</h6>
                                                                    <p>{{$address}}</p>
                                                                    <p>VAT #: 12345678</p>
                                                                    <p>Welcome</p>
                                                                    <p class="mb-0">Simplified tax invoice</p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <p>Receipt #: 1-1012</p>
                                                                    <p class="billing-date"></p>
                                                                    <p>Cashier: {{ Auth::user()->name }}</p>
                                                                    <p>POS: POS 1</p>
                                                                    <p class="order-type-name"></p>
                                                                </div>
                                                                <br/>
                                                            </div>
                                                        </div>
                                                        <hr class="pos-invc">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="col-3 p-0">
                                                                <p class="mb-0">Items</p>
                                                            </div>
                                                            <div class="col-3 p-0">
                                                                <p class="mb-0">Variants</p>
                                                            </div>
                                                            <div class="col-2 p-0 text-center">
                                                                <p class="mb-0">Quantity x Price</p>
                                                            </div>
                                                            <div class="col-2 p-0 text-center">
                                                                <p class="mb-0">Tax</p>
                                                            </div>
                                                            <div class="col-2 p-0 text-right">
                                                                <p class="mb-0">Total Amount</p>
                                                            </div>
                                                        </div>
                                                        <hr class="pos-invc">
                                                        <div class="billing-items"> 
                                                        </div>
                                                        <hr class="pos-invc">
                                                        <div class="d-flex justify-content-between">
                                                            <div><p class="mb-0"><b>Sub Total</b></p></div>
                                                            <div><p class="mb-0"><b class="cart-sub-total"></b></p></div>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <div><p class="mb-0"><b>Tax</b></p></div>
                                                            <div><p class="mb-0"><b class="cart-total-tax"></b></p></div>
                                                        </div>
                                                        <hr class="pos-invc">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <h4 style="font-size:17px;color:#000;">Total with tax</h4>
                                                            </div>
                                                            <div>
                                                                <h4 style="font-size:17px;color:#000;" class="total-cart-amount"></h4>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-between order-cash">
                                                            <div>
                                                                <p class="mb-0">Cash</p>
                                                            </div>
                                                            <div>
                                                                <p class="mb-0 order-cash-amount">0.00</p>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-between order-change">
                                                            <div>
                                                                <p class="mb-0">Change</p>
                                                            </div>
                                                            <div>
                                                                <p class="mb-0 order-change-amount">0.00</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr class="pos-invc">
                                                    <p class="text-center">Thank's for your choice</p>
                                                    <center>
                                                        <img src="{{ URL::asset('assets/cashier-admin/images/QR_code.png') }}" >
                                                    </center>
                                                    <hr/>
                                                    <div class="text-right">
                                                        <a href="#" ><button type="button" class="btn btn-primary btn-sm rounded print-order-invoice" >Print</button></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="modal" id="myModal">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div id="money-1">
                                                <div class="modal-header mb-4">
                                                    <h4 class="modal-title">Will you need change?</h4>
                                                    <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div>
                                                    <a href="#" id="money-11">
                                                        <div class="change d-flex justify-content-between align-items-center change-popup-field" data-type="change-popup">
                                                            <p class="mb-0"><i class="fa fa-check-circle-o text-success fs-5 mr-2"></i> Yes, I will need change</p>
                                                            <i class="fa fa-angle-double-right"></i>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div>
                                                    <a href="#" class="next payment-action-button view-billing" data-type="change-popup">
                                                        <div class="change d-flex justify-content-between align-items-center">
                                                            <p class="mb-0"><i class="fa fa-times-circle-o text-primary fs-5 mr-2"></i> No, I have the exact amount</p>
                                                            <i class="fa fa-angle-double-right"></i>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div id="money-2">
                                                <div class="modal-header mb-4">
                                                    <h4 class="modal-title">How Much change?</h4>
                                                    <!-- <button type="button" class="close btn-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
                                                </div>
                                                <div class="text-center">
                                                    <p class="text-center">ORDER'S AMOUNT: <b style="color:#67bb1b" class="total-cart-amount">SAR 0.00</b></p>
                                                    <br/>
                                                    <div class="row">
                                                        <div class="col-md-4">&nbsp;</div>
                                                        <div class="col-md-4">
                                                            <h1>SAR <input type="text" placeholder="Type here" name="cash_in_hand" value = "" class="form-control cash-in-hand"></h1>
                                                        </div>
                                                        <div class="col-md-4">&nbsp;</div>
                                                    </div> 
                                                    <p class="text-center">Enter the amount you have in hand.</p>
                                                    <br/>
                                                    <p class="text-center">CHANGE: <b style="color:#67bb1b" class="balance-amount">SAR 0.00</b></p>
                                                </div>
                                                <hr/>
                                                <div class="text-right">
                                                    <button type="button" class="btn btn-primary btn-sm rounded next action-button balance-button view-billing" disabled data-type="change-popup" >Next</button>
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
        </div>
        @include('common.cashier_admin.copyright')
        @include('common.cashier_admin.footer')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.minus').click(function () {
                    var input_quantity = $(this).closest(".product-item").find(".quantity");
                    quantity = parseFloat(input_quantity.val()) - 1;
                    input_quantity.val((quantity > 0) ? quantity : 1);
                    input_quantity.change();
                    return false;
                });
                $('.plus').click(function () {
                    var input_quantity = $(this).closest(".product-item").find(".quantity");
                    input_quantity.val(parseFloat(input_quantity.val()) + 1);
                    input_quantity.change();
                    return false;
                });
                total_price = 0; 
                $("#card-details-table").find(".total-product-price").each(function() {
                    variants = $(this).closest("tr").find(".select-variants").val();
                    if(variants == undefined || variants != "") {
                        price = $(this).val();
                        tax_type = $(this).closest("tr").find(".tax-type").val();
                        tax_amount = $(this).closest("tr").find(".tax-amount").val();
                        quantity = $(this).closest("tr").find(".product-quantity").val();
                        if(tax_type == "flat" && tax_amount != "") 
                            tax_amount = quantity * parseFloat(tax_amount);
                        else if(tax_type == "percent") 
                            tax_amount = price * (tax_amount / 100);
                        total_tax_amount = (tax_amount != "") ? parseFloat(tax_amount) : 0;
                        tax_amount = (tax_amount != "") ? parseFloat(tax_amount) : 0;
                        total_amount = parseFloat(price)+parseFloat(total_tax_amount);
                        $(this).closest("tr").find(".single-product-tax").text(tax_amount.toFixed(2));
                        $(this).closest("tr").find(".product-item-amount").text("SAR "+total_amount.toFixed(2));
                        total_price = total_price+parseFloat(price)+parseFloat(total_tax_amount); 
                    }
                });
                $(".total-cart-amount").text("SAR "+total_price.toFixed(2));
                $(".total_cart_amount").val(total_price.toFixed(2));
                variant_combinations = $(".variant-combinations").val();
                if(variant_combinations != "") 
                    variant_combinations = $.parseJSON(variant_combinations);
            });
            $("#money-1").show();
            $("#money-2").hide();
            $("#money-11").click(function(){
                $("#money-1").hide();
                $("#money-2").show();
            });

            $(document).on("change",".select-variants",function() {
                variant_id = $(this).val();
                if(variant_combinations[variant_id]) {
                    variation_combination_data = variant_combinations[variant_id];
                    variant_price = variation_combination_data.variant_price != "" ? variation_combination_data.variant_price : $(this).closest("tr").find(".product-unit-price").val();
                    $(this).closest("tr").find(".single-product-price").text("SAR "+Number(variant_price).toFixed(2));
                    $(this).closest("tr").find(".product-price").val(variant_price); 
                    quantity = $(this).closest("tr").find(".product-quantity").val();
                    total_product_price = quantity * variant_price;
                    $(this).closest("tr").find(".total-product-price").val(total_product_price);
                    total_price = 0;
                    $(this).closest("#card-details-table").find(".total-product-price").each(function() {
                        variants = $(this).closest("tr").find(".select-variants").val();
                        if(variants == undefined || variants != "") {
                            price = $(this).val();
                            tax_type = $(this).closest("tr").find(".tax-type").val();
                            tax_amount = $(this).closest("tr").find(".tax-amount").val();
                            product_quantity = $(this).closest("tr").find(".product-quantity").val();
                            if(tax_type == "flat" && tax_amount != "") 
                                tax_amount = product_quantity * parseFloat(tax_amount);
                            else if(tax_type == "percent") 
                                tax_amount = price * (tax_amount / 100);
                            total_tax_amount = (tax_amount != "") ? parseFloat(tax_amount) : 0;
                            tax_amount = (tax_amount != "") ? parseFloat(tax_amount) : 0
                            $(this).closest("tr").find(".single-product-tax").text(tax_amount.toFixed(2)); 
                            total_amount = parseFloat(price)+parseFloat(total_tax_amount);
                            $(this).closest("tr").find(".product-item-amount").text("SAR "+ total_amount.toFixed(2));
                            total_price = total_price+parseFloat(price)+parseFloat(total_tax_amount);
                        } else if(variants == "") {
                            $(this).closest("tr").find(".single-product-tax").text("0.00"); 
                            $(this).closest("tr").find(".product-item-amount").text("SAR "+ "0.00");
                        }
                    });
                    $(this).closest("form").find(".total-cart-amount").text("SAR "+total_price.toFixed(2));
                    $(this).closest("form").find(".total_cart_amount").val(total_price.toFixed(2));
                }
            });
            $(document).ready(function(){
                var current_fs, next_fs, previous_fs; 
                var opacity;
                var current = 1;
                var steps = $("fieldset").length;
                $(".next").click(function(){
                    _type = $(this).attr("data-type");
                    error = 0;
                    $(this).closest("form").find(".card-details-tbody").find(".select-variants").css("border","1px solid #86a4c3");
                    if($(this).hasClass("add-items-to-cart")) {
                        $(this).closest("form").find(".card-details-tbody").find(".select-variants").each(function() {
                            if($(this).val() == "") {
                                $(this).css("border","2px solid #F30000");
                                error++;
                            }
                        });
                    }
                    if(error > 0)
                        return false;
                    else {
                        if(_type != "payment-method-btn") {
                            if(_type == "change-popup") {
                                $("#money-1").find(".btn-close").click();
                                current_fs = $(".fieldset-3");
                                next_fs = $(".fieldset-3").next();
                            } else {
                                current_fs = $(this).closest("fieldset");
                                next_fs = $(this).closest("fieldset").next();
                            }
                            $(".progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
                            next_fs.show(); 
                            current_fs.animate({opacity: 0}, {
                                step: function(now) {
                                    opacity = 1 - now;
                                    current_fs.css({
                                        'display': 'none',
                                        'position': 'relative'
                                    });
                                    next_fs.css({'opacity': opacity});
                                }, 
                                duration: 500
                            });
                        }
                    }
                });
                $(".previous").click(function(){
                    current_fs = $(this).closest("fieldset");
                    previous_fs = $(this).closest("fieldset").prev();
                    $(".progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");
                    previous_fs.show();
                    current_fs.animate({opacity: 0}, {
                        step: function(now) {
                            opacity = 1 - now;
                            current_fs.css({
                                'display': 'none',
                                'position': 'relative'
                            });
                            previous_fs.css({'opacity': opacity});
                        }, 
                        duration: 500
                    });
                }); 
            });
            hideShowButton();
            function hideShowButton() {
                if($("#card-details-table").find(".card-details-tbody").find(".product-cart-list").length > 0) 
                    $(".add-items-to-cart").css("display","inline-flex");
                else
                    $(".add-items-to-cart").css("display","none");
            }
            $(document).on("click",".add-more-items",function() {
                product_id = [];product_quantity = [];
                $(this).closest("table").find(".card-details-tbody tr").each(function() {
                    product_id.push($(this).find(".product-id").val());
                    product_quantity.push($(this).find(".product-quantity").val());
                });
                sessionStorage.setItem("product_id", product_id);
                sessionStorage.setItem("product_quantity", product_quantity);
            });
            $(document).on("change",".quantity",function() {
                total_amount = 0;
                $(".quantity").each(function() {
                    variants = $(this).closest("tr").find(".select-variants").val();
                    if(variants == undefined || variants != "") {
                        quantity = $(this).val();
                        $(this).closest("tr").find(".product-quantity").val(quantity);
                        item_price = $(this).closest(".product-item").find(".product-price").val();
                        quantity_price = quantity * item_price;
                        tax_type = $(this).closest("tr").find(".tax-type").val();
                        tax_amount = $(this).closest("tr").find(".tax-amount").val();
                        if(tax_type == "flat" && tax_amount != "") 
                            tax_amount = quantity * parseFloat(tax_amount);
                        else if(tax_type == "percent") 
                            tax_amount = quantity_price * (tax_amount / 100);
                        total_tax_amount = (tax_amount != "") ? parseFloat(tax_amount) : 0;
                        tax_amount = (tax_amount != "") ? parseFloat(tax_amount) : 0;  
                        $(this).closest("tr").find(".single-product-tax").text(tax_amount.toFixed(2));
                        total_amount = parseFloat(total_amount) + parseFloat(quantity_price)+parseFloat(total_tax_amount);
                        total_price = parseFloat(quantity_price)+parseFloat(total_tax_amount);
                        $(this).closest("tr").find(".product-item-amount").text("SAR "+total_price.toFixed(2));
                        $(this).closest("tr").find(".total-product-price").val(quantity_price.toFixed(2));
                    } else if(variants == "") {
                        $(this).closest("tr").find(".single-product-tax").text("0.00"); 
                        $(this).closest("tr").find(".product-item-amount").text("SAR "+ "0.00");
                    }
                });
                $(".total-cart-amount").text("SAR "+total_amount.toFixed(2));
                $(".total_cart_amount").val(total_amount.toFixed(2));
            });
            $(document).on("click",".delete-product-item",function(event) {
                event.stopImmediatePropagation();
                $(this).closest("tr").remove();
                $(".quantity").change();
                hideShowButton();
            });
            $(document).on("click",".remove-all-item",function(event) {
                event.stopImmediatePropagation();
                $(this).closest("table").find("tbody").find("tr").remove();
                $(this).closest("table").find("tbody").html('').html('<tr><td colspan="6" class="text-center">Your cart is empty..!</td></tr>');
                $(this).closest("form").find(".total-cart-amount").text("SAR 0.00");
                $(this).closest("form").find(".total_cart_amount").val("0.00");
                hideShowButton();
            });
            $(document).on("change",".cash-in-hand",function() {
                total_cart_amount = $(this).closest("form").find(".total_cart_amount").val();
                cash_in_hand = $(this).val();
                balance_amount = 0;
                if(parseInt(total_cart_amount) <= parseInt(cash_in_hand)) {
                    balance_amount = parseInt(cash_in_hand) - parseInt(total_cart_amount);
                    $(this).closest("form").find(".balance-button").prop("disabled",false);
                } else {
                    $(this).closest("form").find(".balance-button").prop("disabled",true);
                }
                $(this).closest("form").find(".balance-amount").text("SAR "+balance_amount.toFixed(2));
            }); 
            $(document).on("click",".view-billing",function(e) {
                e.preventDefault();
                billing_tr = ''; tax_amount_tr = ''; total_tax_amount = 0; cart_tax_amount = 0; total_product_cart_price = 0;
                $(this).closest("form").find("#card-details-table").find("tbody tr").each(function() {
                    quantity = $(this).find(".product-quantity").val();
                    product_name = $(this).find(".product-name").val();
                    variants = ($(this).find(".select-variants").val() != "") ? ($(this).find(".select-variants option:selected").text()) : "";
                    product_price =  $(this).find(".product-price").val();
                    total_product_price = $(this).find(".total-product-price").val();
                    total_cart_price = $(this).closest("form").find(".total_cart_amount").val();                    
                    tax_type = $(this).find(".tax-type").val(); 
                    tax_amount = $(this).find(".tax-amount").val();
                    tax_percentage = "";
                    if(tax_type == "flat" && tax_amount != "") {
                        product_price = parseFloat(product_price);
                        tax_amount = quantity * parseFloat(tax_amount);
                        tax = parseFloat(tax_amount);
                        tax_percentage =  ((tax / product_price) * 100).toFixed(2)+"%";
                    } else if(tax_type == "percent") {
                        tax_percentage = tax_amount+"%";
                        tax_amount = total_product_price * (tax_amount / 100);
                    }       
                    if(tax_amount != "")  
                        total_tax_amount += parseFloat(tax_amount); ;
                    tax_amount = (tax_amount != "") ? tax_amount : 0;
                    total_product_price = (parseFloat(total_product_price) + parseFloat(tax_amount)).toFixed(2); 
                    total_product_cart_price = (parseFloat(total_product_cart_price) + parseFloat(quantity * product_price)).toFixed(2);
                    cart_tax_amount = (parseFloat(cart_tax_amount) + parseFloat(tax_amount)).toFixed(2);
                    billing_tr += '<div class="d-flex justify-content-between"><div class="col-3 p-0"><p class="mb-0">'+product_name+'</p></div><div class="col-3 p-0"><p class="mb-0">'+variants+'</p></div><div class="col-2 p-0 text-center"><p class="mb-0">'+quantity+' x '+product_price+'</p></div><div class="col-2 p-0 text-center"><p class="mb-0">'+tax_amount.toFixed(2)+'</p></div><div class="col-2 p-0 text-right"><p class="mb-0">'+total_product_price+'</p></div></div></div>';
                });
                total_amount = parseFloat(total_cart_price).toFixed(2);
                $(this).closest("form").find(".billing-items").html(billing_tr);
                $(this).closest("form").find(".no-of-products").val($("#card-details-table").find(".card-details-tbody").find(".product-cart-list").length);
                if($(this).closest("form").find(".balance-amount").text() == "SAR 0.00") 
                    $(this).closest("form").find(".order-cash-amount").text(total_amount);
                else 
                    $(this).closest("form").find(".order-cash-amount").text($(this).closest("form").find(".cash-in-hand").val());  
                $(this).closest("form").find(".order-change-amount").text($(this).closest("form").find(".balance-amount").text());
                var currentdate = new Date(); 
                var datetime = currentdate.getDate() + "/"
                            + (currentdate.getMonth()+1)  + "/" 
                            + currentdate.getFullYear() + " "  
                            + currentdate.getHours() + ":"  
                            + currentdate.getMinutes();
                $(this).closest("form").find(".billing-date").text("Date: " +datetime);
                order_type = $(this).closest("form").find('input[name="pickup"]:checked').val();
                $(this).closest("form").find(".order-type-name").text("Order: " +order_type);
                $(this).closest("form").find(".cart-sub-total").text("SAR "+total_product_cart_price);
                $(this).closest("form").find(".cart-total-tax").text("SAR "+cart_tax_amount);
                $.ajax({
                    data: $('#msform').serialize(),
                    url: "{{ route(config('app.prefix_url').'.'.$store_url.'.'.config('app.module_prefix_url').'.store-order.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (response) {
                        toastr.options =
                        {
                            "closeButton" : true,
                            "progressBar" : true
                        }
                        toastr.success(response.message);
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            });  
            $(document).on("click",".print-order-invoice",function() {
                window.print(); 
            });
        </script>
    </body>
</html>