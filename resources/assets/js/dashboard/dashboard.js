'use strict';

let timeRange = $('#time_range');
const today = moment();
let start = today.clone().startOf('month');
let end = today.clone().endOf('month');
let isPickerApply = false;
let agentId, categoryId = '';

$(document).on('change', 'input[type=radio][name=ticketStatus]', function() {
    loadCategoryTicketChart(this.value);
});

$(document).on('change', 'input[type=radio][name=agentTicketStatus]', function() {
    loadAgentTicketReport(this.value);
});

timeRange.on('apply.daterangepicker', function (ev, picker) {
    startLoader();
    isPickerApply = true;
    start = picker.startDate.format('YYYY-MM-D  H:mm:ss');
    end = picker.endDate.format('YYYY-MM-D  H:mm:ss');
    loadOpenVsCloseTicketChart(start, end, categoryId, agentId);
});

window.cb = function (start, end) {
    timeRange.find('span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'))
}

cb(start, end)

const lastMonth = moment().startOf('month').subtract(1, 'days')

timeRange.daterangepicker({
    startDate: start,
    endDate: end,
    opens: 'left',
    showDropdowns: true,
    autoUpdateInput: false,
    locale: {
        customRangeLabel: Lang.get('messages.common.custom'),
        applyLabel: Lang.get('messages.common.apply'),
        cancelLabel: Lang.get('messages.common.cancel'),
        fromLabel: Lang.get('messages.common.from'),
        toLabel: Lang.get('messages.common.to'),
        monthNames: [
            Lang.get('messages.months.jan'),
            Lang.get('messages.months.feb'),
            Lang.get('messages.months.mar'),
            Lang.get('messages.months.apr'),
            Lang.get('messages.months.may'),
            Lang.get('messages.months.jun'),
            Lang.get('messages.months.jul'),
            Lang.get('messages.months.aug'),
            Lang.get('messages.months.sep'),
            Lang.get('messages.months.oct'),
            Lang.get('messages.months.nov'),
            Lang.get('messages.months.dec'),
        ],

        daysOfWeek: [
            Lang.get('messages.weekdays.sun'),
            Lang.get('messages.weekdays.mon'),
            Lang.get('messages.weekdays.tue'),
            Lang.get('messages.weekdays.wed'),
            Lang.get('messages.weekdays.thu'),
            Lang.get('messages.weekdays.fri'),
            Lang.get('messages.weekdays.sat')],
    },
    ranges: {
        [Lang.get('messages.filter_days.today')]: [moment(), moment()],
        [Lang.get('messages.filter_days.this_week')]: [
            moment().startOf('week'),
            moment().endOf('week')],
        [Lang.get('messages.filter_days.last_week')]: [
            moment().startOf('week').subtract(7, 'days'),
            moment().startOf('week').subtract(1, 'days')],
        [Lang.get('messages.filter_days.this_month')]: [start, end],
        [Lang.get('messages.filter_days.last_month')]: [
            lastMonth.clone().startOf('month'),
            lastMonth.clone().endOf('month')],
        [Lang.get('messages.filter_days.this_year')]: [
            moment().startOf('year'),
            moment().endOf('year')],
    },
}, cb)

$(document).on('change', '#categories, #agents', function (e) {
    e.preventDefault();
    categoryId = $('#categories').val();
    agentId = $('#agents').length > 0 ? $('#agents').val() : '';
    loadOpenVsCloseTicketChart(moment(start).format('YYYY-MM-D'),
        moment(end).format('YYYY-MM-D'), categoryId, agentId);
});

$(document).ready(function () {
    $('#categories').select2({
        width: '170px',
    });
    $('#agents').select2({
        width: '170px',
    });
    if (typeof agentTicketReport != 'undefined') loadAgentTicketReport(1);
    loadCategoryTicketChart(1);
    loadOpenVsCloseTicketChart(moment(start).format('YYYY-MM-D'),
        moment(end).format('YYYY-MM-D'));
});

window.loadAgentTicketReport = function (status) {
    $.ajax({
        type: 'GET',
        data: { 'status': status },
        url: agentTicketReport,
        dataType: 'json',
        cache: false,
    }).done(prepareAgentTicketReport);
};

