<?php

namespace App\Queries;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Class TicketDataTable
 */
class TicketDataTable
{
    /**
     * @return array
     */
    public function get()
    {
        $query = Ticket::with(['user', 'category'])->select('tickets.*')->get();

        if (Auth::user()->hasRole('Agent')) {
            $query = User::find(Auth::id())->ticket()->get();
        }

        $result = $data = [];
        $query->map(function (Ticket $ticket) use ($data, &$result) {
            $data['id'] = $ticket->id;
            $data['title'] = $ticket->title;
            $data['ticket_id'] = $ticket->ticket_id;
            $data['email'] = $ticket->email;
            $data['status'] = $ticket->status;
            $data['user'] = [
                'name' => (! empty($ticket->user->name)) ? $ticket->user->name : 'N/A',
            ];

            $data['created_at'] = $ticket->created_at->toDateTimeString();

            $result[] = $data;
        });

        return $result;
    }

    /**
     * @param $id
     * @param $statusId
     * @param $categoryId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTicketByUser($id, $statusId, $categoryId)
    {
        $query = User::find($id)->ticket()->with('category');
        $query->when($statusId != null, function (Builder $q) use ($statusId) {
            if ($statusId == Ticket::STATUS_ACTIVE) {
                $q->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS]);
            } else {
                $q->where('status', '=', $statusId);
            }
        });
        $query->when($categoryId != null, function (Builder $q) use ($categoryId) {
            $q->where('category_id', '=', $categoryId);
        });
        $query = $query->get();
        if ($query->isEmpty()) {
            $user = User::with('roles')->findOrFail($id);
            if ($user->roles()->first()->id == getCustomerRoleId()) {
                $query = Ticket::with('category')->whereCreatedBy($id)
                    ->when($statusId != null, function (Builder $q) use ($statusId) {
                        if ($statusId == Ticket::STATUS_ACTIVE) {
                            $q->whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS]);
                        } else {
                            $q->where('status', '=', $statusId);
                        }
                    })
                    ->when($categoryId != null, function (Builder $q) use ($categoryId) {
                        $q->where('category_id', '=', $categoryId);
                    })->get();
            }
        }

        return $query;
    }
}
