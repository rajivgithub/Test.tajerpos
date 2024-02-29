$(document).ready(function() {
    cart_product_count();
    get_store_details();
    if($(".page-name").val() != "home_page")
        $(".page-loader").hide();
});
var outOfStockTitle = $('<div/>').text(customerTranslations['out_of_stock']).html();
var addToCart = $('<div/>').text(customerTranslations['add_to_cart']).html();
var updateCart = $('<div/>').text(customerTranslations['update']).html();
var addToWishlist = $('<div/>').text(customerTranslations['add_to_wishlist']).html();

function get_store_details() {
    store_details_url = $(".store-details-url").val();
    $.ajax({
        url: store_details_url,
        type: 'get',
        success: function(response){
            if (response.hasOwnProperty('store_details') && Array.isArray(response.store_details)) {
                var store_details = response.store_details;
                if (store_details.length > 0) {
                    $(".customer-store-phone-number").text(store_details[0].store_phone_number);
                    $(".customer-store-email-id").text(store_details[0].email);
                    $(".customer-store-phone-number").attr("href","tel:" + store_details[0].store_phone_number);
                    $(".customer-store-email-id").attr("href","mailto:" + store_details[0].email);
                    $(".logo-in-customer").attr("src",store_details[0].store_logo);
                    var translationKey = $(".translation-key").val();
                    if(translationKey == "single_product_page_title") {
                        single_product_name = $(".single-product-name").val();
                        var title_tag_content = customerTranslations[translationKey].replace(':company', store_details[0].store_name).replace(':product_name', single_product_name);
                    } else
                        var title_tag_content = customerTranslations[translationKey].replace(':company', store_details[0].store_name);
                    $(".title-content").text(title_tag_content); 
                    var currentYear = new Date().getFullYear();
                    $(".copyright-content").text(customerTranslations['copyrights'].replace(':company', store_details[0].store_name).replace(':year', currentYear));
                    $(".company-name").text(store_details[0].store_name);
                    var address = '';
                    if (store_details[0].store_address)
                        address += store_details[0].store_address + ',';
                    if (store_details[0].city_name)
                        address += store_details[0].city_name + ',';
                    if (store_details[0].state_name)
                        address += store_details[0].state_name + ',';
                    if (store_details[0].country_name)
                        address += store_details[0].country_name;
                    $(".customer-store-address").text(address);
                }
            }
        }
    });
}

function cart_product_count() {
    get_product_count_url = $(".get-product-count-url").val();
    $.ajax({
        url: get_product_count_url,
        type: 'get',
        success: function(response){
            cart_total_quantity = response.cart_total_quantity;
            $(".shopping_cart").find(".shopping-cart-count").removeClass("item_count");
            $(".shopping_cart").find(".shopping-cart-count").text("");
            $(".shopping-bag-details").text(customerTranslations['my_bag']);
            if(cart_total_quantity != null && cart_total_quantity > 0) {
                $(".shopping_cart").find(".shopping-cart-count").addClass("item_count");
                $(".shopping_cart").find(".item_count").text(cart_total_quantity); 
                $(".shopping-bag-details").text(customerTranslations['my_bag']+" ("+cart_total_quantity+" "+customerTranslations['items']+")");
            } else {
                $(".shopping_cart").find(".item_count").text("");
                $(".shopping-bag-details").text(customerTranslations['my_bag']);
            }
        }
    });
}

function showWishlist(_this,product_type,product_id,variants_id,_type = '') {
    wishlist_url = $(".wishlist-url").val();
    $.ajax({
        url: wishlist_url,
        type: 'post',
        data: {_token: CSRF_TOKEN,product_type: product_type,product_id: product_id, variants_id : variants_id},
        success: function(response){
            if(response.wishlist > 0) {
                if(_type == "products-in-home" || _type == "home") 
                    _this.find(".product-wishlist").html('<i data-wishlist-type="remove" data-type="'+_type+'" class="wishlist-icon fas fa-heart"></i>');
                else {
                    if(_type == "view_product" || _type == "product-in-popup")
                        selector = _this.closest("body").find(".product-modal-popup").find(".product-wishlist");
                    else if(_type == "single-product")
                        selector = _this.find(".product-wishlist");
                    else
                        selector = _this.closest("body").find(".product-wishlist");
                    selector.html('<i data-wishlist-type="remove" class="wishlist-icon fas fa-heart"></i> ');
                }
            }   
            else {
                if(_type == "home" || _type == "products-in-home") {
                    _this.find(".product-wishlist").html('<i data-wishlist-type="add" data-type="'+_type+'" class="wishlist-icon far fa-heart"></i>');
                }
                else {
                    if(_type == "view_product" || _type == "product-in-popup")
                        selector = _this.closest("body").find(".product-modal-popup").find(".product-wishlist");
                    else if(_type == "single-product")
                        selector = _this.find(".product-wishlist");
                    else
                        selector = _this.closest("body").find(".product-wishlist");
                    selector.html('<i data-wishlist-type="add" class="wishlist-icon far fa-heart"></i> ');
                }
            }
        }
    });
}

function saveWishlistData(product_id, _type, wishlist_id) {
    let wishlistData = {
        product_type: product_type,
        product_id: product_id,
        _type: _type,
        wishlist_id: wishlist_id
    };
    localStorage.setItem('wishlistData', JSON.stringify(wishlistData));
}

