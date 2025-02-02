<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RateLimitService;
use App\Modules\Authentication\Requests\UserResetPasswordPostRequest;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class UserResetPasswordController extends Controller
{

    public function get(){
        return view('pages.main.auth.reset_password')->with('breadcrumb','Reset Password');
    }

    public function post(UserResetPasswordPostRequest $request, $token){

        $status = Password::reset(
            [...$request->only('email', 'password', 'password_confirmation'), 'token' => $token],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                ])->setRememberToken(str()->random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
        if($status === Password::PASSWORD_RESET){
            (new RateLimitService($request))->clearRateLimit();
            return redirect(route('login'))->with('success_status', __($status));
        }
        return back()->with(['error_popup' => __($status)]);
    }
}
