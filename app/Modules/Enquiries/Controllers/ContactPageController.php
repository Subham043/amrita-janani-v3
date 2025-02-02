<?php

namespace App\Modules\Enquiries\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RateLimitService;
use App\Modules\Enquiries\Requests\ContactPagePostRequest;
use App\Modules\Enquiries\Services\EnquiryService;
use Illuminate\Support\Facades\DB;

class ContactPageController extends Controller
{
    public function __construct(private EnquiryService $enquiryService){}

    public function get(){
        return view('pages.main.contact')->with('breadcrumb','Contact Us');
    }

    public function post(ContactPagePostRequest $request){

        DB::beginTransaction();
        try {
            //code...
            $this->enquiryService->create([
                ...$request->safe()->except(['g-recaptcha-response']),
                'ip_address' => $request->ip()
            ]);
            (new RateLimitService($request))->clearRateLimit();
            return response()->json(["message" => "Message sent successfully."], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["error_popup"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
}
