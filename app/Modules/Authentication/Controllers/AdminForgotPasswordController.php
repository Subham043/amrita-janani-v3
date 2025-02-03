<?php

namespace App\Modules\Authentication\Controllers;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Services\RateLimitService;
use App\Modules\Authentication\Requests\UserForgotPasswordPostRequest;
use Illuminate\Support\Facades\Password;

class AdminForgotPasswordController extends Controller
{

    public function get(){
        return view('pages.admin.auth.forgotpassword');
    }

    public function post(UserForgotPasswordPostRequest $request){

        $status = Password::sendResetLink(
            [
                ...$request->only('email'),
                'status' => UserStatus::Active->value,
			    'user_type' => UserType::Admin->value,
            ]
        );
        if($status === Password::RESET_LINK_SENT){
            (new RateLimitService($request))->clearRateLimit();
            return redirect(route('forgotPassword'))->with(['success_status' => __($status)]);
        }
        return redirect(route('forgotPassword'))->with(['error_status' => __($status)]);
    }
}
