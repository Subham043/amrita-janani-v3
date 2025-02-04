<?php

namespace App\Modules\Banners\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banners\Services\BannerQuoteService;

class BannerQuoteExportController extends Controller
{
    public function __construct(private BannerQuoteService $bannerQuoteService){}

    public function index(){
        return $this->bannerQuoteService->excel()->toBrowser();
    }
}