function addWishlist(product_id,_type, wishlist_id = '',page) {
    var isAuthenticated = $(".is-authenticated").val();
    if (isAuthenticated == 'false') {
        let data = {
            product_id: product_id,
            _type: _type,
            wishlist_id: wishlist_id
        };
        $.ajax({
            url: $(".save-wishlist-url").val(),
            type: 'post',
            data: {_token: CSRF_TOKEN,currentURL: window.location.href,wishlistData: data},
            success: function(response){
                if (response.redirectURL) {
                    window.location.href = response.redirectURL;
                } else {
                    console.log(response);
                }
            }
        });
    } else {
        $.ajax({
            url: $(".add-wishlist-url").val(),
            type: 'post',
            data: {_token: CSRF_TOKEN,product_id: product_id,_type : _type, wishlist_id : wishlist_id},
            success: function(response){
                if(page != "wishlist") {
                    toastr.options =
                    {
                        "closeButton" : true,
                        "progressBar" : true
                    }
                    if(response.type == "success") 
                        toastr.success(response.message);
                    else
                        toastr.error(response.message);
                    if(page == "wishlist_popup") {
                        showWishlistProducts($(".wishlist-details"));
                    }
                }
            }
        });
    }
}

$(document).on("click",".product-wishlist",function(e) {
    e.stopImmediatePropagation();
    var _type = $(this).find(".wishlist-icon").attr('data-wishlist-type');
    $(this).closest(".single-product-details").find(".error-variant-title").css("color","#212529"); 
    var wishlist_type = $(this).find(".wishlist-icon").attr('data-type');
    product_type = $(this).closest(".single-product-details").find(".single-product-type").val();
    product_id = $(this).closest(".single-product-details").find(".single-product-id").val();
    _page = $(this).attr("data-page");
    if(_type == "add") {
        if(wishlist_type == "home")
            $(this).html('<i data-wishlist-type="remove" data-type="'+wishlist_type+'" class="wishlist-icon fas fa-heart"></i>');
        else
            $(this).html('<i data-wishlist-type="remove" class="wishlist-icon fas fa-heart"></i> ');
        $(this).closest("body").find(".single-product-details-"+product_id).find(".product-wishlist").html('<i data-wishlist-type="remove" data-type="'+wishlist_type+'" class="wishlist-icon fas fa-heart"></i>');
    }
    else {
        if(wishlist_type == "home")
            $(this).html('<i data-wishlist-type="add" data-type="'+wishlist_type+'" class="wishlist-icon far fa-heart"></i>');
        else 
            $(this).html('<i data-wishlist-type="add" class="wishlist-icon far fa-heart"></i> ');
        $(this).closest("body").find(".single-product-details-"+product_id).find(".product-wishlist").html('<i data-wishlist-type="add" data-type="'+wishlist_type+'" class="wishlist-icon far fa-heart"></i>');
    }
    addWishlist(product_id,_type,'',_page);
});

$(document).on("click",".product-view",function() {
    product_type = $(this).attr("data-product-type"); 
    product_name = $(this).closest(".single_product").find(".product-name").text();
    product_price = $(this).closest(".single_product").find(".product-price").text(); 
    product_id = $(this).closest(".single_product").find(".product-id").val();
    product_trackable = $(this).closest(".single_product").find(".product-trackable").val();
    modal_product_unit = $(this).closest(".single_product").find(".product-unit").val();
    product_category_images = $(this).closest(".single_product").find(".product-category-images").val();
    product_url = $(this).closest(".single_product").find(".single-product-url").attr("href");
    variants_id = $(this).closest(".single_product").find('.single-product-variants-combination').val(); 
    product_description =  $(this).closest(".single_product").find(".single-product-description").val();
    $(this).closest("body").find(".product-modal-popup").find(".modal-product-name").text(product_name);
    $(this).closest("body").find(".product-modal-popup").find(".modal-product-price").text(product_price);
    $(this).closest("body").find(".product-modal-popup").find(".single-product-id").val(product_id);
    $(this).closest("body").find(".product-modal-popup").find(".single-product-type").val(product_type);
    $(this).closest("body").find(".product-modal-popup").find(".single-product-trackable").val(product_trackable);
    $(this).closest("body").find(".product-modal-popup").find(".product-unit").val(modal_product_unit);
    // $(this).closest("body").find(".product-modal-popup").find(".modal-product-unit").val();
    $(this).closest("body").find(".product-modal-popup").find(".add-product-quantity").val(1);
    // $(this).closest("body").find(".product-modal-popup").find(".single-product-variants-combination").val("");
    $(this).closest("body").find(".product-modal-popup").find(".single-product-variants-combination").val(variants_id);
    product_category_images =  (product_category_images != "") ? product_category_images.split('***') : [];
    modal_product_images = "";
    if(product_category_images.length > 0) {
        modal_product_images = "<div class='tab-content product-details-large'>";
        $(product_category_images).each(function(key,val) {
            active_class = (key == 0) ? "show active" : "";
            modal_product_images += '<div class="tab-pane fade '+active_class+'" id="tab'+key+'" role="tabpanel"><div class="modal_tab_img"><a href="'+product_url+'"><img src="'+val+'" alt=""></a></div></div>';
        });
        modal_product_images += "</div>";

        modal_product_images += '<div class="modal_tab_button"><ul class="nav product_navactive owl-carousel" role="tablist">';
        $(product_category_images).each(function(key,val) {
            active_class = (key == 0) ? " active" : "";
            modal_product_images += '<li><a class="nav-link '+active_class+'" data-toggle="tab" href="#tab'+key+'" role="tab" aria-controls="tab'+key+'" aria-selected="false"><img src="'+val+'" alt=""></a></li>';
        });
        modal_product_images += "</ul></div>";
    }
    $(this).closest("body").find(".product-modal-popup").find(".product-images-modal-tab").html(modal_product_images);
    // Destroy the existing Owl Carousel instance
    $('.product_navactive').trigger('destroy.owl.carousel');
    $productCarousel = $('.product_navactive');
    $productCarousel.owlCarousel({
        loop: false,
        nav: true,
        autoplay: false,
        autoplayTimeout: 8000,
        items: 4,
        dots: false,
        mouseDrag: false,
        navText: [
            '<i class="ion-chevron-left"></i>',
            '<i class="ion-chevron-right"></i>',
        ],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            250: {
                items: 2,
            },
            480: {
                items: 3,
            },
            768: {
                items: 4,
            },
        },
    });
    var itemCount = $productCarousel.find('.owl-item').length;
    if (itemCount <= 4) {
        $productCarousel.find('.owl-nav').hide();
    }
    $productCarousel.on('changed.owl.carousel', function(event) {
        var currentItemCount = event.item.count;
        // Hide or show the navigation arrows based on the current item count
        if (currentItemCount <= 4) {
            $productCarousel.find('.owl-nav').hide();
        } else {
            $productCarousel.find('.owl-nav').show();
        }
    });
    $(this).closest("body").find(".product-modal-popup").find(".modal-product-url").attr("href",product_url);    
    $(this).closest("body").find(".product-modal-popup").find(".modal-product-description").html(product_description);
    $(this).closest("body").find(".product-variants-data").html("");
    if(product_type == "variant") {                  
        variant_combinations_data = $(this).closest(".single_product").find(".variant-combinations").val();
        variant_combination_element = "";
        if(variant_combinations_data != "") {
            variant_combinations = $.parseJSON(variant_combinations_data);
            variant_combination_element = '<ul class="list-variants">';
            if(variants_id != "")
                firstKey = variants_id;
            else {
                var keys = Object.keys(variant_combinations);
                var firstKey = keys[0];
            }
            $.each(variant_combinations, function(key, value) {
                checked = (variants_id == value.variants_combination_id) ? "checked" : ""; 
                variant_combination_element += '<li class="product-variant-dev"><input type="radio" class="btn-check product-variant" data-type="product-in-popup" name="product_variant_combination_'+value.product_id+'" id="product-variant-'+value.variants_combination_id+'" value="'+value.variants_combination_id+'"  data-value="'+value.variants_combination_name+'" '+checked+'><label class="btn btn-outline-secondary avatar-xs-1 rounded-4 d-flex product-variant-label product-variant-'+value.variants_combination_id+'" for="product-variant-'+value.variants_combination_id+'">'+value.variants_combination_name+'</label></li>';
            });
            variant_combination_element += '</ul>';
        }
        $(this).closest("body").find(".product-variants-data").html(variant_combination_element);
        variantCombination($(this),'view_product');
    }
    if(product_type == "single") {
        updateQuantity(product_type,product_id,'',$(this).closest("body").find(".product-modal-popup"),'','view_product');
    }
    is_authenticated = $(this).closest("body").find(".is_authenticated").val();
    if(is_authenticated == 1)
        showWishlist($(this),product_type,product_id,$(this).closest("body").find(".product-modal-popup").find(".single-product-variants-combination").val(),'view_product');
    $('#modal_box').modal('show');
});

