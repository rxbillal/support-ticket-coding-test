<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class ListPublicTicket extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $status;
    public $ticketCategories;
    public $categoryFilter;
    public $statusFilter = Ticket::STATUS_ACTIVE;
    public $search;
    public $searchTickets = '';
    protected $listeners = ['changeFilter'];

    public function mount(Request $request)
    {
        $categoryName = $request->get('category', '');
        $this->search = $request->get('search', '');
        $this->status = Ticket::STATUS;
        $this->ticketCategories = array_flip(Category::orderBy('name')->pluck('name', 'id')->toArray());
        if ($categoryName != '') {
            $this->categoryFilter = Category::where('name', '=', strtolower($categoryName))->first()->id;
        }
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

    public function updatingcategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingstatusFilter()
    {
        $this->resetPage();
    }

    public function updatingsearchTickets()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->categoryFilter = '';
        $this->statusFilter = Ticket::STATUS_ACTIVE;
        $this->searchTickets = '';
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

    public function render()
    {
        $tickets = $this->searchTicket();

        return view('livewire.list-public-ticket', compact('tickets'))->with('searchTickets');
    }

    private function searchTicket()
    {
        if ($this->search != '') {
            $this->statusFilter = '';
        }

        if ($this->search != '') {
            $tickets = Ticket::with(['user', 'category', 'replay.user.media'])
                ->whereIsPublic(1)
                ->where('title', 'LIKE', '%'.strtolower($this->search).'%')
                ->orWhere('email', 'LIKE', '%'.strtolower($this->search).'%');
        } else {
            $tickets = Ticket::with(['user.media', 'category'])
                ->whereIsPublic(Ticket::STATUS_OPEN)->orderByDesc('created_at')->withCount('replay');
        }

        $tickets->when($this->categoryFilter != '', function (Builder $query) {
            $query->where('category_id', '=', $this->categoryFilter);
        })
        ->when($this->statusFilter !== '', function (Builder $query) {
            if ($this->statusFilter == Ticket::STATUS_ACTIVE) {
                $query->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS]);
            } else {
                $query->where('status', '=', $this->statusFilter);
            }
        })
            ->when($this->searchTickets != '', function (Builder $query) {
                $query->where(function (Builder $searchQuery) {
                    $searchQuery->orWhere('title', 'like', '%'.strtolower($this->searchTickets).'%');
                    $searchQuery->orWhere('email', 'like', '%'.strtolower($this->searchTickets).'%');
                    $searchQuery->orWhere('ticket_id', 'like', '%'.strtolower($this->searchTickets).'%');
                    $searchQuery->orWhere('description', 'like', '%'.strtolower($this->searchTickets).'%');
                });
            });

//        $all = $tickets->paginate(8);
//        $currentPage = $all->currentPage();
//        $lastPage = $all->lastPage();
//        if ($currentPage > $lastPage) {
//            $this->page = $lastPage;
//        }

        return $tickets->paginate(8);
    }
}
