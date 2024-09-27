<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Ticket;
use App\Repositories\TicketRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerTicket extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $searchByCustomerTicket;
    public $ticketFilter = Ticket::STATUS_ACTIVE;
    public $isPublicFilter = '';
    public $status;
    public $isPublic = '';
    public $ticketCategories;
    public $categoryFilter;
    protected $listeners = ['changeStatus', 'changeFilter', 'deleteTicket'];

    public function mount()
    {
        $this->status = Ticket::STATUS;
        $this->isPublic = Ticket::TICKET;
        $this->ticketCategories = array_flip(Category::orderBy('name')->pluck('name', 'id')->toArray());
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

    public function updatingsearchByCustomerTicket()
    {
        $this->resetPage();
    }

    public function updatingticketFilter()
    {
        $this->resetPage();
    }

    public function updatingisPublicFilter()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->ticketFilter = Ticket::STATUS_ACTIVE;
        $this->isPublicFilter = '';
        $this->searchByCustomerTicket = '';
        $this->categoryFilter = '';
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
     * @param $id
     * @param $status
     */
    public function changeStatus(Ticket $ticket, $status)
    {
        $result = app(TicketRepository::class)->updateStatus($status, $ticket);
        if ($result) {
            $this->dispatchBrowserEvent('closeAlert');
        }
    }

    /**
     * @param $id
     */
    public function deleteTicket($id)
    {
        $result = app(TicketRepository::class)->deleteTicket($id);
        if ($result) {
            $this->dispatchBrowserEvent('deleted');
            $this->customerTicket();
        }
    }

    /**
     * @return Factory|View
     */
    public function render()
    {
        $tickets = $this->customerTicket();

        return view('livewire.customer-ticket', compact('tickets'));
    }

    /**
     * @return LengthAwarePaginator
     */
    public function customerTicket()
    {
        $query = Ticket::with(['category', 'user.media'])->where('created_by', '=', Auth::id());

        $query->when($this->searchByCustomerTicket != '', function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->whereHas('category', function (Builder $query) {
                    $query->Where('name', 'like', '%'.strtolower($this->searchByCustomerTicket).'%');
                });
                $query->orWhere('title', 'like', '%'.strtolower($this->searchByCustomerTicket).'%');
                $query->orWhere('created_at', 'like', '%'.strtolower($this->searchByCustomerTicket).'%');
                $query->orWhere('tickets.ticket_id', 'like', '%'.strtolower($this->searchByCustomerTicket).'%');
                $query->orWhere('email', 'like', '%'.strtolower($this->searchByCustomerTicket).'%');
            });
        });
        $query->when($this->ticketFilter !== '', function (Builder $query) {
            if ($this->ticketFilter == Ticket::STATUS_ACTIVE) {
                $query->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS]);
            } else {
                $query->where('status', '=', $this->ticketFilter);
            }
        });

        $query->when($this->isPublicFilter != '', function (Builder $query) {
            $query->where('is_public', '=', $this->isPublicFilter);
        });
        $query->when($this->categoryFilter != '', function (Builder $query) {
            $query->where('category_id', '=', $this->categoryFilter);
        });

        $all = $query->paginate(7);
        $currentPage = $all->currentPage();
        $lastPage = $all->lastPage();
        if ($currentPage > $lastPage) {
            $this->page = $lastPage;
        }

        return $all;
    }
}