$(document).on("change keyup",".add-product-quantity",function() {
    product_type = $(this).closest(".single-product-details").find(".single-product-type").val();
    product_id = $(this).closest(".single-product-details").find(".single-product-id").val();
    _type = $(this).closest(".single-product-details").find(".product-variant:checked").data("type");
    if(product_type == "variant") {
        variantCombination($(this),_type);
    } else {
        product_unit = $(".variant-combinations-"+product_id).closest(".single_product").find(".product-unit").val();
        updateQuantity(product_type,product_id,'',$(this).closest(".single_product"),parseFloat(product_unit),_type);
    }
});

function updateQuantity(product_type,product_id,variant_combination_id = '',_this,product_unit = '',_type = '',is_add_to_cart = '') {
    get_product_quantity_url = $(".get-product-quantity-url").val();
    product_quantity = parseFloat(_this.find(".add-product-quantity").val());
    product_trackable = _this.find(".single-product-trackable").val();
    pageName = _this.closest("body").find(".page-type").val();
    on_hand_product = ""; available_quantity = 0;
    $.ajax({
        url: get_product_quantity_url,
        type: 'post',
        async : false,
        data: {_token: CSRF_TOKEN,product_type: product_type,product_id: product_id,variant_combination_id: variant_combination_id},
        success: function(response){
            quantity = parseFloat(response.quantity);
            selector = _this;
            selector.find(".error-message").text("");
            if(product_type == "single") {
                product_unit = parseFloat(selector.find(".product-unit").val());
                available_quantity = (pageName == "view-cart") ? product_unit : product_unit - quantity;
                selector.find(".modal-product-unit").val(available_quantity);
            } else {
                on_hand_product = parseFloat(selector.find(".variant-on-hand").val());
                if(on_hand_product != "" || on_hand_product == 0) {
                    available_quantity = (pageName == "view-cart") ? on_hand_product : on_hand_product - quantity;
                    selector.find(".modal-variant-on-hand").val(available_quantity);
                }
            }
            if(((product_type == "single" && product_trackable == 1) || (product_type == "variant" && (on_hand_product != "" || on_hand_product == 0))) && (product_quantity > available_quantity)) {
                if(_type == "wishlist") {
                    selector.find(".product-add-to-cart").html('<a href="#" title="'+outOfStockTitle+'"><span class="pe-7s-close-circle"></span></a>');
                    selector.find(".product-add-to-cart").prop("disabled",true); 
                } else {
                    if(product_type == "variant" && pageName == "single-product" && is_add_to_cart == "add_to_cart") {
                        variantsByProduct(product_id,selector,pageName);
                    }
                    error_message = "";
                    if(is_add_to_cart == "add_to_cart" || available_quantity <= 0) {
                        selector.find(".add-product-quantity").val(1);
                        selector.find(".product-add-to-cart").text(outOfStockTitle);
                        selector.find(".product-add-to-cart").prop("disabled",true);
                        if(available_quantity <= 0 && is_add_to_cart != "add_to_cart")
                            error_message = customerTranslations['product_not_available'];
                    } else {
                        selector.find(".add-product-quantity").val(available_quantity); 
                        error_message = customerTranslations['quantity_exceeds_stock'].replace(":unit",available_quantity);
                    }
                    if(error_message != "") {
                        var errorMessageElement = selector.find(".error-message");
                        errorMessageElement.text(error_message).css("color", "#F30000");
                        if (errorMessageElement.data("timeoutId")) {
                            clearTimeout(errorMessageElement.data("timeoutId"));
                        }
                        errorMessageElement.show();
                        errorMessageElement.data("timeoutId", setTimeout(function() {
                            errorMessageElement.fadeOut();
                        }, 5000));
                    }
                }
            } else {
                if(_type == "wishlist") {
                    selector.find(".product-add-to-cart").html('<a href="#add-to-cart" title="'+addToCart+'"><span class="pe-7s-shopbag"></span></a>');
                    selector.find(".product-add-to-cart").prop("disabled",false);
                } else {
                    addToCartText = ($(".page-type").val() == 'view-cart') ? updateCart : addToCart;
                    selector.find(".product-add-to-cart").text(addToCartText);
                    if($(".page-type").val() == 'view-cart') 
                        selector.find(".product-add-to-cart").removeClass("add-to-cart").addClass("cart-update");
                    else
                        selector.find(".product-add-to-cart").removeClass("cart-update").addClass("add-to-cart");
                    selector.find(".product-add-to-cart").prop("disabled",false);
                }
            }
        }
    });
}


