<?php

namespace App\Modules\Banners\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banners\Services\BannerQuoteService;
use Illuminate\Http\Request;

class BannerQuotePaginateController extends Controller
{
    public function __construct(private BannerQuoteService $bannerQuoteService){}

    
    public function index(Request $request){
        $data = $this->bannerQuoteService->paginate($request->total ?? 10);
        return view('pages.admin.banner.banner_quote')->with('data', $data);
    }

}
