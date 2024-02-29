// Email Validation
function IsEmail(email) {
    var regex = /^[a-zA-Z\d_.\-+\u0600-\u06FF]+@[a-zA-Z\d.-]+\.[a-zA-Z]{2,}$/u;
    return regex.test(email);
}

//Number Validation
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    // Allow numbers (0-9), decimal point (.), and some special keys
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46 && charCode !== 8 && charCode !== 9 && charCode !== 13 && charCode !== 27) {
        return false;
    }
    // Ensure only one decimal point is allowed
    if (charCode === 46 && evt.target.value.indexOf('.') !== -1) {
        return false;
    }

    return true;
}

//Allow letters, comma, hyphen(-), ampersand(&), whitespace and period(.)
function allowAlphaSpecial(event) {
    var inputValue = String.fromCharCode(event.which);
    if (!/^[A-Za-z,.\&\- ]+$/.test(inputValue)) {
      event.preventDefault();
    }
  }

//Allow letters, whitespace and period(.)
function allowAlphaSpace(event) {
    var inputValue = String.fromCharCode(event.which);
    if (!/^[A-Za-z,. ]+$/.test(inputValue)) {
        return false;
    }
}

function restrictCharacters(event) {
    var pattern = event.target.getAttribute('data-pattern');
    var inputFieldType = event.target.getAttribute('data-type');
    if(inputFieldType == "codeeditor") {
        var summernoteContent = $('#summernote').summernote('code');
        var inputValue = $('<div>').html(summernoteContent).text().trim();
        pattern = pattern.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    } else 
        var inputValue = String.fromCharCode(event.which);
    if (!new RegExp(pattern).test(inputValue)) {
        event.preventDefault();
        return false;
    }
    return true;
}

$(".new-password").on("focus keyup", function () {
    validatePassword($(this).val());
});

$(".new-password").blur(function () {
    $("#pwd_strength_wrap").fadeOut(400);
});

function validatePassword(password) {
    var score = 0;
    var desc = ["Too short", "Weak", "Good", "Strong", "Best"];

    $("#pwd_strength_wrap").fadeIn(400);

    // password length
    if (password.length >= 8) {
        $("#length").removeClass("invalid").addClass("valid");
        score++;
    } else {
        $("#length").removeClass("valid").addClass("invalid");
    }

    // at least 1 digit in password
    if (password.match(/\d/)) {
        $("#pnum").removeClass("invalid").addClass("valid");
        score++;
    } else {
        $("#pnum").removeClass("valid").addClass("invalid");
    }

    // at least 1 capital & lower letter in password
    if (password.match(/[A-Z]/) && password.match(/[a-z]/)) {
        $("#capital").removeClass("invalid").addClass("valid");
        score++;
    } else {
        $("#capital").removeClass("valid").addClass("invalid");
    }

    // at least 1 special character in password
    if (password.match(/[!@#$%^&*?_~-]/)) {
        $("#spchar").removeClass("invalid").addClass("valid");
        score++;
    } else {
        $("#spchar").removeClass("valid").addClass("invalid");
    }

    if (password.length > 0) {
        // show strength text
        $("#passwordDescription").text(desc[score]);
        // show indicator
        $("#passwordStrength").removeClass().addClass("strength" + score);
    } else {
        $("#passwordDescription").text("Password not entered");
        $("#passwordStrength").removeClass().addClass("strength" + score);
    }
}