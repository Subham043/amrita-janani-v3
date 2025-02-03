<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Services\UserService;

class UserViewController extends Controller
{
    public function __construct(private UserService $userService){}

    public function index($id){
        $user = $this->userService->getById($id);
        return view('pages.admin.user.display')->with('data', $user);
    }
}
