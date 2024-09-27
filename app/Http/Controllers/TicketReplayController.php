<?php

namespace App\Http\Controllers;

use App\Models\TicketReplay;
use App\Repositories\TicketReplayRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TicketReplayController extends AppBaseController
{
    private $ticketReplayRepository;

    /**
     * TicketReplayController constructor.
     * @param  TicketReplayRepository  $ticketReplayRepository
     */
    public function __construct(TicketReplayRepository $ticketReplayRepository)
    {
        $this->ticketReplayRepository = $ticketReplayRepository;
    }

    /**
     * @param  Request  $request
     *
     * @throws \Throwable
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();

        /** @var TicketReplay $replay */
        $replay = $this->ticketReplayRepository->store($input);
        
        $replay->load('media');
        $replay->load('user');
        $replay->load('ticket');

        $adminRoleId = getAdminRoleId();
        $isAction = checkLoggedInUserRole();
        $ticket = $replay->ticket;

        $data['html'] = view('tickets.ticket_reply', compact('replay', 'adminRoleId', 'isAction', 'ticket'))->render();
        $data['id'] = $replay->id;

        return $this->sendResponse($data, __('messages.success_message.reply_create'));
    }

    /**
     * @param  TicketReplay  $ticketReplay
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function update(TicketReplay $ticketReplay, Request $request)
    {
        $input = $request->all();
        $ticketReply = $this->ticketReplayRepository->update($input, $ticketReplay->id);

        return $this->sendResponse($ticketReply, __('messages.success_message.reply_update'));
    }

    /**
     * @param $id
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $ticketReply = TicketReplay::find($id)->delete();

        return $this->sendResponse($ticketReply, __('messages.success_message.reply_delete'));
    }

    /**
     * @param  Media  $media
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function deleteAttachment(Media $media)
    {
        $media->delete();

        return $this->sendSuccess(__('messages.success_message.attachment_has_delete'));
    }

    /**
     * @param  Request  $request
     * @throws \Throwable
     *
     * @return JsonResponse
     */
    public function addAttachment(Request $request)
    {
        $input = $request->all();
        $attachment = $this->ticketReplayRepository->updateReplyWithAttachment($input, $input['replyId']);

        return $this->sendResponse($attachment, __('messages.success_message.ticket_reply'));
    }
}
