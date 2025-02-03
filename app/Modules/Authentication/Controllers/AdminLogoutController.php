<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authentication\Services\AdminAuthService;
use Illuminate\Http\Request;

class AdminLogoutController extends Controller
{
    public function __construct(private AdminAuthService $authService){}

    public function get(Request $request){
        $this->authService->logout();
        return redirect(route('signin'));
    }
}
