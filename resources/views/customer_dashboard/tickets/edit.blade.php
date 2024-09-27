@extends('customer_dashboard.app')
@section('title')
    {{ __('messages.ticket.edit_ticket') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('theme-assets/css/dropzone.css') }}">
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/customer_ticket.css') }}" rel="stylesheet" type="text/css"/>
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.ticket.edit_ticket') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('customer.myTicket') }}"
                   class="btn btn-primary form-btn float-right">{{__('messages.common.back')}}</a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                <div class="card-body">
                    {{ Form::model($ticket, ['route' => ['customer.updateTicket', $ticket->id], 'method' => 'put', 'autocomplete' => 'off', 'files' => 'true', 'id' => 'editTicketForm']) }}

                    @include('customer_dashboard.tickets.edit_fields')

                    {{ Form::close() }}
                </div>
            </div>
        </div>
        @include('customer_dashboard.tickets.edit_ticket_attachment_modal')
    </section>
@endsection
@push('scripts')
    <script>
        let getAttachmentUrl = "{{ route('ticket.get-attachments', ['ticket' => $ticket->id]) }}";
        let ticketIndexUrl = "{{ route('customer.myTicket')  }}";
    </script>
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ mix('assets/js/customer_dashboard/tickets.js')}}"></script>
@endpush
