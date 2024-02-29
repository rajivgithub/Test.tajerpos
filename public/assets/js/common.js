$(document).ready(function() {
    $(".form-element-data").each(function() {
        country_id = $(this).find(".country-list").val();
        state_id = $(this).find(".state-id").val();
        city_id = $(this).find(".city-id").val();
        state_list_url = $(".state-list-url").val();
        city_list_url = $(".city-list-url").val();
        if(country_id != "" && country_id != undefined)
            stateList(state_list_url,$(this).find(".country-list"),country_id,state_id);
        if(city_id != "" && city_id != undefined)
            cityList(city_list_url,$(this).find(".state-list"),state_id,city_id);
    });
    if($(".site-language").val() == "ar")
        $("body").addClass('rtl');
    else 
        $("body").removeClass('rtl');  
});

$(document).on("change",".country-list",function() {
    country_id = $(this).val();
    state_list_url = $(this).closest("form").find(".state-list-url").val();
    stateList(state_list_url,$(this),country_id);
});

function stateList(state_list_url,_this,country_id,state_id = null) {
    _this.closest('.form-element-data').find(".city-list").html('').html('<option value="">--Select City--</option>');
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: state_list_url,
        type: 'post',
        data: {_token: CSRF_TOKEN,country_id: country_id},
        success: function(response){
            states = response.states;
            states_list = '<option value="">--Select State--</option>';
            if(states.length > 0) {
                $(states).each(function(key,val) {
                    selected = (state_id != "" && state_id == val.id) ? "selected" : "";
                    states_list += '<option value="'+val.id+'" '+selected+'>'+val.name+'</option>';
                });
            }
            _this.closest('.form-element-data').find(".state-list").html('').html(states_list);
        }
    });
}
$(document).on("change",".state-list",function() {
    _this = $(this);
    state_id = _this.val();
    city_list_url = $(this).closest("form").find(".city-list-url").val();
    cityList(city_list_url,_this,state_id);
});
function cityList(city_list_url,_this,state_id,city_id = null) {
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: city_list_url,
        type: 'post',
        data: {_token: CSRF_TOKEN,state_id: state_id},
        success: function(response){
            cities = response.cities;
            city_list = '<option value="">--Select City--</option>';
            if(cities.length > 0) {
                $(cities).each(function(key,val) {
                    selected = (city_id != "" && city_id == val.id) ? "selected" : "";
                    city_list += '<option value="'+val.id+'" '+selected+'>'+val.name+'</option>';
                });
            }
            _this.closest('.form-element-data').find(".city-list").html('').html(city_list);
        }
    });
}

