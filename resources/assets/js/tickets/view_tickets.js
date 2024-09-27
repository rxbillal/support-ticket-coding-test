'use strict';

Dropzone.autoDiscover = false;
let attachmentDropzone = '', editAttachmentDropzone = '', comment = '',
    replyFormId = '#addRelyForm';
let loadingButton = $('#addTicketReply');
let primaryColor = window.getComputedStyle(document.documentElement).
    getPropertyValue('--primary');

if ($('#addAttachmentDropzone').length) {
    let dropzone = new Dropzone('#addAttachmentDropzone', {
        autoProcessQueue: false,
        thumbnailWidth: 125,
        acceptedFiles: 'image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.ppt,.pptx,.sql,.json',
        timeout: 50000,
        parallelUploads: 10, // Number of files process at a time (default 2)
        addRemoveLinks: true,
        dictRemoveFile: '<i class="fa fa-trash-o text-danger" title="Remove"></i>',
        uploadMultiple: true,
        init: function () {
            attachmentDropzone = this; // closure
            let saveButton = document.querySelector('#save-file');
            let cancelButton = document.querySelector('#cancel-upload-file');

            saveButton.addEventListener('click', function () {
                $('#addAttachment').modal('toggle');
            });
            cancelButton.addEventListener('click', function () {
                attachmentDropzone.removeAllFiles(true);
                $('#addAttachment').modal('toggle');
            });

            // show the submit button only when files are dropped here:
            this.on('addedfile', function (file, dataUrl, mediaId = null) {
                if(file.size > (1024 * 1024 * 10))
                {
                    this.removeFile(file)
                    displayToastr(Lang.get('messages.error_message.error'),
                        'error',
                        Lang.get('messages.validation.file_size'))
                    return false;
                }
                $('.dz-progress').hide();
                $('.dz-remove').html('');
                $('.dz-remove').addClass('fas fa-trash text-danger mt-3');
                $('.dz-remove').prop('title', 'Delete');
                previewFile(file, dataUrl, mediaId);
            });

            this.on('sending', function (file, xhr, formData) {
                // Append all form inputs to the formData Dropzone will POST
                let data = $(replyFormId).serializeArray();
                $.each(data, function (key, el) {
                    formData.append(el.name, el.value);
                });
            });

            function previewFile (file, dataUrl, mediaId) {
                let downloadPath = dataUrl;
                let ext = file.name.split('.').pop();
                if (['pdf'].includes(ext)) {
                    $(file.previewElement).
                        find('.dz-image img').
                        attr('src', '/assets/img/pdf_icon.png');
                } else if (['doc', 'docx'].includes(ext)) {
                    $(file.previewElement).
                        find('.dz-image img').
                        attr('src', '/assets/img/doc_icon.png');
                } else if (['xls', 'xlsx', 'csv'].includes(ext)) {
                    $(file.previewElement).
                        find('.dz-image img').
                        attr('src', '/assets/img/xls_icon.png');
                } else if (['text', 'txt'].includes(ext)) {
                    $(file.previewElement).
                        find('.dz-image img').
                        attr('src', '/assets/img/txt_icon.png');
                }
                $('.dz-image').
                    last().
                    find('img').
                    attr({ width: '100%', height: '100%' });
            }
        },
        complete: function (file) {
            if (this.getQueuedFiles().length > 0) {
                this.processQueue();
            }
        },
        // success: function (file, response) {
        successmultiple: function (file, response) {
            $('#addAttachment').modal('hide');
            loadingButton.button('reset');
            appendReply(response);
            $('#ticketAddReplay').slideToggle();
        },
        error: function (file, response, xhr) {
            // attachmentDropzone.options.processQueue = false;
            attachmentDropzone.removeAllFiles(true);
            processingBtn('#addRelyForm', '#addTicketReply');
            if (xhr == null) this.removeFile(file);
            this.processQueue = false;
            if (response.message != undefined) {
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    response.message)
            } else {
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    response)
            }
        },
    });
}

