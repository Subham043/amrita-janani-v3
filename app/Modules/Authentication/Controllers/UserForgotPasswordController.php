<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RateLimitService;
use App\Modules\Authentication\Requests\UserForgotPasswordPostRequest;
use Illuminate\Support\Facades\Password;

class UserForgotPasswordController extends Controller
{

    public function get(){
        return view('pages.main.auth.forgot_password')->with('breadcrumb','Forgot Password');
    }

    public function post(UserForgotPasswordPostRequest $request){

        $status = Password::sendResetLink(
            $request->only('email')
        );
        if($status === Password::RESET_LINK_SENT){
            (new RateLimitService($request))->clearRateLimit();
            return redirect(route('forgot_password'))->with(['success_status' => __($status)]);
        }
        return redirect(route('forgot_password'))->with(['error_popup' => __($status)]);
    }
}