//Check Email is Unique
function isEmailExist(email,_this,type) {
    email_url = _this.closest("form").find(".email-path").val();
    user_id = _this.closest("form").find(".user-id").val();
    store_id = _this.closest("form").find(".store-id").val();
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    field_label =  _this.closest('.input-field-div').find(".required-field").attr("data-label");
    if(store_id != "" && store_id != undefined) {
        $.ajax({
            url: email_url,
            type: 'post',
            async: false,
            data: {_token: CSRF_TOKEN,email: email,type:type,user_id:user_id,store_id:store_id},
            success: function(response){
                if(response.email_exist > 0) { 
                    // _this.css("border","2px solid #F30000");
                    _this.closest('.input-field-div').find(".error-message").text(langTranslations.exists.replace(':attribute', field_label)).css("color", "#F30000");
                    error++;
                }
            }
        });
    }
}
//Check URL is Unique
function isURLExist(_this) {
    store_url = _this.closest("form").find(".store-url").val();
    url = _this.closest("form").find(".url").val();
    store_id = _this.closest("form").find(".store_id").val();
    // _this.css("border","2px solid #f4f5f9");
    _this.closest('.input-field-div').find(".error-message").text("");
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    field_label =  _this.closest('.input-field-div').find(".required-field").attr("data-label");
    $.ajax({
        url: url,
        type: 'post',
        async: false,
        data: {_token: CSRF_TOKEN,store_url: store_url,store_id:store_id},
        success: function(response){
            if(response.url_exist > 0) {
                // _this.css("border","2px solid #F30000"); 
                _this.closest('.input-field-div').find(".error-message").text(langTranslations.exists.replace(':attribute', field_label)).css("color", "#F30000");
                error++;
            }
        }
    });
}
var error;
//client side validation
function validateFields(_this) {
    error = 0;
    _this.closest("form").find(".error-message").text("");
    _this.closest("form").find(".form-input-field").each(function() {
        field_data_type = $(this).attr("data-type");
        field_value = $(this).val();
        if(field_data_type == "image" && field_value && imageExtension($(this)) == false) {
            $(this).closest(".input-field-div").find(".error.error-message").text(langTranslations.image_extension).css("color", "#F30000");
            error++;
        }
    });
    _this.closest("form").find(".required-field").each(function() {
        field_value = $(this).val(); 
        field_label = $(this).attr("data-label");
        field_type = $(this).attr("type");
        field_id = $(this).attr("id");
        field_data_type = $(this).attr("data-type");
        min_value = $(this).attr("data-min");
        max_value = $(this).attr("data-max");
        var reqErrorMessage = langTranslations.required.replace(':attribute', field_label);
        var invalidErrorMessage = langTranslations.regex.replace(':attribute', field_label);
        if((field_value == "" || (min_value != undefined && min_value != 0 && (parseInt(field_value) < parseInt(min_value)))) || (field_type == "radio" && $("#"+field_id+":checked").length == 0)) {
            if($(this).attr("data-page") == "login")
                $(this).closest(".input-field-div").find(".error.error-message").text(reqErrorMessage).css("color",'rgb(239 134 94)');
            else if(field_data_type == "show-border-error")
                $(this).css("border","2px solid #F30000");
            else
                $(this).closest(".input-field-div").find(".error.error-message").text(reqErrorMessage).css("color", "#F30000");
            error++;
        } 
        if((field_value != "") && (((field_label == "Email Address" || field_type == 'email' || ((field_label == "User Name" || field_label == "Email"))) && IsEmail(field_value) == false) || (field_data_type == "image" && imageExtension($(this)) == false) || (field_type == "url" && isValidURL(field_value) == false) || (field_label == "URL" && validURL(field_value) == false) || (field_label == "Amount" && isAmount(field_value) == false) || ((field_label == "Phone Number" || field_label == "Postal Code") && !($.isNumeric(field_value))))) {
            if($(this).attr("data-page") == "login")
                $(this).closest(".input-field-div").find(".error.error-message").text(invalidErrorMessage).css("color",'rgb(239 134 94)');
            else
                $(this).closest(".input-field-div").find(".error.error-message").text(invalidErrorMessage).css("color", "#F30000");
            error++;
        }
        if(field_data_type == "image" && field_value && imageExtension($(this)) == false) {
            $(this).closest(".input-field-div").find(".error.error-message").text(langTranslations.image_extension).css("color", "#F30000");
            error++;
        }
        if(field_label == "Email Address" && field_value != "") 
            isEmailExist(field_value,$(this),$(this).attr("data-type"));
        if(field_label == "URL" && field_value != "" && validURL(field_value) != false) 
            isURLExist($(this)); 
        if(field_value != "" && max_value != undefined && parseInt(max_value) > 0 && (parseInt(field_value.length) > parseInt(max_value))) {
            $(this).closest(".input-field-div").find(".error.error-message").text(langTranslations.max.numeric.replace(':attribute', field_label).replace(':max', max_value)).css("color", "#F30000");
            error++;
        }
    });
    return error;
}

$(document).on("change",".email-field",function() {
    _this = $(this);
    email = _this.val();
    type = _this.attr("data-type");
    // _this.css("border","2px solid #f4f5f9");
    _this.closest('.input-field-div').find(".error-message").text("");
    if(email != "") 
        isEmailExist(email,_this,type);
});

//Hide and show the password
$(document).on("click","#user-password",function() {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var type = $(this).hasClass("fa-eye-slash") ? "text" : "password";
    $(this).closest(".input-field-div").find(".password").attr("type", type);
});


