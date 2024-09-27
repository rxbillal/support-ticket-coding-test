@extends('customer_dashboard.app')
@section('title')
    {{ __('messages.my_tickets') }}
@endsection
@push('css')
    <link href="{{ mix('assets/css/dashboard-widgets.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/customer_ticket.css') }}" rel="stylesheet" type="text/css"/>
    @livewireStyles
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.my_tickets') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('customer.create.ticket') }}" class="btn btn-primary form-btn my-sm-0 my-1">
                    {{ __('messages.ticket.create_ticket') }}
                    <i class="fas fa-plus"></i></a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @livewire('customer-ticket')
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let activeStatus = "{{ \App\Models\Ticket::STATUS_ACTIVE }}"
    </script>
    <script src="{{ mix('assets/js/tickets/tickets.js')}}"></script>
@endpush
