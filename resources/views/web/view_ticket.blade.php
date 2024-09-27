@php
    /** @var $ticket \App\Models\Ticket */
  $adminRoleId = getAdminRoleId();
  $isAction = checkLoggedInUserRole();
@endphp
@extends('web.app')
@section('title')
    {{ __('messages.ticket.is_public') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('theme-assets/css/dropzone.css') }}">
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('theme-assets/css/dropzone.css') }}">
    <link href="{{ asset('theme-assets/css/animate.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/ticket.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <div class="container wow fadeInUp">
        <h2 class="text-center text-primary mt-6 mb-md-5 mb-3">{{ __('messages.ticket.ticket_details') }}</h2>
        @if(!empty($ticket))
            <div class="row">
                <div class="col-lg-8 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>
                                <sapn class="text-primary">{{ '#'.$ticket->ticket_id }}</sapn>
                                {{ $ticket->is_public ? __('messages.ticket.is_public') : __('messages.ticket.is_private') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="tickets">
                                <div class="ticket-content w-100">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="ticket-header">
                                                <div class="ticket-sender-picture img-shadow">
                                                    <img
                                                            src="{{ $ticket->user ? $ticket->user->photo_url : asset('assets/img/infyom-logo.png') }}"
                                                            class="reply-user-img object-fit-cover"
                                                            alt="image">
                                                </div>
                                                <div class="ticket-detail">
                                                    <div class="font-weight-600">{{ $ticket->title }}</div>
                                                    <div
                                                            class="text-primary font-weight-600">{{ $ticket->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                            <div class="ticket-description">
                                                <b>{{ Form::label('description', __('messages.common.description').':') }}</b>
                                                <div class="description-scroll">
                                                    <p>{!! $ticket->description ? $ticket->description : __('messages.common.n/a') !!}</p>
                                                </div>
                                            </div>
                                            <div class="ticket-divider"></div>
                                            @if(canUserReplyTicket($ticket))
                                                <div class="p-3 bg-primary">
                                                    <button id="btnPostReplay"
                                                            class="btn btn-outline-white rounded-0 fa-lg">
                                                        <i class="fas fa-comment-alt mr-2"></i>
                                                        {{__('messages.ticket.post_reply')}}</button>
                                                </div>

                                                <div class="card-body ticket-add-replay m-0 px-3 pb-2 display-none"
                                                     id="ticketAddReplay">
                                                    <div class="ticket-form ">
                                                        <div class="form-group">
                                                            <form id="addRelyForm" action="{{ route('ticket.add-reply') }}">
                                                                {{ Form::hidden('ticket_id',$ticket->id) }}
                                                                <strong>{{ Form::label('addReplyContainer', __('messages.ticket.add_reply').':') }}
                                                                    <span class="text-danger">*</span></strong>
                                                                <textarea name="description"
                                                                          id="addReplyContainer"></textarea>
                                                                <div class="text-left mt-3">
                                                                    {{ Form::button(__('messages.ticket.post_reply'), ['type'=>'submit', 'class' => 'btn btn-primary custom-ticket-btn', 'id'=>'addTicketReply', 'data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span>". __('messages.placeholder.processing')]) }}
                                                                    <button type="button" id="attachmentButton"
                                                                            data-toggle="modal"
                                                                            data-target="#addAttachment"
                                                                            class="btn btn-info btn-icon custom-ticket-btn">
                                                                        <i class="fas fa-paperclip"></i>
                                                                        {{ __('messages.common.add').' ' }} {{ __('messages.ticket.attachments') }}
                                                                    </button>
                                                                    <button type="button" id="btnCancelReplay"
                                                                            class="btn btn-secondary mt-sm-0 mt-2 text-dark custom-ticket-btn">
                                                                        {{__('messages.common.cancel')}}
                                                                    </button>

                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="tickets">
                                                <div class="ticket-content w-100">
                                                    <div class="ticket-reply-box p-0 ticket-reply-scroll">
                                                        @foreach($ticket->replay as $replay)
                                                            @include('tickets.ticket_reply',['adminRoleId' => $adminRoleId, 'isAction'=>$isAction])
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="text-primary">{{ __('messages.ticket.ticket_details') }}</h6>
                        </div>
                        <div class="card-body pt-0">
                            <div class="ribbon float-right tickets-ribbon ribbon-{{ \App\Models\Ticket::STATUS_COLOR[$ticket->status] }}">
                                <span> {{ \App\Models\Ticket::STATUS[$ticket->status] }}</span>
                            </div>
                            <div class="mt-3">
                                <span class="ticket-show-label">
                                    {{ __('messages.customer.customer').':' }}
                                </span>
                                <span class="ticket-show-value">
                                    {{ $ticket->user->name }}
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="ticket-show-label">
                                {{ __('messages.common.email').':' }}
                                </span>
                                <span class="ticket-show-value">
                                {{ $ticket->email }}
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="ticket-show-label">
                                {{ __('messages.category.category').':' }}
                                </span>
                                <span class="ticket-show-value">
                                {{ $ticket->category->name }}
                            </span>
                            </div>
                            <div class="mt-1">
                                <span class="ticket-show-label">
                                {{ __('messages.agent.agents').':' }}
                                </span>
                                <span class="ticket-show-value">
                                    @forelse($ticket->assignTo as $counter => $assignUser)
                                        @if($counter < 4)
                                            <span class="text-decoration-none avatar mr-2 avatar-sm">
                                            <img alt="" src="{{ $assignUser->photo_url }}"
                                                 data-toggle="tooltip" data-placement="bottom"
                                                 title="{{ $assignUser->name }}">
                                        </span>
                                        @elseif($counter == (count($ticket->assignTo) - 1))
                                            <span class="user_remaining_assignee avatar mr-2 avatar-sm"><span
                                                        class="user_remaining_count"><b> + {{ (count($ticket->assignTo) - 4) }}</b></span></span>
                                        @endif
                                    @empty
                                        {{ __('messages.agent.not_assigned') }}
                                    @endforelse
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="ticket-show-label">
                                {{ __('messages.common.created').':' }}
                                </span>
                                <span class="ticket-show-value">
                                    {{ $ticket->created_at->diffForHumans() }}
                                </span>
                            </div>

                            @if(count($ticket->media) > 0)
                                <p class="mb-0"><b>{{ __('messages.ticket.attachments').':' }}</b></p>
                                <div class="gallery gallery-md attachment__section">
                                    @foreach($ticket->media as $media)
                                        <div class="gallery-item ticket-attachment cursor-default"
                                             data-image="{{ mediaUrlEndsWith($media->getFullUrl()) }}"
                                             data-title="{{ substr($media->name, 0, 15) .'...' }}"
                                             href="{{ mediaUrlEndsWith($media->getFullUrl()) }}"
                                             title="{{ $media->name }}">
                                            <div class="ticket-attachment__icon d-none">
                                                <a href="{{ $media->getFullUrl() }}" target="_blank"
                                                   class="text-decoration-none attachment-icon view-icon"
                                                   data-placement="top"
                                                   title="{{ __('messages.common.view') }}"><i
                                                            class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('download.media',$media) }}" download="{{ $media->name }}"
                                                   class="text-warning text-decoration-none pr-1"
                                                   data-id="{{ $media->id }}"
                                                   data-placement="top"
                                                   title="{{ __('messages.common.download') }}"><i
                                                            class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <h4 class="p-5 mb-0 d-flex justify-content-center">No Ticket Found</h4>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @include('web.templates.templates')
    @include('tickets.ticket_reply_attachment_modal')
    @include('tickets.edit_reply_attachment_modal')
@endsection
@push('scripts')
    <script>
        let addReplyUrl = "{{ route('ticket.add-reply') }}";
        @if(!empty($ticket))
        let updateTicketUrl = "{{ route('ticket-status.update', ['ticket' => $ticket->id]) }}";
        let ticketId = {{ $ticket->id }};
        @endif
        let deleteTicketReplyUrl = "{{ url('reply') }}/";
        let ticketDeleteUrl = '{{ route('ticket.delete') }}';
    </script>
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script src="{{ asset('theme-assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script src="{{ mix('assets/js/tickets/view_tickets.js')}}"></script>
    <script>
        new WOW().init();
    </script>
@endpush