$(document).ready(function() {
    category_list_url = $(".category-list-url").val();
    category_search_name = $(".category-search-name").val();
    if(category_list_url != undefined && category_search_name != undefined) {
        $.ajax({
            url: category_list_url,
            type: 'get',
            data: {type: "category-list"},
            success: function(response){   
                category_list = response.category_details;
                if(category_list.length > 0) {
                    selected = (category_search_name == 'all') ? "selected" : '';
                    category_list_option = "<option value='all' "+selected+">All Category</option>";
                    $(category_list).each(function(key,val) {
                        if(val.product_count > 0) {
                            selected = (category_search_name == val.category_id) ? "selected" : '';
                            category_list_option += "<option value='"+val.category_id+"' "+selected+">"+val.category_name+"</option>";
                        }
                    });
                    $(".category-list-search").html(category_list_option);
                }
            }
        });
    }
    //Hide and show the image preview
    $(".image-preview").each(function() {
        data_type = $(this).attr("data-type");
        if($(this).attr("src") == "") {
            // $(".file-preview").addClass("dnone");
            $(this).closest(".file-preview").addClass("dnone");
            if(data_type == "Profile") {
                $(".profile-image-preview").removeClass("dnone"); 
                var intials = $('.auth-user-name').val().charAt(0);
                $('.profile-image-preview').text(intials);
            }
        }
        else {
            // $(".file-preview").removeClass("dnone");
            $(this).closest(".file-preview").removeClass("dnone");
            if(data_type == "Profile")
                $(".profile-image-preview").addClass("dnone");
        }
    });
    if($(".profile-image-name").val() == "") {
        var intials = $('.auth-user-name').val().charAt(0);
        $('.default-profile-image').text(intials);
    }
    product_list_url = $(".product-list-url").val();
    $("#product-name-search").autocomplete({
        source: function(request, response) {
          $.ajax({
            url: product_list_url,
            method: "post",
            data: {
              _token: CSRF_TOKEN,
              product_name: request.term,
              category_id : $(".category-list-search").val()
            },
            success: function(data) {
                response(data); // Pass the response data to the autocomplete function
            },
            error: function() {
              response([]); // Provide an empty array if there is an error
            }
          });
        },
        minLength: 1,
        select: function(event, ui) {
            var selectedValue = ui.item.value;
        }
    });      
});
//Remove the image
$(document).on('click',".remove-attachment", function() { 
    $(this).closest(".input-field-div").find('.image-field').val(''); 
    $(this).closest(".input-field-div").find('.image-preview').attr('src',"");
    _type =  $(this).attr("data-type"); 
    if(_type == "customer") {
        default_image = $(this).closest("form").find(".default-image").val();
        $(this).closest(".input-field-div").find('.image-preview').attr('src',default_image);
        action_url = $(this).closest("form").find(".remove-profile-url").val();
        user_id = $(this).closest("form").find(".user-id").val();
        CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: action_url,
            type: 'post',
            data: {_token: CSRF_TOKEN,user_id: user_id},
            success: function(response){
                toastr.options =
                {
                    "closeButton" : true,
                    "progressBar" : true
                }
                toastr.success(response.message);                
            }
        });
    } else {
        $(this).closest(".input-field-div").find('.file-preview').addClass("dnone");
        $(this).closest(".input-field-div").find('.profile-image-preview').removeClass("dnone");
        var intials = $(this).closest("body").find('.auth-user-name').val().charAt(0);
        $(this).closest(".input-field-div").find('.profile-image-preview').text(intials);
        $(this).closest(".input-field-div").find(".remove-image").val(1);
        data_image_type =  $(this).attr("data-image-type"); 
        if(data_image_type == "required") 
            $(this).closest(".input-field-div").find(".image-field").addClass("required-field");
        if(_type == "logo") {
            $(this).closest(".input-field-div").find('.image-path-data').val("");
        }
    }
        
}); 
//Display the image before upload
$(document).on("change",".image-field",function() {
    _this = $(this);
    _this.closest(".input-field-div").find(".error.error-message").text("");
    // _this.closest("form").find(".required-field").css("border","2px solid #f4f5f9");
    if(this.files.length > 0) {
        if(imageExtension(_this) == false) 
            _this.closest(".input-field-div").find(".error.error-message").text(langTranslations.image_extension).css("color", "#F30000");
        else {
            const file = this.files[0];
            if (file){
                let reader = new FileReader();
                reader.onload = function(event){
                    var img = new Image();
                    img.src = event.target.result;
                    /*img.onload = function() {
                        var requiredWidth = 1440;
                        var requiredHeight = 470;
                        if (img.width !== requiredWidth || img.height !== requiredHeight) {
                            _this.closest(".input-field-div").find(".error.error-message").text('Image dimensions must be ' + requiredWidth + 'x' + requiredHeight + ' pixels.').css("color", "#F30000");
                        }
                    };*/
                    _this.closest(".input-field-div").find('.image-preview').attr('src', event.target.result);
                    _this.closest(".input-field-div").find('.file-preview').removeClass("dnone");
                    _this.closest(".input-field-div").find('.profile-image-preview').addClass("dnone");
                }
                reader.readAsDataURL(file);
            } else {
                _this.closest(".input-field-div").find('.file-preview').addClass("dnone");
                _this.closest(".input-field-div").find('.profile-image-preview').removeClass("dnone");
            }
        }
    } else {
        image_path = _this.closest(".input-field-div").find('.image-path-data').val();
        if(image_path != undefined && image_path != "") {
            _this.closest(".input-field-div").find('.image-preview').attr('src', image_path);
            _this.closest(".input-field-div").find('.file-preview').removeClass("dnone");
        } else {
            _this.closest(".input-field-div").find('.file-preview').addClass("dnone");
        }
    }
});
function imageExtension(_this) {
    var extension = _this.val().split('.').pop().toLowerCase();
    if($.inArray(extension, ['png','jpg','jpeg']) == -1) 
        return false;
    else
        return true;
}

