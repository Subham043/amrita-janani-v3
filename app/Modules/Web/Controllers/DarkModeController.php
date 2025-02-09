<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Web\Services\WebPageService;

class DarkModeController extends Controller
{

    public function __construct(private WebPageService $webPageService){}

    public function get(){
        $this->webPageService->toggleDarkMode();
        return redirect()->back();
    }
}
