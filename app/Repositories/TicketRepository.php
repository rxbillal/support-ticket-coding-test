<?php

namespace App\Repositories;

use App\Mail\TicketsMail;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail as Email;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

/**
 * Class TicketRepository
 * @version August 25, 2020, 10:52 am UTC
 */
class TicketRepository extends BaseRepository
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
        return Ticket::class;
    }

    /**
     * @return mixed
     */
    public function getUniqueTicketId()
    {
        $ticketUniqueId = strtoupper(Str::random(8));
        while (true) {
            $isExist = Ticket::whereTicketId($ticketUniqueId)->exists();
            if ($isExist) {
                self::getUniqueTicketId();
            }
            break;
        }

        return $ticketUniqueId;
    }

    /**
     * @param  array  $input
     * @throws Throwable
     *
     * @return array
     */
    public function store($input)
    {
        try {
            DB::beginTransaction();
            
            $input['created_by'] = Auth::id();
            if(isset($input['customer'])){
                $input['created_by'] = $input['customer'];
                $input['email'] = User::whereId($input['customer'])->firstOrFail()->email;
            }
            
            $input['ticket_id'] = $this->getUniqueTicketId();
            $input['status'] = Ticket::STATUS_OPEN;
            $ticketAttachments = Arr::only($input, ['file']);
            $ticket = $this->create($input);

            /** Store notification for ticket agent */
            if (getLoggedInUserRoleId() == getAgentRoleId()) {
                $ticket->assignTo()->sync(getLoggedInUserId());
                $notificationRecord = [
                    'Ticket assigned you.',
                    UserNotification::ASSIGN_TICKET_TO_AGENT,
                    ucfirst($input['title']).' assigned you.',
                    getLoggedInUserId()
                ];
                addNotification($notificationRecord);
            }

            /** Store notification for admin */
            if(getLoggedInUserId() != getAdminUserId()) {
                $notificationRecord = [
                    'New Ticket Created.',
                    UserNotification::NEW_TICKET_CREATED,
                    ucfirst(getLoggedInUser()->name).' create ticket '.$input['title'],
                    getAdminUserId()
                ];
                addNotification($notificationRecord);

                sendEmailToAdmin('mail.admin.new_ticket_created',
                    'Ticket Successfully Created',
                    $input);
            }

            sendEmailToCustomer($input['created_by'],
                'mail.new_ticket_created',
                'Ticket Successfully Created',
                $input);

            if (isset($input['assignTo']) && ! empty($input['assignTo'])) {
                $ticket->assignTo()->sync($input['assignTo']);
                
                foreach ($input['assignTo'] as $agentID){
                    sendEmailToAgent($agentID,
                        'mail.ticket_assigned_you',
                        'Ticket Successfully Assigned You',
                        $input);
                    
                    $notificationRecord = [
                        'Ticket assigned you.',
                        UserNotification::ASSIGN_TICKET_TO_AGENT,
                        ucfirst($input['title']).' assigned you.',
                        $agentID
                    ];
                    addNotification($notificationRecord);
                }
            }

            if (! empty($ticketAttachments)) {
                foreach ($ticketAttachments['file'] as $attachment) {
                    $ticket->addMedia($attachment)
                        ->withCustomProperties(['user_id' => Auth::id()])
                        ->toMediaCollection(Ticket::COLLECTION_TICKET,
                            config('app.media_disc'));
                }
            }

            DB::commit();

            return $input;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  array  $input
     * @throws Throwable
     *
     * @return mixed
     */
    public function webStore($input)
    {
        try {
            DB::beginTransaction();

            $ticketAttachments = Arr::only($input, ['file']);

            if (! Auth::user()) {
                $input['password'] = Hash::make($input['password']);
                $user = User::create([
                    'name'     => $input['user_name'],
                    'email'    => $input['email'],
                    'password' => $input['password'],
                ]);
                $customerRole = Role::whereName('Customer')->first();
                $user->assignRole($customerRole);
                Auth::loginUsingId($user->id);
            }

            if (Auth::user()) {
                $input['email'] = Auth::user()->email;
                if (isset($input['customer_id'])) {
                    $input['email'] = User::whereId($input['customer_id'])->firstOrFail()->email;
                }
            }

            $input['created_by'] = $input['customer_id'] ?? Auth::id();
            $input['ticket_id'] = $this->getUniqueTicketId();
            $input['status'] = Ticket::STATUS_OPEN;
            $ticket = $this->create($input);

            /** Store notification for admin */
            if(getLoggedInUserId() != getAdminUserId()){
                $notificationRecord = [
                    'New Ticket Created.',
                    UserNotification::NEW_TICKET_CREATED,
                    ucfirst(getLoggedInUser()->name).' create ticket '.$input['title'],
                    getAdminUserId()
                ];
                addNotification($notificationRecord);

                sendEmailToAdmin('mail.admin.new_ticket_created',
                    'Ticket Successfully Created',
                    $input);
            }

            sendEmailToCustomer($input['created_by'],
                'mail.new_ticket_created',
                'Ticket Successfully Created',
                $input);
            
            if (getLoggedInUserRoleId() == getAgentRoleId()) {
                $ticket->assignTo()->sync(getLoggedInUserId());
            }

            if (! empty($ticketAttachments)) {
                foreach ($ticketAttachments['file'] as $attachment) {
                    $ticket->addMedia($attachment)
                        ->withCustomProperties(['user_id' => Auth::id()])
                        ->toMediaCollection(Ticket::COLLECTION_TICKET,
                            config('app.media_disc'));
                }
            }

            DB::commit();
            
            return $input;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  array  $input
     * @param  Ticket  $ticket
     * @throws Throwable
     *
     * @return array
     */
    public function update($input, $ticket)
    {
        try {
            DB::beginTransaction();

            if (isset($input['customer'])) {
                $input['created_by'] = $input['customer'];
            }
            if (isset($input['deletedMediaId'])) {
                $mediaIdArray = explode(',', $input['deletedMediaId']);

                foreach ($mediaIdArray as $media) {
                    $media = Media::whereId($media)->firstOrFail();
                    $media->delete();
                }
            }
            $ticketAttachments = Arr::only($input, ['attachments']);
            
            $ticket->update($input);
            
            if (isset($input['assignTo']) && ! empty($input['assignTo'])) {
                $oldAgentIds = array();
                foreach ($ticket->assignTo as $agent){
                    $oldAgentIds[] = $agent->id;
                }
                
                $newAgentIds = array_diff($input['assignTo'], $oldAgentIds);
                $ticket->assignTo()->sync($input['assignTo']);
                if(! empty($newAgentIds))
                {
                    $input['ticket_id'] = $ticket->ticket_id;
                    foreach ($newAgentIds as $agentId){
                        sendEmailToAgent($agentId,
                            'mail.ticket_assigned_you',
                            'Ticket Successfully Assigned You',
                            $input);
                        
                        $notificationRecord = [
                            'Ticket assigned you.',
                            UserNotification::ASSIGN_TICKET_TO_AGENT,
                            ucfirst($input['title']).' assigned you.',
                            $agentId
                        ];
                        addNotification($notificationRecord);
                    }
                }
            }
            
            if (! empty($ticketAttachments)) {
//                $ticket->clearMediaCollection(Ticket::COLLECTION_TICKET);
                foreach ($ticketAttachments['attachments'] as $attachment) {
                    $ticket->addMedia($attachment)->toMediaCollection(Ticket::COLLECTION_TICKET,
                        config('app.media_disc'));
                }
            }

            DB::commit();

            return $input;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function updateStatus($status, $ticket){
        try {
            DB::beginTransaction();
            
            $oldStatus = Ticket::STATUS[$ticket->status];
            
            $ticket->update(['status' => $status]);
            
            $newStatus = Ticket::STATUS[$ticket->status];
            if ($status == Ticket::STATUS_CLOSED) {
                $ticket->update(['close_at' => Carbon::now()]);
            }
            
            $userIds = array_diff(
                $ticket->assignTo->pluck('id')->toArray(),
                [getLoggedInUserId()]
            );
            if($ticket->created_by != getLoggedInUserId()) {
                $userIds[] = $ticket->created_by;
            }
            if(getLoggedInUserId() != getAdminUserId()) {
                $userIds[] = getAdminUserId();
            }
            $userIds = array_unique($userIds);

            foreach ($userIds as $userId){
                $notificationRecord = [
                    'Ticket status changed by '.getLoggedInUser()->name.'.',
                    UserNotification::CHANGE_TICKET_STATUS,
                    ucfirst(getLoggedInUser()->name).' change ticket status '.strtolower($oldStatus).' to '.strtolower($newStatus).'.',
                    $userId
                ];
                addNotification($notificationRecord);
            }
            
            DB::commit();
            
            return $status;
        } catch (Exception $e) {
            DB::rollBack();

            throw new UnprocessableEntityHttpException($e->getMessage());
        }
        
    }
    
    /**
     * @return array
     */
    public function prepareData()
    {
        $data['categories'] = Category::orderBy('name')->pluck('name', 'id');
        $data['users'] = User::whereHas('roles', function (Builder $query) {
            $query->whereNotIn('name', ['Admin', 'Customer']);
        })->orderBy('name')->pluck('name', 'id');
        $data['customers'] = User::whereHas('roles', function (Builder $query) {
            $query->where('name', '=', 'Customer');
        })->orderBy('name')->pluck('name', 'id');
        $data['roles'] = Role::orderBy('name')->pluck('name', 'id');

        return $data;
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Support\Collection
     */
    public function deleteTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->assignTo()->detach($id);
        $result = $ticket->delete($id);

        return $result;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function unassignedFromTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->assignTo()->detach(Auth::user()->id);

        return $ticket;
    }

    /**
     * @param $ticketId
     *
     * @return array
     */
    public function getAttachments($ticketId)
    {
        /** @var Ticket $ticket */
        $ticket = $this->find($ticketId);
        $attachments = $ticket->media;

        $result = [];

        foreach ($attachments as $attachment) {
            $obj['id'] = $attachment->id;
            $obj['name'] = $attachment->file_name;
            $obj['size'] = $attachment->size;
            $obj['url'] = $attachment->getFullUrl();
            $obj['user_id'] = $attachment->getCustomProperty('user_id');
            $result[] = $obj;
        }

        return $result;
    }

    /**
     * @param $ticket
     * @param $attachments
     *
     * @return bool
     */
    public function uploadFile($ticket, $attachments)
    {
        try {
            if (! empty($attachments)) {
                foreach ($attachments as $attachment) {
                    $ticket->addMedia($attachment)
                        ->withCustomProperties(['user_id' => Auth::id()])
                        ->toMediaCollection(Ticket::COLLECTION_TICKET,
                            config('app.media_disc'));
                }
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        return true;
    }
}