window.prepareAgentTicketReport = function (result) {
    $('#agentWiseTicket').html('');
    $('canvas#agentTicketChart').remove();
    $('#agentWiseTicket').
        append(
            '<canvas id="agentTicketChart" class="chartjs-render-monitor"></canvas>');
    let data = result.data;
    if (data.assignTicket.filter(val => val > 0).length === 0) {
        $('#agentWiseTicket').empty();
        $('#agentWiseTicket').
            append(
                '<div class="no-record-chart">'+Lang.get('messages.admin_dashboard.no_records_found')+'</div>');
        return true;
    }
    let ctx = document.getElementById('agentTicketChart').
        getContext('2d');
    ctx.canvas.style.height = '400px';
    ctx.canvas.style.width = '100%';
    let dailyWorkReportChart = new Chart(ctx, {
        type: 'pie',
        data: {
            datasets: [
                {
                    data: data.assignTicket,
                    backgroundColor: data.color,
                    label: 'Dataset 1',
                }],
            labels: data.agents,
        },
        options: {
            responsive: true,
            legend: {
                display: false,
            },
        },
    });
};

window.loadCategoryTicketChart = function (status) {
    $.ajax({
        type: 'GET',
        data: { 'status': status },
        url: categoryTicket,
        cache: false,
    }).done(prepareCategoryTicketChart);
};

window.loadOpenVsCloseTicketChart = function (
    start, end, categoryId = null, agentId = null) {
    $.ajax({
        type: 'GET',
        beforeSend: function () {
            startLoader();
        },
        data: {
            'start_date': start,
            'end_date': end,
            'categoryId': categoryId,
            'agentId': agentId,
        },
        url: openVsCloseTicket,
        cache: false,
    }).done(prepareTicketChart);
};

window.prepareTicketChart = function (result) {
    $('#ticketChartContainer').html('');
    $('canvas#ticketChart').remove();
    $('#ticketChartContainer').
        append(
            '<canvas id="ticketChart" class="chartjs-render-monitor"></canvas>');

    let data = result.data;
    let openTicketCounts = data.openTicketCounts;
    let closeTicketCounts = data.closeTicketCounts;
    let dateLabels = data.dateLabels;
    if (openTicketCounts.every(item => item === 0) &&
        closeTicketCounts.every(item => item === 0)) {
        $('#ticketChartContainer').empty();
        $('#ticketChartContainer').
            append(
                '<div class="text-center">'+Lang.get('messages.admin_dashboard.no_records_found')+'</div>');
        stopLoader();

        return true;
    }
    let ctx = document.getElementById('ticketChart').getContext('2d');
    ctx.canvas.style.height = '300px';
    ctx.canvas.style.width = '100%';
    let myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dateLabels,
            datasets: [
                {
                    label: Lang.get('messages.admin_dashboard.closed_tickets'),
                    data: closeTicketCounts,
                    borderWidth: 2,
                    fill: false,
                    backgroundColor: 'transparent',
                    borderColor: 'rgba(254,86,83,.7)',
                    pointBackgroundColor: 'rgba(254,86,83,.7)',
                    pointBorderColor: 'transparent',
                },
                {
                    label: Lang.get('messages.admin_dashboard.open_tickets'),
                    data: openTicketCounts,
                    borderWidth: 2,
                    fill: false,
                    backgroundColor: 'transparent',
                    borderColor: 'rgba(99,237,122)',
                    pointBackgroundColor: 'rgba(99,237,122)',
                    pointBorderColor: 'transparent',
                },
            ]
        },
        options: {
            legend: {
                display: true,
            },
            scales: {
                yAxes: [
                    {
                        gridLines: {
                            drawBorder: false,
                            color: '#f2f2f2',
                        },
                        ticks: {
                            min: 0,
                            stepSize: 2,
                        },
                    }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            },
        }
    });
    stopLoader();
};

window.prepareCategoryTicketChart = function (result) {
    $('#ticketCategoryChartContainer').html('');
    $('canvas#ticketCategoryChart').remove();
    $('#ticketCategoryChartContainer').
        append(
            '<canvas id="ticketCategoryChart" class="chartjs-render-monitor"></canvas>');

    let data = result.data;
    if (data.categories.length === 0) {
        $('#ticketCategoryChartContainer').empty();
        $('#ticketCategoryChartContainer').
            append(
                '<div class="no-record-chart" >'+Lang.get('messages.admin_dashboard.no_records_found')+'</div>');
        return true;
    }
    let ctx = document.getElementById('ticketCategoryChart').getContext('2d');
    ctx.canvas.style.height = '400px';
    ctx.canvas.style.width = '100%';
    let myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            datasets: [
                {
                    data: data.categoriesTicket,
                    backgroundColor: data.color,
                    label: 'Dataset 1',
                }],
            labels: data.categories,
        },
        options: {
            responsive: true,
            legend: {
                display: false,
            },
        },
    });
    stopLoader();
};
