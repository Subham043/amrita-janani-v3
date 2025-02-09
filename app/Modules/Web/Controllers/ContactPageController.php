<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RateLimitService;
use App\Modules\Web\Requests\ContactPagePostRequest;
use App\Modules\Web\Services\WebPageService;
use Illuminate\Support\Facades\DB;

class ContactPageController extends Controller
{
    public function __construct(private WebPageService $webPageService){}

    public function get(){
        return view('pages.main.contact')->with('breadcrumb','Contact Us');
    }

    public function post(ContactPagePostRequest $request){

        DB::beginTransaction();
        try {
            //code...
            $this->webPageService->createEnquiry([
                ...$request->safe()->except(['g-recaptcha-response', 'system_info']),
                'ip_address' => $request->ip(),
                'system_info' => json_decode($request->system_info, true)
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
