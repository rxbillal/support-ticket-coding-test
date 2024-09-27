@extends('web.app')
@section('title')
    {{ __('messages.ticket.search_ticket') }}
@endsection
@push('css')
    <link href="{{ asset('assets/css/login_register.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <div class="container pt-5">
        @include('layouts.errors')
        <div class="row justify-content-center shadow  large-card">
            <div class="col-md-6 pr-0 d-flex justify-content-center align-items-center">
                <img class="mw-100" src="{{ asset('theme-assets/img/search_ticket.jpg') }}" alt="">
            </div>
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                <div>
                    <h2 class="display-sm-2 my-md-4 my-2 text-center">{{ __('messages.ticket.search_ticket') }}</h2>
                </div>
                <div class="my-4">
                    <form method="GET" action="{{ route('web.search_ticket') }}" autocomplete="off" class="web-user-form">
                        @include('flash::message')
                        <div class="row justify-content-center">
                            <div class="form-group col-md-12">
                                <label id="ticket_id" class="ml-1">{{ __('messages.ticket.ticket_number').':' }}</label>
                                <span class="text-danger">*</span>
                                {{ Form::text('ticket_id',  null , ['class' => 'form-control','required','maxlength'=>'8','style'=>'text-transform:uppercase']) }}
                            </div>
                            <div class="form-group col-md-12 ">
                                <label id="ticket_id" class="ml-1">{{ __('messages.common.email').':' }}</label>
                                <span class="text-danger">*</span>
                                {{ Form::email('email', null, ['class' => 'form-control', 'required']) }}
                            </div>
                            <div class="mt-3 text-center">
                                <button type="submit" class="btn btn-primary btn-lg btn-block btn-submit">
                                    {{ __("messages.search") }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
@endpush
