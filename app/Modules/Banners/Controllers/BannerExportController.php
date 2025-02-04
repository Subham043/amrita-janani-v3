<?php

namespace App\Modules\Banners\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banners\Services\BannerService;

class BannerExportController extends Controller
{
    public function __construct(private BannerService $bannerService){}

    public function index(){
        return $this->bannerService->excel()->toBrowser();
    }
}