$(document).on("click",".add-to-cart",function(e) {
    e.stopImmediatePropagation();
    _this = $(this);
    _this.closest(".single-product-details").find(".error-variant-title").css("color","#212529"); 
    _type = _this.data("type");
    if(_type == "wishlist") 
        $(this).html('<a href="#add-to-cart" title="'+addToCart+'"><span class="pe-7s-shopbag"></span></a>');
    else 
        $(this).text(addToCart);
    product_type = $(this).closest(".single-product-details").find(".single-product-type").val();
    productId = $(this).closest(".single-product-details").find(".single-product-id").val();
    quantity = $(this).closest(".single-product-details").find(".quantity").val();
    product_variants_combination = $(this).closest(".single-product-details").find(".single-product-variants-combination").val();
    add_to_cart_url = $(".add-to-cart-url").val();
    on_hand_product = (product_type == "variant") ? $(this).closest(".single-product-details").find(".modal-variant-on-hand").val() : $(this).closest(".single-product-details").find(".modal-product-unit").val();                
    product_trackable = $(this).closest(".single-product-details").find(".single-product-trackable").val();
    if(((product_type == "single" && product_trackable == 1) || (product_type == "variant" && (on_hand_product != "" || on_hand_product == 0))) && (parseInt(quantity) > parseInt(on_hand_product))) {
        if(_type == "wishlist") 
            $(this).html('<a href="#" title="'+outOfStockTitle+'"><span class="pe-7s-close-circle"></span></a>');
        else {
            $(this).text(outOfStockTitle); 
        }
        return false;
    } else {
        $.ajax({
            url: add_to_cart_url,
            type: 'POST',
            data: {_token: CSRF_TOKEN,product_id: productId,quantity: quantity,product_variants_combination:product_variants_combination},
            success: function (response) {
                if(response.success != undefined) {
                    toastr.options =
                    {
                        "closeButton" : true,
                        "progressBar" : true
                    }
                    toastr.success(customerTranslations['product_added_to_cart_success']);
                    $(this).closest(".single-product-details").find(".quantity").val(1);
                    cart_product_count();
                    updateQuantity(product_type,productId,product_variants_combination,_this.closest(".single-product-details"),'',_type,'add_to_cart');
                    _page = _this.closest("body").find(".page-name").val();
                    if(_type == "product-in-popup" && (_page == "home_page" || _page == "category_product")) {
                        category_id = _this.closest("body").find(".pagination").find("li.current").attr("data-category-id");
                        pagination_page_no = _this.closest("body").find(".pagination").find("li.current").attr("data-page-no");
                        showProducts(category_id,$(".product-list-by-category"),pagination_page_no);
                    }
                    $('#modal_box').modal('hide');
                }
                if(response.error != undefined) {
                    toastr.options =
                    {
                        "closeButton" : true,
                        "progressBar" : true
                    }
                    toastr.error(customerTranslations['product_not_found_error']);
                }
            },
            error: function (xhr, status, error) {
                // Handle the error, such as displaying an error message
                console.log('Error adding product to cart:', error);
            }
        });       
    }
});

$(document).on("click",".product-images-btn",function(event) {
    event.preventDefault();
    product_img_path = $(this).find(".product-img").attr("src");
    $(this).closest(".single_product").find(".product-image-path").attr("src",product_img_path);
});  

function getSelectedVariants(_this) {
    var selectedVariants = [];
    _this.closest(".single-product-details").find('.selected-variant-id').each(function() {
        var value = $(this).val();
        selectedVariants.push(value);
    });
    variantsCombination = Object.values(selectedVariants).join(' / ');
    return variantsCombination;
}

