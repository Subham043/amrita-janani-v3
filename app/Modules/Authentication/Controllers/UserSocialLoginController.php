<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authentication\Services\UserAuthService;
use Laravel\Socialite\Facades\Socialite;

class UserSocialLoginController extends Controller
{
    public function __construct(private UserAuthService $authService){}

    public function google(){
        return Socialite::driver('google')->redirect();
    }

    public function google_Callback(){
        try {
            //code...
            $data = Socialite::driver('google')->user();
            $user = $this->authService->socialRegister($data->email, [
                'name' => $data->nickname,
                'email' => $data->email,
            ]);
            $this->authService->loginViaUser($user);
            return redirect()->intended(route('content_dashboard'))->with('success_status', 'Registered successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            return redirect(route('login'))->with('error_popup', 'Oops! Something went wrong. Please try again.');
        }
    }
    
    public function facebook(){
        return Socialite::driver('facebook')->redirect();
    }

    public function facebook_Callback(){
        try {
            //code...
            $data = Socialite::driver('facebook')->user();
            $user = $this->authService->socialRegister($data->email, [
                'name' => $data->name,
                'email' => $data->email,
            ]);
            $this->authService->loginViaUser($user);
            return redirect()->intended(route('content_dashboard'))->with('success_status', 'Registered successfully.');
        } catch (\Throwable $th) {
            return redirect(route('login'))->with('error_popup', 'Oops! Something went wrong. Please try again.');
        }
    }

}
