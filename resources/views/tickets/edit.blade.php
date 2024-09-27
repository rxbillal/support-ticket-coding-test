@extends('layouts.app')
@section('title')
    {{ __('messages.ticket.edit_ticket') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('theme-assets/css/dropzone.css') }}">
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/nano.min.css') }}">
    <link href="{{ asset('assets/css/ticket.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <section class="section">
        <div class="section-header flex-wrap">
            <h1 class="mr-3">{{ __('messages.ticket.edit_ticket') }}</h1>
            <div class="section-header-breadcrumb my-sm-0 my-1">
                @role('Admin')
                <a href="{{ route('ticket.index') }}"
                   class="btn btn-primary form-btn float-right">{{__('messages.common.back')}}</a>
                @endrole
                @role('Agent')
                <a href="{{ route('agent.ticket.index') }}"
                   class="btn btn-primary form-btn float-right">{{__('messages.common.back')}}</a>
                @endrole
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                <div class="card-body">
                    @role('Admin')
                    {{ Form::model($ticket, ['route' => ['ticket.update', $ticket->id], 'method' => 'put', 'autocomplete' => 'off', 'files' => 'true', 'id' => 'editTicketForm']) }}
                    @endrole
                    @role('Agent')
                    {{ Form::model($ticket, ['route' => ['agent.ticket.update', $ticket->id], 'method' => 'put', 'autocomplete' => 'off', 'files' => 'true', 'id' => 'editTicketForm']) }}
                    @endrole

                    @include('tickets.edit_fields')

                    {{ Form::close() }}
                </div>
            </div>
        </div>
        @include('tickets.edit_ticket_attachment_modal')
        @include('categories.add_modal')
        @include('tickets.create_customer_modal')
    </section>
@endsection
@push('scripts')
    <script>
        let getAttachmentUrl = "{{ route('ticket.get-attachments', ['ticket' => $ticket->id]) }}";
        @role('Admin')
        let ticketIndexUrl = "{{ route('ticket.index')  }}";
        @endrole
        @role('Agent')
        let ticketIndexUrl = "{{ route('agent.ticket.index')  }}";
        @endrole
    </script>
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pickr.min.js') }}"></script>
    <script src="{{mix('assets/js/tickets/create_edit.js')}}"></script>
@endpush
