@extends('customer_dashboard.app')
@section('title')
    {{ __('messages.dashboard') }}
@endsection
@push('css')
    <link href="{{ mix('assets/css/dashboard-widgets.css') }}" rel="stylesheet" type="text/css"/>
    @livewireStyles
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.dashboard') }}</h1>
        </div>
        <!-- statistics count starts -->
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon open-ticket-bg">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ __('messages.admin_dashboard.open_tickets') }}</h4>
                        </div>
                        <div class="card-body mt-0">
                            {{ $data['totalOpenTickets'] }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon closed-ticket-bg">
                        <i class="fas fa-window-close"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ __('messages.admin_dashboard.closed_tickets') }}</h4>
                        </div>
                        <div class="card-body mt-0">
                            {{ $data['totalClosedTickets'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- statistics count ends -->
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
@endpush
