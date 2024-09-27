<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Queries\AdminDataTable;
use App\Repositories\AdminRepository;
use DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laracasts\Flash\Flash;
use Spatie\Permission\Models\Role;

class AdminController extends AppBaseController
{
    /** @var AdminRepository */
    private $adminRepository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new AdminDataTable())->get())->make(true);
        }

        return view('admins.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('admins.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();
        $input['role'] = User::ADMIN;
        $this->adminRepository->store($input);

        Flash::success(__('messages.admin.admin_created_successfully'));

        return redirect(route('admins.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param User $admin
     * @return Application|Factory|View
     */
    public function show(User $admin)
    {
        return view('admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $admin
     * @return Application|Factory|View
     */
    public function edit(User $admin)
    {
        $admin->load('media');

        return view('admins.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $admin
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $admin)
    {
        $input = $request->all();
        $this->adminRepository->update($input, $admin->id);

        Flash::success(__('messages.admin.admin_updated_successfully'));

        return redirect(route('admins.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $admin
     * @return Response
     */
    public function destroy(User $admin)
    {
        $admin->delete();

        return $this->sendSuccess('Admin deleted successfully.');
    }
}
