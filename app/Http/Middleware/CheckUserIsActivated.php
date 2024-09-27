<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Utils\ResponseUtil;
use Auth;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Session;

class CheckUserIsActivated
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $user->is_active || ! $user->email_verified_at) {
            $errorMessage = (! $user->email_verified_at) ? 'Please check your email inbox to confirm your account. Do not forget to check your spam folder as well.' : 'Your account has been deactivated, Please contact to admin for account activation.';

            $user->update(['is_online' => 0, 'last_seen' => Carbon::now()]);
            Session::flash('error', $errorMessage);

            if ($request->ajax()) {
                $userTokens = $user->tokens;
                foreach ($userTokens as $token) {
                    /** var Laravel\Passport\Token $token */
                    $token->revoke();
                }

                return JsonResponse::fromJsonString(ResponseUtil::makeError($errorMessage),
                    Response::HTTP_UNAUTHORIZED);
            }

            \Auth::logout();

            return redirect('login');
        }

        return $next($request);
    }
}
