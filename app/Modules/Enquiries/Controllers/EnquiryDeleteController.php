<?php

namespace App\Modules\Enquiries\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Enquiries\Services\EnquiryService;

class EnquiryDeleteController extends Controller
{
    public function __construct(private EnquiryService $enquiryService){}

    
    public function index($id){
        $enquiry = $this->enquiryService->getById($id);

        try {
            //code...
            $this->enquiryService->forceDelete(
                $enquiry
            );
            return redirect()->intended(route('enquiry_view'))->with('success_status', 'Data Deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->intended(route('enquiry_view'))->with('error_status', 'Something went wrong. Please try again');
        }
    }

}
