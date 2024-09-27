'use strict'

let tableName = '#adminTbl'
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
        url: adminUrl,
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
                    data
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
                return '<div class="d-flex align-items-center">' +
                    ' <div class="image image-circle image-mini me-3">' +
                    '<a href="#">' +
                    ' <div class="">' +
                    '<img src="' + row.photo_url +
                    '" class="user-img rounded-circle object-cover" height="40" width="40">' +
                    '</div></a></div>    <div class="d-flex flex-column ml-3">\n' +
                    '        <a  class="text-decoration-none mb-1">' +
                    row.name + '</a>\n' +
                    '        <span>' + row.email + '</span>\n' +
                    '    </div>\n' +
                    '</div>'

            },
            name: 'name',
        },
        {
            data: function (row) {
                if (!isEmpty(row.phone)) {
                    return row.phone
                } else
                    return 'N/A'
            },
            name: 'phone',
        },
        {
            data: function (row) {
                let data = [
                    {
                        'url': route('admins.edit', row.id),
                        'id': row.id,
                    }]
                return prepareTemplateRender('#adminActionTemplate',
                    data)
            }, name: 'id',
        },
    ],
})
$(document).on('click', '.admin-delete-btn', function (event) {
    let deleteAdminId = $(this).attr('data-id')
    console.log(deleteAdminId)
    deleteItem(route('admins.destroy', deleteAdminId), '#adminTbl',
        Lang.get('messages.admin.admin'))
})
