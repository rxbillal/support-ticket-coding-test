<?php

namespace App\Repositories;

use App\Events\PublicUserEvent;
use App\Events\UserEvent;
use App\Models\ArchivedUser;
use App\Models\Conversation;
use App\Models\MessageAction;
use App\Models\User;
use App\Traits\ImageTrait;
use Auth;
use DB;
use Embed\Embed;
use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\UploadedFile;
use Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class ChatRepository
 */
class ChatRepository extends BaseRepository
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'from_id', 'to_id', 'message', 'status', 'file_name',
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
        return Conversation::class;
    }

    public function getConversation($id, $input)
    {
        $orderBy = 'desc';
        $firstUnreadMessage = null;
        $limit = 100;
        /** @var User $user */
        $user = User::with(['assign.agent'])->find($id);
        if (empty($user)) {
            throw new BadRequestHttpException('User not found.');
        }
        $query = Conversation::query();
        $query->whereDoesntHave('messageAction');
        $authUser = Auth::user();

        if ($authUser) {
            // only agent to agent and admin to agent chat
            $fromId = $user->assign->user_id ?? $user->id;
            $toId = $user->assign ? $user->id : $authUser->id;

            $query->where(function (Builder $q) use ($toId, $fromId, $id) {
                $q->orWhere(function (Builder $q) use ($toId, $fromId, $id) {
                    $q->where('to_id', '=', $toId);
                    $q->where('from_id', '=', $fromId);
                });
                $q->orWhere(function (Builder $q) use ($toId, $fromId, $id) {
                    $q->where('from_id', '=', $toId);
                    $q->where('to_id', '=', $fromId);
                });
            });
//            if(!isset($input['isCustomerChat']) && $input['isCustomerChat'] != 1){
//                $query->where('send_by', '=', null);
//            }
        } else {
            // only font customer chat
            $query->where(function (Builder $q) use ($user, $id) {
                $q->where(function (Builder $q) use ($user, $id) {
                    $q->where('from_id', '=', $user->id);
                })->orWhere(function (Builder $q) use ($user, $id) {
                    $q->where('to_id', '=', $user->id);
                });
            });
        }
        $query->with(['sender.media', 'receiver.media', 'sendByUser.media', 'sendByUser.roles']);
        $mediaQuery = clone $query;
        $countQuery = clone $query;
        $unreadCount = $countQuery->where('status', '=', 0)->where('to_id', '=', getLoggedInUserId())->count();
        $allMedia = $mediaQuery->whereIn('message_type', Conversation::MEDIA_MESSAGE_TYPES
        )->get(['conversations.*']);

        $needToReverse = false;

        if (isset($input['before']) && ! empty($input['before'])) {
            $query->where('conversations.id', '<', $input['before']);
        } elseif (isset($input['after']) && ! empty($input['after'])) {
            $query->where('conversations.id', '>', $input['after']);
            $orderBy = 'ASC';
        } elseif ($unreadCount > $limit) {
            $query->where('status', '=', 0);
            $orderBy = 'ASC';
            $needToReverse = true;
        }

        if (! empty($firstUnreadMessage) && ! isset($input['before']) && ! isset($input['after'])) {
            $orderBy = 'ASC';
            $needToReverse = true;
            $query->where('conversations.id', '>=', $firstUnreadMessage->conversation_id);
        }

        $query->limit($limit);
        $query->orderBy('conversations.id', $orderBy);
        $messages = $query->get()->toArray();
        $messages = ($needToReverse) ? array_reverse($messages) : $messages;

        /** @var NotificationRepository $notificationRepo */
        $notificationRepo = app(NotificationRepository::class);
        $notificationRepo->readNotificationWhenOpenChatWindow($id);

        return [
            'user'          => $user,
            'conversations' => $messages,
            'media'         => $allMedia,
        ];
    }

    /**
     * @param  array  $input
     *
     * @return array
     */
    public function getLatestConversations($input = [])
    {
        $isArchived = isset($input['isArchived']) ? 1 : 0;
        $authUser = Auth::user();
        $authId = $authUser->id;
        $isAuthUserAdmin = $authUser->hasRole('Admin');

        $subQuery = Conversation::leftJoin('users as u', 'u.id', '=', DB::raw("if(from_id = $authId, to_id, from_id)"))
            ->leftJoin('message_action as ma', function (JoinClause $join) use ($authId) {
                $join->on('ma.deleted_by', '=', DB::raw("$authId"));
                $join->on('ma.conversation_id', '=', 'conversations.id');
            })
            ->whereNull('u.deleted_at');

        if ($isAuthUserAdmin) {
            $subQuery->where(function (Builder $q) use ($authId) {
                $q->orWhere('to_id', '=', $authId)
                    ->orWhere('send_by', '=', $authId)
                    ->orWhere('from_id', '=', $authId);
            });
        } else {
            $subQuery->where(function (Builder $q) use ($authId) {
                $q->where('from_id', '=', $authId)->orWhere('to_id', '=', $authId);
            });
        }


        $subQuery->where(function (Builder $q) {
            $q->whereColumn('ma.conversation_id', '!=', 'conversations.id')
                ->orWhereNull('ma.conversation_id');
        })->selectRaw(
            "max(conversations.id) as latest_id , u.id as user_id,
                sum(if(conversations.status = 0 and from_id != $authId, 1, 0)) as unread_count"
        );
        if ($isAuthUserAdmin) {
            $subQuery->groupBy(DB::raw("if(to_id = $authId, from_id,to_id)"));
        } else {
            $subQuery->groupBy(DB::raw("if(from_id = $authId, to_id, from_id)"));
        }


        $bindings = $subQuery->getBindings();
        $subQueryStr = $subQuery->toSql();

        /** @var ArchivedUser $archiveUsers */
        $archiveUsers = ArchivedUser::toBase()
            ->select('owner_id')
            ->whereArchivedBy(getLoggedInUserId())
            ->whereOwnerType(User::class)
            ->pluck('owner_id')->toArray();

        $chatList = Conversation::with('user.media', 'receiver.media', 'user.roles', 'receiver.roles')->newQuery();
        $chatList = $chatList->select("temp.*", "cc.*");
        $chatList->from(DB::raw("($subQueryStr) as temp"));
        $chatList->setBindings($bindings)
            ->leftJoin("conversations as cc", 'cc.id', '=', 'temp.latest_id');
        if (! $isArchived) {
            if($isAuthUserAdmin){
                $chatList->whereNotIn('to_id', $archiveUsers);
            }
            $chatList->whereNotIn('temp.user_id', $archiveUsers);

        } else {
            $chatList = $chatList->where(function ($query) use ($isAuthUserAdmin, $archiveUsers) {
                $query->whereIn('temp.user_id', $archiveUsers);
                if($isAuthUserAdmin){
                    $query->orWhereIn('to_id', $archiveUsers);
                }
            });
        }
//        $query = str_replace(array('?'), array('\'%s\''), $chatList->toSql());
//        $query = vsprintf($query, $chatList->getBindings());
//        dd($query);
        $chatList = $chatList->orderBy("cc.created_at", 'desc')
            ->get()->keyBy('id');

        $chatList = array_values($chatList->toArray());

        return $chatList;
    }

    /**
     * @param  array  $input
     *
     * @throws Exception
     *
     * @return Conversation
     */
    public function sendMessage($input)
    {
        if (isset($input['is_archive_chat']) && $input['is_archive_chat'] == 1) {
            $archivedUser = ArchivedUser::whereOwnerId($input['to_id'])->whereArchivedBy(getLoggedInUserId())->first();
            if (! empty($archivedUser)) {
                $archivedUser->delete();
            }
        }
        if (! isset($input['from_id'])) {
            $input['from_id'] = getLoggedInUserId();
        }
        $fromUser = User::findOrFail($input['from_id']);

        if (empty(Session::get('admin_user_id'))) {
            $adminUser = User::role('Admin')->first();
            Session::put('admin_user_id', $adminUser->id);
        }

        $input['send_by'] = (isset($input['send_by']) && $input['send_by'] != '') ? $input['send_by'] : null;


        if (isValidURL($input['message'])) {
            $input['message_type'] = detectURL($input['message']);
        }

        $pattern = '~[a-z]+://\S+~';
        $message = $input['message'];

        if ($num_found = preg_match_all($pattern, $message, $out)) {
            $link = $out[0];
            try {
                $info = Embed::create($link[0]);

                $input['url_details'] = [
                    'title'       => $info->title,
                    'image'       => $info->image,
                    'description' => $info->description,
                    'url'         => $info->url,
                ];
            } catch (Exception $e) {
            }
        }

        /** @var $conversation Conversation */
        $conversation = $this->create($input)->fresh();
        $conversation->sender;

        $broadcastData = $conversation->toArray();
        $broadcastData['type'] = User::NEW_PRIVATE_CONVERSATION;
        broadcast(new UserEvent($broadcastData,
            ($conversation->to_id) ?? getAdminUserId()))->toOthers();

        $broadcastData['type'] = User::PUBLIC_USER_MESSAGE_RECEIVED;
        broadcast(new PublicUserEvent($broadcastData, ($conversation->to_id ?? getAdminUserId())))->toOthers();

        $notificationInput = [
            'owner_id'     => $conversation['from_id'],
            'owner_type'   => User::class,
            'notification' => $conversation['message'],
            'to_id'        => $conversation['to_id'],
            'message_type' => $conversation['message_type'],
            'file_name'    => $conversation['file_name'],
        ];
        /** @var NotificationRepository $notificationRepo */
        $notificationRepo = app(NotificationRepository::class);
        $notificationRepo->sendNotification($notificationInput, $conversation['to_id']);

        return $conversation;
    }


    /**
     * @param  UploadedFile  $file
     *
     * @throws UnprocessableEntityHttpException
     *
     * @return string|void
     */
    public function addAttachment($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension,
            [
                'xls', 'pdf', 'doc', 'docx', 'xlsx', 'jpg', 'gif', 'jpeg', 'png', 'mp4', 'mkv', 'avi', 'txt', 'mp3',
                'ogg', 'wav', 'aac', 'alac',
            ])) {
            throw new UnprocessableEntityHttpException('You can not upload this file.', Response::HTTP_BAD_REQUEST);
        }

        if (in_array($extension, ['jpg', 'gif', 'png', 'jpeg'])) {
            $fileName = ImageTrait::makeImage($file, Conversation::PATH, []);

            return $fileName;
        }

        if (in_array($extension, ['xls', 'pdf', 'doc', 'docx', 'xlsx', 'txt'])) {
            $fileName = ImageTrait::makeAttachment($file, Conversation::PATH);

            return $fileName;
        }

        if (in_array($extension, ['mp4', 'mkv', 'avi'])) {
            $fileName = ImageTrait::uploadVideo($file, Conversation::PATH);

            return $fileName;
        }

        if (in_array($extension, ['mp3', 'ogg', 'wav', 'aac', 'alac'])) {
            $fileName = ImageTrait::uploadFile($file, Conversation::PATH);

            return $fileName;
        }
    }

    /**
     * @param  string  $extension
     *
     * @return int
     */
    public function getMessageTypeByExtension($extension)
    {
        $extension = strtolower($extension);
        if (in_array($extension, ['jpg', 'gif', 'png', 'jpeg'])) {
            return Conversation::MEDIA_IMAGE;
        } elseif (in_array($extension, ['doc', 'docx'])) {
            return Conversation::MEDIA_DOC;
        } elseif ($extension == 'pdf') {
            return Conversation::MEDIA_PDF;
        } elseif (in_array($extension, ['mp3', 'ogg', 'wav', 'aac', 'alac'])) {
            return Conversation::MEDIA_VOICE;
        } elseif (in_array($extension, ['mp4', 'mkv', 'avi'])) {
            return Conversation::MEDIA_VIDEO;
        } elseif (in_array($extension, ['txt'])) {
            return Conversation::MEDIA_TXT;
        } elseif (in_array($extension, ['xls', 'xlsx'])) {
            return Conversation::MEDIA_XLS;
        } else {
            return 0;
        }
    }

    /**
     * @param  array  $input
     *
     * @return array
     */
    public function markMessagesAsRead($input)
    {
        $senderId = Auth::id();
        $remainingUnread = 0;
        if (! empty($input['ids'])) {
            $unreadIds = $input['ids'];
            $unreadIds = (is_array($unreadIds)) ? $unreadIds : [$unreadIds];
            $firstUnreadConversationId = $unreadIds[0];
            Conversation::whereIn('id', $unreadIds)->update(['status' => 1]);

            $conversation = Conversation::find($firstUnreadConversationId);
            $senderId = ($conversation->from_id == getLoggedInUserId()) ? $conversation->to_id : $conversation->from_id;
            $receiverId = ($conversation->from_id == getLoggedInUserId()) ? $conversation->from_id : $conversation->to_id;
            $remainingUnread = $this->getUnreadMessageCount($senderId);

            broadcast(new UserEvent(
            [
                'user_id' => $receiverId,
                'ids'     => $unreadIds,
                'type'    => User::PRIVATE_MESSAGE_READ,
            ], $conversation->from_id))->toOthers();
            
            broadcast(new PublicUserEvent([
                'user_id' => $receiverId,
                'ids'     => $unreadIds,
                'type'    => User::PRIVATE_MESSAGE_READ,
            ], $conversation->from_id))->toOthers();
        }

        return ['senderId' => $senderId, 'remainingUnread' => $remainingUnread];
    }

    /**
     * @param  int  $senderId
     * @param  bool  $isGroup
     *
     * @return int
     */
    public function getUnreadMessageCount($senderId, $isGroup = false)
    {
        return Conversation::where(function (Builder $q) use ($senderId) {
            $q->where('from_id', '=', $senderId)->where('to_id', '=', getLoggedInUserId());
        })->where('status', '=', 0)->count();
    }

    /**
     * @param $userId
     * @param $input
     */
    public function deleteConversation($userId, $input)
    {
        $chatIds = Conversation::leftJoin('message_action as ma', function (JoinClause $join) {
            $authUserId = getLoggedInUserId();
            $join->on('ma.deleted_by', '=', DB::raw("$authUserId"));
            $join->on('ma.conversation_id', '=', 'conversations.id');
        })
            ->when($input['isCustomerChat'] == 0, function (Builder $query) use ($userId) {
                $query->where(function (Builder $q) use ($userId) {
                    $q->where(function (Builder $q) use ($userId) {
                        $q->where('from_id', '=', $userId)
                            ->where('to_id', '=', getLoggedInUserId());
                    })->orWhere(function (Builder $q) use ($userId) {
                        $q->where('from_id', '=', getLoggedInUserId())
                            ->where('to_id', '=', $userId);
                    });
                });
            })
            ->when($input['isCustomerChat'] == 1, function (Builder $query) use ($userId) {
                $query->where(function (Builder $q) use ($userId) {
                    $q->orWhere('from_id', '=', $userId);
                    $q->orWhere('to_id', '=', $userId);
                });
            })
            ->where(function (Builder $q) {
                $q->whereColumn('ma.conversation_id', '!=', 'conversations.id')
                    ->orWhereNull('ma.conversation_id');
            })
            ->get(['conversations.*'])
            ->pluck('id')
            ->toArray();

        $input = [];
        foreach ($chatIds as $chatId) {
            $input[] = [
                'conversation_id' => $chatId,
                'deleted_by'      => getLoggedInUserId(),
            ];
        }
        Conversation::where('from_id', '=', $userId)->where('to_id', '=', getLoggedInUserId())->update([
            'status' => 1
        ]);

        MessageAction::insert($input);
    }


    /**
     * @param $id
     */
    public function deleteMessage($id)
    {
        MessageAction::create([
            'conversation_id' => $id,
            'deleted_by'      => getLoggedInUserId(),
            'is_hard_delete'  => 1,
        ]);
    }
}
