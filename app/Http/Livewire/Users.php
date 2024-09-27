<?php

namespace App\Http\Livewire;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $searchByUser;
    public $userRoleFilter = '';
    protected $listeners = ['setEmailVerified'];
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

    public function updatingsearchByUser()
    {
        $this->resetPage();
    }

    public function setEmailVerified($userId, $isEmailVerified)
    {
        /** @var User $user */
        $user = User::whereId($userId)->firstOrFail();
        $result = $user->update([
            'email_verified_at' => Carbon::now()
        ]);
        if ($result) {
            $this->dispatchBrowserEvent('successEmailVerification');
        }
    }

    /**
     * @return Factory|View
     */
    public function render()
    {
        $users = $this->searchUser();

        return view('livewire.users', compact('users'))->with('searchByUser');
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchUser()
    {
        /** @var User $query */
        $query = User::with(['roles', 'media'])->whereHas('roles', function (Builder $q) {
            $q->whereNotIn('name', ['Admin', 'Customer']);
        })->withCount('ticket');

        $query->when($this->searchByUser != '', function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->orWhere('name', 'like', '%'.strtolower($this->searchByUser).'%');
                $query->orWhere('email', 'like', '%'.strtolower($this->searchByUser).'%');
            });
        });

        $query->when($this->userRoleFilter != '', function (Builder $query) {
            $query->whereHas('roles', function (Builder $query) {
                $query->Where('name', '=', $this->userRoleFilter);
            });
        });
        $query->orderBy('created_at', 'desc');

        $all = $query->paginate(12);
        $currentPage = $all->currentPage();
        $lastPage = $all->lastPage();
        if ($currentPage > $lastPage) {
            $this->page = $lastPage;
        }

        return $query->paginate(12);
    }
}
