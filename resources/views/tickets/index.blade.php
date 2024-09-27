@extends('layouts.app')
@section('title')
    {{ __('messages.ticket.tickets') }}
@endsection
@push('css')
    @notifyCss
    @livewireStyles
@endpush
@push('page_css')
    <link href="{{ asset('assets/css/ticket.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <section class="section">
        <div class="section-header flex-wrap">
            <h1 class="mr-3">{{ __('messages.ticket.tickets') }}</h1>
            @role('Admin')
            <div class="section-header-breadcrumb">
                <a href="{{ route('ticket.create') }}" class="btn btn-primary form-btn my-sm-0 my-1">
                    {{ __('messages.ticket.create_ticket') }}
                    <i class="fas fa-plus"></i></a>
            </div>
            @endrole
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @livewire('tickets')
                </div>
            </div>
        </div>
        <div id="taskEditModal">
            @include('tickets.edit_assignee_modal')
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let ticketUrl = '{{ url('admin/tickets') }}/'
        let activeStatus = '{{ \App\Models\Ticket::STATUS_ACTIVE }}'
    </script>
    <script src="{{ mix('assets/js/tickets/tickets.js') }}"></script>
    @notifyJs
@endpush

