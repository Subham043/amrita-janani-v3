<?php

namespace App\Modules\Enquiries\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enquiries\Services\EnquiryService;
use Illuminate\Http\Request;

class EnquiryPaginateController extends Controller
{
    public function __construct(private EnquiryService $enquiryService){}

    
    public function index(Request $request){
        $data = $this->enquiryService->paginate($request->total ?? 10);
        return view('pages.admin.enquiry.list')->with('data', $data);
    }

}
