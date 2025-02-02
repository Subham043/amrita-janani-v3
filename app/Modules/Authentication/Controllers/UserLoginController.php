<?php

namespace App\Modules\Authentication\Controllers;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Services\RateLimitService;
use App\Modules\Authentication\Requests\UserLoginPostRequest;
use App\Modules\Authentication\Services\UserAuthService;

class UserLoginController extends Controller
{
    public function __construct(private UserAuthService $authService){}

    public function get(){
        return view('pages.main.auth.login')->with('breadcrumb','Sign In');
    }

    public function post(UserLoginPostRequest $request){

        $is_authenticated = $this->authService->loginViaCredentials([...$request->safe()->except(['g-recaptcha-response']), 'status' => UserStatus::Active->value]);
        if ($is_authenticated) {
            (new RateLimitService($request))->clearRateLimit();
            return redirect()->intended(route('content_dashboard'));
        }
        return redirect(route('login'))->with('error_popup', 'Oops! You have entered invalid credentials');
    }
}
