'use strict';

$(document).ready(function () {
    $('#defaultLanguage').select2({
        width: '100%',
    });
});

$('#passwordValidationErrorBox').hide();
$(document).on('submit', '#editProfileForm', function (event) {
    event.preventDefault();
    let loadingButton = jQuery(this).find('#btnPrEditSave');
    loadingButton.button('loading');
    $.ajax({
        url: profileUpdateUrl,
        type: 'post',
        data: new FormData($(this)[0]),
        processData: false,
        contentType: false,
        success: function (result) {
            displaySuccessMessage(result.message);
            $('#editProfileModal').modal('hide');
            location.reload();
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            loadingButton.button('reset');
        },
    });
});

$(document).on('submit', '#changePasswordForm', function (event) {
    event.preventDefault();
    let isValidate = validatePassword();
   
    if (!isValidate) {
        return false;
    }
    let loadingButton = jQuery(this).find('#btnPrPasswordEditSave');
    loadingButton.button('loading');
    $.ajax({
        url: changePasswordUrl,
        type: 'post',
        data: new FormData($(this)[0]),
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                $('#changePasswordModal').modal('hide');
                displaySuccessMessage(result.message);
            }
        },
        error: function (result) {
            $('#passwordValidationErrorBox').
                html(result.responseJSON.message).
                show();
            $(document).ready(function () {
                $('.alert').delay(5000).slideUp(300);
            });
        },
        complete: function () {
            loadingButton.button('reset');
        },
    });
});

$('#editProfileModal').on('hidden.bs.modal', function () {
    resetModalForm('#editProfileForm', '#editProfileValidationErrorsBox');
    resetModalForm('#editProfileForm', '#profilePictureValidationErrorsBox');
});
$('#changeLanguageModal').on('hide.bs.modal', function () {
    resetModalForm('#changeLanguageForm', '#editProfileValidationErrorsBox');
});
// open edit user profile model
$(document).on('click', '.editProfileModal', function (event) {
    renderProfileData();
});

window.renderProfileData = function () {
    $.ajax({
        url: profileUrl,
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let user = result.data;
                $('#editUserId').val(user.id);
                $('#firstName').val(user.name);
                $('#userEmail').val(user.email);
                $('#userPhone').val(user.phone);
                $('.editProfileBtnCc').text(user.region_code);
                $('.edit_profile_flag').addClass(user.region_code_flag);
                $('.edit_profile_region_code').val(user.region_code);
                $('.edit_profile_region_code_flag').val(user.region_code_flag);
                $('#profilePicturePreview').attr('src', user.photo_url);
                $('#editProfileModal').appendTo('body').modal('show');
            }
        },
    });
};

$(document).on('change', '#profilePicture', function () {
    let validFile = isValidFile($(this), '#profilePictureValidationErrorsBox');
    if (validFile) {
        displayPhoto(this, '#profilePicturePreview');
    } else {
        $(this).val('');
    }
});

$('#changePasswordModal').on('hidden.bs.modal', function () {
    $('.fa-eye').toggleClass('fa-eye-slash');
    $('#pfCurrentPassword, #pfNewPassword, #pfNewConfirmPassword').
        prop('type', 'password');
    resetModalForm('#changePasswordForm', '#editPasswordValidationErrorsBox');
});

function validatePassword () {
    let currentPassword = $('#pfCurrentPassword').val().trim();
    let password = $('#pfNewPassword').val().trim();
    let confirmPassword = $('#pfNewConfirmPassword').val().trim();

    if (currentPassword == '' || password == '' || confirmPassword == '') {
        $('#editPasswordValidationErrorsBox').
            show().
            html(Lang.get('messages.validation.all_field_required'))
        return false;
    }
    return true;
}

$(document).on('submit', '#changeLanguageForm', function (event) {
    event.preventDefault();
    let loadingButton = $(this).find('#btnLanguageChange');
    loadingButton.button('loading');
    
    $.ajax({
        url: changeLanguageUrl,
        type: 'post',
        data: new FormData($(this)[0]),
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                $('#changeLanguageModal').modal('hide');
                displaySuccessMessage('Language Updated Successfully.');
                location.reload();
            }
        },
        error: function (result) {
            manageAjaxErrors(result, '#changeLanguageValidationErrorsBox');
        },
        complete: function () {
            loadingButton.button('reset');
        },
    });
});

$(document).on('click', '.changePasswordModal', function () {
    $('#changePasswordModal').appendTo('body').modal('show');
});

$(document).on('click', '.changeLanguageModal', function () {
    $('#changeLanguageModal').appendTo('body').modal('show');
});

$(document).on('click', '.emailNotificationSetting', function () {
    $.ajax({
        url: route('get.email-update'),
        type: 'get',
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                $("input[type='checkbox'][name='email_setting']").prop('checked', result.data);
                $('#changeEmailSetting').appendTo('body').modal('show');
            }
        },
        error: function (result) {
            manageAjaxErrors(result, '#emailSettingMessageBox');
        }
    });
});

$(document).on('submit', '#changeEmailSettingFrom', function (event) {
    event.preventDefault();
    let loadingButton = $(this).find('#btnEmailSettingChange');
    loadingButton.button('loading');

    $.ajax({
        url: route('set.email-update'),
        type: 'post',
        data: new FormData($(this)[0]),
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.success) {
                $('#changeEmailSetting').modal('hide');
                displaySuccessMessage(result.message);
            }
        },
        error: function (result) {
            manageAjaxErrors(result, '#emailSettingMessageBox');
        },
        complete: function () {
            loadingButton.button('reset');
        },
    });
});
