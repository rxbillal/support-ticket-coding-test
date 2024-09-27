<?php

namespace App\Http\Livewire;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Livewire\Component;

class TicketReplay extends Component
{
    public $ticket;
    public $replays;
    public $description;

    public function mount($ticket)
    {
        $this->ticket = $ticket;
        $this->replays = $ticket->replay;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields()
    {
        $this->description = '';
    }

    public function store()
    {
        $input = $this->validate([
            'description' => 'required',
        ]);

        $input['ticket_id'] = $this->ticket->id;
        $input['user_id'] = Auth::id();
        $ticketReplay = \App\Models\TicketReplay::create($input);

        $this->resetInputFields();
        $this->replays[] = $ticketReplay;
    }

    public function destory($id)
    {
        \App\Models\TicketReplay::find($id)->delete();

        $this->replays = Ticket::find($this->ticket->id)->replay;
    }

    public function render()
    {
        return view('livewire.ticket-replay');
    }
}
