<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authentication\Services\UserAuthService;
use Illuminate\Http\Request;

class UserLogoutController extends Controller
{
    public function __construct(private UserAuthService $authService){}

    public function get(Request $request){
        $this->authService->logout();
        return redirect(route('index'));
    }
}