if ($('#editAttachmentDropzone').length) {
    let dropzone = new Dropzone('#editAttachmentDropzone', {
        autoProcessQueue: false,
        thumbnailWidth: 125,
        acceptedFiles: 'image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.ppt,.pptx,.sql,.json',
        timeout: 50000,
        parallelUploads: 10, // Number of files process at a time (default 2)
        addRemoveLinks: true,
        dictRemoveFile: '<i class="fa fa-trash-o text-danger" title="Remove"></i>',
        uploadMultiple: true,
        init: function () {
            editAttachmentDropzone = this; // closure
            let saveButton = document.querySelector('#edit-save-file');
            let cancelButton = document.querySelector(
                '#edit-cancel-upload-file');

            saveButton.addEventListener('click', function () {
                $('#editAttachment').modal('toggle');
            });
            cancelButton.addEventListener('click', function () {
                editAttachmentDropzone.removeAllFiles(true);
                $('#editAttachment').modal('toggle');
            });

            // show the submit button only when files are dropped here:
            this.on('addedfile', function (file, dataUrl, mediaId = null) {
                $('.dz-progress').hide();
                $('.dz-remove').html('');
                $('.dz-remove').addClass('fas fa-trash text-danger mt-3');
                $('.dz-remove').prop('title', 'Delete');
                previewFile(file, dataUrl, mediaId);
            });

            this.on('sending', function (file, xhr, formData) {
                // Append all form inputs to the formData Dropzone will POST
                formData.append('description', comment);
                formData.append('ticket_id', ticketId);
            });

            function previewFile (file, dataUrl, mediaId) {
                let downloadPath = dataUrl;
                let ext = file.name.split('.').pop();
                if (['pdf'].includes(ext)) {
                    $(file.previewElement).
                        find('.dz-image img').
                        attr('src', '/assets/img/pdf_icon.png');
                } else if (['doc', 'docx'].includes(ext)) {
                    $(file.previewElement).
                        find('.dz-image img').
                        attr('src', '/assets/img/doc_icon.png');
                } else if (['xls', 'xlsx', 'csv'].includes(ext)) {
                    $(file.previewElement).
                        find('.dz-image img').
                        attr('src', '/assets/img/xls_icon.png');
                } else if (['text', 'txt'].includes(ext)) {
                    $(file.previewElement).
                        find('.dz-image img').
                        attr('src', '/assets/img/txt_icon.png');
                }
                $('.dz-image').
                    last().
                    find('img').
                    attr({ width: '100%', height: '100%' });
            }
        },
        complete: function (file) {
            if (this.getQueuedFiles().length > 0) {
                this.processQueue();
            }
        },
        successmultiple: function (file, response) {
            editAttachmentDropzone.removeAllFiles(true);
            $('#editAttachment').modal('hide');
            $('#editTicketReply').button('reset');
            if ($('#' + replyId + '-attachment-div').length == 0) {
                let mainDiv = '<div class="reply-attached-files" id="' +
                    replyId +
                    '-attachment-div"><label class="ml-3 text-muted">Attachments:</label></div>';
                $('.' + replyId + '-attachment-main-div').append(mainDiv);
            }
            $.each(response.data, function (key, el) {
                let html = '<div class="ml-3 mb-1"><a target="_blank" href="' +
                    el.url + '" class="text-muted">' + el.file_name +
                    '</a><a href="javascript:void(0)" class="remove-attached-file text-muted ml-1" data-media-id="' +
                    el.id + '"><i class="far fa-times-circle"></i></a></div>';

                $('#' + replyId + '-attachment-div').append(html);
            });

            afterUpdateTicketReply();
        },
        error: function (file, response, xhr) {
            if (xhr == null) this.removeFile(file);
            this.processQueue = false;
            if (response.message != undefined) {
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    response.message)
            } else {
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    response)
            }
        },
    });
}

$(document).ready(function () {
    'use strict';
    $(document).on('click', '#attachmentButton', function () {
        $('#addAttachment').appendTo('body').modal('show');
    });

    $(document).on('click', '#editAttachmentBtn', function () {
        $('#editAttachment').appendTo('body').modal('show');
    });

    $(document).on('mouseenter', '.ticket-attachment', function () {
        $(this).find('.ticket-attachment__icon').removeClass('d-none');
    });

    $(document).on('mouseleave', '.ticket-attachment', function () {
        $(this).find('.ticket-attachment__icon').addClass('d-none');
    });
});
$(document).on('click', '.delete-replay', function (event) {
    let replayId = $(event.currentTarget).data('id');
    let url = deleteTicketReplyUrl + replayId;
    deleteItem(url, 'Replay', '.activities', '.activity', '#notfoundReplay');
});

$(document).on('click', '#btnPostReplay', function (event) {
    $('#ticketAddReplay').slideToggle();
});
$(document).on('click', '#btnCancelReplay', function (event) {
    $('#addReplyContainer').summernote('reset');
    attachmentDropzone.removeAllFiles(true);
    $('#ticketAddReplay').slideToggle();
});

// Summernote editor initialization scripts
let addReply = $('#addReplyContainer').summernote({
    placeholder: Lang.get('messages.placeholder.add_ticket_reply'),
    height: '200px',
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]],
    disableResizeEditor: true,
});

let editReply = $('.editReplyContainer').summernote({
    height: '200px',
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]],
    disableResizeEditor: true,
});

