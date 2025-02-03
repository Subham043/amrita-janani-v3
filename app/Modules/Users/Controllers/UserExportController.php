<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Services\UserService;

class UserExportController extends Controller
{
    public function __construct(private UserService $userService){}

    public function index(){
        return $this->userService->excel()->toBrowser();
    }
}
