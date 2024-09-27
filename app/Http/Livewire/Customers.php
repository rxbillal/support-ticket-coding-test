<?php

namespace App\Http\Livewire;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Customers extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $searchByUser;
    public $userRoleFilter;
    public $userRoles = '';
    protected $listeners = ['setEmailVerified'];

    public function mount()
    {
        $this->userRoles = Role::all()->pluck('name', 'id')->toArray();
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
     * @return Application|Factory|View
     */
    public function render()
    {
        $users = $this->searchCustomer();

        return view('livewire.customers', compact('users'))->with('searchByUser');
    }

    public function searchCustomer()
    {
        /** @var User $query */
        $query = User::with(['roles', 'media'])->whereHas('roles', function (Builder $q) {
            $q->where('id', '=', getCustomerRoleId());
        })->withCount('tickets', 'activeTickets', 'inProgressTickets', 'closeTickets');

        $query->when(! empty($this->searchByUser != ''), function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->orWhere('name', 'like', '%'.strtolower($this->searchByUser).'%');
                $query->orWhere('email', 'like', '%'.strtolower($this->searchByUser).'%');
            });
        });
        $query->orderBy('created_at', 'desc');

        $all = $query->paginate(8);
        $currentPage = $all->currentPage();
        $lastPage = $all->lastPage();
        if ($currentPage > $lastPage) {
            $this->page = $lastPage;
        }

        return $query->paginate(8);
    }
}