let replyId;
$(document).on('submit', replyFormId, function (e) {
    e.preventDefault();
    let descriptionValue = addReply.summernote('code');
    let description = $('<div />').html(addReply.summernote('code'));
    let empty = isOnlyContainWhiteSpace(description.text());
    if (addReply.summernote('isEmpty')) {
        displayErrorMessage(
            Lang.get('messages.validation.ticket_reply_required'))

        $('#addReplyContainer').val('');
        return false;
    } else if (empty) {
        displayErrorMessage(
            Lang.get('messages.validation.ticket_reply_white_space'))
        return false;
    }
    loadingButton.button('loading');
    if ($('.dz-preview').length == 0) {
        submitAddReplyForm(descriptionValue, $(this).serialize());
    } else {
        attachmentDropzone.processQueue();
        return true;
    }
});

function submitAddReplyForm (descriptionValue, data) {
    $.ajax({
        url: addReplyUrl,
        type: 'post',
        data: data,
        success: function (result) {
            if (result.success) {
                appendReply(result);
            }
            loadingButton.button('reset');
            $('#ticketAddReplay').slideToggle();
        },
        error: function (result) {
            loadingButton.button('reset');
            printErrorMessage('#taskValidationErrorsBox', result);
        },
    });
}

function appendReply (result) {
    attachmentDropzone.removeAllFiles(true);
    addReply.summernote('code', '');
    $('.ticket-reply-box').prepend(result.data.html);
    $('#editReply-' + result.data.id).summernote({
        height: '200px',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['paragraph']]],
        disableResizeEditor: true,
    });
}

$(document).on('click', '#editTicketReply', function (event) {
    comment = $('#editReply-' + replyId).summernote('code');

    let description = $('<div />').
        html($('#editReply-' + replyId).summernote('code'));
    let empty = isOnlyContainWhiteSpace(description.text());
    if ($('#editReply-' + replyId).summernote('isEmpty')) {
        $('#editReplyContainer').val('');

        return false;
    } else if (empty) {
        displayErrorMessage(
            Lang.get('messages.validation.ticket_reply_white_space'))

        return false;
    }
    let loadingButton = $(this);
    loadingButton.button('loading');

    if ($('.dz-preview').length == 0) {
        $.ajax({
            url: baseUrl + 'reply-update/' + replyId,
            type: 'put',
            data: { 'description': comment, 'ticket_id': ticketId },
            success: function (result) {
                if (result.success) {
                    afterUpdateTicketReply();
                }
                loadingButton.button('reset');
            },
            error: function (result) {
                loadingButton.button('reset');
                printErrorMessage('#taskValidationErrorsBox', result);
            },
        });
    } else {
        editAttachmentDropzone.processQueue();
        return true;
    }
});

function afterUpdateTicketReply () {
    let currentTime = moment().fromNow();
    editReply.summernote('code', '');
    $('.description-' + replyId).html(comment);
    $('.replyTime-' + replyId).html(currentTime);
    $('.description-' + replyId).removeClass('d-none');
    $('#editTicketReply-' + replyId).addClass('d-none');
}

$(document).on('click', '.del-reply', function (event) {
    let replyId = $(this).data('id');
    swal({
            title: Lang.get('messages.swal_message.delete'),
            text: Lang.get('messages.swal_message.reply_are_you_sure'),
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: primaryColor,
            cancelButtonColor: '#d33',
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonText: Lang.get('messages.common.yes'),
        },
        function () {
            $.ajax({
                url: deleteTicketReplyUrl + replyId,
                type: 'DELETE',
                data: { 'ticket_id': replyId },
                success: function (result) {
                    if (result.success) {
                        $('.ticket-reply-box').
                            find(`[data-remove-id='${replyId}']`).
                            remove();
                        swal({
                            title: Lang.get('messages.swal_message.delete'),
                            text: Lang.get(
                                'messages.swal_message.reply_delete'),
                            type: 'success',
                            confirmButtonColor: primaryColor,
                            confirmButtonText: Lang.get('messages.common.ok'),
                            timer: 2000,
                        });
                    }
                },
                error: function (data) {
                    swal({
                        title: '',
                        text: data.responseJSON.message,
                        type: 'error',
                        confirmButtonColor: primaryColor,
                        timer: 5000,
                    });
                },
            });
        });
});

let quillCommentEdit = [];
$(document).on('click', '.edit-reply', function () {
    replyId = $(this).data('id');
    $('#editAttachmentReplyId').val(replyId);
    let replyData = $.trim($('.description-' + replyId).html());
    $('.editReplyBox').addClass('d-none');

    $('.reply-description').removeClass('d-none');
    $('.description-' + replyId).addClass('d-none');

    $('#editTicketReply-' + replyId).toggleClass('d-none ');
    $('#editReply-' + replyId).summernote('code', '');
    $('#editReply-' + replyId).summernote('code', replyData);
});

