<?php

namespace App\Modules\Enquiries\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enquiries\Services\EnquiryService;

class EnquiryViewController extends Controller
{
    public function __construct(private EnquiryService $enquiryService){}

    public function index($id){
        $enquiry = $this->enquiryService->getById($id);
        return view('pages.admin.enquiry.display')->with('data', $enquiry);
    }
}
