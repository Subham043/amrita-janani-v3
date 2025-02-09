<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Web\Services\WebPageService;

class FAQPageController extends Controller
{

    public function __construct(private WebPageService $webPageService){}

    public function get(){
        $data = $this->webPageService->getFaqs();
        return view('pages.main.faq')
        ->with('breadcrumb','Frequently Asked Questions')
        ->with([
            'faq' => $data,
        ]);
    }
}
