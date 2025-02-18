<?php

namespace App\Modules\Users\Controllers;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Modules\Users\Services\UserService;

class UserStatusToggleController extends Controller
{
    public function __construct(private UserService $userService){}

    
    public function index($id){
        $user = $this->userService->getById($id);

        if($user->isAdmin()){
            return redirect()->back()->with('error_status', 'Can not toggle admin status.');
        }

        try {
            //code...
            $this->userService->update(
                [
                    'status' => $user->status == UserStatus::Active->value ? UserStatus::Blocked->value : UserStatus::Active->value
                ],
                $user
            );
            return redirect()->back()->with('success_status', 'Updated user status successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error_status', 'Something went wrong. Please try again');
        }
    }

}
