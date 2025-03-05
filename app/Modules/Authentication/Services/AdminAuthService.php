<?php

namespace App\Modules\Authentication\Services;

use App\Enums\UserStatus;
use App\Enums\UserType;
use Illuminate\Support\Facades\Auth;

class AdminAuthService
{

    public function loginViaCredentials(array $credentials, $remember = false): bool
	{
		return Auth::guard('admin')->attempt([
            ...$credentials,
            'status' => UserStatus::Active->value,
			'user_type' => UserType::Admin->value,
        ], $remember);
	}
    
    public function logout(): void
	{
		Auth::guard('admin')->logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();
	}

}
