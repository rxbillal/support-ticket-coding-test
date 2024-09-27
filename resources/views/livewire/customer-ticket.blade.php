<div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
{{--            @if(count($tickets) || $isPublicFilter != '' || $ticketFilter != null || $searchByCustomerTicket != '' || $categoryFilter != null)--}}
                <div class="d-flex mb-3 flex-wrap justify-content-center align-items-center sm-flex-column">
                    <div class="d-flex mb-2  ticket-type-filter">
                        <div class="selectgroup  ticket-status">
                            <label class="selectgroup-item mb-0">
                                <input type="radio" name="status" value="" wire:model="isPublicFilter"
                                       class="selectgroup-input">
                                <span class="selectgroup-button">{{ __('messages.common.all') }}</span>
                            </label>
                            @foreach($isPublic as $key => $value)
                                <label class="selectgroup-item mb-0">
                                    <input type="radio" name="ticketStatus" value="{{ $key }}"
                                           wire:model="isPublicFilter"
                                           class="selectgroup-input" checked="">
                                    <span class="selectgroup-button">
                                           @if($value == 'Private')
                                            {{ __('messages.group.private') }}
                                        @elseif($value == 'Public')
                                            {{ __('messages.group.public') }}
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <button type="button"
                                title="{{ __('messages.common.reset_filter') }}"
                                class="d-md-none d-block btn btn-sm btn-icon icon-left btn-danger ml-2 resetFilter"
                                wire:click="resetFilter()">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                    <div class="d-flex flex-1 flex-wrap justify-content-md-end justify-content-center mobile-responsive search-ticket">
                        <div class="px-2 ticket-filter-spacer">
                            <input wire:model.debounce.100ms="searchByCustomerTicket" type="search"
                                   id="searchByCustomerTicket"
                                   placeholder="{{ __('messages.ticket.search_ticket') }}" autocomplete="off"
                                   class="form-control customer-dashboard-ticket-search t-control-height show-overflow-ellipsis mr-0">
                        </div>
                        <div class="px-2 ticket-filter-spacer status-select">
                            {{ Form::select('status', $status, null, ['id'=>'ticketFilter', 'class' => 'form-control','placeholder' => __('messages.common.all'), 'wire:model' => 'ticketFilter']) }}
                        </div>
                        <div class="px-2 mt-lg-0 ticket-filter-spacer category-select">
                            {{ Form::select('category_id', array_flip($ticketCategories), null, ['id'=>'categoryFilterId','class' => 'form-control','placeholder' => __('messages.admin_dashboard.select_category'), 'wire:model' => 'categoryFilter']) }}
                        </div>
                        <div class="px-2 pr-0 mt-lg-0 sm-hide">
                            <button type="button"
                                    title="{{ __('messages.common.reset_filter') }}"
                                    class="float-right btn btn-block btn-icon icon-left btn-danger resetFilter t-control-height"
                                    wire:click="resetFilter()">
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>
                    </div>
                </div>
            {{--            @endif--}}
            @if(count($tickets) > 0)
                <div class="content">
                    <div class="row  position-relative">
                        @php
                            /** @var \App\Models\Ticket $ticket */
                        @endphp
                        @foreach($tickets as $ticket)
                            <div class="col-12">
                                <div class="border {{ $loop->last ? '' : 'border-bottom-0' }} position-relative">
                                    <div class="ribbon float-right tickets-ribbon ribbon-{{ \App\Models\Ticket::STATUS_COLOR[$ticket->status] }}">
                                        <a href="#" data-toggle="dropdown"
                                           class="tickets-notification-toggle action-dropdown position-xs-bottom"
                                           aria-expanded="false">
                                            {{ \App\Models\Ticket::STATUS[$ticket->status] }}
                                            @if(\App\Models\Ticket::STATUS[$ticket->status] != \App\Models\Ticket::STATUS[3])
                                                <i class="fa fa-caret-down"></i>
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
                                    <a title="<?php echo __('messages.common.delete')?>"
                                       class="text-danger action-btn delete-btn btn-delete-ticket"
                                       data-id="{{ $ticket->id }}"
                                       href="#">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <div>
                                        <div class="d-flex flex-wrap p-3 align-items-center">
                                            <div class="mx-2">
                                                <img class="img-ticket-user"
                                                     src="{{ $ticket->user ? $ticket->user->photo_url : '' }}" alt="">
                                            </div>
                                            <div class="ml-2">
                                                <label class="mb-0 d-block">
                                                    {{ $ticket->user ? $ticket->user->name : __('messages.common.n/a') }}
                                                </label>
                                                @if($ticket->is_public)
                                                    <i class="fas fa-lock-open"></i>
                                                @else
                                                    <i class="fas fa-lock"></i>
                                                @endif
                                                <a href="{{ route('ticket.view',$ticket->id) }}"
                                                   class="d-inline-block text-decoration-none">
                                                    <h1 class="font-weight-bold custom-ticket-title">
                                                        {{ $ticket->title }}
                                                    </h1>
                                                </a>
                                                @php
                                                    $inStyle = 'style';
                                                    $styleBackground = 'color: ';
                                                @endphp
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <label class="mb-0">
                                                        <i class="fas fa-sitemap" {{$inStyle}}=
                                                        "{{ $styleBackground }}{{ $ticket->category->color }}"></i>
                                                        {{ $ticket->category->name }}
                                                    </label>
                                                    <label class="ml-2 mb-0">
                                                        <i class="far fa-clock  text-lightgreen"></i>
                                                        <span show-local-timeZone="{{ $ticket->created_at->toDateTimeString() }}"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-1 mb-5 col-12">
                        <div class="row paginatorRow">
                            <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                                <span class="d-inline-flex">
                                    {{ __('messages.common.showing') }}
                                    <span class="font-weight-bold ml-1 mr-1">{{ $tickets->firstItem() }}</span> -
                                    <span class="font-weight-bold ml-1 mr-1">{{ $tickets->lastItem() }}</span> {{ __('messages.common.of') }}
                                    <span class="font-weight-bold ml-1">{{ $tickets->total() }}</span>
                                </span>
                            </div>
                            <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end pt-2">
                                @if($tickets->count() > 0)
                                    {{ $tickets->links() }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-lg-12 col-md-12 d-flex justify-content-center">
                    @if($searchByCustomerTicket == null || empty($searchByCustomerTicket))
                        <h1 class="font-size">{{ __('messages.ticket.no_tickets_available') }}</h1>
                    @else
                        <h1 class="font-size">{{ __('messages.ticket.ticket_not_found') }}</h1>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
