<?php

namespace App\Modules\Users\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Services\UserService;
use Illuminate\Http\Request;

class UserPaginateController extends Controller
{
    public function __construct(private UserService $userService){}

    
    public function index(Request $request){
        $data = $this->userService->paginate($request->total ?? 10);
        return view('pages.admin.user.list')->with('data', $data)->with('filter_status', $request->query('filter')['status'] ?? 'all')->with('filter_verification', $request->query('filter')['verification'] ?? 'all');
    }

}
