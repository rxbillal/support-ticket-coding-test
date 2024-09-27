'use strict';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
});

$('input:text:not([readonly="readonly"])').first().focus();

$(function () {
    $('.modal').on('shown.bs.modal', function () {
        $(this).find('input:text').first().focus();
    });
});

window.resetModalForm = function (formId, validationBox) {
    $(formId)[0].reset();
    $('select.select2Selector').each(function (index, element) {
        let drpSelector = '#' + $(this).attr('id');
        $(drpSelector).val('');
        $(drpSelector).trigger('change');
    });
    $(validationBox).hide();
};

window.printErrorMessage = function (selector, errorResult) {
    $(selector).show().html('');
    $(selector).text(errorResult.responseJSON.message);
};

window.manageAjaxErrors = function (data) {
    var errorDivId = arguments.length > 1 && arguments[1] !== undefined
        ? arguments[1]
        : 'editValidationErrorsBox';
    if (data.status == 404) {
        iziToast.error({
            title: 'Error!',
            message: data.responseJSON.message,
            position: 'topRight',
        });
    } else {
        printErrorMessage('#' + errorDivId, data);
    }
};

window.displaySuccessMessage = function (message) {
    iziToast.success({
        title: Lang.get('messages.success_message.success'),
        message: message,
        position: 'topRight',
    });
};

window.displayErrorMessage = function (message) {
    iziToast.error({
        title: Lang.get('messages.error_message.error'),
        message: message,
        position: 'topRight',
    });
};

window.deleteItem = function (url, tableId, header, callFunction = null) {
    swal({
            title: deleteHeading + ' !',
            text: deleteMessage + ' "' + header + '" ?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#00b074',
            cancelButtonColor: '#d33',
            cancelButtonText: noMessages,
            confirmButtonText: yesMessages,
        },
        function () {
            deleteItemAjax(url, tableId, header, callFunction = null);
        });
};

function deleteItemAjax (url, tableId, header, callFunction = null) {
    $.ajax({
        url: url,
        type: 'DELETE',
        dataType: 'json',
        success: function (obj) {
            if (obj.success) {
                if ($(tableId).DataTable().data().count() == 1) {
                    $(tableId).DataTable().page('previous').draw('page');
                } else {
                    $(tableId).DataTable().ajax.reload(null, false);
                }
            }
            swal({
                title: 'Deleted!',
                text: header + ' has been deleted.',
                type: 'success',
                confirmButtonColor: '#00b074',
                timer: 2000,
            });
            if (callFunction) {
                eval(callFunction);
            }
        },
        error: function (data) {
            swal({
                title: '',
                text: data.responseJSON.message,
                type: 'error',
                confirmButtonColor: '#00b074',
                timer: 5000,
            });
        },
    });
}

window.format = function (dateTime) {
    var format = arguments.length > 1 && arguments[1] !== undefined
        ? arguments[1]
        : 'DD-MMM-YYYY';
    return moment(dateTime).format(format);
};

window.processingBtn = function (selecter, btnId, state = null) {
    var loadingButton = $(selecter).find(btnId);
    if (state === 'loading') {
        loadingButton.button('loading');
    } else {
        loadingButton.button('reset');
    }
};

window.prepareTemplateRender = function (templateSelector, data) {
    let template = $.templates(templateSelector);
    return template.render(data);
};

window.isValidFile = function (inputSelector, validationMessageSelector) {
    let ext = $(inputSelector).val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['png', 'jpg', 'jpeg']) == -1) {
        $(inputSelector).val('');
        $(validationMessageSelector).removeClass('d-none');
        $(validationMessageSelector).
            html('The image must be a file of type: jpeg, jpg, png.').
            show();
        return false;
    }
    $(validationMessageSelector).hide();
    return true;
};

