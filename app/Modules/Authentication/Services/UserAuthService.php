<?php

namespace App\Modules\Authentication\Services;

use App\Abstracts\AbstractAuthenticableService;
use App\Enums\Restricted;
use App\Modules\Users\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserAuthService
{

    public function model(): Builder
    {
        return User::with('roles')->where('is_blocked', 0);
    }

    public function loginViaCredentials(array $credentials): bool
	{
		return Auth::attempt($credentials);
	}
    
    public function loginViaUser(User $user): void
	{
		Auth::login($user);
	}
    
    public function logout(): void
	{
		Auth::logout();

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
        }
        return $user;
    }

}
