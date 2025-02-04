<?php

namespace App\Modules\Banners\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banners\Services\BannerService;
use Illuminate\Http\Request;

class BannerPaginateController extends Controller
{
    public function __construct(private BannerService $bannerService){}

    
    public function index(Request $request){
        $data = $this->bannerService->paginate($request->total ?? 10);
        return view('pages.admin.banner.banner')->with('data', $data);
    }

}
