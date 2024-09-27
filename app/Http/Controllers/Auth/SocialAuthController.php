<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SocialAuthController extends Controller
{
    /**
     * @param $provider
     *
     *
     * @return RedirectResponse
     */
    public function redirectToSocial($provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * @param $provider
     *
     *
     * @return Application|\Illuminate\Http\RedirectResponse|Redirector
     */
    public function handleSocialCallback($provider){
        if(Auth::check()){
            return redirect('/');
        }
        $socialUser = Socialite::driver($provider)->user();
        if(empty($socialUser['email'])){
            Flash::error('We couldn\'t find email address in your Facebook account');

            return redirect(route('register'));
        }
        
        try {
            DB::beginTransaction();

            /** @var User $user */
            $user = User::whereRaw('lower(email) = ?', strtolower($socialUser['email']))->first();
            
            $existingAccount = null;
            if (! empty($user)) {
                /** @var SocialAccount $existingProfile */
                $existingAccount = SocialAccount::whereUserId($user->id)->whereProviderId($socialUser->id)->first();
            } else {
                $userData['name'] = $socialUser['name'];
                $userData['email'] = $socialUser['email'];
                $userData['email_verified_at'] = Carbon::now();
                $userData['password'] = bcrypt(Str::random(40));

                /** @var User $user */
                $user = User::create($userData);
                $user->assignRole(getCustomerRoleId());
            }

            if (empty($existingAccount)) {
                $socialAccount = new SocialAccount();
                $socialAccount->user_id = $user->id;
                $socialAccount->provider = $provider;
                $socialAccount->provider_id = $socialUser->id ;
                $socialAccount->save();
            }
            Db::commit();
            Auth::login($user);

            return redirect(RouteServiceProvider::CUSTOMER_DASHBOARD);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
        
    }
}
