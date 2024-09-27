<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Ticket;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class WebHomeRepository
 * @version August 25, 2020, 10:52 am UTC
 */
class WebHomeRepository extends BaseRepository
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
        return Category::class;
    }

    /**
     * @return Ticket[]|Builder[]|Collection
     */
    public function getPublicTickets()
    {
        return Ticket::with(['user.media', 'category'])->whereIsPublic(1)
            ->withCount('replay')->orderByDesc('created_at')->limit(5)->get();
    }

    /**
     * @param $input
     *
     * @return Ticket|null
     */
    public function searchTicket($input)
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::with(['category', 'replay.user', 'assignTo'])->whereTicketId($input['ticket_id'])->whereEmail($input['email'])->first();

        if(empty($ticket)){
            return null;
        }
        if ($ticket->is_public || (\Auth::check() && \Auth::user()->hasRole('Admin'))) {
            return $ticket;
        }
        $user = \Auth::user();
        if ($user) {
            $isAgent = $ticket->assignTo()->where('user_id', '=', $user->id)->exists();
            if ($ticket->created_by == $user->id || $isAgent) {
                return $ticket;
            }
        }

        return null;
    }
}