function isValidURL(url) {
    // Regular expression for a simple URL validation
    var urlPattern = /^(https?:\/\/)?([\w.-]+\.[a-z]{2,})(\/\S*)?$/;
    return urlPattern.test(url);
}

//Amount
$(document).on("keyup",".amount",function() {
    val = this.value;
    if(!isAmount(val))
        this.value = val.substring(0, val.length - 1);
});
//Amount Validation
function isAmount(value) {
    return /^\d{0,9}(\.\d{0,2})?$/.test(value);
}
//URL Validation
function validURL(value) {
    var regex = /^[a-zA-Z0-9%\/.-]+$/;
    return regex.test(value);
}

function saveProductSearch(product_name,category_id) {
    $.ajax({
        url: $(".category-search-url").val(),
        type: 'post',
        data: {_token: CSRF_TOKEN,product_name: product_name, category_id : category_id, type: "save"}
    });
}

$(document).on("keyup",".product-name-search",function() {
    product_name = $(this).val();
    category_id = $(this).closest("form").find(".category-list-search").val();
    saveProductSearch(product_name,category_id);
});
$(document).on("change",".category-list-search",function() {
    category_id = $(this).val();
    product_name = $(this).closest("form").find(".product-name-search").val();
    saveProductSearch(product_name,category_id);
});

function get_unread_chat_count() {
    unread_chat_url = $(".unread-chat-url").val();
    $.ajax({
        url: unread_chat_url,
        type: 'get',
        success: function(response){
            unread_chat_count = response.unread_chat_count;
            unread_message_count = (response.unread_chat_count[0].unread_message_count > 0) ? response.unread_chat_count[0].unread_message_count : 0;
            if(unread_message_count > 0) {
                $(".chat-details").find(".unread-chat-count").text(unread_message_count);
                $(".chat-details").find(".unread-chat-count").addClass("label label-primary");
            }
            else {
                $(".chat-details").find(".unread-chat-count").text("");
                $(".chat-details").find(".unread-chat-count").removeClass("label label-primary");
            }
                
        }
    });
}

