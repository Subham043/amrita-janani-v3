<?php

namespace App\Modules\Enquiries\Controllers;

use App\Events\AdminEnquiryReplied;
use App\Http\Controllers\Controller;
use App\Modules\Enquiries\Requests\EnquiryReplyPostRequest;
use App\Modules\Enquiries\Services\EnquiryService;

class EnquiryReplyController extends Controller
{
    public function __construct(private EnquiryService $enquiryService){}

    public function index(EnquiryReplyPostRequest $request, $id){
        $enquiry = $this->enquiryService->getById($id);
        event(new AdminEnquiryReplied($enquiry, $request->subject, $request->message));
        return response()->json(["message" => "Replied successfully."], 200);
    }
}
