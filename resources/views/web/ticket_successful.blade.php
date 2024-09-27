@extends('web.app')
@section('title')
    {{ __('messages.ticket.ticket_details') }}
@endsection
@section('content')
    <div class="row justify-content-center mt-5 mx-0">
        <div class="col-lg-8 col-md-8">
            @include('flash::message')
            <div class="card card-primary shadow">
                <div class="card-header"><h4>{{ __('messages.ticket.ticket_details') }}</h4></div>
                <div class="card-body mt-0 pt-0">
                    <div class="row">
                        <div class="form-group col-xl-12 col-md-12 col-sm-12">
                            <h6>{{ Form::label('ticket_number', __('messages.ticket.ticket_number').':') }}</h6>
                            <span id="copyTicketId">{{ $ticket->ticket_id }}</span>
                        </div>
                        <div class="form-group col-xl-12 col-md-12 col-sm-12">
                            <h6>{{ Form::label('title', __('messages.ticket.ticket_title').':') }}</h6>
                            <span>{{ $ticket->title }}</span>
                        </div>
                        <div class="form-group col-xl-12 col-md-12 col-sm-12">
                            <h6>{{ Form::label('email', __('messages.common.email').':') }}</h6>
                            <span>{{ $ticket->email }}</span>
                        </div>
                        @if(count($ticket->media) > 0)
                            <div class="form-group col-xl-12 col-md-12 col-sm-12">
                                <h6>{{ Form::label('attachments', __('messages.ticket.attachments').':') }}</h6>
                                <div class="gallery gallery-md attachment__section">
                                    @foreach($ticket->media as $media)
                                        <div class="gallery-item ticket-attachment"
                                             data-image="{{ mediaUrlEndsWith($media->getFullUrl()) }}"
                                             data-title="{{ substr($media->name, 0, 15) .'...' }}"
                                             href="{{ mediaUrlEndsWith($media->getFullUrl()) }}"
                                             title="{{ $media->name }}">
                                            <div class="ticket-attachment__icon d-none">
                                                <a href="{{ $media->getFullUrl() }}" target="_blank"
                                                   class="text-decoration-none text-primary" data-toggle="tooltip"
                                                   data-placement="top"
                                                   title="{{ __('messages.common.view') }}"><i
                                                            class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('download.media',$media) }}"
                                                   download="{{ $media->name }}"
                                                   class="text-warning text-decoration-none"
                                                   data-id="{{ $media->id }}" data-toggle="tooltip"
                                                   data-placement="top"
                                                   title="{{ __('messages.common.download') }}"><i
                                                            class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="form-group col-xl-12 col-md-12 col-sm-12">
                            <a href="javascript:void(0)" class="btn btn-primary" id="copyButton"
                               onclick="copyToClipboard('#copyTicketId','#copyButton')">{{ __('messages.ticket.copy_ticket_number') }}</a>
                            <a href="javascript:void(0)" class="btn btn-primary d-none"
                               id="copiedButton">{{ __('messages.ticket.copied').'!' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {

            $(document).on('mouseenter', '.ticket-attachment', function () {
                $(this).find('.ticket-attachment__icon').removeClass('d-none');
            });

            $(document).on('mouseleave', '.ticket-attachment', function () {
                $(this).find('.ticket-attachment__icon').addClass('d-none');
            });
        });
    </script>
@endpush
