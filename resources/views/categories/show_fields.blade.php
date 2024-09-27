@php
    $inStyle = 'style';
    $border = 'border-top: 3px solid';
    $color = 'color:';
    $bgColor = 'background-color:';
@endphp

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <b>{{ Form::label('name',  __('messages.common.name').':') }}</b>
            <p>{{ $categories->name }}</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <b>{{ Form::label('color', __('messages.common.color').':') }}</b>
            <p {{$inStyle}}="{{$bgColor}} {{ $categories->color }}" class="category-color">{{ $categories->color }}</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <b>{{ Form::label('total_ticket',__('messages.ticket.total_ticket').':') }}</b>
            <p>{{ $categories->ticket_count }}</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <b>{{ Form::label('open_ticket',__('messages.admin_dashboard.open_tickets').':') }}</b>
            <p>{{ $counter['openTicket'] }}</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <b>{{ Form::label('close_ticket',__('messages.admin_dashboard.closed_tickets').':') }}</b>
            <p>{{ $counter['closeTicket'] }}</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <b>{{ Form::label('progress_ticket',__('messages.admin_dashboard.in_progress_tickets').':') }}</b>
            <p>{{ $counter['inProgressTicket'] }}</p>
        </div>
    </div>
</div>

<div class="row justify-content-center mb-4">
    <h4>{{ $categories->ticket_count <=0 ? __('messages.ticket.no_found_ticket').' ' : __('messages.ticket.tickets_of').' ' }}
        <span {{$inStyle}}="{{$color}} {{ $categories->color }}">{{ $categories->name }}</span></h4>
</div>

<div class="row">
    @foreach($tickets as $ticket)
        <div class="col-12 col-sm-6 col-md-6 col-xl-4">
            <div class="hover-effect-tickets position-relative mb-4 card-hover-border">
                <div class="ribbon float-right tickets-ribbon ribbon-{{ \App\Models\Ticket::STATUS_COLOR[$ticket->status] }}">
                    <span>{{ \App\Models\Ticket::STATUS[$ticket->status] }}</span>
                </div>
                <div class="tickets-listing-details" {{$inStyle}}="{{$border}} {{ $categories->color }}">
                <div class="d-flex tickets-listing-description">
                    <div class="tickets-data">
                        <h3 class="tickets-listing-title mb-1">
                            <a href="{{ route('ticket.show',$ticket->id) }}"
                               target="_blank"
                               class="tickets-listing-ticketid">{{ __('messages.common.#').$ticket->ticket_id }}</a>
                        </h3>
                        <h3 class="tickets-listing-title">
                            <i class="fas fa-briefcase text-black"></i>
                            &nbsp;{{ $ticket->title }}
                        </h3>
                        <h3 class="tickets-listing-title">
                            <i class="far fa-envelope text-darkorange"></i>
                            &nbsp;{{ $ticket->email }}
                        </h3>
                        <h3 class="tickets-listing-title">
                            <i class="far fa-user text-pick"></i>
                            &nbsp;{{ $ticket->user->name }}
                        </h3>
                        <h3 class="tickets-listing-title">
                            <i class="far fa-clock  text-lightgreen"></i>
                            &nbsp;{{ $ticket->created_at->format('dS M, Y') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
</div>
@endforeach
</div>
