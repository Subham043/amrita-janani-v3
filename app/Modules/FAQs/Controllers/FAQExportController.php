<?php

namespace App\Modules\FAQs\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\FAQs\Services\FAQService;

class FAQExportController extends Controller
{
    public function __construct(private FAQService $faqService){}

    public function index(){
        return $this->faqService->excel()->toBrowser();
    }
}
