<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;

class ListCategories extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

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

    public function render()
    {
        $categories = $this->searchCategories();

        return view('livewire.list-categories', compact('categories'));
    }

    private function searchCategories()
    {
        $categories = Category::withCount([
            'ticket' => function ($query) {
                $query->where('status', '=', Ticket::STATUS_OPEN);
            },
        ])
            ->orderBy('name')
            ->having('ticket_count', '>', 0);

        $all = $categories->paginate(12);
        $currentPage = $all->currentPage();
        $lastPage = $all->lastPage();
        if ($currentPage > $lastPage) {
            $this->page = $lastPage;
        }

        return $all;
    }
}
