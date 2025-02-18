<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RateLimitService;
use App\Modules\Authentication\Requests\UserLoginPostRequest;
use App\Modules\Authentication\Services\AdminAuthService;

class AdminLoginController extends Controller
{
    public function __construct(private AdminAuthService $authService){}

    public function get(){
        return view('pages.admin.auth.login');
    }

    public function post(UserLoginPostRequest $request){

        $is_authenticated = $this->authService->loginViaCredentials([...$request->safe()->except(['g-recaptcha-response'])]);

        if ($is_authenticated) {
            (new RateLimitService($request))->clearRateLimit();
            return redirect()->to(route('dashboard'));
        }
        return redirect(route('signin'))->with('error_status', 'Oops! You have entered invalid credentials');
    }
}
