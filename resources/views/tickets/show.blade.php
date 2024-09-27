@php
    /** @var $ticket \App\Models\Ticket */
  $adminRoleId = getAdminRoleId();
  $isAction = checkLoggedInUserRole();
@endphp
@extends(Auth::user()->hasRole('Customer') ? 'customer_dashboard.app' : 'layouts.app')
@section('title')
    {{ __('messages.ticket.ticket_details')  }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('theme-assets/css/dropzone.css') }}">
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/ticket.css') }}" rel="stylesheet" type="text/css"/>
    @livewireStyles
@endpush
@section('content')
    <section class="section">
        <div class="section-header flex-wrap">
            <h1 class="mr-3">{{ __('messages.ticket.ticket_details').' '  }}</h1>
            <div class="section-header-breadcrumb ticket-action-section my-sm-0 my-1">
                @role('Admin')
                <a href="{{ route('ticket.edit',$ticket->id) }}"
                   class="btn btn-warning form-btn float-right mr-2">{{ __('messages.ticket.edit_ticket') }}</a>
                <a href="{{ route('ticket.index') }}"
                   class="btn btn-primary form-btn float-right" id="cancelBtn">{{__('messages.common.back')}}</a>
                @endrole
                @role('Agent')
                <a href="{{ route('agent.ticket.edit',$ticket->id) }}"
                   class="btn btn-warning form-btn float-right mr-2">{{ __('messages.ticket.edit_ticket') }}</a>
                <a href="{{ route('agent.ticket.index') }}"
                   class="btn btn-primary form-btn float-right" id="cancelBtn">{{__('messages.common.back')}}</a>
                @endrole
                @role('Customer')
                <a href="{{ route('customer.editTicket',$ticket->id) }}"
                   class="btn btn-warning form-btn float-right mr-2">{{ __('messages.ticket.edit_ticket') }}</a>
                <a href="{{ route('customer.myTicket') }}"
                   class="btn btn-primary form-btn float-right" id="cancelBtn">{{__('messages.common.back')}}</a>
                @endrole
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            @include('flash::message')

            <div class="row">
                <div class="col-xl-9 col-lg-8 col-md-12 col-sm-12 col-12">
                    @include('flash::message')
                    <div class="card rounded-0">
                        <div class="p-3 text-white {{ $ticket->is_public ? 'bg-success' : 'bg-danger' }}">
                            <i class="fas {{ $ticket->is_public ? 'fa-lock-open' : 'fa-lock' }}"></i>
                            <span class="ml-2">
                                {{ $ticket->is_public ? __('messages.ticket.is_public') : __('messages.ticket.is_private') }}
                                {{ '#'.$ticket->ticket_id }}
                           </span>
                        </div>
                        <div class="p-3">
                            <h2 class="font-weight-normal">{{$ticket->title}}</h2>
                            <div>
                                <label for="description"
                                       class="font-weight-bold">{{ __('messages.common.description').':' }}</label><br>
                                {!! $ticket->description !!}
                            </div>
                        </div>
                        @if($ticket->status != \App\Models\Ticket::STATUS_CLOSED)
                            <div class="p-3 bg-primary">
                                <button id="btnPostReplay" class="btn btn-outline-white rounded-0 fa-lg">
                                    <i class="fas fa-comment-alt mr-2"></i>
                                    {{__('messages.ticket.post_reply')}}</button>
                            </div>

                            <div class="card-body ticket-add-replay m-0 px-3 pb-0 display-none" id="ticketAddReplay">
                                <div class="ticket-form ">
                                    <div class="form-group">
                                        <form id="addRelyForm" action="{{ route('ticket.add-reply') }}">
                                            {{ Form::hidden('ticket_id',$ticket->id) }}
                                            <strong>{{ Form::label('addReplyContainer', __('messages.ticket.add_reply').':') }}
                                                <span class="text-danger">*</span></strong>
                                            <textarea name="description" id="addReplyContainer"></textarea>
                                            <div class="text-left mt-3">
                                                {{ Form::button(__('messages.ticket.post_reply'), ['type'=>'submit', 'class' => 'btn btn-primary custom-ticket-btn', 'id'=>'addTicketReply', 'data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span>". __('messages.placeholder.processing')]) }}
                                                <button type="button" id="attachmentButton" data-toggle="modal"
                                                        data-target="#addAttachment" class="btn btn-info btn-icon custom-ticket-btn">
                                                    <i class="fas fa-paperclip"></i>
                                                    {{ __('messages.common.add').' '.__('messages.ticket.attachments') }}
                                                </button>
                                                <button type="button" id="btnCancelReplay"
                                                        class="btn btn-secondary mt-sm-0 text-dark custom-ticket-btn">
                                                    {{__('messages.common.cancel')}}
                                                </button>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="card-body p-0">
                            <div class="tickets">
                                <div class="ticket-content w-100">
                                    <div class="ticket-reply-box p-0 ticket-reply-scroll">
                                        @foreach($ticket->replay as $replay)
                                            @include('tickets.ticket_reply',['adminRoleId' => $adminRoleId,'isAction'=>$isAction])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 ribbon-responsive-lg col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="text-muted">{{ __('messages.ticket.ticket_details') }}</h6>
                        </div>
                        <div class="card-body pt-0">
                            <div class="">
                                <a href="javascript:void(0)"
                                   @if($ticket->status != \App\Models\Ticket::STATUS_CLOSED) data-toggle="dropdown"
                                   @endif
                                   class="btn btn-outline-secondary color-hover-black d-flex w-100 {{ $ticket->status != \App\Models\Ticket::STATUS_CLOSED ? 'action-dropdown position-xs-bottom' : '' }} "
                                   aria-expanded="false">
                                    <span>Status: {{ \App\Models\Ticket::STATUS[$ticket->status] }}</span>
                                    @if(\App\Models\Ticket::STATUS[$ticket->status] != \App\Models\Ticket::STATUS[3])
                                        <span class="ml-auto"><i class="fa fa-caret-right fa-lg"></i></span>
                                    @endif
                                </a>
                                @if(\App\Models\Ticket::STATUS[$ticket->status] == \App\Models\Ticket::STATUS[1])
                                    <div class="dropdown-menu dropdown-menu-right tickets-dropdown-menu">
                                        <div class="dropdown-list-content-tickets dropdown-list-icons">
                                            <a href="#"
                                               class="dropdown-item dropdown-item-desc change-status"
                                               data-id="{{ $ticket->id }}"
                                               data-status="{{ \App\Models\Ticket::STATUS_IN_PROGRESS }}">{{ __('messages.common.in_progreess') }}
                                            </a>
                                            <a href="#"
                                               class="dropdown-item dropdown-item-desc change-status"
                                               data-id="{{ $ticket->id }}"
                                               data-status="{{ \App\Models\Ticket::STATUS_CLOSED }}">{{ __('messages.common.closed') }}
                                            </a>
                                        </div>
                                    </div>
                                @elseif(\App\Models\Ticket::STATUS[$ticket->status] == \App\Models\Ticket::STATUS[2])
                                    <div class="dropdown-menu dropdown-menu-right tickets-dropdown-menu">
                                        <div class="dropdown-list-content-tickets dropdown-list-icons">
                                            <a href="#"
                                               class="dropdown-item dropdown-item-desc change-status"
                                               data-id="{{ $ticket->id }}"
                                               data-status="{{ \App\Models\Ticket::STATUS_OPEN }}">{{ __('messages.common.open') }}
                                            </a>
                                            <a href="#"
                                               class="dropdown-item dropdown-item-desc change-status"
                                               data-id="{{ $ticket->id }}"
                                               data-status="{{ \App\Models\Ticket::STATUS_CLOSED }}">{{ __('messages.common.closed') }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
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
                                     <a href="{{ (Auth::user()->hasRole('Customer|Agent')) ? route('public.tickets', ['category' => $ticket->category->name]) : route('category.show', $ticket->category->id) }}"
                                        class="text-decoration-none hover-primary">
                                    {{ $ticket->category->name }}
                                </a>&nbsp;
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="ticket-show-label">
                                {{ __('messages.agent.agents').':' }}
                                </span>
                                <span class="ticket-show-value">
                                    @forelse($ticket->assignTo as $counter => $assignUser)
                                        @if($counter < 4)
                                            @if(Auth::user()->hasRole('Admin'))
                                                <a href="{{ route('user.show',$assignUser->id) }}"
                                                   class="text-decoration-none avatar mr-2 avatar-sm">
                                                    <img alt="" src="{{ $assignUser->photo_url }}"
                                                         data-toggle="tooltip" data-placement="bottom"
                                                         title="{{ $assignUser->name }}">
                                                </a>
                                            @else
                                                <span class="text-decoration-none avatar mr-2 avatar-sm">
                                                    <img alt="" src="{{ $assignUser->photo_url }}"
                                                         data-toggle="tooltip" data-placement="bottom"
                                                         title="{{ $assignUser->name }}">
                                                </span>
                                            @endif
                                        @elseif($counter == (count($ticket->assignTo) - 1))
                                            <span class="user_remaining_assignee avatar mr-2 avatar-sm"><span
                                                        class="user_remaining_count"><b> + {{ (count($ticket->assignTo) - 4) }}</b></span></span>
                                        @endif
                                    @empty
                                        {{ __('messages.common.n/a') }}
                                    @endforelse
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="ticket-show-label">
                                {{ __('messages.common.created_on').':' }}
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
                                                   class="text-decoration-none text-primary"
                                                   data-placement="top"
                                                   title="{{ __('messages.common.view') }}"><i
                                                            class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('download.media',$media) }}"
                                                   download="{{ $media->name }}"
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

                            <div class="mt-3">
                                @if(Auth::user()->hasRole('Agent'))
                                    <button data-id="{{ $ticket->id }}" type="button"
                                            class="btn btn-outline-danger btn-block unassigned-btn">
                                        {{ __('messages.common.delete').' '.__('messages.ticket.ticket') }}
                                    </button>
                                @else
                                    <button data-id="{{ $ticket->id }}" type="button"
                                            class="btn btn-outline-danger btn-block delete-btn">
                                        {{ __('messages.common.delete').' '.__('messages.ticket.ticket') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('tickets.templates.templates')
        @include('tickets.ticket_reply_attachment_modal')
        @include('tickets.edit_reply_attachment_modal')
    </section>
@endsection
@push('scripts')
    <script>
        let addReplyUrl = "{{ route('ticket.add-reply') }}"
        let updateTicketUrl = "{{ route('ticket-status.update', ['ticket' => $ticket->id]) }}"
        let deleteTicketReplyUrl = "{{ url('reply') }}/"
        let ticketId = {{ $ticket->id }};
        let ticketDeleteUrl = '{{ route('ticket.delete') }}'
    </script>
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/js/summernote.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ mix('assets/js/tickets/view_tickets.js')}}"></script>
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
@endpush
