<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RateLimitService;
use App\Modules\Authentication\Requests\UserResetPasswordPostRequest;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;

class AdminResetPasswordController extends Controller
{

    public function get(Request $request, $token){
        if (! $request->hasValidSignature()) {
            abort(403);
        }
        return view('pages.admin.auth.reset_password');
    }

    public function post(UserResetPasswordPostRequest $request, $token){

        if (! $request->hasValidSignature()) {
            return back()->with(['error_status' => "This password reset link has expired."]);
        }
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
            return redirect(route('signin'))->with('success_status', __($status));
        }
        return back()->with(['error_status' => __($status)]);
    }
}