$(document).on("click",".product-variant",function(event) {
    event.preventDefault();
    _type = $(this).attr("data-type");
    _page = $(this).data("page");
    _this = $(this);
    $(this).closest(".variants-group").find(".selected-variant-id").val(_this.val());
    if(_type == "product-in-popup" || _type == "single-product") { 
        $(this).closest(".variants-group").find(".product-variant-label").css({"background-color":"#fff","color":"#000","border-color":"#000"});
        $(this).closest(".product-variant-dev").find(".product-variant-label").css({"background-color":"#000","color":"#fff","border-color":"#000"});
    } else {
        $(this).closest(".variants-group").find(".product-variant-label").css({"background-color":"#ffffff","color":"#6c757d","border-color":"#6c757d"});
        $(this).closest(".product-variant-dev").find(".product-variant-label").css({"background-color":"#6c757d","color":"#fff","border-color":"#6c757d"});
    }
    $(this).closest(".single-product-details").find(".error-variant-title").css("color","#212529"); 
    product_id = $(this).closest(".single-product-details").find(".product-id").val();
    variant_combinations_data = $(this).closest("body").find(".variant-combinations-"+product_id).val();
    product_type = $(this).closest(".single-product-details").find(".single-product-type").val();
    variants_id = getSelectedVariants(_this);
    $(this).closest(".single-product-details").find(".single-product-variants-combination").val(variants_id);
    if(_page == "cart") {
        cartUpdateQuantity(_this,variant_combinations_data);
    } else {
        $(".page-loader").show();
        variantCombination($(this),_type);
        is_authenticated = $(this).closest("body").find(".is_authenticated").val();
        if(is_authenticated == 1)
            showWishlist($(this).closest(".single-product-details"),product_type,product_id,$(this).closest(".single-product-details").find(".single-product-variants-combination").val(),_type);
        $(".page-loader").hide();
    }
});

$(document).on('click', '.variant-option-img', function() {
    var radioBtn = $(this).closest('.product-variant-dev').find('.product-variant');
    radioBtn.prop('checked', true);
    $(this).closest(".variants-group").find(".selected-variant-id").val(radioBtn.val());
    radioBtn.trigger('click');
});

function cartUpdateQuantity(_this,variant_combinations_data) {
    quantity = _this.closest(".single-product-details").find(".quantity").val();
    variant_combinations_data = $.parseJSON(variant_combinations_data);
    variants_id = _this.closest(".single-product-details").find(".single-product-variants-combination").val();
    variant_combination = _this.closest(".single-product-details").find("#variant-combination-"+variants_id).closest(".product-variant-dev").find(".product-variant-label").text();
    $(variant_combinations_data).each(function(key,val) {
        if(val['variants_combination_name'] == variant_combination) {
            _this.closest(".single-product-details").find(".product-price").val(val['variant_price']);
            _this.closest(".single-product-details").find(".variants-quantity").val(val['on_hand']);
            if(val['on_hand'] != "" && $.isNumeric(val['on_hand']) && (parseInt(quantity) > parseInt(val['on_hand']))) {
                _this.closest(".single-product-details").find(".cart-update").text(outOfStockTitle);
                _this.closest(".single-product-details").find(".cart-update").prop("disabled",true);
            } else {
                _this.closest(".single-product-details").find(".cart-update").text("Update");
                _this.closest(".single-product-details").find(".cart-update").prop("disabled",false);
            }
        }
    });
}
var single_product_main_img = single_product_btn_img = null;
function singleProductPageImage() {
     // Slick Slider Activation
    single_product_main_img = $('.product_gallery_main_img').slick({
        centerMode: true,
        centerPadding: '0',
        slidesToShow: 1,
        arrows: true,
        vertical: true,
        asNavFor: '.product_gallery_btn_img',
		 prevArrow:
            '<button class="prev_arrow"><span class="ion-arrow-left-b"></span> </button>',
        nextArrow:
            '<button class="next_arrow"><span class="ion-arrow-right-b"></span></button>',
    });

    // Slick Slider2 Activation
    single_product_btn_img = $('.product_gallery_btn_img').slick({
        centerMode: true,
        centerPadding: '0',
        slidesToShow: 5,
        arrows: true,
        focusOnSelect: true,
        vertical: true,
        asNavFor: '.product_gallery_main_img',
        prevArrow:
            '<button class="prev_arrow"><span class="ti-angle-left"></span> </button>',
        nextArrow:
            '<button class="next_arrow"><span class="ti-angle-right"></span></button>',
        responsive: [
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 4,
                    vertical: false,
                    dots: true,
                    arrows: false,
                },
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 3,
                },
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 3,
                },
            },
        ],
    });
}

