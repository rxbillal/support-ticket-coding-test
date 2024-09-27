'use strict';

let categoryFilterId = null;
let statusFilter = null;
$(document).ready(function () {
    $('#categoryFilterId,#statusFilter').select2({
        width: '100%',
    });
    $('#ticketFilter').select2({
        width: '150px',
    });
    $('#txtEditAssignee').select2({
        width: '100%',
        placeholder: Lang.get('messages.ticket.select_agents'),
        sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
    });
    $('#categoryFilterId').on('change', function () {
        categoryFilterId = $(this).val();
        window.livewire.emit('changeFilter', 'categoryFilter', $(this).val());
    });
    $('#statusFilter').on('change', function () {
        statusFilter = $(this).val();
        window.livewire.emit('changeFilter', 'statusFilter', $(this).val());
    });
    if ($('#ticketFilter').val() != undefined) {
        statusFilter = $('#ticketFilter').val();
    }
    if ($('#statusFilter').val() != undefined) {
        statusFilter = $('#statusFilter').val();
    }
    $('#ticketFilter').on('change', function () {
        statusFilter = $(this).val();
        window.livewire.emit('changeFilter', 'ticketFilter', $(this).val());
    });
});

$(document).on('click', '.delete-btn', function (event) {
    let ticketId = $(this).attr('data-id');
    let alertMessage = '<div class="alert alert-warning swal__alert">\n' +
        '<strong class="swal__text-warning">' +
        deleteMessage + ' "' + Lang.get('messages.ticket.ticket') + '" ?' +
        '</strong><div class="swal__text-message">' +
        Lang.get('messages.ticket.by_deleting_this_ticket') + '.' +
        '</div></div>';
    swal({
            type: 'input',
            inputPlaceholder: Lang.get('messages.ticket.type_delete_for_deletion') +
                ' .',
            title: deleteHeading + ' !',
            text: alertMessage,
            html: true,
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#5cb85c',
            cancelButtonColor: '#d33',
            cancelButtonText: noMessages,
            confirmButtonText: yesMessages,
            imageUrl: baseUrl + 'assets/images/warning.png',
        },

        function (inputVal) {
            if (inputVal === false) {
                return false;
            }
            if (inputVal == '' || inputVal.toLowerCase() != 'delete') {
                swal.showInputError(
                    Lang.get('messages.ticket.type_delete_for_deletion') +
                    ' .');
                $('.sa-input-error').css('top', '23px!important');
                $(document).find('.sweet-alert.show-input :input').val('');
                return false;
            }
            if (inputVal.toLowerCase() === 'delete') {
                window.livewire.emit('deleteTicket', ticketId);
            }
        });
});

window.addEventListener('deleted', function (data) {
    if (data.detail === 'Ticket can\'t be deleted.') {
        swal({
            title: '',
            text: data.detail,
            type: 'error',
            confirmButtonColor: '#00b074',
            timer: 5000,
        });
    } else {
        swal({
            title: Lang.get('messages.common.deleted') + ' !',
            text: Lang.get('messages.ticket.ticket') + ' ' +
                Lang.get('messages.common.has_been_deleted') + '.',
            type: 'success',
            confirmButtonColor: '#00b074',
            confirmButtonText: Lang.get('messages.common.ok'),
            timer: 2000,
        });
    }
});

$(document).on('click', '.unassigned-btn', function (event) {
    let ticketId = $(this).attr('data-id');
    swal({
        title: Lang.get('messages.common.attention') + ' !',
        text: Lang.get('messages.ticket.unassigned_warning') + ' ?',
        type: 'info',
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
        confirmButtonColor: '#00b074',
        cancelButtonColor: '#d33',
        cancelButtonText: noMessages,
        confirmButtonText: yesMessages,
    }, function () {
        window.livewire.emit('unassignedTicket', ticketId);
    });
});

window.addEventListener('unassignedFromTicket', function (data) {
    swal({
        title: Lang.get('messages.ticket.unassigned_ticket') + ' !',
        text: Lang.get('messages.ticket.unassigned_from_ticket') + '.',
        type: 'success',
        confirmButtonColor: '#00b074',
        confirmButtonText: Lang.get('messages.common.ok'),
        timer: 2000,
    });
});

$(document).on('click', '.change-status', function (event) {
    let ticketId = $(this).attr('data-id');
    let ticketStatus = $(this).attr('data-status');
    swal({
        title: Lang.get('messages.common.attention') + ' !',
        text: Lang.get('messages.ticket.sure_for_change_status') + ' ?',
        type: 'info',
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
        confirmButtonColor: '#00b074',
        cancelButtonColor: '#d33',
        cancelButtonText: noMessages,
        confirmButtonText: yesMessages,
    }, function () {
        window.livewire.emit('changeStatus', ticketId, ticketStatus);
    });

});

window.addEventListener('closeAlert', function () {
    swal.close();
});

document.addEventListener('livewire:load', function (event) {
    window.Livewire.hook('message.processed', () => {
        $('#categoryFilterId,#statusFilter').select2({
            width: '100%',
        });
        $('#ticketFilter').select2({
            width: '150px',
        });
        $('[data-toggle="tooltip"]').tooltip();
        $('#categoryFilterId').val(categoryFilterId).trigger('change.select2');
        $('#statusFilter').val(statusFilter).trigger('change.select2');
        $('#ticketFilter').val(statusFilter).trigger('change.select2');
    });
});

$(document).on('click', '.resetFilter', function () {
    categoryFilterId = null;
    $('#categoryFilterId').val(null).trigger('change');
    $('#statusFilter').val(activeStatus).trigger('change');
    $('#ticketFilter').val(activeStatus).trigger('change');
});

$(document).on('click', '.edit-ticket-assignees', function (event) {
    let id = $(event.currentTarget).attr('data-id');
    startLoader();
    $.ajax({
        url: ticketUrl + id + '/edit-assignees',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                let ticket = result.data.ticket;
                const users = result.data.users;
                $('#txtEditAssignee').empty();
                for (const key in users) {
                    if (users.hasOwnProperty(key)) {
                        $('#txtEditAssignee').
                            append($('<option>',
                                { value: key, text: users[key] }));
                    }
                }
                $('#hiddenTicketId').val(ticket.id);

                let userIds = result.data.assignUsers;

                $('#txtEditAssignee').val(userIds).trigger('change');
                setTimeout(function () {
                    $('#txtEditAssignee').
                        val(result.data.assignUsers).
                        trigger('change');
                }, 1000);

                stopLoader();
                $('#EditAssigneeModal').appendTo('body').modal('show');
            }
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
    });
});

$(document).on('click', '#EditAssigneeModal #btnSaveAssignees', function () {
    let loadingButton = jQuery(this);
    loadingButton.button('loading');
    window.livewire.emit('updateAssignees', $('#txtEditAssignee').val(),
        $('#hiddenTicketId').val());
});

window.addEventListener('assigneeUpdated', function () {
    let loadingButton = $('#btnSaveAssignees');
    $('#EditAssigneeModal').modal('hide');
    $('#EditAssigneeModal').on('hidden.bs.modal', function () {
        loadingButton.button('reset')
    })
    displaySuccessMessage(
        Lang.get('messages.toast_message.ticket_agent_update'))
});
