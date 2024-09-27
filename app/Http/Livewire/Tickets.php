<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserNotification;
use App\Repositories\TicketRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Tickets extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $searchByTicket;
    public $statusFilter = Ticket::STATUS_ACTIVE;
    public $ticketStatus;
    public $ticketCategories;
    public $status;
    public $isPublicFilterTickets = '';
    public $isEnabledCategory;
    public $category;
    public $ticketId = '';
    public $categoryFilter;
    protected $listeners = [
        'changeStatus', 'deleteTicket', 'RemoveAssignUserId', 'changeFilter', 'updateAssignees', 'resetFilter',
        'unassignedTicket',
    ];

    public function mount($category = null)
    {
        $this->ticketStatus = Ticket::STATUS;
        $this->status = Ticket::TICKET;
        if (is_null($category)) {
            $this->isEnabledCategory = false;
            $this->ticketCategories = array_flip(Category::orderBy('name')->pluck('name', 'id')->toArray());
        } else {
            $this->isEnabledCategory = true;
            $this->category = $category;
        }
    }

    /**
     * @return Factory|View
     */
    public function render()
    {
        $data['tickets'] = $this->searchTicket();

        return view('livewire.tickets')->with($data);
    }

    /**
     * @param $input
     * @param $id
     */
    public function updateAssignees($input, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $oldAgentIds = array();
        foreach ($ticket->assignTo as $agent){
            $oldAgentIds[] = $agent->id;
        }

        $assignees = ! empty($input) ? $input : $input = [getLoggedInUserId()];
        $newAgentIds = array_diff($assignees, $oldAgentIds);
        $ticket->assignTo()->sync($assignees);
        
        if (! empty($newAgentIds)) {
            $input = $ticket->only('title', 'ticket_id', 'email');
            foreach ($newAgentIds as $agentId) {
                if ($agentId != getLoggedInUserId()){
                    sendEmailToAgent($agentId,
                        'mail.ticket_assigned_you',
                        'Ticket Successfully Assigned You',
                        $input);

                    $notificationRecord = [
                        'Ticket assigned you.',
                        UserNotification::ASSIGN_TICKET_TO_AGENT,
                        ucfirst($ticket->title).' assigned you.',
                        $agentId,
                    ];
                    addNotification($notificationRecord);
                }
            }
        }

        $this->dispatchBrowserEvent('assigneeUpdated');
    }

    /**
     * @return string
     */
    public function paginationView()
    {
        return 'livewire.custom-pagenation';
    }

//    public function nextPage($lastPage)
//    {
//        if ($this->page < $lastPage) {
//            $this->page = $this->page + 1;
//        }
//    }
//
//    public function previousPage()
//    {
//        if ($this->page > 1) {
//            $this->page = $this->page - 1;
//        }
//    }

    /**
     * @param  $id
     *
     * @throws Exception
     */
    public function deleteTicket($id)
    {
        $result = app(TicketRepository::class)->deleteTicket($id);
        if ($result) {
            $this->dispatchBrowserEvent('deleted');
            $this->searchTicket();
        }
    }

    public function unassignedTicket($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $ticket->assignTo()->detach(Auth::user()->id);
        $this->dispatchBrowserEvent('unassignedFromTicket');
        $this->searchTicket();
    }

    /**
     * @param $id
     *
     * @param $status
     */
    public function changeStatus(Ticket $ticket, $status)
    {
        $result = app(TicketRepository::class)->updateStatus($status, $ticket);
        if ($result) {
            $this->dispatchBrowserEvent('closeAlert');
        }
    }

    public function updatingsearchByTicket()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->isPublicFilterTickets = '';
        $this->categoryFilter = '';
        $this->statusFilter = Ticket::STATUS_ACTIVE;
        $this->searchByTicket = '';
        $this->resetPage();
    }

    /**
     * @param $param
     *
     * @param $value
     */
    public function changeFilter($param, $value)
    {
        $this->resetPage();
        $this->$param = $value;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchTicket()
    {
        if (Auth::user()->hasRole('Agent')) {
            $query = User::find(Auth::id())->ticket()->with('user.media', 'assignTo', 'category');
        } else {
            $query = Ticket::with(['user.media', 'category', 'assignTo.media'])
                ->when(($this->isEnabledCategory && $this->category), function (Builder $query) {
                    $query->where('category_id', $this->category->id);
                });
        }

        $query->when($this->searchByTicket != '', function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->whereHas('user', function (Builder $query) {
                    $query->Where('name', 'like', '%'.strtolower($this->searchByTicket).'%');
                });
                $query->orWhere('title', 'like', '%'.strtolower($this->searchByTicket).'%');
                $query->orWhere('created_at', 'like', '%'.strtolower($this->searchByTicket).'%');
                $query->orWhere('tickets.ticket_id', 'like', '%'.strtolower($this->searchByTicket).'%');
                $query->orWhere('email', 'like', '%'.strtolower($this->searchByTicket).'%');
            });
        });

        $query->when($this->statusFilter !== '', function (Builder $query) {
            if ($this->statusFilter == Ticket::STATUS_ACTIVE) {
                $query->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS]);
            } else {
                $query->where('status', '=', $this->statusFilter);
            }
        });

        $query->when($this->isPublicFilterTickets != '', function (Builder $query) {
            $query->where('is_public', '=', $this->isPublicFilterTickets);
        });

        $query->when($this->categoryFilter != '', function (Builder $query) {
            $query->where('category_id', '=', $this->categoryFilter);
        });
        $query->orderBy('created_at', 'desc');

        $all = $query->paginate(7);
        $currentPage = $all->currentPage();
        $lastPage = $all->lastPage();
        if ($currentPage > $lastPage) {
            $this->page = $lastPage;
        }

        return $all;
    }
}
