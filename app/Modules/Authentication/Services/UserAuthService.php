<?php

namespace App\Modules\Authentication\Services;

use App\Enums\Restricted;
use App\Enums\UserStatus;
use App\Events\UserSocialRegistered;
use App\Modules\Users\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class UserAuthService
{

    public function loginViaCredentials(array $credentials): bool
	{
		return Auth::guard('web')->attempt([
            ...$credentials,
            'status' => UserStatus::Active->value
        ]);
	}
    
    public function loginViaUser(User $user): void
	{
		Auth::guard('web')->login($user);
	}
    
    public function logout(): void
	{
		Auth::guard('web')->logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();
	}

    public function register(array $data): User
    {
        $user = User::create($data);
        event(new Registered($user));
        return $user;
    }
    
    public function socialRegister(string $email, array $data): User
    {
        $user = User::where('email', $email)->first();
        if(!$user){
            $user = User::create([
                ...$data,
                'password' => null,
                'is_social' => Restricted::Yes->value(),
            ]);
            $user->email_verified_at = now();
            $user->save();
            $user->refresh();
            event(new UserSocialRegistered($user));
        }
        return $user;
    }

}
