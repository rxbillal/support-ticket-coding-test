'use strict';

const pickr = Pickr.create({
    el: '.color-wrapper',
    theme: 'nano', // or 'monolith', or 'nano'
    closeWithKey: 'Enter',
    autoReposition: true,
    defaultRepresentation: 'HEX',
    position: 'bottom-end',
    default: '#33b0b0',
    swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 1)',
        'rgba(156, 39, 176, 1)',
        'rgba(103, 58, 183, 1)',
        'rgba(63, 81, 181, 1)',
        'rgba(33, 150, 243, 1)',
        'rgba(3, 169, 244, 1)',
        'rgba(0, 188, 212, 1)',
        'rgba(0, 150, 136, 1)',
        'rgba(76, 175, 80, 1)',
        'rgba(139, 195, 74, 1)',
        'rgba(205, 220, 57, 1)',
        'rgba(255, 235, 59, 1)',
        'rgba(255, 193, 7, 1)',
    ],

    components: {
        // Main components
        preview: true,
        hue: true,

        // Input / output Options
        interaction: {
            input: true,
            clear: false,
            save: false,
        },
    },
});

pickr.on('change', function () {
    const color = pickr.getColor().toHEXA().toString();
    if (wc_hex_is_light(color)) {
        $('#validationErrorsBox').
            html('Pick a different color').
            show();
        $(':input[id="btnSave"]').prop('disabled', true);
        setTimeout(function () {
            $('.alert').slideUp(300);
        }, 3000);
        return;
    }
    $('#validationErrorsBox').hide();
    $(':input[id="btnSave"]').prop('disabled', false);
    pickr.setColor(color);
    $('#color').val(color);
});

$(document).keydown(function (event) {
    if (event.keyCode === 27) {
        $('#addModal').modal('hide');
        $('#editModal').modal('hide');
    }
});

function wc_hex_is_light (color) {
    const hex = color.replace('#', '');
    const c_r = parseInt(hex.substr(0, 2), 16);
    const c_g = parseInt(hex.substr(2, 2), 16);
    const c_b = parseInt(hex.substr(4, 2), 16);
    const brightness = ((c_r * 299) + (c_g * 587) + (c_b * 114)) / 1000;
    return brightness > 240;
}

pickr.on('init', instance => {
    const color = pickr.getColor().toHEXA().toString();
    pickr.setColor(color);
    $('#color').val(color);
});

const editPickr = Pickr.create({
    el: '.color-wrapper',
    theme: 'nano', // or 'monolith', or 'nano'
    closeWithKey: 'Enter',
    autoReposition: true,
    defaultRepresentation: 'HEX',
    position: 'bottom-end',
    swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 1)',
        'rgba(156, 39, 176, 1)',
        'rgba(103, 58, 183, 1)',
        'rgba(63, 81, 181, 1)',
        'rgba(33, 150, 243, 1)',
        'rgba(3, 169, 244, 1)',
        'rgba(0, 188, 212, 1)',
        'rgba(0, 150, 136, 1)',
        'rgba(76, 175, 80, 1)',
        'rgba(139, 195, 74, 1)',
        'rgba(205, 220, 57, 1)',
        'rgba(255, 235, 59, 1)',
        'rgba(255, 193, 7, 1)',
    ],

    components: {
        // Main components
        preview: true,
        hue: true,

        // Input / output Options
        interaction: {
            input: true,
            clear: false,
            save: false,
        },
    },
});

editPickr.on('change', function () {
    const color = editPickr.getColor().toHEXA().toString();
    if (wc_hex_is_light(color)) {
        $('#editValidationErrorsBox').
            html('Pick a different color').
            show();
        $(':input[id="btnEditSave"]').prop('disabled', true);
        setTimeout(function () {
            $('.alert').slideUp(300);
        }, 3000);
        return;
    }
    $('#editValidationErrorsBox').hide();
    $(':input[id="btnEditSave"]').prop('disabled', false);
    editPickr.setColor(color);
    $('#edit_color').val(color);
});

let picked = false;

$(document).on('click', '#color', function () {
    picked = true;
});
$(document).on('click', '.addModal', function () {
    $('#addModal').appendTo('body').modal('show');
});

$(document).on('submit', '#addNewForm', function (e) {
    if ($('#color').val() == '') {
        displayErrorMessage('Please select your color.');
        return false;
    }
    e.preventDefault();
    processingBtn('#addNewForm', '#btnSave', 'loading');
    $.ajax({
        url: categorySaveUrl,
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addModal').modal('hide');
                location.reload(true);
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#addNewForm', '#btnSave');
        },
    });
});

$(document).on('click', '.edit-btn', function (event) {
    let categoryId = $(event.currentTarget).attr('data-id');
    renderData(categoryId);
});

window.renderData = function (id) {
    $.ajax({
        url: categoryUrl + '/' + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#categoryId').val(result.data.id);
                $('#editName').val(result.data.name);
                $('#editColor').val(result.data.color);
                editPickr.setColor(result.data.color);
                $('#editModal').appendTo('body').modal('show');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
};
$(document).on('submit', '#editForm', function (event) {
    event.preventDefault();
    processingBtn('#editForm', '#btnEditSave', 'loading');
    const id = $('#categoryId').val();
    $.ajax({
        url: categoryUrl + '/' + id,
        type: 'put',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#editModal').modal('hide');
                location.reload(true);
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#editForm', '#btnEditSave');
        },
    });
});

$('#addModal').on('hidden.bs.modal', function () {
    resetModalForm('#addNewForm', '#validationErrorsBox');
    pickr.setColor('#33b0b0');
    pickr.hide();
    $('#colorError').text('');
});

$('#editModal').on('hidden.bs.modal', function () {
    resetModalForm('#editForm', '#editValidationErrorsBox');
    pickr.setColor('#33b0b0');
    pickr.hide();
    $('#editColorError').text('');
});

$(document).on('click', '.delete-btn', function (event) {
    let categoryId = $(this).attr('data-id');
    swal({
            title: deleteHeading + ' !',
            text: deleteMessage + ' "' + Lang.get('messages.category.category') +
                '" ?',
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
            window.livewire.emit('deleteCategory', categoryId);
        });
});

window.addEventListener('deleted', function (data) {
    if (data.detail === 'Category can\'t be deleted.') {
        swal({
            title: '',
            text: data.detail,
            type: 'error',
            confirmButtonColor: '#00b074',
            confirmButtonText: Lang.get('messages.common.ok'),
            timer: 5000,
        });
    } else {
        swal({
            title: Lang.get('messages.common.deleted') + ' !',
            text: Lang.get('messages.category.category') + ' ' +
                Lang.get('messages.common.has_been_deleted') + '.',
            type: 'success',
            confirmButtonColor: '#00b074',
            confirmButtonText: Lang.get('messages.common.ok'),
            timer: 2000,
        });
    }
});
