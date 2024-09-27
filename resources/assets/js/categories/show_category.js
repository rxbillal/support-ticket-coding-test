$(document).ready(function (){
    'use strict';

    $(document).keydown(function (event) {
        if (event.keyCode === 27) {
            $('#editModal').modal('hide');
        }
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
            $('#editForm #editValidationErrorsBox').
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
            url: route('category.update', id),
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

    $('#editModal').on('hidden.bs.modal', function () {
        resetModalForm('#editForm', '#editValidationErrorsBox');
        editPickr.setColor('#33b0b0');
        editPickr.hide();
        $('#editColorError').text('');
    });

    function wc_hex_is_light (color) {
        const hex = color.replace('#', '');
        const c_r = parseInt(hex.substr(0, 2), 16);
        const c_g = parseInt(hex.substr(2, 2), 16);
        const c_b = parseInt(hex.substr(4, 2), 16);
        const brightness = ((c_r * 299) + (c_g * 587) + (c_b * 114)) / 1000;
        return brightness > 240;
    }
});
