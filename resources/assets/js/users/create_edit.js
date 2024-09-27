$(document).ready(function () {
    'use strict';

    $('input[type=radio][name=gender]').on('change', function () {
        let file = $('#userProfilePicture').val();
        if (isEmpty(file) && !(isEdit)) {
            if (this.value == 1) {
                $('#userProfilePicturePreview').
                    attr('src', '/assets/icons/male.png')
            } else if (this.value == 2) {
                $('#userProfilePicturePreview').
                    attr('src', '/assets/icons/female.png')
            }
        }
    });
});
$(document).on('submit', '#adminAddForm', function (event) {
    event.preventDefault()
    let loadingButton = $('#btnSave')
    loadingButton.button('loading')
    let email = $('#email').val()
    let emailExp = new RegExp(
        /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i)
    let emailCheck = (email == '' ? true : (email.match(emailExp)
        ? true
        : false))
    if (!emailCheck) {
        displayErrorMessage(Lang.get('messages.validation.email_valid'))
        loadingButton.button('reset')
        return false
    }
    if ($('#error-msg').text() !== '') {
        $('#phoneNumber').focus()
        loadingButton.button('reset')
        return false
    }
    let phoneNumber = $('#phoneNumber').val()
    phoneNumber = phoneNumber.replace(/\s/g, '')
    $('#phoneNumber').val(phoneNumber)
    $('#adminAddForm')[0].submit()

    return true
})

$(document).on('submit', '#addusersForm', function (event) {
    event.preventDefault();
    let loadingButton = $('#btnSave');
    loadingButton.button('loading');
    let description = $('<div />').html($('#about').summernote('code'));
    let isEmpty = isOnlyContainWhiteSpace(description.text());
    let email = $('#email').val();
    let emailExp = new RegExp(
        /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
    let emailCheck = (email == '' ? true : (email.match(emailExp)
        ? true
        : false));
    if (!emailCheck) {
        displayErrorMessage(Lang.get('messages.validation.email_valid'))
        loadingButton.button('reset')
        return false;
    }
    if ($('#error-msg').text() !== '') {
        $('#phoneNumber').focus();
        loadingButton.button('reset');
        return false;
    }
    const isEditorEmpty = $('#about').summernote('isEmpty');
    if (isEditorEmpty || isEmpty) {
        $('#about').val(null);
    }

    let phoneNumber = $('#phoneNumber').val();
    phoneNumber = phoneNumber.replace(/\s/g, '');
    $('#phoneNumber').val(phoneNumber);

    $('#addusersForm')[0].submit();

    return true;
});

$(document).on('submit', '#editCompanyForm', function (event) {
    event.preventDefault();
    let loadingButton = $('#btnSave');
    loadingButton.button('loading');
    let description = $('<div />').html($('#editAbout').summernote('code'));
    let isEmpty = isOnlyContainWhiteSpace(description.text());
    let email = $('#editEmail').val();
    let emailExp = new RegExp(
        /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
    let emailCheck = (email == '' ? true : (email.match(emailExp)
        ? true
        : false));
    if (!emailCheck) {
        displayErrorMessage(Lang.get('messages.validation.email_valid'))
        loadingButton.button('reset')
        return false;
    }
    if ($('#error-msg').text() !== '') {
        $('#phoneNumber').focus();
        loadingButton.button('reset');
        return false;
    }
    const isEditorEmpty = $('#editAbout').summernote('isEmpty');
    if (isEditorEmpty || isEmpty) {
        $('#editAbout').val(null);
    }

    let phoneNumber = $('#phoneNumber').val();
    phoneNumber = phoneNumber.replace(/\s/g, '');
    $('#phoneNumber').val(phoneNumber);

    $('#editCompanyForm')[0].submit();

    return true;
});

$('#about, #editAbout').summernote({
    placeholder: Lang.get('messages.user.add_something_about_this'),
    height: '200',
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['height', ['height']],
        ['para', ['paragraph']]],
    disableResizeEditor: true,
});

$(document).on('change', '#userProfilePicture', function () {
    let validFile = isValidFile($(this),
        '#userProfilePictureValidationErrorsBox');
    if (validFile) {
        displayPhoto(this, '#userProfilePicturePreview');
    } else {
        $(this).val('');
    }
});
