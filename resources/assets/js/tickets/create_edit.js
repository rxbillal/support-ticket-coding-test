'use strict';

let attachmentDropzone = '';
let editAttachmentDropzone = '';
let submittedFormId = '';
let deletedMediaIds = [];
let errorCounter = 0;
Dropzone.autoDiscover = false;

if ($('#addAttachmentDropzone').length) {
    let dropzone = new Dropzone('#addAttachmentDropzone', {
        thumbnailWidth: 125,
        acceptedFiles: 'image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.ppt,.pptx,.sql,.json',
        timeout: 50000,
        autoProcessQueue: false,
        parallelUploads: 50, // Number of files process at a time (default 2)
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
                $('#attachment-counter').html(attachmentDropzone.files.length);
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
                $('#attachment-counter').html(attachmentDropzone.files.length);
                $('.dz-progress').hide();
                $('.dz-remove').html('');
                $('.dz-remove').addClass('fas fa-trash text-danger mt-3');
                $('.dz-remove').prop('title', 'Delete');
                previewFile(file, dataUrl, mediaId);
            });

            this.on('sending', function (file, xhr, formData) {
                // Append all form inputs to the formData Dropzone will POST
                let data = $(submittedFormId).serializeArray();
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
        removedfile: function (file) {
            let attachmentCounter = parseInt($('#attachment-counter').html());
            if (attachmentCounter > 0) {
                $('#attachment-counter').html(attachmentCounter - 1);
                let fileRef;
                return (fileRef = file.previewElement) != null
                    ?
                    fileRef.parentNode.removeChild(file.previewElement)
                    : void 0;
            }
        },
        success: function (file, response) {
            let data = response.data;
            $('#addAttachment').modal('hide');
            errorCounter = 0;
            window.location.replace(data.redirectUrl);
        },
        error: function (file, response, xhr) {
            // attachmentDropzone.options.processQueue = false;
            attachmentDropzone.removeAllFiles(true);
            $('#attachment-counter').html(attachmentDropzone.files.length);
            processingBtn('#addTicketForm', '#btnSave');
            if (response.message != undefined) {
                if (errorCounter == 2) return;
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    response.message)
                errorCounter = 2
                setTimeout(function (){
                    errorCounter = 0;
                }, 1000);
                
            } else {
                if (errorCounter) return;
                if (xhr == null) this.removeFile(file);
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    response)
                errorCounter = 1
            }
        },
    });
}