function showProductImage(_this,type = '') {
    var variant_combination_id = 0;
    product_type = _this.closest(".single_product").find(".single-product-type").val();
    if(product_type == "variant") {
        productImages = _this.closest("body").find(".variant-combinations-images-"+product_id).val();
        var variant_combination_id = _this.closest(".single_product").find(".single-product-variants-combination").val();
    } else {
        productImages = _this.closest(".single_product").find(".product-category-images").val();
    }
    productImagesHTML = "";
    if(productImages != "") {
        allProductImages = $.parseJSON(productImages);
        if (typeof allProductImages != undefined && variant_combination_id in allProductImages && product_type == "variant") {
            allProductImages = allProductImages[variant_combination_id];
        }
        if (allProductImages) {
            if (allProductImages.length > 0) {
                if(type == 'single-product') {
                    if(allProductImages.length > 1) {
                        productImagesHTML += `<div class="product_gallery_btn_img">`;
                        allProductImages.forEach(function(images, index) {
                            productImagesHTML += `<a class="gallery_btn_img_list" href="javascript:void(0)"><img src="${images.image_path}" alt="tab-thumb"></a>`;
                        });
                        productImagesHTML += `</div>`;
                    }
                    productImagesHTML += `<div class="product_gallery_main_img">`;
                    allProductImages.forEach(function(images, index) {
                        productImagesHTML += `<div class="gallery_img_list"><img data-image="${images.image_path}" src="${images.image_path}" alt=""></div>`;
                    });
                    productImagesHTML += `</div>`;
                    _this.closest("body").find(".product-images").html(productImagesHTML);
                    if (single_product_main_img) {
                        single_product_main_img.slick('unslick');
                    }
                    if (single_product_btn_img) {
                        single_product_btn_img.slick('unslick');
                    }
                    singleProductPageImage();
                } else {
                    if(type == "product-in-popup")
                        var productURL = _this.closest("body").find(".product-modal-popup").find(".modal-product-url").attr("href");
                    else
                        var productURL = _this.closest(".single_product").find(".single-product-url").attr("href");
                    productImagesHTML += "<div class='tab-content product-details-large'>";
                
                    allProductImages.forEach(function(images, index) {
                        image_path = (product_type == 'variant') ? images.image_path : images;
                        var active_class = (index === 0) ? "show active" : "";
                        productImagesHTML += `<div class="tab-pane fade ${active_class}" id="tab${index}" role="tabpanel"><div class="modal_tab_img"><a href="${productURL}"><img src="${image_path}" alt=""></a></div></div>`;
                    });
                
                    productImagesHTML += "</div>";
                    if (allProductImages.length > 1) {
                        productImagesHTML += '<div class="modal_tab_button"><ul class="nav product_navactive owl-carousel" role="tablist">';
                    
                        allProductImages.forEach(function(images, index) {
                            var active_class = (index === 0) ? " active" : "";
                            image_path = (product_type == 'variant') ? images.image_path : images;
                            productImagesHTML += `<li><a class="nav-link ${active_class}" data-toggle="tab" href="#tab${index}" role="tab" aria-controls="tab${index}" aria-selected="false"><img src="${image_path}" alt=""></a></li>`;
                        });
                    
                        productImagesHTML += "</ul></div>";
                    }
                    _this.closest("body").find(".product-modal-popup").find(".product-images-modal-tab").html(productImagesHTML);
                    initializeImgCarousel(true);
                }
            }
        } 
    }
    return true;
}

function variantCombination(_this,type='') {
    product_id = _this.closest(".single_product").find(".single-product-id").val();
    product_type = _this.closest(".single_product").find(".single-product-type").val();
    var variant_combination_id = _this.closest(".single_product").find(".single-product-variants-combination").val();
    variant_combinations_data =  _this.closest("body").find(".variant-combinations-"+product_id).val();
    if(type == "products-in-home" || type == "single-product")
        selector = _this.closest(".single_product");
    else
        selector = _this.closest("body").find(".product-modal-popup"); 
    selector.find(".variants-group").each(function() {
        variantOptionTitle = $(this).data("variant-name");
        variantOptionName = $(this).find(".product-variant:checked").data("variant-option-name"); 
        $(this).prev().text(variantOptionTitle+" : "+variantOptionName);
    });
    if(variant_combinations_data != "") {
        variant_combinations_data = $.parseJSON(variant_combinations_data);
        if(variant_combinations_data.hasOwnProperty(variant_combination_id)) {
            product_price = variant_combinations_data[variant_combination_id]['variant_price'];
            product_price = (product_price > 0) ? parseFloat(product_price).toFixed(2) : product_price;
            on_hand_product = variant_combinations_data[variant_combination_id]['on_hand'];
            selector.find(".modal-product-price").text("SAR "+product_price);
            selector.find(".product-price").val(product_price);
            selector.find(".single-product-variants-combination").val(variant_combinations_data[variant_combination_id]['variants_id']);
            selector.find(".modal-variant-on-hand").val(on_hand_product);
            selector.find(".variant-on-hand").val(on_hand_product); 
            selector.find(".product-variant-label").removeClass("out-of-stock");
            firstSelectedVariantID = selector.find(".variants-group:first").find(".selected-variant-id:first").val();
            if (!$.isEmptyObject(variant_combinations_data)) {
                checkProductAvailable(selector,variant_combinations_data,variant_combination_id,firstSelectedVariantID);
            }
            showProductImage(_this,type);
            updateQuantity(product_type,product_id,variant_combinations_data[variant_combination_id]['variants_id'],selector,'',type);
        } else {
            selector.find(".product-add-to-cart").text(outOfStockTitle);
            selector.find(".product-add-to-cart").prop("disabled",true);
        }
    }
}

function checkProductAvailable(selector,variant_combinations_data,variant_combination_id,firstSelectedVariantID = '') {
    splitSelectedVariant = variant_combination_id != "" ? variant_combination_id.split(' / ') : [];
    if(firstSelectedVariantID == "" && splitSelectedVariant.length > 0) 
        firstSelectedVariantID = splitSelectedVariant[0];
    var selectedVariantObject = [];
    $.each(variant_combinations_data, function(key, value) {
        variantKey = (key != "") ? key.split(' / ') : [];
        if (variantKey.includes(firstSelectedVariantID) && selectedVariantObject.indexOf(value.variants_id) === -1) {
            selectedVariantObject[key] = value;
        }
    });
    selector.find(".list-variants .variant-combination").each(function() {
        selectedVariantID = $(this).val();
        productAvailable = false;
        $.each(variant_combinations_data, function(key, value) {
            variantKey = (key != "") ? key.split(' / ') : [];
            if (variantKey.includes(selectedVariantID) && value['product_available'] != "out-of-stock") {
                productAvailable = true;
                return false;
            } 
        });
        if(!productAvailable) {
            selector.find(".variant-combination-"+selectedVariantID).addClass("out-of-stock");
        }
    });
    if(splitSelectedVariant.length > 1 && Object.keys(selectedVariantObject).length > 0) {
        for (const key in selectedVariantObject) {
            let selectedVariantKey = (key !== "") ? key.split(' / ') : [];
            if (selectedVariantKey.length > 1) {
                selectedVariantKey.shift();
                if(selectedVariantKey.length == 1 && key.includes(selectedVariantKey[0]) && selectedVariantObject[key]['product_available'] == "out-of-stock") {
                    selector.find(".variant-combination-"+selectedVariantKey[0]).addClass("out-of-stock");
                } else if(selectedVariantKey.length > 1) {
                    for(i = 0; i<selectedVariantKey.length;i++) {
                        let isProductExist = false;
                        for (const variant in selectedVariantObject) {
                            if (selectedVariantObject.hasOwnProperty(variant)) {
                                if ((variant.includes(selectedVariantKey[i]) && selectedVariantObject[variant]['product_available'] !== "out-of-stock") && (selectedVariantObject[variant]['product_available'] == "" || (selectedVariantObject[variant]['product_available'] == 'out-of-stock' && !splitSelectedVariant.includes(selectedVariantKey[i])))) {
                                    isProductExist = true;
                                    break;  
                                }
                            }
                        }
                        if(!isProductExist)
                            selector.find(".variant-combination-"+selectedVariantKey[i]).addClass("out-of-stock");
                    }
                }
            }
        }
    }
}

