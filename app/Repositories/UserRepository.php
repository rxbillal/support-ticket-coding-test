<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Auth;
use Exception;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class UserRepository
 * @version January 11, 2020, 11:09 am UTC
 */
class UserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'phone',
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
        return User::class;
    }

    /**
     * @param $input
     *
     * @throws \Throwable
     *
     * @return User
     */
    public function store($input)
    {
        try {
            DB::beginTransaction();

            $input['password'] = Hash::make($input['password']);
            $input['is_system'] = 1;
            /** @var User $user */
            $user = User::create(Arr::only($input, (new User())->getFillable()));

            $user->assignRole($input['role']);
            if (isset($input['image']) && ! empty($input['image'])) {
                $user->addMedia($input['image'])->toMediaCollection(User::PROFILE, config('app.media_disc'));
            }

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  array  $input
     *
     * @return bool
     */
    public function profileUpdate($input)
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $user->update($input);

            if ((isset($input['image']))) {
                $user->clearMediaCollection(User::PROFILE);
                $user->addMedia($input['image'])
                    ->toMediaCollection(User::PROFILE, config('app.media_disc'));
            }

            return true;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  array  $input
     *
     * @return bool
     */
    public function changePassword($input)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            if (! Hash::check($input['password_current'], $user->password)) {
                throw new UnprocessableEntityHttpException('Current password is invalid.');
            }
            $input['password'] = Hash::make($input['password']);
            $user->update($input);

            return true;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  array  $input
     *
     * @return bool
     */
    public function storeAndUpdateNotification($input)
    {
        /** @var User $user */
        $user = Auth::user();

        $user->update($input);

        return true;
    }

    /**
     * @param  array  $input
     * @param  int  $id
     *
     * @return bool|Builder|Builder[]|Collection|Model
     */
    public function update($input, $id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);

        try {
            $user->update($input);

            if (! empty($input['image'])) {
                $user->clearMediaCollection(User::PROFILE);
                $user->addMedia($input['image'])
                    ->toMediaCollection(User::PROFILE, config('app.media_disc'));
            }

            return true;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function checkUserTicket($id)
    {
        $userTicket = Ticket::where([
            ['status', '=', Ticket::STATUS_OPEN],
            ['created_by', '=', $id],
        ])->orWhere('status', '=', Ticket::STATUS_IN_PROGRESS)->pluck('id');

        foreach ($userTicket as $id) {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();
        }

        return $userTicket->count();
    }
}
