$(document).ready(function() {
    showAddress();
});
function showAddress(type = '') {
    $(".page-loader").show();
    $.ajax({
        url: $(".address-list-url").val(),
        type: 'get',
        data : { _type: "list" },
        success: function (data) {
            var addressContainer = $('.address-details-container');
            addressContainer.html("");
            if (data.address_details && data.address_details.length > 0) {
                data.address_details.forEach(function(address) {
                    var destroyUrl = "{{ route($store_url.'.customer.address.destroy', Crypt::encrypt(':address_id')) }}";
                    destroyUrl = destroyUrl.replace(':address_id', address.address_id);
                    checked_default_address = (address.is_default == 1) ? "checked" : "";
                    var addressHtml = `
                        <div class="col-lg-4 col-sm-6 address-details">
                            <input type="hidden" class="address-id" value="${address.address_id}">
                            <input type="hidden" class="country-id" value="${address.country_id}">
                            <input type="hidden" class="state-id" value="${address.state_id}">
                            <input type="hidden" class="city-id" value="${address.city_id}">
                            <div class="form-check card-radio rounded-bottom-0">
                                <input id="shippingAddress`+address.address_id+`" name="shippingAddress" type="radio" class="form-check-input shipping-address" `+checked_default_address+`>
                                <label class="form-check-label" for="shippingAddress`+address.address_id+`">
                                    <span class="mb-3 fw-semibold d-block text-muted text-uppercase customer-address-type">${address.address_type}</span>
                                    <span class="fs-md mb-2 d-block fw-medium customer-name">${address.customer_name}</span>
                                    <input type="hidden" class="customer-street-name" value="${address.street_name}">
                                    <input type="hidden" class="customer-building-name" value="${address.building_name}">
                                    <input type="hidden" class="customer-pincode" value="${address.pincode}">
                                    <span class="text-muted fw-normal text-wrap mb-1 d-block">
                                        ${address.building_name ? `${address.building_name},` : ''}
                                        ${address.street_name ? `${address.street_name},` : ''}
                                        ${address.city_name ? `${address.city_name},` : ''}
                                        ${address.state_name ? `${address.state_name},` : ''}
                                        ${address.country_name ? `${address.country_name},` : ''}
                                        ${address.pincode ? `${address.pincode}` : ''}
                                    </span>
                                    <span class="text-muted fw-normal d-block customer-mobile-no">${address.mobile_number}</span>
                                    <span class="text-muted fw-normal d-block customer-landmark">${address.landmark ? `${address.landmark}` : ''}</span>
                                </label>
                            </div>
                            <div class="d-flex flex-wrap p-2 py-1 rounded-bottom border mt-n1">
                                <div>
                                    <a href="#" class="d-block text-body p-1 px-2 address-details-info" data-type="edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="fa fa-pencil text-muted me-1"></i>`+customerTranslations['edit']+`</a>
                                </div>
                                <div>
                                    <a href="`+destroyUrl+`" class="d-block text-body p-1 px-2 remove-the-address"><i class="fa fa-trash text-muted me-1"></i> `+customerTranslations['remove']+`</a>
                                </div>
                            </div>
                        </div>
                    `;
                    addressContainer.append(addressHtml);
                });
            }
            add_address_element = addressContainer.closest("body").find(".clone-address-details").html();
            addressContainer.append(add_address_element);
            if(type == "save")
                $('#offcanvasRight').offcanvas('hide');
            $(".page-loader").hide();
        }
    });                
}
function addAddress(_this,_type) {
    save_address_url = _this.closest("body").find(".address-from-data").attr("action");
    if(_type == "save")
        var addressFormData = _this.closest("form").serialize();
    else 
        var addressFormData = {_token: CSRF_TOKEN, address_id : _this.closest(".address-details").find(".address-id").val(), mode : _type };
    $.ajax({
        type: 'POST', // or 'GET' depending on your backend configuration
        url: save_address_url, // URL where you want to submit the form data
        data: addressFormData,
        success: function(response) {
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
            toastr.success(response.message);
            if(_type == "save")
                showAddress(_type);
        },
        error: function(error) {
            // Handle any errors that occurred during the AJAX request
            console.error('Error submitting form:', error);
        }
    });
}
$(document).on("click","#save-address-info",function(event) {
    $(this).closest("body").find(".address-error-message").text("");
    check_fields = validateFields($(this));
    if(check_fields > 0)
        return false;
    else {
        event.preventDefault();
        addAddress($(this),"save");
    }
        
});  
$(document).on("click",".address-details-info",function() {
    _type = $(this).attr("data-type");
    $('.address-from-data')[0].reset(); 
    $(this).closest("body").find(".address-info-popup").find(".mode").val("add");
    $(this).closest("body").find(".address-info-popup").find(".modal-address-title").text(customerTranslations['add_address']);
    if(_type == "edit") {
        customer_name = $(this).closest(".address-details").find(".customer-name").text();
        customer_mobile_no = $(this).closest(".address-details").find(".customer-mobile-no").text();
        customer_street_name = $(this).closest(".address-details").find(".customer-street-name").val();
        customer_building_name = $(this).closest(".address-details").find(".customer-building-name").val();
        customer_country_id = $(this).closest(".address-details").find(".country-id").val();
        state_list_url = $(this).closest("body").find(".state-list-url").val();
        customer_state_id = $(this).closest(".address-details").find(".state-id").val();
        city_list_url = $(this).closest("body").find(".city-list-url").val();
        customer_city_id = $(this).closest(".address-details").find(".city-id").val();
        customer_pincode = $(this).closest(".address-details").find(".customer-pincode").val();
        customer_landmark = $(this).closest(".address-details").find(".customer-landmark").text();
        customer_address_type = $(this).closest(".address-details").find(".customer-address-type").text();
        address_id = $(this).closest(".address-details").find(".address-id").val();
        $(this).closest("body").find(".address-info-popup").find(".customer-name").val(customer_name);
        $(this).closest("body").find(".address-info-popup").find(".customer-mobile-no").val(customer_mobile_no);
        $(this).closest("body").find(".address-info-popup").find(".customer-street-name").val(customer_street_name);
        $(this).closest("body").find(".address-info-popup").find(".customer-building-name").val(customer_building_name);
        $(this).closest("body").find(".address-info-popup").find(".country-list").val(customer_country_id);
        $(this).closest("body").find(".address-info-popup").find(".customer-landmark").val(customer_landmark);
        $(this).closest("body").find(".address-info-popup").find(".customer-pincode").val(customer_pincode);
        $(this).closest("body").find(".address-info-popup").find(".customer-address-type").val(customer_address_type);
        $(this).closest("body").find(".address-info-popup").find(".address-id").val(address_id);
        $(this).closest("body").find(".address-info-popup").find(".mode").val("edit");
        stateList(state_list_url,$(".state-list"),customer_country_id,customer_state_id);
        cityList(city_list_url,$(".city-list"),customer_state_id,customer_city_id);
        $(this).closest("body").find(".address-info-popup").find(".modal-address-title").text(customerTranslations['edit_address']);
    } 
}); 
$(document).on("click",".remove-the-address",function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    _this = $(this);
    delete_address_link = $(this).attr("href");
    swal({
        title: customerTranslations['delete_confirmation_title'].replace(':name',customerTranslations['address']),
        text: customerTranslations['delete_confirmation_description'],
        icon: "warning",
        buttons: {
            cancel: {
                text: customerTranslations['cancel_button_text'],  // Replace with your translation
                value: null,
                visible: true,
                className: "swal-button-cancel",
                closeModal: true,
            },
            confirm: {
                text: customerTranslations['ok_button_text'],  // Replace with your translation
                value: true,
                visible: true,
                className: "swal-button-confirm",
                closeModal: true
            }
        },
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $(".page-loader").show();
            $.ajax({
                url: delete_address_link,
                type: 'DELETE',
                data: {_token: CSRF_TOKEN },
                success: function(response) {
                    toastr.options =
                    {
                        "closeButton" : true,
                        "progressBar" : true
                    }
                    toastr.success(response.message);
                    _this.closest(".address-details").remove();
                    $(".page-loader").hide();
                },
                error: function(error) {
                    // Handle errors
                    console.error('Error deleting address:', error);
                }
            });
        }
    });
});
$(document).on("change",".shipping-address",function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    addAddress($(this),"default_address");
});