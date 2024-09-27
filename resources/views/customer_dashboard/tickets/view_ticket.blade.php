@extends('customer_dashboard.app')
@section('title')
    {{ __('messages.ticket.is_public') }}
@endsection
@push('css')
    <link href="{{ asset('assets/css/ticket.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('theme-assets/css/animate.css') }}" rel="stylesheet"/>
@endpush
@section('content')

    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.ticket.ticket_details').' '  }}
                <sapn class="text-primary">{{ '#'.$ticket->ticket_id }}</sapn>
                {{ $ticket->is_public ? __('messages.ticket.is_public') : __('messages.ticket.is_private') }}
            </h1>
            <div class="section-header-breadcrumb ticket-action-section">
                <a href="{{ route('customer.myTicket') }}"
                   class="btn btn-primary form-btn float-right">{{__('messages.common.back')}}</a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            @include('flash::message')
            <div class="row">
                <div class="col-lg-8 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="tickets">
                                <div class="ticket-content w-100">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="ticket-header">
                                                <div class="ticket-sender-picture img-shadow">
                                                    <img src="{{ $ticket->user ? $ticket->user->photo_url : asset('assets/img/infyom-logo.png') }}"
                                                         class="reply-user-img object-fit-cover"
                                                         alt="image">
                                                </div>
                                                <div class="ticket-detail">
                                                    <div class="font-weight-600">{{ $ticket->title }}</div>
                                                    <div class="text-primary font-weight-600">{{ $ticket->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                            <div class="ticket-description">
                                                <b>{{ Form::label('description', __('messages.common.description').':') }}</b>
                                                <p>{!! $ticket->description ?  $ticket->description : __('messages.common.n/a') !!}</p>
                                            </div>
                                            <div class="ticket-divider"></div>
                                            @if(canUserReplyTicket($ticket))
                                                <div class="ticket-form">
                                                    @include('flash::message')
                                                    <div class="form-group">
                                                        <strong>{{ Form::label('add_comment', __('messages.common.replay').':') }}</strong>
                                                        <div id="addReplyContainer"></div>
                                                        <div class="text-left mt-3">
                                                            {{ Form::button(__('messages.common.save'), ['type'=>'button', 'class' => 'btn btn-primary', 'id'=>'addTicketReply', 'data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span>". __('messages.placeholder.processing')]) }}
                                                            <a href="{{ route('customer.myTicket') }}"
                                                               class="btn btn-secondary text-dark">{{__('messages.common.cancel')}}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-12 col-lg-12">
                                            <div class="ticket-reply-box">
                                                @foreach($ticket->replay as $reply)
                                                    <div class="jumbotron jumbotron-fluid ticket-reply"
                                                         data-remove-id="{{$reply->id}}">
                                                        <div class="row no-gutters">
                                                            <div class="col-xl-1 col-lg-2 col-md-2 col-sm-12 col-12 text-center">
                                                                <img width="50px" height="50px" class="reply-user-img"
                                                                     src="{{ $reply->user ? $reply->user->photo_url : asset('assets/img/infyom-logo.png') }}">
                                                            </div>
                                                            <div class="col-xl-11 col-lg-10 col-md-10 col-sm-12 col-10 pl-3">
                                                                <p class="mb-0">
                                                                    <span class="reply-user-name">{{ $reply->user->name }}</span>
                                                                    @if(checkLoggedInUserRole() && (getLoggedInUserId() == $reply->user->id || getLoggedInUserId() == getAdminRoleId()) && $ticket->status != \App\Models\Ticket::STATUS_CLOSED)
                                                                        <span class="float-right ticket-action-btn">
                                                                        <a href="javascript:void(0)"
                                                                           class="del-reply text-danger"
                                                                           data-id="{{ $reply->id }}"><i
                                                                                    class="fa fa-trash"></i></a>
                                                                        <a href="javascript:void(0)"
                                                                           class="edit-reply text-warning"
                                                                           data-id="{{ $reply->id }}"><i
                                                                                    class="ml-2 fa fa-edit"></i></a>
                                                                    </span>
                                                                    @endif
                                                                </p>
                                                                <p class="mb-0 replyTime-{{ $reply->id }}">{{ $reply->updated_at->timezone('Asia/Kolkata')->format('dS M, Y g:i A') }}</p>
                                                                <span class="reply-description description-{{ $reply->id }}">{!! $reply->description !!}</span>

                                                                <div id="editTicketReply-{{ $reply->id }}"
                                                                     class="d-none editReplyBox">
                                                                    <div class="editReplyContainer"
                                                                         id="editReply-{{ $reply->id }}"></div>
                                                                    <div class="text-left mt-3">
                                                                        <button class="btn btn-primary"
                                                                                id="editTicketReply"
                                                                                data-loading-text="<span class='spinner-border spinner-border-sm'></span> {{__('messages.placeholder.processing')}}">{{ __('messages.common.save') }}</button>
                                                                        <a href="#"
                                                                           class="btn btn-secondary text-dark cancelEditReply"
                                                                           id="cancelEditReply"
                                                                           data-id="{{ $reply->id }}">{{__('messages.common.cancel')}}</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
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
                        <div class="card-body">
                            <div class="ribbon float-right tickets-ribbon ribbon-{{ \App\Models\Ticket::STATUS_COLOR[$ticket->status] }}">
                                <a href="#" data-toggle="dropdown"
                                   class="tickets-notification-toggle action-dropdown position-xs-bottom"
                                   aria-expanded="false">
                                    {{ \App\Models\Ticket::STATUS[$ticket->status] }}
                                </a>
                            </div>
                            <p class="mb-0">
                                <b>{{ __('messages.common.created_by').':' }}</b> {{ $ticket->user ? $ticket->user->name : __('messages.common.n/a')}}
                            </p>
                            <p class="mb-0"><b>{{ __('messages.common.email').':' }}</b> {{ $ticket->email }}</p>
                            <p class="mb-0">
                                <b>{{ __('messages.category.category').':' }}</b>
                                {{ $ticket->category->name }}
                            </p>
                            @if(count($ticket->media) > 0)
                                <p class="mb-0"><b>{{ __('messages.ticket.attachments').':' }}</b></p>
                                <div class="row">
                                    @foreach($ticket->media as $media)
                                        <div class="col-xl-6 col-lg-6 col-md-3 col-sm-6 col-6 mb-2">
                                            <img class="img-thumbnail thumbnail-preview"
                                                 src="{{ mediaUrlEndsWith($media->getFullUrl()) }}"/>
                                            <a href="{{ $media->getFullUrl() }}" target="_blank"
                                               class="text-decoration-none">{{ __('messages.common.view') }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @include('web.templates.templates')
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        let addReplyUrl = "{{ route('ticket.add-reply') }}";
        let updateTicketUrl = "{{ route('ticket-status.update', ['ticket' => $ticket->id]) }}";
        let deleteTicketReplyUrl = "{{ url('reply') }}/";
        let ticketId = {{ $ticket->id }};
    </script>
    <script src="{{ asset('theme-assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ mix('assets/js/tickets/view_tickets.js')}}"></script>
    <script>
        new WOW().init();
    </script>
@endpush