function initializeImgCarousel(destroy = false) {
    var $productNavactive = $('.product_navactive');
    if(destroy)
        $productNavactive.trigger('destroy.owl.carousel');
    if ($productNavactive.length > 0) {
        $('.product_navactive').owlCarousel({
            loop: false,
            nav: true,
            autoplay: true,
            autoplayTimeout: 8000,
            items: 4,
            dots: false,
            mouseDrag: false,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                },
                250: {
                    items: 2,
                },
                480: {
                    items: 3,
                },
                768: {
                    items: 4,
                },
            },
            navText: [
                '<i class="ion-chevron-left"></i>',
                '<i class="ion-chevron-right"></i>',
            ],
            onInitialized: checkNavVisibility, 
            onResized: checkNavVisibility,
        });
    }
}
function checkNavVisibility(event) {
    var itemCount = event.item.count;

    // Show/hide navigation arrows based on the number of items
    if (itemCount <= 4) {
        $('.product_navactive').find('.owl-nav').hide();
    } else {
        $('.product_navactive').find('.owl-nav').show();
    }
}

$(document).on("click",".product-quick-view",function() {
    _this = $(this);
    $(".page-loader").show();
    _this.closest("body").find(".product-modal-popup").find(".variants-product-features").empty();
    page_type = $(this).attr("data-page-type");
    product_id = $(this).closest(".single_product").find(".single-product-id").val();
    product_type = $(this).closest(".single_product").find(".single-product-type").val();
    product_name = $(this).closest(".single_product").find(".product-name").text();
    product_price = $(this).closest(".single_product").find(".product-price").text(); 
    product_url = $(this).closest(".single_product").find(".single-product-url").attr("href");
    product_description =  $(this).closest(".single_product").find(".single-product-description").val();
    product_unit = $(this).closest(".single_product").find(".product-unit").val();
    modal_product_unit = $(this).closest(".single_product").find(".modal-product-unit").val();
    product_trackable = $(this).closest(".single_product").find(".single-product-trackable").val();
    category_name = $(this).closest(".single_product").find(".product-category-name").val();                
    sub_category_name = $(this).closest(".single_product").find(".product-subcategory-name").val();
    if(page_type == "view-cart") {
        element = $(this).closest(".product-cart-list").data("element");
        $(this).closest("body").find(".product-modal-popup").find(".selected-element-class").val(element);
        productQuantity = $(this).closest(".single_product").find(".quantity").val();
        $(this).closest("body").find(".product-modal-popup").find(".quantity").val(productQuantity);
        $(this).closest("body").find(".product-modal-popup").find(".product-wishlist").addClass("dnone");
        $(this).closest("body").find(".product-modal-popup").find(".product-add-to-cart").addClass("no-margin-left");
    } else {
        $(this).closest("body").find(".product-modal-popup").find(".add-product-quantity").val(1);
        $(this).closest("body").find(".product-modal-popup").find(".product-wishlist").removeClass("dnone").html('<i data-wishlist-type="add" class="wishlist-icon far fa-heart"></i>');
        $(this).closest("body").find(".product-modal-popup").find(".product-add-to-cart").removeClass("no-margin-left");
    }
    $(this).closest("body").find(".product-modal-popup").find(".modal-product-name").text(product_name);
    $(this).closest("body").find(".product-modal-popup").find(".modal-product-price").text(product_price);
    $(this).closest("body").find(".product-modal-popup").find(".product-price").val(product_price);
    $(this).closest("body").find(".product-modal-popup").find(".modal-product-url").attr("href",product_url);    
    $(this).closest("body").find(".product-modal-popup").find(".single-product-id").val(product_id);
    $(this).closest("body").find(".product-modal-popup").find(".modal-product-unit").val(modal_product_unit);
    $(this).closest("body").find(".product-modal-popup").find(".product-unit").val(product_unit);
    if(product_description != "")
        $(this).closest("body").find(".product-modal-popup").find(".modal-product-description").removeClass("dnone");
    else 
        $(this).closest("body").find(".product-modal-popup").find(".modal-product-description").addClass("dnone");
    $(this).closest("body").find(".product-modal-popup").find(".modal-product-description").html(product_description);
    $(this).closest("body").find(".product-modal-popup").find(".single-product-trackable").val(product_trackable);
    $(this).closest("body").find(".product-modal-popup").find(".single-product-type").val(product_type);
    $(this).closest("body").find(".product-modal-popup").find(".category-name").text(category_name);
    $(this).closest("body").find(".product-modal-popup").find(".sub-category-name").text(sub_category_name);
    if(product_type == "single") {
        showProductImage($(this),'product-in-popup');
        $(this).closest("body").find(".product-modal-popup").find(".single-product-variants-combination").val("");
    }
    $(this).closest("body").find(".product-variants-data").html("");
    if(product_type == "variant") {
        checked_variants_id = (page_type != "wishlist") ? $(this).closest(".single_product").find('.single-product-variants-combination').val() : "";
        selectedVariant = (checked_variants_id != "") ? checked_variants_id.split(' / ') : [];
        variantsByProduct(product_id,_this,'',selectedVariant);
    } else if(product_type == "single") {
        if(modal_product_unit <= 0 && product_trackable == 1) {
            _this.closest("body").find(".product-modal-popup").find(".product-add-to-cart").text(outOfStockTitle);
            _this.closest("body").find(".product-modal-popup").find(".product-add-to-cart").prop("disabled",true);
        } else {
            addToCartText = ($(".page-type").val() == 'view-cart') ? updateCart : addToCart;
            if($(".page-type").val() == 'view-cart') 
                _this.closest("body").find(".product-modal-popup").find(".product-add-to-cart").removeClass("add-to-cart").addClass("cart-update");
            else
                _this.closest("body").find(".product-modal-popup").find(".product-add-to-cart").removeClass("cart-update").addClass("add-to-cart");
            _this.closest("body").find(".product-modal-popup").find(".product-add-to-cart").text(addToCartText);
            _this.closest("body").find(".product-modal-popup").find(".product-add-to-cart").prop("disabled",false);
        }
        $('#modal_box').modal('show');
        $(".page-loader").hide();
    }
    if(page_type != "wishlist" && page_type != "view-cart") {
        is_authenticated = $(this).closest("body").find(".is_authenticated").val();
        if(is_authenticated == 1) 
            showWishlist($(this),product_type,product_id,$(this).closest("body").find(".product-modal-popup").find(".single-product-variants-combination").val(),'view_product');
    }
    
});

