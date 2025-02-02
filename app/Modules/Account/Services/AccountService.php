<?php

namespace App\Modules\Account\Services;

use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Auth;

class AccountService
{
    public function update(array $data): User
    {
        $user = User::where('id', Auth::user()->id)->firstOrFail();
        $user->update($data);
        $user->refresh();
        return $user;
    }

}
