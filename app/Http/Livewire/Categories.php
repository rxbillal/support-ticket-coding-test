<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Ticket;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $searchByCategory = '';
    protected $listeners = ['deleteCategory'];

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
     * @param  Category  $id
     *
     * @throws Exception
     */
    public function deleteCategory($id)
    {
        $Models = [
            Ticket::class,
        ];
        $result = canDelete($Models, 'category_id', $id);
        if ($result) {
            $this->dispatchBrowserEvent('deleted', __('messages.error_message.category_delete'));
        } else {
            $category = Category::find($id);
            $category->delete();
            $this->dispatchBrowserEvent('deleted');
            $this->searchCategory();
        }
    }

    public function updatingsearchByCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = $this->searchCategory();

        return view('livewire.categories', compact('categories'))->with('searchByCategory');
    }

    /**
     * @return mixed
     */
    public function searchCategory()
    {
        /** @var Category $query */
        $query = Category::withCount(['ticket', 'openTickets']);

        $query->when($this->searchByCategory != '', function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->orWhere('name', 'like', '%'.strtolower($this->searchByCategory).'%');
                $query->orWhere('color', 'like', '%'.strtolower($this->searchByCategory).'%');
            });
        });
        $query->orderBy('created_at');

        $all = $query->paginate(9);
        $currentPage = $all->currentPage();
        $lastPage = $all->lastPage();
        if ($currentPage > $lastPage) {
            $this->page = $lastPage;
        }

        return $all;
    }
}
