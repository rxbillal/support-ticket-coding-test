<?php

namespace App\Http\Controllers;

use App\Repositories\DashboardRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends AppBaseController
{
    /** @var DashboardRepository */
    private $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $data['dashboardData'] = $this->dashboardRepository->getDashboardAssociatedData();

        return view('dashboard.index', compact('data'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function agentDashBoard()
    {
        $data['dashboardData'] = $this->dashboardRepository->getDashboardAssociatedData();

        return view('dashboard.index', compact('data'));
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function categoryTicketChart(Request $request)
    {
        $data = $this->dashboardRepository->getCategoryReport($request->input('status'));

        return $this->sendResponse($data, 'Categories Ticket chart data retrieved successfully.');
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function ticketChart(Request $request)
    {
        $input = $request->all();
        $data = $this->dashboardRepository->ticketChart($input);

        return $this->sendResponse($data, 'Open Vs Close chart data retrieved successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function agentTicketReport(Request $request)
    {
        $data = $this->dashboardRepository->agentTicketChart($request->all());

        return $this->sendResponse($data, 'Agent wise tickets Chart retrieved successfully.');
    }
}