window.displayPhoto = function (input, selector) {
    let displayPreview = true;
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            let image = new Image();
            image.src = e.target.result;
            image.onload = function () {
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
window.removeCommas = function (str) {
    return str.replace(/,/g, '');
};

window.isEmpty = (value) => {
    return value === undefined || value === null || value === '';
};

window.screenLock = function () {
    $('#overlay-screen-lock').show();
    $('body').css({ 'pointer-events': 'none', 'opacity': '0.6' });
};

window.screenUnLock = function () {
    $('body').css({ 'pointer-events': 'auto', 'opacity': '1' });
    $('#overlay-screen-lock').hide();
};

window.onload = function () {
    window.startLoader = function () {
        $('.infy-loader').show();
    };

    window.stopLoader = function () {
        $('.infy-loader').hide();
    };

// infy loader js
    stopLoader();
};

window.startLoader = function () {
    $('.infy-loader').show();
};

window.stopLoader = function () {
    $('.infy-loader').hide();
};

window.htmlSpecialCharsDecode = function (string) {
    return jQuery('<div />').html(string).text();
};

window.setLocalStorageItem = function (variable, data) {
    localStorage.setItem(variable, data);
};

window.getLocalStorageItem = function (variable) {
    return localStorage.getItem(variable);
};

window.removeLocalStorageItem = function (variable) {
    localStorage.removeItem(variable);
};
window.displayToastr = function (heading, icon, message) {
    iziToast.info({
        title: heading,
        message: message,
        position: 'topRight',
        icon: icon,
    });
};
window.getCookie = function (cname) {
    let name = cname + '=';
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '';
};
window.setCookie = function (cname, cvalue, exdays) {
    let d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = 'expires=' + d.toUTCString();
    document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/';
};

window.copyToClipboard = function (copyElementId, buttonSelector) {
    let element = $(copyElementId);
    let $temp = $('<input>');
    $('body').append($temp);
    $temp.val($(element).text()).select();
    document.execCommand('copy');
    $temp.remove();
    $(buttonSelector).toggleClass('d-none ');
    $('#copiedButton').toggleClass('d-none ');
};

$(document).ready(function () {
    $('.inset-0').delay(5000).slideUp(300);

    $('input[name="email"]').keyup(function () {
        this.value = this.value.toLowerCase();
    });
    $('input[name="email"]').keypress(function (e) {
        if (e.which === 32)
            return false;
    });
    $('input[type="password"]').keypress(function (e) {
        if (e.which === 32)
            return false;
    });
    setLocalTimeAll();
});

window.setLocalTimeAll = () => {
    $('[show-local-timeZone]').each(function (){
        let utcTime = $(this).attr('show-local-timeZone'); //M d, Y m:s
        $(this).text(moment.utc(utcTime).local().format('MMM DD, YYYY hh:mm'));
    })
}

window.showPassword = function (elementId) {
    let element = document.getElementById(elementId);
    if (element.type === 'password') {
        element.type = 'text';
        $(element).next().find('i').toggleClass('fa-eye-slash fa-eye');
    } else {
        element.type = 'password';
        $(element).next().find('i').toggleClass('fa-eye-slash fa-eye');
    }
};

document.addEventListener('livewire:load', function (event) {
    window.Livewire.hook('message.processed', () => {
        $('[data-toggle="tooltip"]').tooltip('dispose');
        $('[data-toggle="tooltip"]').tooltip();
        setLocalTimeAll();
    });
});

window.displayErrorMessage = function (message) {
    iziToast.error({
        title: Lang.get('messages.error_message.error'),
        message: message,
        position: 'topRight',
    });
};

window.isOnlyContainWhiteSpace = function (value) {
    return value.trim().replace(/ \r\n\t/g, '') === '';
};

$(document).on('click', '#readNotification', function (e) {
    e.preventDefault();
    e.stopPropagation();
    let notificationId = $(this).data('id');
    let notification = $(this);
    $.ajax({
        type: 'POST',
        url: route('read-notification', notificationId),
        data: { notificationId: notificationId },
        success: function () {
            let count = parseInt($('#header-notification-counter').text());
            $('#header-notification-counter').text(count - 1);
            notification.remove();
            let notificationCounter = document.getElementsByClassName(
                'readNotification').length;
            if (notificationCounter == 0) {
                $('#header-notification-counter').addClass('d-none');
                $('#readAllNotification').addClass('d-none');
                $('.empty-state').removeClass('d-none');
                $('.notification-toggle').removeClass('beep');
            }
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
    });
});

$(document).on('click', '#readAllNotification', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $.ajax({
        type: 'POST',
        url: route('read-all-notification'),
        success: function () {
            $('#header-notification-counter').text(0);
            $('#header-notification-counter').addClass('d-none');
            $('.readNotification').remove();
            $('#readAllNotification').addClass('d-none');
            $('.empty-state').removeClass('d-none');
            $('.notification-toggle').removeClass('beep');
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
    });
});
