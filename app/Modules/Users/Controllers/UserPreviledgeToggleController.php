<?php

namespace App\Modules\Users\Controllers;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Modules\Users\Services\UserService;

class UserPreviledgeToggleController extends Controller
{
    public function __construct(private UserService $userService){}

    
    public function index($id){
        $user = $this->userService->getById($id);

        if($user->isAdmin()){
            return redirect()->back()->with('error_status', 'Can not toggle admin previledge.');
        }

        try {
            //code...
            $this->userService->update(
                [
                    'user_type' => $user->user_type == UserType::User->value ? UserType::PreviledgeUser->value : UserType::User->value
                ],
                $user
            );
            return redirect()->back()->with('success_status', 'Updated user accessibility successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error_status', 'Something went wrong. Please try again');
        }
    }

}
