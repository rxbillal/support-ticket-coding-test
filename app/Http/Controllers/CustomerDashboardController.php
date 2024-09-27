<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use App\Repositories\TicketRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

class CustomerDashboardController extends AppBaseController
{
    private $ticketRepository;

    /**
     * TicketController constructor.
     * @param  TicketRepository  $ticketRepository
     */
    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $data['totalOpenTickets'] = Ticket::whereCreatedBy(Auth::id())->whereStatus(Ticket::STATUS_OPEN)->count();
        $data['totalClosedTickets'] = Ticket::whereCreatedBy(Auth::id())->whereStatus(Ticket::STATUS_CLOSED)->count();

        return view('customer_dashboard.index', compact('data'));
    }

    /**
     * @return Application|Factory|View
     */
    public function viewCustomerTicket()
    {
        return view('customer_dashboard.tickets.index');
    }

    public function editCustomerTicket($ticket_id)
    {
        $ticket = Ticket::whereId($ticket_id)->where('created_by', '=', \Auth::id())->firstOrFail();
        if ($ticket->status == Ticket::STATUS_CLOSED) {
            Flash::warning(__('messages.error_message.ticket_not_editable'));

            return redirect()->route('customer.myTicket');
        }
        $data = $this->ticketRepository->prepareData();

        return view('customer_dashboard.tickets.edit', compact('data', 'ticket'));
    }

    /**
     * @param  UpdateTicketRequest  $request
     * @param  Ticket  $ticket
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCustomerTicket(UpdateTicketRequest $request, Ticket $ticket)
    {
        $input = $request->all();
        $this->ticketRepository->update($input, $ticket);
        Flash::success(__('messages.success_message.ticket_update'));

        return redirect()->route('customer.myTicket');
    }

    /**
     * @return Application|Factory|View
     */
    public function createTicket()
    {
        $data = $this->ticketRepository->prepareData();

        return view('customer_dashboard.tickets.create')->with('data', $data);
    }

    /**
     * @param  CreateTicketRequest  $request
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function storeTicket(CreateTicketRequest $request)
    {
        $input = $request->all();
        $data = $this->ticketRepository->store($input);

        Flash::success(__('messages.success_message.ticket_create'));
        if (isset($data['file'])) {
            $data['redirectUrl'] = route('customer.myTicket');

            return $this->sendResponse($data, __('messages.success_message.ticket_create'));
        }

        return redirect()->route('customer.myTicket');
    }  
}
