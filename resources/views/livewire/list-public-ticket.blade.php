<div>
    <div class="row public-ticket-list">
        <div class="col-xl-5 col-lg-5 col-md-8 col-sm-12 col-12 mb-2">
            <div class="input-group mb-md-3">
                <input type="search" name="searchTickets" class="form-control" id="searchTickets" autocomplete="off"
                       placeholder="{{ __('messages.ticket.search_your_ticket_here') }}"
                       wire:model.debounce.100ms="searchTickets">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-12 mb-md-3 mb-2">
            {{ Form::select('status', $status, \App\Models\Ticket::STATUS_ACTIVE, ['id'=>'statusFilterId','class' => 'form-control','placeholder' => __('messages.common.show_all'), 'wire:model' => 'statusFilter']) }}
        </div>
        <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12 mb-md-3 mb-2">
            {{ Form::select('category_id', array_flip($ticketCategories), null, ['id'=>'categoryFilterId','class' => 'form-control','placeholder' => __('messages.admin_dashboard.select_category'), 'wire:model' => 'categoryFilter']) }}
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-12 mb-md-3 mb-2">
            <div class="public-ticket-reset w-100">
                <button type="button" class="float-right btn btn-icon btn-block icon-left btn-danger resetFilter"
                        title="{{ __('messages.common.reset_filter') }}"
                        wire:click="resetFilter()">
                    <i class="fas fa-redo"></i> {{ __('messages.common.reset') }}
                </button>
            </div>
        </div>
    </div>
    @if(count($tickets) > 0)
        <div class="row justify-content-center">
            @foreach($tickets as $ticket)
                <a href="{{ route('web.ticket.view', $ticket->ticket_id) }}"
                   class="col-lg-6 col-md-12 text-decoration-none ticketZIndex mt-2"
                   target="_blank">
                    <div class="support-public-tickets mb-1 p-2 position-relative">
                        <div
                                class="ticketsContainer float-right ticketsContainer-{{ \App\Models\Ticket::STATUS_COLOR[$ticket->status] }}">
                            {{ \App\Models\Ticket::STATUS[$ticket->status] }}
                        </div>
                        <div class="mt-2 text-muted ticket-created-date">
                            <i class="far fa-clock"></i>
                            {{ \Carbon\Carbon::parse($ticket->created_at)->diffForHumans() }}
                        </div>
                        <div class="d-flex">
                            <div class="pl-2 user-avatar">
                                <img
                                        src="{{ $ticket->user ? $ticket->user->photoUrl : asset('assets/img/infyom-logo.png') }}"
                                        class="is-ticket-avatar" alt="img"/>
                            </div>
                            <div class="row ml-3 public-ticket-section">
                                <div class="col-12">
                                    <div class="ticket-subject-text text-primary">{{ $ticket->title }}</div>
                                    <div class="text-muted">
                                        {{ __('messages.common.created_by') }}
                                        : {{ $ticket->user ?  $ticket->user->name : __('messages.common.n/a') }}
                                    </div>
                                    <div class="mt-2 text-muted">
                                        <i class="fas fa-stream"></i>
                                        {{ $ticket->category ?  $ticket->category->name : __('messages.common.n/a') }}
                                    </div>
                                    <div class="mt-2 text-muted">
                                        @if($ticket->replay_count > 0)
                                            <i class="far fa-comment-alt">
                                            </i> {{ $ticket->replay_count }} {{ __('messages.common.comments') }}
                                        @else
                                            <span>&nbsp;</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-3 mb-md-0 mb-2 col-12">
            <div class="row paginatorRow">
                <div class="col-lg-2 col-md-6 col-sm-12 pt-2 text-md-left text-center mb-md-0 mb-3">
                <span class="d-inline-flex">
                    {{ __('messages.common.showing') }}
                    <span class="font-weight-bold ml-1 mr-1">{{ $tickets->firstItem() }}</span> - 
                    <span class="font-weight-bold ml-1 mr-1">{{ $tickets->lastItem() }}</span> {{ __('messages.common.of') }} 
                    <span class="font-weight-bold ml-1">{{ $tickets->total() }}</span>
                </span>
                </div>
                <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-md-end justify-content-center">
                    @if($tickets->count() > 0)
                        {{ $tickets->links() }}
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="row mt-5">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 text-center">
                @if($searchTickets == null || empty($searchTickets))
                    <h3>{{ __('messages.ticket.no_tickets_available') }}</h3>
                @else
                    <h3>{{ __('messages.ticket.ticket_not_found') }}</h3>
                @endif
            </div>
        </div>
    @endif
</div>
