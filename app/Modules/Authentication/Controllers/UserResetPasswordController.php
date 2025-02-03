<?php

namespace App\Modules\Authentication\Controllers;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Services\RateLimitService;
use App\Modules\Authentication\Requests\UserResetPasswordPostRequest;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;

class UserResetPasswordController extends Controller
{

    public function get(Request $request, $token){
        if (! $request->hasValidSignature()) {
            abort(401);
        }
        return view('pages.main.auth.reset_password')->with('breadcrumb','Reset Password');
    }

    public function post(UserResetPasswordPostRequest $request, $token){
        if (! $request->hasValidSignature()) {
            return back()->with(['error_popup' => "This password reset link has expired."]);
        }
        $status = Password::reset(
            [
                ...$request->only('email', 'password', 'password_confirmation'), 
                'token' => $token,
                'status' => UserStatus::Active->value,
            ],
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
