<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class DashboardRepository
 */
class DashboardRepository
{
    /**
     * @return mixed
     */
    public function getDashboardAssociatedData()
    {
        $data = [];
        if (getLoggedInUserRoleId() == getAdminRoleId()) {
            $data['totalAgents'] = User::role('Agent')->count();
            $data['totalCategories'] = Category::count();
            $data['totalOpenTickets'] = Ticket::whereStatus(Ticket::STATUS_OPEN)->count();
            $data['totalClosedTickets'] = Ticket::whereStatus(Ticket::STATUS_CLOSED)->count();
        }
        if (getLoggedInUserRoleId() == getAgentRoleId()) {
            $data['totalAgents'] = User::count();
            $data['totalCategories'] = Category::count();
            $data['totalOpenTickets'] = Ticket::whereStatus(Ticket::STATUS_OPEN)->whereHas('assignTo',
                function (Builder $query) {
                    $query->where('users.id', '=', getLoggedInUserId());
                })->count();
            $data['totalClosedTickets'] = Ticket::whereStatus(Ticket::STATUS_CLOSED)->whereHas('assignTo',
                function (Builder $query) {
                    $query->where('users.id', '=', getLoggedInUserId());
                })->count();
        }
        $data['categories'] = Category::orderBy('name')->pluck('name', 'id');
        $data['agents'] = User::whereHas('roles', function (Builder $query) {
            $query->where('id', '=', 2);
        })->orderBy('name')->pluck('name', 'id');

        return $data;
    }

    /**
     * @param $status
     *
     * @return array
     */
    public function getCategoryReport($status)
    {
        $categoriesTicketCounter = [];
        if (getLoggedInUserRoleId() == getAdminRoleId()) {
            $categories = Category::whereHas('ticket', function (Builder $q) use ($status) {
                $q->where('status', '=', $status);
            });
            foreach ($categories->pluck('id') as $key => $categoryId) {
                $categoriesTicketCounter[$key] = Ticket::where([
                    ['category_id', '=', $categoryId],
                    ['status', '=', $status],
                ])->count();
            }
        }
        if (getLoggedInUserRoleId() == getAgentRoleId()) {
            $agentTickets = Ticket::whereHas('assignTo', function (Builder $query) {
                $query->where('user_id', '=', getLoggedInUserId());
            });
            $categories = Category::whereHas('ticket', function (Builder $q) use ($status, $agentTickets) {
                $q->where('status', '=', $status)->whereIn('id', $agentTickets->pluck('id'));
            });
            foreach ($categories->pluck('id') as $key => $categoryId) {
                $categoriesTicketCounter[$key] = Ticket::where([
                    ['category_id', '=', $categoryId],
                    ['status', '=', $status],
                ])->whereHas('assignTo', function (Builder $q) {
                    $q->where('user_id', '=', getLoggedInUserId());
                })->count();
            }
        }

        $result = [];
        $result['categories'] = $categories->pluck('name');
        $result['color'] = $categories->pluck('color');
        $result['categoriesTicket'] = $categoriesTicketCounter;

        return $result;
    }

    /**
     * @param $input
     * @return array
     */
    public function ticketChart($input)
    {
        $dateS = Carbon::parse($input['start_date']);
        $dateE = Carbon::parse($input['end_date']);

        $openTickets = Ticket::whereStatus(Ticket::STATUS_OPEN)->whereBetween('created_at',
            [$dateS->format('Y-m-d').' 00:00:00', $dateE.' 23:59:59'])
            ->when($input['categoryId'] != '', function (Builder $query) use ($input) {
                $query->where('category_id', '=', $input['categoryId']);
            })
            ->when($input['agentId'] != '', function (Builder $query) use ($input) {
                $query->whereHas('assignTo', function (Builder $query) use ($input) {
                    $query->where('users.id', '=', $input['agentId']);
                });
            })
            ->when(getLoggedInUserRoleId() == getAgentRoleId(), function (Builder $query) {
                $query->whereHas('assignTo', function (Builder $query) {
                    $query->where('users.id', '=', getLoggedInUserId());
                });
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get([
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'),
                DB::raw('count(*) as total'),
            ])
            ->keyBy('date')
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date);

                return $item;
            });

        $closeTickets = Ticket::whereStatus(Ticket::STATUS_CLOSED)->whereBetween('close_at',
            [$dateS->format('Y-m-d').' 00:00:00', $dateE.' 23:59:59'])
            ->when($input['categoryId'] != '', function (Builder $query) use ($input) {
                $query->where('category_id', '=', $input['categoryId']);
            })
            ->when($input['agentId'] != '', function (Builder $query) use ($input) {
                $query->whereHas('assignTo', function (Builder $query) use ($input) {
                    $query->where('users.id', '=', $input['agentId']);
                });
            })
            ->when(getLoggedInUserRoleId() == getAgentRoleId(), function (Builder $query) {
                $query->whereHas('assignTo', function (Builder $query) {
                    $query->where('users.id', '=', getLoggedInUserId());
                });
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get([
                DB::raw('DATE_FORMAT(close_at, "%Y-%m-%d") as date'),
                DB::raw('count(*) as total'),
            ])
            ->keyBy('date')
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date);

                return $item;
            });

        $period = CarbonPeriod::create($dateS, $dateE);

        // get all date labels
        $labelsData = array_map(function ($datePeriod) {
            return $datePeriod->format('M d');
        }, iterator_to_array($period));

        // get all open tickets in date period
        $openTicketsData = array_map(function ($datePeriod) use ($openTickets) {
            $date = $datePeriod->format('Y-m-d');

            return $openTickets->has($date) ? $openTickets->get($date)->total : 0;
        }, iterator_to_array($period));

        // get all close tickets in date period
        $closeTicketsData = array_map(function ($datePeriod) use ($closeTickets) {
            $date = $datePeriod->format('Y-m-d');

            return $closeTickets->has($date) ? $closeTickets->get($date)->total : 0;
        }, iterator_to_array($period));

        $result['openTicketCounts'] = $openTicketsData;
        $result['closeTicketCounts'] = $closeTicketsData;
        $result['dateLabels'] = $labelsData;

        return $result;
    }

    /**
     * @return array
     */
    public function agentTicketChart($input)
    {
        $status = $input['status'] ?? null;
        $data = [];

        $agent = User::whereHas('roles', function (Builder $query) {
            $query->where('id', '=', getAgentRoleId());
        });
        $assignTicket = User::withCount([
            'ticket' => function (Builder $q) use ($status) {
                $q->where('status', '=', $status);
            },
        ])->whereIn('id', $agent->pluck('id'));

        $color = [];
        for ($i = 0; $i < count($agent->pluck('id')); $i++) {
            $color[$i] = getNewColor($i);
        }

        $data['agents'] = $agent->pluck('name');
        $data['assignTicket'] = $assignTicket->pluck('ticket_count');
        $data['color'] = $color;

        return $data;
    }
}
