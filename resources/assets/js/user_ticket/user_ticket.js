'use strict';

$(document).ready(function () {
    $('#status-filter').select2({
        width: '100%',
    });
    $('#category-filter').select2({
        width: '100%',
    });
});

let tableName = '#ticketsTbl';
$(tableName).DataTable({
    'pageLength': 10,
    oLanguage: {
        'sEmptyTable': Lang.get('messages.common.empty_data_table'),
        'sInfo': Lang.get('messages.common.data_base_entries'),
        oPaginate: {
            sFirst: Lang.get('messages.common.first'),
            sLast: Lang.get('messages.common.last'),
            sNext: Lang.get('messages.common.next'),
            sPrevious: Lang.get('messages.common.last'),
        },
        sLengthMenu: Lang.get('messages.common.menu_entry'),
        sInfoEmpty: Lang.get('messages.common.no_entry'),
        sInfoFiltered: Lang.get('messages.common.filter_by'),
        sZeroRecords: Lang.get('messages.common.no_matching'),
    },
    'fnDrawCallback': function () {
        if (Math.ceil((this.fnSettings().fnRecordsDisplay()) /
            this.fnSettings()._iDisplayLength) > 1) {
            $('.dataTables_paginate').css('display', 'block')
            // $('.dataTables_length').css('display', 'block');
        } else {
            $('.dataTables_paginate').css('display', 'none')
        }

        if (Math.ceil((this.fnSettings().fnRecordsDisplay())) < 11) {
            // $('.dataTables_length').css('display', 'none');
        }

        if (this.fnSettings().fnRecordsDisplay() < 10 &&
            $(this).find('tbody tr').length <= 1) {
            if (($('input[type="search"]').val() === '') &&
                ($('#status-filter').val() === '') &&
                ($('#category-filter').val() === '')) {
                // $('.dataTables_filter').css('display', 'none');
                // $('.status-filter').css('display', 'none');
                // $('#status-filter').next().css('display', 'none');
                // $('#status-filter').css('display', 'none');
                // $('#category-filter').next().css('display', 'none');
                // $('#category-filter').css('display', 'none');
            }
        }
        $('.dataTable_custom_filter').
            css('right', $('#ticketsTbl_filter').outerWidth());
        $('.dataTable_custom_filter_category').
            css('right', ($('#ticketsTbl_filter').outerWidth() +
                $('#status-filter').next().outerWidth() + 10));
    },
    processing: true,
    serverSide: true,
    'order': [[3, 'desc']],
    ajax: {
        url: ticketByUserUrl,
        data: function (data) {
            data.statusId = $('#status-filter').val();
            data.categoryId = $('#category-filter').val();
        },
    },
    columnDefs: [
        {
            'targets': [0],
            'width': '15%',
        },
        {
            'targets': [1],
            'width': '30%',
        },
        {
            'targets': [2],
            'width': '25%',
        },
        {
            'targets': [3],
            'width': '25%',
        },
        {
            'targets': [4],
            'orderable': false,
            'className': 'text-center',
            'width': '10%',
        },
    ],
    columns: [
        {
            data: function (row) {
                let url = ticketUrl + '/' + row.id;
                return '<a href="' + url +
                    '" class="text-decoration-none text-primary hover-primary">' +
                    row.ticket_id + '</a>';
            },
            name: 'ticket_id',
        },
        {
            data: function (row) {
                return row.title;
            },
            name: 'title',
        },
        {
            data: function (row) {
                return '<a href="' + route('category.show', row.category.id) +
                    '" class="text-decoration-none">' +
                    row.category.name + '</a>';
            },
            name: 'category.name',
        },
        {
            data: function (row) {
                return moment(row.created_at).format('Do MMM, Y');
            },
            name: 'created_at',
        },
        {
            data: function (row) {
                return '<span class="ticket-application-status badge badge-' +
                    statusColorArray[row.status] + '">'
                    + statusArray[row.status] + '</span>';
            },
            name: 'status',
        },
    ],
    'fnInitComplete': function () {
        $(document).on('change', '#status-filter', function () {
            // $('.dataTables_length').css('display', 'block');
            $(tableName).DataTable().ajax.reload(null, true);
        });
        $(document).on('change', '#category-filter', function () {
            // $('.dataTables_length').css('display', 'block');
            $(tableName).DataTable().ajax.reload(null, true);
        });
    },
});
