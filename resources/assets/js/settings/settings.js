'use strict';

$(document).on('change', '#logo', function () {
    if (isValidFile($(this), '#validationErrorsBox')) {
        displayPhoto(this, '#logoPreview');
    }
    $('.alert').delay(5000).slideUp(300);
});

window.displayFavicon = function (input, selector) {
    let displayPreview = true;
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            let image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                if ((image.height != 16 || image.width != 16) &&
                    (image.height != 32 || image.width != 32)) {
                    $('#favicon').val('');
                    $('#validationErrorsBox').removeClass('d-none');
                    $('#validationErrorsBox').
                        html(Lang.get('messages.setting.fav_icon_tooltip')).
                        show();
                    $('.alert').delay(5000).slideUp(300);
                    return false;
                }
                $(selector).attr('src', e.target.result);
                displayPreview = true;
            };
        };
        if (displayPreview) {
            reader.readAsDataURL(input.files[0]);
            $(selector).show();
        }
    }
};

window.isValidFavicon = function (inputSelector, validationMessageSelector) {
    let ext = $(inputSelector).val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'ico']) == -1) {
        $(inputSelector).val('');
        $(validationMessageSelector).removeClass('d-none');
        $(validationMessageSelector).
            html(Lang.get('messages.validation.image_type_valid')).
            show();
        $('.alert').delay(5000).slideUp(300);
        return false;
    }
    $(validationMessageSelector).hide();
    return true;
};

$(document).on('change', '#favicon', function () {
    $('#validationErrorsBox').addClass('d-none');
    if (isValidFavicon($(this), '#validationErrorsBox')) {
        displayFavicon(this, '#faviconPreview');
    }
});

$(document).on('submit', '#editForm', function (event) {
    event.preventDefault();
    let loadingButton = $('#btnSave');
    loadingButton.button('loading');

    $('#editForm').
        find('input:text:visible:first').
        focus();

    let facebookUrl = $('#facebookUrl').val();
    let twitterUrl = $('#twitterUrl').val();
    let googlePlusUrl = $('#googlePlusUrl').val();
    let linkedInUrl = $('#linkedInUrl').val();

    let facebookExp = new RegExp(
        /^(https?:\/\/)?((m{1}\.)?)?((w{2,3}\.)?)facebook.[a-z]{2,3}\/?.*/i);
    let twitterExp = new RegExp(
        /^(https?:\/\/)?((m{1}\.)?)?((w{2,3}\.)?)twitter\.[a-z]{2,3}\/?.*/i);
    let googlePlusExp = new RegExp(
        /^(https?:\/\/)?(plus\.)?(google\.[a-z]{2,3})\/?(([a-zA-Z 0-9._])?).*/i);
    let linkedInExp = new RegExp(
        /^(https?:\/\/)?((w{2,3}\.)?)linkedin\.[a-z]{2,3}\/?.*/i);

    let facebookCheck = (facebookUrl == '' ? true : (facebookUrl.match(
        facebookExp) ? true : false));
    if (!facebookCheck) {
        displayErrorMessage(Lang.get('messages.validation.facebook_url'))
        loadingButton.button('reset')
        return false;
    }
    let twitterCheck = (twitterUrl == '' ? true : (twitterUrl.match(twitterExp)
        ? true
        : false));
    if (!twitterCheck) {
        displayErrorMessage(Lang.get('messages.validation.twitter_url'))
        loadingButton.button('reset')
        return false;
    }
    let linkedInCheck = (linkedInUrl == '' ? true : (linkedInUrl.match(
        linkedInExp) ? true : false));
    if (!linkedInCheck) {
        displayErrorMessage(Lang.get('messages.validation.linkedin_url'))
        loadingButton.button('reset')
        return false;
    }
    $('#editForm')[0].submit();

    return true;
});

function checkOnlySpace (val) {
    return val.replace(/\s/g, '').length;
}

$(document).on('submit', '#settingForm', function (event) {
    event.preventDefault();
    let loadingButton = $('#btnSave');
    let applicationName = $('#application_name').val();
    let aboutUs = $('#aboutUs').val();
    let companyAddress = $('#company_address').val();
    if (!checkOnlySpace(applicationName)) {
        displayErrorMessage(
            Lang.get('messages.validation.application_name_white_space'))
        return false;
    }
    if (!checkOnlySpace(aboutUs)) {
        displayErrorMessage(
            Lang.get('messages.validation.about_us_white_space'))
        return false;
    }
    if (!checkOnlySpace(companyAddress)) {
        displayErrorMessage(
            Lang.get('messages.validation.address_white_space'))
        return false;
    }
    let email = $('#email').val();
    let emailExp = new RegExp(
        /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/);

    let emailValid = (email == '' ? false : (email.match(
        emailExp) ? true : false));
    if (!emailValid) {
        displayErrorMessage(Lang.get('messages.validation.email_valid'))

        return false;
    }
    if ($('#error-msg').text() !== '') {
        $('#phoneNumber').focus();
        return false;
    }
    loadingButton.button('loading');
    $('#settingForm')[0].submit();

    return true;
});
