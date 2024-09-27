'use strict';

$(document).ready(function () {
    new WOW().init();
    
    status = $('#statusFilterId').val();
    CategoryFilterId = $('#categoryFilterId').val();
    $('#categoryFilterId').on('change', function () {
        CategoryFilterId = $(this).val();
        window.livewire.emit('changeFilter', 'categoryFilter',
            $(this).val());
    });

    $('#statusFilterId').on('change', function () {
        status = $(this).val();
        window.livewire.emit('changeFilter', 'statusFilter',
            $(this).val());
    });
});

let CategoryFilterId = null;
let status = null;
document.addEventListener('livewire:load', function (event) {
    window.Livewire.hook('message.processed', () => {
        $('#categoryFilterId, #statusFilterId').select2({
            width: '100%',
        });
        $('#categoryFilterId').val(CategoryFilterId).trigger('change.select2');
        $('#statusFilterId').val(status).trigger('change.select2');
        setTimeout(function () { $('.alert').fadeOut('fast'); }, 4000);
    });
});

$(document).ready(function () {
    $('#statusFilterId').select2({
        width: '100%',
    });

    $('#categoryFilterId').select2({
        width: '100%',
        placeholder: 'Select Category',
        sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
    });
});

$(document).on('click', '.resetFilter', function () {
    $('#categoryFilterId').val(null).trigger('change');
    $('#statusFilterId').val(ticketStatus).trigger('change');
});
