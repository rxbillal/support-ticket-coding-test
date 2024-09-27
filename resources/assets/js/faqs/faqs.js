'use strict';

let tableName = '#faqsTbl';
$(tableName).DataTable({
    processing: true,
    serverSide: true,
    oLanguage: {
        'sEmptyTable': Lang.get('messages.common.empty_data_table'),
        'sInfo': Lang.get('messages.common.data_base_entries'),
        oPaginate: {
            sFirst: Lang.get('messages.common.first'),
            sLast: Lang.get('messages.common.last'),
            sNext: Lang.get('messages.common.next'),
            sPrevious: Lang.get('messages.common.previous'),
        },
        sLengthMenu: Lang.get('messages.common.menu_entry'),
        sInfoEmpty: Lang.get('messages.common.no_entry'),
        sInfoFiltered: Lang.get('messages.common.filter_by'),
        sZeroRecords: Lang.get('messages.common.no_matching'),
    },
    'order': [[0, 'asc']],
    ajax: {
        url: faqUrl,
    },
    columnDefs: [
        {
            'targets': [0],
            'width': '25%',
        },
        {
            'targets': [1],
            'orderable': false,
            render: function (data) {
                return data.length > 200 ?
                    data.substr(0, 200) + '...' :
                    data;
            },
        },
        {
            'targets': [2],
            'orderable': false,
            'className': 'text-center',
            'width': '8%',
        },
    ],
    columns: [
        {
            data: function (row) {
                return '<a href="javascript:void(0)" class="show-btn" data-id="' + row.id +
                    '">' + row.title + '</a>';
            },
            name: 'title',
        },
        {
            data: function (row) {
                if (!isEmpty(row.description)) {
                    let element = document.createElement('textarea');
                    element.innerHTML = row.description;
                    return element.value;
                } else
                    return 'N/A';
            },
            name: 'description',
        },
        {
            data: function (row) {
                let data = [{ 'id': row.id }];
                return prepareTemplateRender('#faqActionTemplate',
                    data);
            }, name: 'id',
        },
    ],
});

$(document).on('click', '.addFaqModal', function () {
    $('#addModal').appendTo('body').modal('show');
});

$(document).on('submit', '#addNewForm', function (e) {
    e.preventDefault();
    let description = $('<div />').html($('#description').summernote('code'));
    let isEmpty = isOnlyContainWhiteSpace(description.text());
    if ($('#description').summernote('isEmpty')) {
        displayErrorMessage(Lang.get('messages.common.description') + ' ' +
            Lang.get('messages.validation.required_field'))
        return false;
    } else if (isEmpty) {
        displayErrorMessage(Lang.get('messages.common.description') + ' ' +
            Lang.get('messages.validation.white_space'))
        return false;
    }
    processingBtn('#addNewForm', '#btnSave', 'loading');
    $.ajax({
        url: route('faqs.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addModal').modal('hide');
                $(tableName).DataTable().ajax.reload(null, false);
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
    let faqId = $(event.currentTarget).data('id');
    renderData(faqId);
});

window.renderData = function (id) {
    $.ajax({
        url: faqUrl + '/' + id + '/edit',
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#faqId').val(result.data.id);
                $('#editTitle').val(result.data.title);
                $('#editDescription').
                    summernote('code', result.data.description);
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
    let description = $('<div />').
        html($('#editDescription').summernote('code'));
    let isEmpty = isOnlyContainWhiteSpace(description.text());
    if ($('#editDescription').summernote('isEmpty')) {
        displayErrorMessage(Lang.get('messages.common.description') + ' ' +
            Lang.get('messages.validation.required_field'))
        return false;
    } else if (isEmpty) {
        displayErrorMessage(Lang.get('messages.common.description') + ' ' +
            Lang.get('messages.validation.white_space'))
        return false;
    }
    processingBtn('#editForm', '#btnEditSave', 'loading');
    const id = $('#faqId').val();
    $.ajax({
        url: faqUrl + '/' + id,
        type: 'put',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#editModal').modal('hide');
                $(tableName).DataTable().ajax.reload(null, false);
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

$(document).on('click', '.show-btn', function (event) {
    let faqId = $(event.currentTarget).attr('data-id');
    $.ajax({
        url: faqUrl + '/' + faqId,
        type: 'GET',
        success: function (result) {
            if (result.success) {
                $('#showName').html('');
                $('#showDescription').html('');
                $('#showName').append(result.data.title);
                let element = document.createElement('textarea');
                element.innerHTML = result.data.description;
                $('#showDescription').append(element.value);
                $('#showModal').appendTo('body').modal('show');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
    });
});

$(document).on('click', '.delete-btn', function (event) {
    let faqId = $(event.currentTarget).data('id');
    swal({
            title: deleteHeading + ' !',
            text: deleteMessage + ' "' + Lang.get('messages.faq.faq') + '" ?',
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
            $.ajax({
                url: faqUrl + '/' + faqId,
                type: 'DELETE',
                dataType: 'json',
                success: function (obj) {
                    if (obj.success) {
                        swal({
                            title: Lang.get('messages.common.deleted') + ' !',
                            text: Lang.get('messages.faq.faq') + ' ' +
                                Lang.get('messages.common.has_been_deleted') +
                                '.',
                            type: 'success',
                            confirmButtonColor: '#00b074',
                            confirmButtonText: Lang.get('messages.common.ok'),
                            timer: 2000,
                        });
                    }
                    if ($(tableName).DataTable().data().count() == 1) {
                        $(tableName).DataTable().page('previous').draw('page');
                    } else {
                        $(tableName).DataTable().ajax.reload(null, false);
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
        });
});

$('#addModal').on('hidden.bs.modal', function () {
    resetModalForm('#addNewForm', '#validationErrorsBox');
    $('#description').summernote('code', '');
});

$('#editModal').on('hidden.bs.modal', function () {
    resetModalForm('#editForm', '#editValidationErrorsBox');
});

$('#description, #editDescription').summernote({
    placeholder: Lang.get('messages.faq.description_placeholder'),
    height: '200',
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['height', ['height']],
        ['para', ['paragraph']]],
    disableResizeEditor: true,
    callbacks: {
        onImageUpload: function (image) {
            uploadImage(image[0])
        },
    },
});

function uploadImage (image) {
    let data = new FormData()
    data.append('image', image)
    $.ajax({
        url: route('faqs.upload'),
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        type: 'post',
        success: function (result) {
            let image = $('<img height="50" width="50">').
                attr('src', result.data.url)
            $('#description').summernote('insertNode', image[0])
            $('#editDescription').summernote('insertNode', image[0])
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
        },
    })
}
