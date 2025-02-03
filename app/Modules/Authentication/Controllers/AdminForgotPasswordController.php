<?php

namespace App\Modules\Authentication\Controllers;

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
            $request->only('email')
        );
        if($status === Password::RESET_LINK_SENT){
            (new RateLimitService($request))->clearRateLimit();
            return redirect(route('forgotPassword'))->with(['success_status' => __($status)]);
        }
        return redirect(route('forgotPassword'))->with(['error_status' => __($status)]);
    }
}