var dropdownVisible = false;
$(document).ready(function() {
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    if($(".unread-chat-url").val() != undefined) 
        get_unread_chat_count();
    if($(".admin-unread-chat-url").val() != undefined) 
        admin_unread_chat_count();
    $(document).on("click", ".chat-list", function(e) {
        e.preventDefault();
        _this = $(this);
        if (dropdownVisible) {
            _this.closest('.chat-details').find(".chat-dropdown-list").hide();
            dropdownVisible = false;
        } else {
            chat_list_url = $(this).closest("body").find(".chat-list-url").val();
            $.ajax({
                url: chat_list_url,
                type: 'get',
                success: function(response){
                    user_details = response.user_details;
                    if(user_details.length > 0) {
                        chat_list = "";
                        $(user_details).each(function(key, val) {
                            var firstLetter = val.name.charAt(0).toUpperCase();
                            var imagePath = (val.profile_image != "" && val.profile_image != null) ? '<img src="' + val.profile_image + '" class="rounded-circle" alt="User Image">' : '<div class="rounded-circle" style="background: #512DA8; color: #fff; text-align: center; width: 40px; height: 40px; line-height: 40px;">' + firstLetter + '</div>';
                            var style = (val.profile_image == "" || val.profile_image == null) ? "style='width:50px'" : "";
                            chat_list += '<li class="official-chat-list" data-store-id="' + val.store_id + '" data-user-id="' + val.id + '"><a href="#" data-toggle="control-sidebar"><div class="pull-left" '+style+'>' + imagePath + '</div><div class="mail-contnet"><h4 class="chat-user-name">' + val.name + '</h4><span>' + val.last_message + '</span></div>' + (val.unread_count > 0 ? '<span class="badge badge-danger text-right">' + val.unread_count + '</span>' : '') + '</a></li>';
                        });
                    } else
                        chat_list = "<li>User Not Found..!</li>";
                    _this.closest('.chat-details').find(".all-chat-list").html('').html(chat_list);
                    _this.closest('.chat-details').find(".chat-dropdown-list").css("display","block");
                }
            });
            _this.closest('.chat-details').find(".chat-dropdown-list").show();
            dropdownVisible = true;
        }
    });
});
function get_chat() {
    get_chat_url = _this.closest("body").find(".typing-area").find(".get-chat-url").val();
    $.ajax({
        url: get_chat_url,
        type: 'get',
        data: {_token: CSRF_TOKEN,incoming_msg_id: user_id},
        success: function(response){
            if(response.status === 200) {
                let data = response.chat_data;
                _this.closest("body").find(".chat-box-area").html(data);
                if(!$(".chat-box-area").hasClass("active")) {
                    scrollToBottom();
                }
            }
        }
    });
    get_unread_chat_count();
}
function admin_unread_chat_count() {
    unread_chat_url = $(".admin-unread-chat-url").val();
    $.ajax({
        url: unread_chat_url,
        type: 'get',
        success: function(response){
            unread_chat_count = response.unread_chat_count;
            unread_message_count = (response.unread_chat_count[0].unread_message_count > 0) ? response.unread_chat_count[0].unread_message_count : 0;
            if(unread_message_count > 0) {
                $(".admin-chat-details").find(".admin-unread-chat-count").text(unread_message_count);
                $(".admin-chat-details").find(".admin-unread-chat-count").addClass("badge rounded-pill");
            }
            else {
                $(".admin-chat-details").find(".admin-unread-chat-count").text("");
                $(".admin-chat-details").find(".admin-unread-chat-count").removeClass("badge rounded-pill");
            }
                
        }
    });
}
function admin_get_chat() {
    get_chat_url = _this.closest("body").find(".typing-area").find(".get-chat-url").val();
    $.ajax({
        url: get_chat_url,
        type: 'get',
        data: {_token: CSRF_TOKEN,incoming_msg_id: user_id},
        success: function(response){
            if(response.status === 200) {
                let data = response.chat_data;
                _this.closest("body").find(".admin-chat-box-area").html(data);
                if(!$(".admin-chat-box-area").hasClass("active")) {
                    scrollToBottom();
                }
            }
        }
    });
    admin_unread_chat_count();
}
var officialChatVisible = false;
var getChatInterval;
$(document).on("click", ".official-chat-list", function(e) {
    e.preventDefault();
    _this = $(this);
    if (officialChatVisible) {
        officialChatVisible = false;
    } else { 
        _this.closest('.chat-details').find(".chat-dropdown-list").hide();
        dropdownVisible = false;
        officialChatVisible = true;
        chat_user_name = _this.find(".chat-user-name").text();
        user_id = _this.attr("data-user-id");
        _this.closest("body").find(".incoming-msg-id").val(user_id);
        _this.closest("body").find(".message-area").find(".message-user-name").text(chat_user_name);
        getChatInterval = setInterval(get_chat, 1000);
    }
}); 
$(document).on("click", ".close-chat-option", function(e) {
    e.preventDefault();
    officialChatVisible = false;
    clearInterval(getChatInterval);
});
var adminChatVisible = false;
$(document).ready(function() {
    $(document).on("click", ".admin-chat-list", function(e) {
        e.preventDefault();
        _this = $(this);
        if (adminChatVisible) {
            _this.closest('.admin-chat-details').find(".admin-chat-dropdown-list").hide();
            adminChatVisible = false;
        } else {
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            chat_list_url = $(this).closest("body").find(".chat-list-url").val();
            $.ajax({
                url: chat_list_url,
                type: 'get',
                success: function(response){
                    user_details = response.user_details;
                    if(user_details.length > 0) {
                        chat_list = "";
                        $(user_details).each(function(key,val) {
                            var firstLetter = val.name.charAt(0).toUpperCase();
                            var imagePath = (val.profile_image != "" && val.profile_image != null) ? '<img src="' + val.profile_image + '" class="rounded-circle" alt="User Image">' : '<div class="rounded-circle" style="background: #512DA8; color: #fff; text-align: center; width: 40px; height: 40px; line-height: 40px;">' + firstLetter + '</div>';
                            var style = (val.profile_image == "" || val.profile_image == null) ? "style='width:50px'" : "";
                            chat_list += '<li style="margin-top:4px;margin-bottom:10px;" class="admin-official-chat-list" data-user-id="'+val.id+'"><a href="#" data-toggle="control-sidebar"><div class="pull-left" '+style+'>'+imagePath+'</div><div class="mail-contnet"><h4 class="chat-user-name">'+val.name+'</h4><span>' + val.last_message + '</span></div>' + (val.unread_count > 0 ? '<span class="badge badge-danger text-right">' + val.unread_count + '</span>' : '') + '</a></li>';
                        });
                    } else
                        chat_list = "<li>User Not Found..!</li>";
                    _this.closest('.admin-chat-details').find(".admin-chat-all-list").html('').html(chat_list);
                    _this.closest('.admin-chat-details').find(".admin-chat-dropdown-list").css("display","block");
                }
            });
            _this.closest('.admin-chat-details').find(".admin-chat-dropdown-list").show();
            adminChatVisible = true;
        }
    });
});
var adminOfficialChat = false;
var getAdminChatInterval;
$(document).on("click", ".admin-official-chat-list", function(e) {
    e.preventDefault();
    _this = $(this);
    if (adminOfficialChat) {
        adminOfficialChat = false;
    } else { 
        _this.closest('.admin-chat-details').find(".admin-chat-dropdown-list").hide();
        adminChatVisible = false;
        adminOfficialChat = true;
        chat_user_name = _this.find(".chat-user-name").text();
        user_id = _this.attr("data-user-id");
        store_id = _this.attr("data-store-id");
        _this.closest("body").find(".incoming-msg-id").val(user_id);
        _this.closest("body").find(".user-store-id").val(store_id);
        _this.closest("body").find(".message-area").find(".message-user-name").text(chat_user_name);
        getAdminChatInterval = setInterval(admin_get_chat, 1000);
    }
}); 
$(document).on("click", ".admin-close-chat-option", function(e) {
    e.preventDefault();
    adminOfficialChat = false;
    clearInterval(getAdminChatInterval);
});

