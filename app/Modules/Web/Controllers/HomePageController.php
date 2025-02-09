<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Web\Services\WebPageService;

class HomePageController extends Controller
{

    public function __construct(private WebPageService $webPageService){}

    public function get(){
        $data = $this->webPageService->getData('home');
        return view('pages.main.index')
        ->with('breadcrumb',$data->title)
        ->with([
            'home' => $data,
            'bannerImage' => $this->webPageService->getBannerImage(),
            'bannerQuote' => $this->webPageService->getBannerQuote(),
        ]);
    }
}
