<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Response;

/**
 * Class AdminUsersAPIController
 */
class AdminUsersAPIController extends AppBaseController
{
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     *
     * @throws Exception
     * @return Response
     */
    public function index()
    {
        $users = User::with(['roles'])->orderBy('name', 'asc')->get()->except(getLoggedInUserId());
        foreach ($users as $key => $user) {
            /** @var User $user */
            $users[$key] = $user->apiObj();
        }

        return $this->sendResponse(['users' => $users], 'Users retrieved successfully.');
    }

    /**
     * Display the specified User.
     *
     * @param  User  $user
     * @return Response
     */
    public function show(User $user)
    {
        $user->roles;
        $user = $user->apiObj();

        return $this->sendResponse($user, 'User retrieved successfully');
    }
}
