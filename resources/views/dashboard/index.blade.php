@extends('layouts.app')
@section('title')
    {{ __('messages.dashboard') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@push('page_css')
    <link href="{{ mix('assets/css/dashboard-widgets.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.dashboard') }}</h1>
        </div>
        <!-- statistics count starts -->
        <div class="row">
            @if(getLoggedInUserRoleId() == getAdminRoleId())
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon total-agents-bg">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('messages.admin_dashboard.total_agents') }}</h4>
                            </div>
                            <div class="card-body mt-0">
                                {{ $data['dashboardData']['totalAgents'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon verified-category-bg">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ __('messages.admin_dashboard.total_categorys') }}</h4>
                        </div>
                        <div class="card-body mt-0">
                            {{ $data['dashboardData']['totalCategories'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon open-ticket-bg">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ __('messages.admin_dashboard.open_tickets') }}</h4>
                        </div>
                        <div class="card-body mt-0">
                            {{ $data['dashboardData']['totalOpenTickets'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon closed-ticket-bg">
                        <i class="fas fa-window-close"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ __('messages.admin_dashboard.closed_tickets') }}</h4>
                        </div>
                        <div class="card-body mt-0">
                            {{ $data['dashboardData']['totalClosedTickets'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- statistics count ends -->

        <!-- Chart Data starts -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header p-lg-3 p-2 d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h4>{{ __('messages.admin_dashboard.new_vs_close_ticket') }}</h4>
                        </div>
                        <div class="d-flex mt-md-0 mt-3 flex-wrap ml-auto mobile-width-100">
                            @role('Admin')
                            <div class="mr-2  mb-2 responsive-100">
                                {{ Form::select('agents', $data['dashboardData']['agents'], null, ['id' => 'agents','class' => 'form-control', 'placeholder' => __('messages.admin_dashboard.select_agent')]) }}
                            </div>
                            @endrole
                            <div class="mb-2 responsive-100">
                                {{ Form::select('categories', $data['dashboardData']['categories'], null, ['id' => 'categories','class' => 'form-control', 'placeholder' => __('messages.admin_dashboard.select_category')]) }}
                            </div>
                            <div id="time_range" class="time_range time_range_width ml-sm-2 ml-0 mb-2 responsive-100">
                                <i class="far fa-calendar-alt"
                                   aria-hidden="true"></i>&nbsp;&nbsp;<span></span> <b
                                        class="caret"></b>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="ticketChartContainer">
                        <canvas id="ticketChart" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>

            @role('Admin')
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header justify-content-between flex-md-row flex-column flex-wrap custom-dash-header">
                        <h4 class="ml-md-2 ml-0">{{ __('messages.admin_dashboard.agent_ticket_chart') }}</h4>
                        <div class="mt-md-0 mt-3 ml-md-auto">
                            <div class="selectgroup d-flex justify-content-end selectgroup-pills custom-seclect-pills">
                                <label class="selectgroup-item mb-0">
                                    <input type="radio" name="agentTicketStatus" value="1" class="selectgroup-input"
                                           checked="">
                                    <span class="selectgroup-button">{{ __('messages.admin_dashboard.open_tickets') }}</span>
                                </label>
                                <label class="selectgroup-item mb-0">
                                    <input type="radio" name="agentTicketStatus" value="3" class="selectgroup-input">
                                    <span class="selectgroup-button">{{ __('messages.admin_dashboard.closed_tickets') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="agentWiseTicket">
                        <canvas id="agentTicketChart" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
            @endrole

            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header justify-content-between flex-md-row flex-column flex-wrap custom-dash-header">
                        <h4 class="ml-md-2 ml-0">{{ __('messages.admin_dashboard.tickets_by_categories') }}</h4>
                        <div class="mt-md-0 mt-3 ml-md-auto">
                            <div class="selectgroup d-flex justify-content-end selectgroup-pills custom-seclect-pills">
                                <label class="selectgroup-item mb-0">
                                    <input type="radio" name="ticketStatus" value="1" class="selectgroup-input"
                                           checked="">
                                    <span
                                            class="selectgroup-button">{{ __('messages.admin_dashboard.open_tickets') }}</span>
                                </label>
                                <label class="selectgroup-item mb-0">
                                    <input type="radio" name="ticketStatus" value="3" class="selectgroup-input">
                                    <span
                                            class="selectgroup-button">{{ __('messages.admin_dashboard.closed_tickets') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="ticketCategoryChartContainer">
                        <canvas id="ticketCategoryChart" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Chart Data ends -->
    </section>
@endsection
@push('scripts')
    <script>
        @role('Admin')
        let categoryTicket = "{{ route('category-ticket-chart') }}";
        let openVsCloseTicket = "{{ route('ticket-chart') }}";
        let agentTicketReport = "{{ route('agent-ticket-report') }}";
        @endrole

        @role('Agent')
        let categoryTicket = "{{ route('agent.category-ticket-chart') }}";
        let openVsCloseTicket = "{{ route('agent.ticket-chart') }}";
        @endrole
    </script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ mix('assets/js/Chart.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/dashboard.js') }}"></script>
@endpush
