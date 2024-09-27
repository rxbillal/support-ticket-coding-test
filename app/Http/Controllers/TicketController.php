<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use App\Queries\TicketDataTable;
use App\Repositories\TicketRepository;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewProvider;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Spatie\MediaLibrary\Models\Media;
use Throwable;
use Yajra\DataTables\DataTables;

class TicketController extends AppBaseController
{
    private $ticketRepository;
    public $settings = null;

    /**
     * TicketController constructor.
     * @param  TicketRepository  $ticketRepository
     */
    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
        // to share settings value to all view files.
        $this->settings = Setting::all()->pluck('value', 'key')->toArray();
        ViewProvider::share('settings', $this->settings);
    }

    /**
     * @param  Request  $request
     *
     * @throws Exception
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $statusArray = Ticket::STATUS;
        $statusColorArray = Ticket::STATUS_COLOR;

        $data['users'] = User::whereHas('roles', function (Builder $query) {
            $query->where('id', '!=', getCustomerRoleId());
        })->pluck('name', 'id');
        $data['assignees'] = $data['users'];

        return view('tickets.index', compact('statusArray', 'statusColorArray'))->with($data);
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        $data = $this->ticketRepository->prepareData();

        return view('tickets.create')->with('data', $data);
    }
    
    /**
     * @param  CreateTicketRequest  $request
     * @throws Throwable
     *
     * @return RedirectResponse
     */
    public function store(CreateTicketRequest $request)
    {
        $input = $request->all();
        $data = $this->ticketRepository->store($input);
        
        Flash::success(__('messages.success_message.ticket_create'));
        if (isset($data['file'])) {
            $data['redirectUrl'] = route('ticket.index');

            return $this->sendResponse($data, __('messages.success_message.ticket_create'));
        }
        
        return redirect()->route('ticket.index');
    }

    /**
     * @param $id
     *
     * @return Factory|View
     */
    public function show($id)
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::with(['media', 'replay.media', 'replay.user.media', 'assignTo.media']);
        if (Auth::user()->hasRole('Agent')) {
            $ticket = $ticket->whereHas('assignTo', function (Builder $query) {
                $query->where('user_id', '=', Auth::id());
            })->findOrFail($id);
        } elseif (Auth::user()->hasRole('Customer')) {
            $ticket = $ticket->whereId($id)->whereCreatedBy(Auth::id())->firstOrFail();
        } else {
            $ticket = $ticket->findOrFail($id);
        }

        return view('tickets.show', compact('ticket'));
    }

    /**
     * @param  Ticket  $ticket
     *
     * @return Application|Factory|RedirectResponse|View
     */
    public function edit($ticket_id)
    {
        if (getLoggedInUserRoleId() == getAgentRoleId()) {
            $ticket = Ticket::whereHas('assignTo', function (Builder $query) {
                $query->where('user_id', '=', Auth::id());
            })->findOrFail($ticket_id);
        } else {
            $ticket = Ticket::findOrFail($ticket_id);
        }
        if ($ticket->status == Ticket::STATUS_CLOSED) {
//            notify()->warning('Close ticket not editable.');
            Flash::warning(__('messages.error_message.ticket_not_editable'));

            if (getLoggedInUserRoleId() == getAgentRoleId()) {
                return redirect()->route('agent.ticket.index');
            } else {
                return redirect()->route('ticket.index');
            }
        }
        $data = $this->ticketRepository->prepareData();

        return view('tickets.edit', compact('data', 'ticket'));
    }

    /**
     * @param  UpdateTicketRequest  $request
     * @param  Ticket  $ticket
     *
     * @throws Throwable
     *
     * @return RedirectResponse
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $input = $request->all();
        $this->ticketRepository->update($input, $ticket);

        Flash::success(__('messages.success_message.ticket_update'));
        if (getLoggedInUserRoleId() == getAgentRoleId()) {
            return redirect()->route('agent.ticket.index');
        } elseif (getLoggedInUserRoleId() == getCustomerRoleId()) {
            return redirect()->route('customer.myTicket');
        } else {
            return redirect()->route('ticket.index');
        }
    }

    public function delete(Request $request)
    {
        $result = app(TicketRepository::class)->deleteTicket($request->get('id'));

        return $this->sendSuccess(__('messages.success_message.ticket_delete'));
    }

    /**
     * @param  CreateTicketRequest  $request
     * @throws Throwable
     *
     * @return Application|Factory|RedirectResponse|View
     */
    public function webStore(CreateTicketRequest $request)
    {
        $input = $request->all();
        $data = $this->ticketRepository->webStore($input);
        if (isset($data['file'])) {
            Flash::success(__('messages.success_message.ticket_create'));
            $data['redirectUrl'] = route('web.ticket_successful', $data['ticket_id']);
            
            return $this->sendResponse($data, 'Ticket created successfully.');
        }
        Flash::success(__('messages.success_message.ticket_create'));

        return redirect()->route('web.ticket_successful', $data['ticket_id']);
    }

    /**
     * @param $ticketId
     * @return Application|Factory|View
     */
    public function ticketSuccessView($ticketId)
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::whereTicketId($ticketId)->firstOrFail();

        if (Auth::check()) {
            $user = \Auth::user();
            $isAgent = $ticket->assignTo()->where('user_id', '=', $user->id)->exists();
            if ($ticket->created_by == $user->id || $isAgent || \Auth::user()->hasRole('Admin')) {
                $ticket = $ticket;
            } else {
                abort(404, 'Ticket Not Found');
            }
        } else {
            abort(404, 'Ticket Not Found');
        }

        return view('web.ticket_successful', compact('ticket'));
    }

    /**
     * @param $id
     * @param  Request  $request
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function ticketByUser($id, Request $request)
    {
        $statusId = $request->get('statusId');
        $categoryId = $request->get('categoryId');
        if ($request->ajax()) {
            return DataTables::of((new TicketDataTable())->getTicketByUser($id, $statusId, $categoryId))->make(true);
        }
    }

    /**
     * @param $id
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function changeStatus(Request $request, Ticket $ticket )
    {
        $status = $request->input('ticket_status');
        $this->ticketRepository->updateStatus($status, $ticket);

        return $this->sendResponse($ticket, __('messages.success_message.ticket_status'));
    }

    /**
     * @param $ticketId
     *
     * @return Application|Factory|View
     */
    public function viewTicket($ticketId)
    {
        $ticket = Ticket::whereTicketId($ticketId)->firstOrFail();
        abort_if(! $ticket->is_public, 404, 'Ticket Not Found');
        $ticket = Ticket::with([
            'media', 'replay.media', 'replay.user.media', 'assignTo.media', 'user.media',
        ])->whereId($ticket->id)->first();

        return view('web.view_ticket', compact('ticket'));
    }

    /**
     * @return Factory|View
     */
    public function showAllPublicTickets()
    {
        return view('web.public_tickets');
    }

    /**
     * @param  Request  $request
     *
     * @throws Throwable
     *
     * @return array|string
     */
    public function showFilterPublicTickets(Request $request)
    {
        $tickets = $this->ticketRepository->getFilteredTickets($request->all());

        return view('web.public_ticket_lists', compact('tickets'))->render();
    }

    /**
     * @return JsonResponse
     */
    public function attachmentDelete(Request $request)
    {
        $mediaId = $request->all();
        $attachment = Media::findOrFail($mediaId['mediaId'])->delete();

        return $this->sendSuccess(__('messages.success_message.attachment_delete'));
    }

    /**
     * @param  Ticket  $ticket
     *
     * @return JsonResponse
     */
    public function editAssignee(Ticket $ticket)
    {
        $data['assignUsers'] = $ticket->assignTo->pluck('id');
        $data['ticket'] = $ticket;
        $data['users'] = User::whereHas('roles', function (Builder $query) {
            $query->where('id', '!=', getCustomerRoleId());
        })->pluck('name', 'id');

        return $this->sendResponse($data, 'Ticket Assignee retrieved successfully.');
    }

    /**
     * @param  Ticket  $ticket
     *
     * @return JsonResponse
     */
    public function getAttachment(Ticket $ticket)
    {
        $result = $this->ticketRepository->getAttachments($ticket->id);

        return $this->sendResponse($result, 'Ticket retrieved successfully.');
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function downloadAttachment($id)
    {
        /** @var Media $media */
        $media = Media::findOrFail($id);

        return $media;
    }

    /**
     * @param  Media  $media
     *
     * @throws \Exception
     *
     * @return JsonResponse
     */
    public function deleteAttachment(Media $media)
    {
        if (getLoggedInUserRoleId() == getCustomerRoleId()) {
            if ($media->getCustomProperty('user_id') != getLoggedInUserId()) {
                return $this->sendError(__('messages.error_message.attachment_not_delete'));
            }
        }
        $media->delete();

        return $this->sendSuccess(__('messages.success_message.file_delete'));
    }

    /**
     * @param  Ticket  $ticket
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function addAttachment(Ticket $ticket, Request $request)
    {
        $input = $request->all();
        $this->ticketRepository->update($input, $ticket);
        try {
            $result = $this->ticketRepository->uploadFile($ticket, $input['file']);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode());
        }
        if (isset($input['file'])) {
            Flash::success('Ticket updated successfully.');
            if (getLoggedInUserRoleId() == getAgentRoleId()) {
                $input['redirectUrl'] = route('agent.ticket.index');
            } elseif (getLoggedInUserRoleId() == getCustomerRoleId()) {
                $input['redirectUrl'] = route('customer.myTicket');
            } else {
                $input['redirectUrl'] = route('ticket.index');
            }

            return $this->sendResponse($input, __('messages.success_message.ticket_update'));
        }

        return redirect()->route('ticket.index');
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function unassignedFromTicket(Request $request)
    {
        $result = app(TicketRepository::class)->unassignedFromTicket($request->get('id'));

        return $this->sendSuccess(__('messages.success_message.ticket_unassigned'));
    }
}
