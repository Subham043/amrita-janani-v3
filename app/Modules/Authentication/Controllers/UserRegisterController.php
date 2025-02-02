<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RateLimitService;
use App\Modules\Authentication\Requests\UserRegisterPostRequest;
use App\Modules\Authentication\Services\UserAuthService;
use Illuminate\Support\Facades\DB;

class UserRegisterController extends Controller
{
    public function __construct(private UserAuthService $authService){}

    public function get(){
        return view('pages.main.auth.register')->with('breadcrumb','Sign Up');
    }

    public function post(UserRegisterPostRequest $request){

        DB::beginTransaction();
        try {
            //code...
            $user = $this->authService->register([...$request->safe()->except(['g-recaptcha-response'])]);
            $this->authService->loginViaUser($user);
            (new RateLimitService($request))->clearRateLimit();
            return redirect()->intended(route('content_dashboard'))->with('success_status', 'Registered successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect(route('signup'))->with('error_popup', 'Oops! Something went wrong. Please try again.');
        } finally {
            DB::commit();
        }
    }
}
