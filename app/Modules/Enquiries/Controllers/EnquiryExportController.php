<?php

namespace App\Modules\Enquiries\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enquiries\Services\EnquiryService;

class EnquiryExportController extends Controller
{
    public function __construct(private EnquiryService $enquiryService){}

    public function index(){
        return $this->enquiryService->excel()->toBrowser();
    }
}
