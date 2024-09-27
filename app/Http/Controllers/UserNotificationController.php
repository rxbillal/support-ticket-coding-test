<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class UserNotificationController extends AppBaseController
{
    /**
     * @param  UserNotification  $notification
     *
     * @return JsonResponse
     */
    public function readNotification(UserNotification $notification)
    {
        $notification->read_at = Carbon::now();
        $notification->save();

        return $this->sendSuccess(__('messages.success_message.notification'));
    }

    /**
     * @return JsonResponse
     */
    public function readAllNotification()
    {
        UserNotification::whereReadAt(null)->where('user_id', getLoggedInUserId())->update(['read_at' => Carbon::now()]);

        return $this->sendSuccess(__('messages.success_message.all_notification'));
    }
}
