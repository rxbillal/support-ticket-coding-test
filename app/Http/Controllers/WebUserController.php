<?php

namespace App\Http\Controllers;

use App\Events\UserEvent;
use App\Http\Requests\ChatUserRequest;
use App\Models\AssignedChat;
use App\Models\Conversation;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\ChatRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class WebUserController extends AppBaseController
{
    /**
     * @param  ChatUserRequest  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeChatUser(ChatUserRequest $request)
    {
        try {
            $input = $request->all();
            $input['password'] = Hash::make(Str::random(6));
            $input['is_system'] = 0;
            $input['is_online'] = 1;
            $user = User::create($input);
            $customerRole = Role::whereName('Customer')->first();
            $user->assignRole($customerRole);
            $formId = getAdminUserId();
            $toId = $user->id;
            $message = 'Hello'.' '.$user->name;
            
            /** @var Conversation $conversation */
            $conversation = Conversation::create([
                'from_id' => $formId, 'to_id' => $toId, 'message' => $message, 'send_by' => $formId, 'status' => 1
            ])->fresh();
            $conversation['sender'] = $conversation->receiver->toArray();
            Notification::create([
                'owner_id' => $formId, 'owner_type' => User::class, 'notification' => $message, 'to_id' => $toId,
            ]);
            $broadcastData = $conversation->toArray();
            $broadcastData['to_id'] = $formId;
            $broadcastData['from_id'] = $toId;
            $broadcastData['type'] = User::NEW_PRIVATE_CONVERSATION;

            broadcast(new UserEvent($broadcastData,
                ($conversation->from_id) ?? getAdminUserId()))->toOthers();

            return $this->sendResponse($user, __('messages.success_message.user_create'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 422);
        }
    }

    /**
     * @param  Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssignAgent(Request $request)
    {
        $id = $request->get('id');
        /** @var  AssignedChat $assignAgent */
        $assignAgent = AssignedChat::with('agent')->where('customer_id', $id)->first();
        $assignAgent = (empty($assignAgent)) ? getAdminUserId() : $assignAgent;

        return $this->sendResponse($assignAgent, 'Assign Agent retrieved successfully.');
    }

    /**
     * @param  Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function readMessages(Request $request)
    {
        $data = app(ChatRepository::class)->markMessagesAsRead($request->all());

        return $this->sendResponse($data, __('messages.success_message.status_update'));
    }
}
