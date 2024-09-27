<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Models\TicketReplay;
use App\Models\User;
use Auth;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

/**
 * Class TicketRepository
 * @version August 25, 2020, 10:52 am UTC
 */
class TicketReplayRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TicketReplay::class;
    }

    /**
     * @param  array  $input
     *
     * @throws Throwable
     *
     * @return TicketReplay
     */
    public function store($input)
    {
        try {
            DB::beginTransaction();

            $input['user_id'] = Auth::id();
            /** @var TicketReplay $ticketReplay */
            $ticketReplay = $this->create($input);
           
            if (isset($input['file'])) {
                foreach ($input['file'] as $file) {
                    $ticketReplay->addMedia($file)
                        ->toMediaCollection(TicketReplay::COLLECTION_TICKET,
                            config('app.media_disc'));
                }
            }
            
            $this->sendTicketReplyMail($ticketReplay);

            DB::commit();

            return $ticketReplay;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  array  $input
     * @param $id
     *
     * @return Builder|Builder[]|Collection|Model|int
     */
    public function update($input, $id)
    {
        try {
            DB::beginTransaction();
            $reply = TicketReplay::findOrFail($id);
            $reply->update($input);

            DB::commit();

            return $reply;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function prepareData()
    {
        $data['categories'] = Category::pluck('name', 'id');
        $data['users'] = User::pluck('name', 'id');

        return $data;
    }

    /**
     * @param $input
     * @param $id
     * @throws Throwable
     *
     * @return array
     */
    public function updateReplyWithAttachment($input, $id)
    {
        try {
            DB::beginTransaction();
            $ticketReply = TicketReplay::findOrFail($id);
            $ticketReply->update($input);

            $attachment = [];
            if (isset($input['file'])) {
                foreach ($input['file'] as $i => $file) {
                    $media = $ticketReply->addMedia($file)
                        ->toMediaCollection(TicketReplay::COLLECTION_TICKET,
                            config('app.media_disc'));

                    $attachment[$i]['id'] = $media->id;
                    $attachment[$i]['url'] = $media->getFullUrl();
                    $attachment[$i]['file_name'] = substr($media->file_name, 0, 15).'...';
                }
            }
            DB::commit();

            return $attachment;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
    
    function sendTicketReplyMail(TicketReplay $ticketReplay)
    {
        $data = [];
        /** @var User $currentUser */
        $currentUser = Auth::user();

        /** @var Ticket $ticket */
        $ticket = Ticket::whereId($ticketReplay->ticket_id)->firstOrFail();
        
        /** @var User $ticketCustomer */
        $ticketCustomer = $ticket->user;
        
        /** @var User $ticketAgents */
        $ticketAgents = $ticket->assignTo;
        
        $data['ticket_id'] = $ticket->ticket_id;
        $data['title'] = $ticket->title;
        $data['description'] = $ticketReplay->description;
        $data['reply_user_name'] = ucfirst($currentUser->name);
        $data['email'] = $ticketCustomer->email;

        if ($currentUser->id != $ticketCustomer->id) {
            sendEmailToCustomer($ticketCustomer->id,
                'mail.ticket_reply_for_customer',
                $data['reply_user_name'].' Replied on Your Ticket',
                $data);
        }
        
        if($currentUser->id != getAdminUserId()){
            sendEmailToAdmin('mail.ticket_reply_for_admin_agent',
                $data['reply_user_name'].' Replied on Ticket '.ucfirst($ticket->title),
                $data);
        }

        foreach ($ticketAgents as $ticketAgent){
            if($currentUser->id != $ticketAgent->id && $ticketCustomer->id != $ticketAgent->id){
                sendEmailToAgent($ticketAgent->id,
                    'mail.ticket_reply_for_admin_agent',
                    $data['reply_user_name'].' Replied on Ticket '.ucfirst($ticket->title),
                    $data);
            }
        }
    }
}
