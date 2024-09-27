<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;

class SearchUsers extends Component
{
    public $users = [];
    public $searchTerm;
    public $male;
    public $female;
    public $isAssignToAgent;
    public $blockUserIds = [];

    protected $listeners = ['clearSearchUsers' => 'clearSearchUsers', 'setIsAssignToAgent'];


    /**
     * initialize variables
     * @param $blockUserIds
     * @param  bool  $isAssignToAgent
     */
    public function mount($blockUserIds, bool $isAssignToAgent = false)
    {
        $this->blockUserIds = $blockUserIds;
        $this->isAssignToAgent = $isAssignToAgent;
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        $this->searchUsers();

        return view('livewire.search-users');
    }

    public function clearSearchUsers()
    {
        $this->male = false;
        $this->female = false;
        $this->searchTerm = '';

        $this->searchUsers();
    }

    /**
     * search users and apply filters
     */
    public function searchUsers()
    {
        $male = $this->male;
        $female = $this->female;
        if ($this->male && $this->female) {
            $male = false;
            $female = false;
        }
        $users = User::with('media', 'roles')->whereNotIn('id', $this->blockUserIds)
            ->when($male, function ($query) {
                return $query->where('gender', '=', User::MALE);
            })
            ->when($female, function ($query) {
                return $query->where('gender', '=', User::FEMALE);
            })
            ->when($this->searchTerm, function ($query) {
                return $query->where(function ($q) {
                    $q->whereRaw('name LIKE ?', ['%'.strtolower($this->searchTerm).'%'])
                        ->orWhereRaw('email LIKE ?', ['%'.strtolower($this->searchTerm).'%']);
                });
            })
            ->when($this->isAssignToAgent, function ($query) {
//                return $query->where('is_system', '=', User::IS_SYSTEM);
                $query->whereHas('roles', function (Builder $q) {
                    $q->where('name', '!=', 'Customer');
                });
            })
//            ->whereHas('roles', function (Builder $q) {
//                $q->where('id', '!=', getCustomerRoleId());
//            })
            ->orderBy('name')
            ->select(['id', 'is_online', 'gender', 'photo_url', 'name', 'email', 'is_system'])
            ->get()
            ->append('role_name')
            ->except(getLoggedInUserId());

        $this->users = $users;
    }

    public function setIsAssignToAgent($val)
    {
        $this->isAssignToAgent = $val;
    }
}