$(document).on('click', '.cancelEditReply', function () {
    replyId = $(this).data('id');
    $('.description-' + replyId).removeClass('d-none');
    $('#editTicketReply-' + replyId).addClass('d-none');
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
            confirmButtonColor: primaryColor,
            cancelButtonColor: '#d33',
            cancelButtonText: noMessages,
            confirmButtonText: yesMessages,
        },
        function () {
            $.ajax({
                url: updateTicketUrl,
                type: 'put',
                data: {
                    'ticket': updateTicketUrl,
                    'ticket_status': ticketStatus,
                },
                success: function (result) {
                    if (result.success) {
                        swal({
                            title: Lang.get('messages.swal_message.updated'),
                            text: Lang.get(
                                'messages.swal_message.status_changed'),
                            type: 'success',
                            confirmButtonText: Lang.get('messages.common.ok'),
                            timer: 2000,
                        });
                        location.reload();
                    }
                },
                error: function (data) {
                    swal({
                        title: '',
                        text: data.responseJSON.message,
                        type: 'error',
                        confirmButtonText: Lang.get('messages.common.ok'),
                        timer: 5000,
                    });
                },
            });
        });
});

$(document).on('click', '.remove-attached-file', function (event) {
    event.preventDefault();
    let id = $(this).attr('data-media-id');
    let parentDiv = $(this).parent('div');
    let mainParentDiv = $(this).parent('div').parent();
    swal({
            title: Lang.get('messages.common.delete') + ' !',
            text: Lang.get('messages.common.are_you_sure_delete') + ' "' +
                Lang.get('messages.ticket.attachment') + '" ?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: primaryColor,
            cancelButtonColor: '#d33',
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonText: Lang.get('messages.common.yes'),
        },
        function () {
            deleteAttachedFile(id, parentDiv, mainParentDiv);
        });
});

function deleteAttachedFile (id, parentDiv, mainParentDiv) {
    $.ajax({
        url: route('ticket.replay.attachment.delete', id),
        type: 'DELETE',
        dataType: 'json',
        success: function (obj) {
            if (obj.success) {
                parentDiv.remove();
                if ($('.reply-attached-files').
                    find('.remove-attached-file').length == 0) {
                    mainParentDiv.remove();
                }
            }
            swal({
                title: Lang.get('messages.common.delete') + ' !',
                text: Lang.get('messages.ticket.attachment') + ' ' +
                    Lang.get('messages.common.has_been_deleted') + '.',
                type: 'success',
                confirmButtonColor: primaryColor,
                confirmButtonText: Lang.get('messages.common.ok'),
                timer: 2000,
            });
        },
        error: function (data) {
            swal({
                title: '',
                text: data.responseJSON.message,
                type: 'error',
                confirmButtonColor: primaryColor,
                timer: 5000,
            });
        },
    });
}

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
        $.ajax({
            type: 'POST',
            url: route('ticket.unassigned'),
            data: {
                id: ticketId,
            },
            success: function (data) {
                if (data.success) {
                    swal({
                        title: Lang.get('messages.ticket.unassigned_ticket') +
                            ' !',
                        text: data.message,
                        type: 'success',
                        confirmButtonColor: '#00b074',
                        confirmButtonText: Lang.get('messages.common.ok'),
                        timer: 1800,
                    });
                    setTimeout(function () {
                        location.href = $('#cancelBtn').attr('href');
                    }, 2000);
                }
            },
        });
    });
});

$(document).on('click', '.delete-btn', function (event) {

    let ticketId = $(this).attr('data-id');
    let alertMessage = '<div class="alert alert-warning swal__alert">\n' +
        '<strong class="swal__text-warning">' +
        Lang.get('messages.common.are_you_sure_delete') + ' "' +
        Lang.get('messages.ticket.ticket') + '" ?' +
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
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonText: Lang.get('messages.common.yes'),
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
                $.ajax({
                    type: 'DELETE',
                    url: ticketDeleteUrl,
                    data: {
                        id: ticketId,
                    },
                    success: function (data) {
                        if (data.success) {
                            swal({
                                title: Lang.get('messages.common.deleted') +
                                    ' !',
                                text: Lang.get('messages.ticket.ticket') + ' ' +
                                    Lang.get(
                                        'messages.common.has_been_deleted') +
                                    '.',
                                type: 'success',
                                confirmButtonColor: primaryColor,
                                confirmButtonText: Lang.get(
                                    'messages.common.ok'),
                                timer: 2000,
                            });
                            setTimeout(function () {
                                location.href = $('#cancelBtn').attr('href');
                            }, 2000);
                        }
                    },
                });
            }
        });
});
