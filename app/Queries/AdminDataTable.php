<?php

namespace App\Queries;

use App\Models\User;

/**
 * Class CategoryDataTable
 */
class AdminDataTable
{
    /**
     * @return User
     */
    public function get()
    {
        /** @var User $query */
        $query = User::query()->role('admin')->with('media')->where('id', '!=', getLoggedInUserId());

        return $query->select('users.*');
    }
}
