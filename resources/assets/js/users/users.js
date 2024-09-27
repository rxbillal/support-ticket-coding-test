'use strict';

let tableName = '#usersTbl';

$(document).on('click', '.delete-btn', function (event) {
    let agentId = $(event.currentTarget).data('id');
    deleteItem(userUrl + '/' + agentId, tableName,
        Lang.get('messages.agent.agent_and_ticket'));
});

$(document).on('click', '.customer-delete-btn', function (event) {
    let agentId = $(event.currentTarget).data('id');
    deleteItem(userUrl + '/' + agentId, tableName,
        Lang.get('messages.customer.customer_and_ticket'));
});

$(document).ready(function () {
    $('#userRole').select2();

    $('.email-verification-toggle').on('change', function () {
        let userId = $(this).data('id');
        window.livewire.emit('setEmailVerified', userId, $(this).prop('checked'));
    });
});

window.addEventListener('successEmailVerification', function () {
    displaySuccessMessage(
        Lang.get('messages.success_message.user_email_verify'))
});

window.deleteItem = function (url, tableId, header, callFunction = null) {
    swal({
            title: Lang.get('messages.common.delete') + ' !',
            text: Lang.get('messages.common.are_you_sure_delete') + ' "' + header +
                '" ?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#00b074',
            cancelButtonColor: '#d33',
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonText: Lang.get('messages.common.yes'),
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
                location.reload();
            }
            swal({
                title: Lang.get('messages.common.deleted') + ' !',
                text: header + ' ' +
                    Lang.get('messages.common.has_been_deleted') +
                    '.',
                type: 'success',
                confirmButtonColor: '#00b074',
                confirmButtonText: Lang.get('messages.common.ok'),
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
                confirmButtonText: Lang.get('messages.common.ok'),
                timer: 5000,
            });
        },
    });
}