if ($('#editAttachmentDropzone').length) {
    let editDropzone = new Dropzone('#editAttachmentDropzone', {
        // maxFilesize: 12,
        thumbnailWidth: 125,
        acceptedFiles: 'image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.ppt,.pptx,.sql,.json',
        addRemoveLinks: true,
        dictRemoveFile: '<i class="fa fa-trash-o text-danger" title="Remove"></i>',
        timeout: 50000,
        autoProcessQueue: false,
        parallelUploads: 50, // Number of files process at a time (default 2)
        uploadMultiple: true,
        init: function () {
            editAttachmentDropzone = this;
            let saveButton = document.querySelector('#save-file');
            let cancelButton = document.querySelector('#cancel-upload-file');

            saveButton.addEventListener('click', function () {
                $('#editAttachment').modal('toggle');
            });
            cancelButton.addEventListener('click', function () {
                editAttachmentDropzone.removeAllFiles(true);
                $('#attachment-counter').html(0);
                deletedMediaIds = [];
                $('div.dz-preview').remove();
                getAttachment();
                $('#editAttachment').modal('toggle');
            });
            getAttachment();

            function getAttachment () {
                $.get(getAttachmentUrl, function (data) {
                    $.each(data.data, function (key, value) {
                        $('#attachment-counter').html(parseInt($('#attachment-counter').html()) + 1);
                        let mockFile = { name: value.name, id: value.id };

                        editAttachmentDropzone.options.addedfile.call(
                            editAttachmentDropzone, mockFile, mockFile.id);
                        editAttachmentDropzone.options.thumbnail.call(
                            editAttachmentDropzone, mockFile,
                            value.url);
                        editAttachmentDropzone.emit('complete', mockFile);
                        editAttachmentDropzone.emit('thumbnail', mockFile,
                            value.url, mockFile.id);
                        $('.dz-remove').eq(key).attr('data-file-id', value.id);
                        $('.dz-remove').
                            eq(key).
                            attr('data-file-url', value.url);
                        $('.dz-remove').html('');
                        $('.dz-progress').hide();
                        $('.dz-remove').
                            addClass('fas fa-trash text-danger mt-3');
                        $('.dz-remove').prop('title', 'Delete');
                    });
                });
            }

            this.on('thumbnail', function (file, dataUrl, mediaId = null) {
                $(file.previewTemplate).
                    find('.dz-details').
                    css('display', 'none');
                previewFile(file, dataUrl, mediaId);
                let fileNameExtArr = file.name.split('.');
                let fileName = fileNameExtArr[0].replace(/\s/g, '').
                    replace(/\(/g, '_').
                    replace(/\)/g, '');
                let ext = file.name.split('.').pop();
                let previewEle = '';
                let clickDownload = true;
                $(file.previewElement).
                    find('.download-link').
                    on('click', function () {
                        clickDownload = false;
                    });
                if ($.inArray(ext, ['jpg', 'JPG', 'jpeg', 'png', 'PNG']) > -1) {
                    previewEle = '<a class="' + fileName +
                        '" data-fancybox="gallery" href="' + dataUrl +
                        '" data-toggle="lightbox" data-gallery="example-gallery"></a>';
                    $('.previewEle').append(previewEle);
                }

                file.previewElement.addEventListener('click', function () {
                    if (clickDownload) {
                        let fileName = file.previewElement.querySelector(
                            '[data-dz-name]').innerHTML;
                        let fileExt = fileName.split('.').pop();
                        if ($.inArray(fileExt,
                            ['jpg', 'JPG', 'jpeg', 'png', 'PNG']) >
                            -1) {
                            let onlyFileName = fileName.split('.')[0];
                            $(document.getElementsByClassName(onlyFileName)).
                                trigger('click');
                        } else {
                            window.open(dataUrl, '_blank');
                        }
                    }
                    clickDownload = true;
                });
            });
            this.on('addedfile', function (file, dataUrl, mediaId = null) {
                $(file.previewTemplate).
                    find('.dz-remove').
                    attr('data-file-id', '00');
                if(file.size > (1024 * 1024 * 10))
                {
                    this.removeFile(file)
                    displayToastr(Lang.get('messages.error_message.error'),
                        'error',
                        Lang.get('messages.validation.file_size'))
                    return false;
                }
                $('#attachment-counter').html(parseInt($('#attachment-counter').html()) + 1);
                $('.dz-progress').hide();
                $('.dz-remove').html('');
                $('.dz-remove').addClass('fas fa-trash text-danger mt-3');
                $('.dz-remove').prop('title', 'Delete');
                
                previewFile(file, dataUrl, mediaId);
            });

            this.on('sending', function (file, xhr, formData) {
                // Append all form inputs to the formData Dropzone will POST
                let data = $(submittedFormId).serializeArray();
                $.each(data, function (key, el) {
                    if (!(el.name).startsWith('_')) {
                        formData.append(el.name, el.value);
                    }
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
                if ($(file.previewElement).
                    find('.download-link').
                    attr('href') === 'undefined') {
                    $(file.previewElement).find('.download-link').hide();
                }
                if ($(file.previewElement).find('.download-link').length < 1 &&
                    mediaId != null) {
                    var anchorEl = document.createElement('a');
                    anchorEl.setAttribute('href',
                        ticketIndexUrl + '/media/' + mediaId);
                    anchorEl.setAttribute('class', 'download-link');
                    anchorEl.innerHTML = '<br>Download';
                    file.previewElement.appendChild(anchorEl);
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
        processing: function () {
            $('.dz-remove').html('');
            $('.dz-remove').addClass('fas fa-trash mt-3 text-danger');
            $('.dz-remove').prop('title', 'Delete');
            $('.dz-details').hide();
        },
        removedfile: function (file) {
            let attachmentId = file.previewElement.querySelector(
                '[data-file-id]').
                getAttribute('data-file-id');
            if (attachmentId != '00') {
                removeFileAlert(file, attachmentId);
                $('#attachment-counter').html(parseInt($('#attachment-counter').html()) - 1);
                return true;
            }
            $('#attachment-counter').html(parseInt($('#attachment-counter').html()) - 1);
            let fileRef;
            return (fileRef = file.previewElement) != null
                ?
                fileRef.parentNode.removeChild(file.previewElement)
                : void 0;
        },
        success: function (file, response) {
            let data = response.data;
            $('#editAttachment').modal('hide');
            window.location.replace(data.redirectUrl);
        },
        error: function (file, response, xhr) {
            // editAttachmentDropzone.options.processQueue = false;
            editAttachmentDropzone.removeAllFiles(true);
            $('#attachment-counter').html(0);
            processingBtn('#editTicketForm', '#btnSave');
            if (response.message != undefined) {
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    response.message)
                checkDropZonePreview('#editAttachmentDropzone')
            } else {
                if (xhr == null) this.removeFile(file);
                checkDropZonePreview('#editAttachmentDropzone')
                displayToastr(Lang.get('messages.error_message.error'), 'error',
                    response)
                return false;
            }
        },
    });
}

function removeFileAlert (file, attachmentId) {
    swal({
            title: Lang.get('messages.swal_message.delete'),
            text: Lang.get('messages.swal_message.sure_file_delete'),
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#5cb85c',
            cancelButtonColor: '#d33',
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonText: Lang.get('messages.common.yes'),
        },
        function () {
            deletedMediaIds.push(parseInt(attachmentId));
            checkDropZonePreview('#editAttachmentDropzone');
            swal({
                title: Lang.get('messages.swal_message.delete'),
                text: Lang.get('messages.swal_message.file_delete'),
                type: 'success',
                confirmButtonColor: '#6777ef',
                confirmButtonText: Lang.get('messages.common.ok'),
                timer: 1000,
            });
            let fileRef;
            return (fileRef = file.previewElement) != null
                ?
                fileRef.parentNode.removeChild(file.previewElement)
                : void 0;
        });
}

function checkDropZonePreview (dropzoneId) {
    if ($('.dz-preview').length >= 1) {
        $(dropzoneId).
            find('.dz-message').
            css({ 'display': 'none' });
    } else {
        $(dropzoneId).
            find('.dz-message').
            css({ 'display': 'block' });
    }
}

$(document).ready(function () {
    $(document).on('click', '#attachmentButton', function () {
        $('#addAttachment').appendTo('body').modal('show');
    });
    $(document).on('click', '#editAttachmentButton', function () {
        $('#editAttachment').appendTo('body').modal('show');
    });
    $('#categoryId').select2({
        width: 'calc(100% - 44px)',
    });
    $('#customer').select2({
        width: 'calc(100% - 44px)',
    });
    $('#customerId, #webCategoryId').select2({
        width: '100%',
    });
    $('#assignedTo').select2({
        width: '100%',
        placeholder: Lang.get('messages.ticket.select_agents'),
    });
    $('#details, #Editdetails').summernote({
        placeholder: Lang.get('messages.ticket.add_ticket_description'),
        height: '200px',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['paragraph']]],
        disableResizeEditor: true,
    });

    $(document).on('submit', '#addTicketForm', function (event) {
        event.preventDefault();
        submittedFormId = '#addTicketForm';
        let loadingButton = $('#btnSave');
        loadingButton.button('loading');
        let description = $('<div />').html($('#details').summernote('code'));
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
        if ($('#details').summernote('isEmpty')) {
            $('#details').val('')
            displayErrorMessage(Lang.get('messages.common.description') + ' ' +
                Lang.get('messages.validation.required_field'))
            loadingButton.button('reset')
            return false;
        } else if (isEmpty) {
            displayErrorMessage(Lang.get('messages.common.description') + ' ' +
                Lang.get('messages.validation.white_space'))
            loadingButton.button('reset')
            return false;
        }
        if ($('.dz-preview').length == 0) {
            $('#addTicketForm')[0].submit();
        } else {
            attachmentDropzone.processQueue();
        }

        return true;
    });

    $(document).on('submit', '#webTicketForm', function (event) {
        event.preventDefault();
        submittedFormId = '#webTicketForm';
        let description = $('<div />').html($('#details').summernote('code'));
        let isEmpty = isOnlyContainWhiteSpace(description.text());
        if ($('#details').summernote('isEmpty')) {
            $('#details').val('')
            displayErrorMessage(Lang.get('messages.common.description') + ' ' +
                Lang.get('messages.validation.required_field'))
            return false;
        } else if (isEmpty) {
            displayErrorMessage(Lang.get('messages.common.description') + ' ' +
                Lang.get('messages.validation.white_space'))
            return false;
        }
        if ($('#user-name').length > 0 &&
            isOnlyContainWhiteSpace($('#user-name').val())) {
            displayErrorMessage(Lang.get('messages.common.name') + ' ' +
                Lang.get('messages.validation.white_space'))
            return false;
        }
        if (isOnlyContainWhiteSpace($('#ticket-title').val())) {
            displayErrorMessage(Lang.get('messages.ticket.ticket_title') + ' ' +
                Lang.get('messages.validation.white_space'))
            return false;
        }
        if ($('.dz-preview').length == 0) {
            $('#webTicketForm')[0].submit();
        } else {
            attachmentDropzone.processQueue();
        }

        return true;
    });

    $(document).on('submit', '#editTicketForm', function (event) {
        event.preventDefault();
        $('input:hidden[name=deletedMediaId]').val(deletedMediaIds);
        submittedFormId = '#editTicketForm';
        let loadingButton = $('#btnSave');
        loadingButton.button('loading');
        let description = $('<div />').html($('#details').summernote('code'));
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
        if ($('#details').summernote('isEmpty')) {
            $('#details').val('')
            displayErrorMessage(Lang.get('messages.common.description') + ' ' +
                Lang.get('messages.validation.required_field'))
            loadingButton.button('reset')
            return false;
        } else if (isEmpty) {
            displayErrorMessage(Lang.get('messages.common.description') + ' ' +
                Lang.get('messages.validation.white_space'))
            loadingButton.button('reset')
            return false;
        }
        if ($('.dz-preview').length == 0 ||
            editAttachmentDropzone.getQueuedFiles().length == 0) {
            $('#editTicketForm')[0].submit();
        } else {
            editAttachmentDropzone.processQueue();
        }

        return true;
    });
});

$(document).on('click', '.addCategoryModal', function () {
    $('#addModal').appendTo('body').modal('show');
});

$(document).on('submit', '#addNewForm', function (e) {
    if ($('#color').val() == '') {
        displayErrorMessage(Lang.get('messages.validation.color_required'))
        return false;
    }
    e.preventDefault();
    processingBtn('#addNewForm', '#btnSave', 'loading');
    $.ajax({
        url: route('category.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addModal').modal('hide');
                let data = {
                    id: result.data.id,
                    text: result.data.name,
                };

                let newOption = new Option(data.text, data.id, false, true);
                $('#categoryId').append(newOption).trigger('change');
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

if ($('#color').length > 0) {
    $('#addModal').on('hidden.bs.modal', function () {
        resetModalForm('#addNewForm', '#validationErrorsBox');
        pickr.setColor('#33b0b0');
        pickr.hide();
        $('#colorError').text('');
    });

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

    let picked = false;

    $(document).on('click', '#color', function () {
        picked = true;
    });

    $(document).keydown(function (event) {
        if (event.keyCode === 27) {
            $('#addModal').modal('hide');
        }
    });
}

$(document).on('click', '.addCustomerModal', function () {
    $('#createCustomerModal').appendTo('body').modal('show');
});

$(document).on('submit', '#createCustomer', function (e) {
    e.preventDefault();
    processingBtn('#createCustomer', '#btnSave', 'loading');
    $.ajax({
        url: route('user.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#createCustomerModal').modal('hide');
                let data = {
                    id: result.data.id,
                    text: result.data.name,
                };

                let newOption = new Option(data.text, data.id, false, true);
                $('#customer').append(newOption).trigger('change');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#createCustomer', '#btnSave');
        },
    });
});

$('#createCustomerModal').on('hidden.bs.modal', function () {
    resetModalForm('#createCustomer', '#validationErrorsBox');
});

$(document).on('click', '#registrationUser', function () {
    $('#loginBlock').removeClass('show');
});

$(document).on('click', '#loginUser', function () {
    $('#registrationBlock').removeClass('show');
});
