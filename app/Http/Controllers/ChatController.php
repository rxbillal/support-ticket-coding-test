<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\BlockUserRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Session;

/**
 * Class ChatController
 * @package App\Http\Controllers
 */
class ChatController extends AppBaseController
{
    /**
     * Show the application dashboard.
     *
     * @param  Request  $request
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $conversationId = $request->get('conversationId');
        $data['conversationId'] = ! empty($conversationId) ? $conversationId : 0;

        /** @var BlockUserRepository $blockUserRepository */
        $blockUserRepository = app(BlockUserRepository::class);
        list($blockUserIds, $blockedByMeUserIds) = $blockUserRepository->blockedUserIds();

        $data['users'] = User::toBase()
            ->limit(50)
            ->orderBy('name')
            ->select(['name', 'id'])
            ->pluck('name', 'id')
            ->except(getLoggedInUserId());
        $data['blockUserIds'] = $blockUserIds;
        $data['blockedByMeUserIds'] = $blockedByMeUserIds;

        $adminUser = User::without(['media','roles'])->select('id')->role('Admin')->toBase()->first();
        Session::put('admin_user_id', $adminUser->id);

        return view('chat.index')->with($data);
    }
}
