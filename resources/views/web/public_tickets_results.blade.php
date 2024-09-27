<ul class="dropdown-menu d-block position-relative w-100">
    @if(!$publicTickets->isEmpty())
        @foreach($publicTickets as $publicTicket)
            <li>
                <a href="{{ route('web.ticket.view', $publicTicket->ticket_id) }}" class="text-decoration-none d-block"
                   target="_blank">
                    {{ $publicTicket->title }}
                </a>
            </li>
        @endforeach
    @else
        <li class="px-2 noTicketsFound">{{ __('messages.ticket.ticket_not_found') }}</li>
    @endif
</ul>
