<?php

namespace App\Repositories;

use App\Events\UserEvent;
use App\Models\Conversation;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Container\Container as Application;

//use App\Models\Group;
//use App\Models\GroupMessageRecipient;

/**
 * Class ChatRepository
 */
class NotificationRepository extends BaseRepository
{
    /**
     * NotificationRepository constructor.
     *
     * @param  Application  $app
     *
     * @throws Exception
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'owner_id',
        'owner_type',
        'notification',
        'to_id',
        'is_read',
        'read_at',
    ];

    /**
     * Return searchable fields.
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model.
     **/
    public function model()
    {
        return Notification::class;
    }

    /**
     * @param  array  $notificationInput
     * @param  integer  $receiverId
     */
    public function sendNotification($notificationInput, $receiverId, $userEventType = 0)
    {
        /** @var Notification $notification */
        $notification = $this->create($notificationInput);
        $notificationArray = $notification->toArray();

        $notificationCount = Notification::whereOwnerId($notificationInput['owner_id'])->whereToId($notificationInput['to_id'])->whereIsRead(0)->get()->count();

        $sender = (getLoggedInUser()) ? getLoggedInUser() : User::findOrFail($notificationInput['owner_id']);


        if ($notification->owner_type == User::class) {
            $sender = [
                'id'          => $sender->id,
                'name'        => $sender->name,
                'is_online'   => $sender->is_online,
                'profile_url' => $sender->photo_url,
            ];
        }
        $notificationArray['unread_count'] = $notificationCount;
        $notificationArray['senderUser'] = $sender;
        $notificationArray['type'] = ($userEventType > 0) ? $userEventType : User::MESSAGE_NOTIFICATION;

        broadcast(new UserEvent($notificationArray, $receiverId))->toOthers();
    }

    /**
     * @return array
     */
    public function getNotifications()
    {
        $notifications = Notification::whereIsRead(0)->whereToId(getLoggedInUserId())->with([
            'sender', 'latestMsg',
        ])->selectRaw(
            "max(notifications.id) as latest_id,
            sum(if(notifications.is_read = 0, 1, 0)) as unread_count,
            notifications.*"
        )->orderBy("notifications.created_at", 'desc')->groupBy('owner_id')->get();

        $notificationsArray = [];
        foreach ($notifications as $notification) {
            /** @var Notification $notification */
            $notificationArray = $notification->latestMsg->toArray();
            $notificationArray['unread_count'] = $notification->unread_count;
            if ($notification->owner_type == User::class) {
                $sender = [
                    'id'          => $notification->sender->id ?? null,
                    'name'        => $notification->sender->name ?? null,
                    'is_online'   => $notification->sender->is_online ?? null,
                    'profile_url' => $notification->sender->photo_url ?? null,
                ];
            }
            $notificationArray['senderUser'] = $sender;
            $notificationsArray[] = $notificationArray;
        }

        return $notificationsArray;
    }

    /**
     * @param  integer  $notificationId
     */
    public function readNotification($notificationId)
    {
        $notification = Notification::find($notificationId);

        Notification::whereToId($notification->to_id)->whereOwnerId($notification->owner_id)->update([
            'is_read' => true, 'read_at' => Carbon::now(),
        ]);
    }

    /**
     * @param  integer  $senderId
     */
    public function readNotificationWhenOpenChatWindow($senderId)
    {
        Notification::whereOwnerId($senderId)->whereToId(getLoggedInUserId())->update([
            'is_read' => 1, 'read_at' => Carbon::now(),
        ]);
    }

    /**
     * @return array
     */
    public function readAllNotification()
    {
        $authId = getLoggedInUserId();

        Notification::whereToId($authId)->update(['is_read' => true, 'read_at' => Carbon::now()]);

        $query = Conversation::whereToId($authId)->where('status', '=', 0);
        $messageIds = array_unique($query->select(['id'])->pluck('id')->toArray());
        $messageSenderIds = $query->select(['from_id'])->groupBy('from_id')->pluck('from_id')->toArray();
        $query->update(['status' => 1]);

        foreach ($messageSenderIds as $senderId) {
            broadcast(new UserEvent(
                [
                    'user_id' => $authId,
                    'ids'     => $messageIds,
                    'type'    => User::PRIVATE_MESSAGE_READ,
                ], $senderId))->toOthers();
        }

        return $messageSenderIds;
    }
}
