<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authentication\Services\UserAuthService;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

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
    
    public function facebook_deauthorize(Request $request){
        $signedRequest = $request->input('signed_request');

        if (!$signedRequest) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $appSecret = config('services.facebook.client_secret');

        list($encodedSig, $payload) = explode('.', $signedRequest, 2);

        $expectedSig = hash_hmac('sha256', $payload, $appSecret, true);
        $decodedSig = base64_decode(strtr($encodedSig, '-_', '+/'));

        if (!hash_equals($decodedSig, $expectedSig)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

        if (!isset($data['user_id'])) {
            return response()->json(['error' => 'User ID not found'], 400);
        }

        // TODO: Delete user data from the database
        // Example: User::where('facebook_id', $data['user_id'])->delete();
        // User::whereIn('id', [$data['user_id']])->where('user_type', '!=', UserType::Admin)->forceDelete();

        return response()->json(['message' => 'User deauthorized'], 200);
    }

}