function variantsByProduct(product_id,_this,type = '',selectedVariant = '') {
    $.ajax({
        url: $(".variants-by-product").val(),
        type: 'get',
        "data":{product_id: product_id,_type : type},
        success: function (response) {
            variantsCombinations = response.product_variants_combinations;
            _this.closest("body").find(".variant-combinations-"+product_id).val(JSON.stringify(variantsCombinations));
            if(type != "single-product") {
                variantsHTML = "";
                productVariants = response.product_variants;
                variantsOptions = response.variants_options;
                variantImages = response.variantImages;
                if(productVariants.length > 0) {
                    variantsHTML += `<div class="variants_size">`;
                    $(productVariants).each(function(index,variants) {
                        variantsHTML += `<h2>${variants.variants_name}</h2>`;
                        if(variantsOptions.hasOwnProperty(variants.variants_id)) {
                            productVariantsOption = variantsOptions[variants.variants_id];
                            if(page_type == "view-cart" && selectedVariant.length > 0 && index in selectedVariant) 
                                firstVariantOptionId  = selectedVariant[index];
                            else 
                                firstVariantOptionId  = (productVariantsOption.length > 0) ? productVariantsOption[0].variant_options_id : 0;
                            variants_scroll_class = (response.product_variants_combinations.length > 4) ? "popup-variants-carousel" : "";
                            variantsHTML += `<div class="variants-group" data-variant-name = "${variants.variants_name}"><input type="hidden" class="selected-variant-id" value="${firstVariantOptionId}"><ul class="list-variants ${variants_scroll_class}">`;
                            productVariantsOption.forEach(function(option,key) {
                                checked = (firstVariantOptionId == option.variant_options_id) ? 'checked' : '';
                                variantsHTML += `<li class="product-variant-dev"><input type="radio" data-variant-option-name="${option.variant_options_name}" class="btn-check product-variant variant-combination" data-type="product-in-popup" name="product_variant_combination_${option.variants_id}" id="variant-combination-${option.variant_options_id}" value="${option.variant_options_id}" data-value="${option.variant_options_id}" ${checked}>`;
                                if(option.variants_option_image != null && option.variants_option_image != "") {
                                    variantsHTML += `<img src="${option.variants_option_image}" style="height: 30px;" class="rounded-circle variant-option-img" title="${option.variant_options_name}" alt="" >`;
                                } else {
                                    checked_style = (firstVariantOptionId == option.variant_options_id) ? "background-color: #000;color: #fff;" : "";
                                    variantsHTML += `<label style="${checked_style}" class="btn btn-outline-secondary avatar-xs-1 rounded-4 d-flex product-variant-label variant-combination-${option.variant_options_id}" for="variant-combination-${option.variant_options_id}">${option.variant_options_name}</label>`;
                                }
                                variantsHTML += `</li>`;
                            });
                            variantsHTML += `</ul></div>`;
                        }
                    });
                    variantsHTML += `</div>`;
                }
                _this.closest("body").find(".product-modal-popup").find(".variants-product-features").html(variantsHTML);
                _this.closest("body").find(".variant-combinations-images-"+product_id).val(JSON.stringify(variantImages));
                variantCombination(_this);
                $('select:not(.site-language), .select_option:not(.site-language)').niceSelect(); 
                $('#modal_box').modal('show');
                $(".page-loader").hide();
            } else {
                variant_combinations_data =  _this.closest("body").find(".variant-combinations-"+product_id).val();
                if(variant_combinations_data != "") {
                    variant_combinations_data = $.parseJSON(variant_combinations_data);
                    checkProductAvailable(selector,variant_combinations_data,variant_combination_id);
                }
            }
        }
    });
}



