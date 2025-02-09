<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Web\Services\WebPageService;

class AboutPageController extends Controller
{

    public function __construct(private WebPageService $webPageService){}

    public function get(){
        $data = $this->webPageService->getData('about');
        return view('pages.main.about')
        ->with('breadcrumb', $data->title)
        ->with([
            'about' => $data,
        ]);
    }
}