$(document).on("change",".change-online-order-status",function(event) {
    event.stopImmediatePropagation();
    $(this).css("border","1px solid #86a4c3");
    $(this).closest("section").find(".error-message").text("");
    var checked = $('input[name="online_order_checkbox"]:checked').length > 0;
    _type = $(this).attr("data-type");
    if ((_type == "multi-bulk-action" && !checked)){
        $(this)[0].selectedIndex = 0;
        $(this).closest("section").find(".error-message").text("Please check at least one checkbox").css("color","#F30000");
        return false;
    } else if($(this).val() == "") {
        $(this).css("border","2px solid #F30000");
        $(this).closest("section").find(".error-message").text("Please choose the order status").css("color","#F30000");
        return false;
    } else {
        update_order_status_url = $(this).closest("section").find(".update_order_status_url").val();
        status_id = $(this).val();
        var data = { "_token": CSRF_TOKEN,'order_ids[]' : [],'status_id': status_id};
        if(_type == "multi-bulk-action") {
            $(".online-order-checkbox:checked").each(function() {
                data['order_ids[]'].push($(this).val());
            });
        } else if(_type == "single-order") {
            store_order_id = $(this).closest("section").find(".online_order_id").val();
            data['order_ids[]'].push(store_order_id);
        } else {
            store_order_id = $(this).closest("tr").find(".online-order-checkbox").val();
            data['order_ids[]'].push(store_order_id);
        }
        $.ajax({
            url: update_order_status_url,
            type: 'post',
            data: data,
            async : false,
            success: function(response){
                toastr.options =
                {
                    "closeButton" : true,
                    "progressBar" : true
                }
                toastr.success(response.message);
                if(_type != "single-order") {
                    if($(".sort-by-status").val() != "") 
                        onlineOrderList('online_order_status',$(".sort-by-status").val());
                    else 
                        onlineOrderList('all');
                }
            }
        });
    }
});
