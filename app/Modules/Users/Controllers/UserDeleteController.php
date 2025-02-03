<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Services\UserService;

class UserDeleteController extends Controller
{
    public function __construct(private UserService $userService){}

    
    public function index($id){
        $user = $this->userService->getById($id);

        try {
            //code...
            $this->userService->forceDelete(
                $user
            );
            return redirect()->intended(route('subadmin_view'))->with('success_status', 'Data Deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->intended(route('subadmin_view'))->with('error_status', 'Something went wrong. Please try again');
        }
    }

}
