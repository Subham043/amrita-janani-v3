<?php

namespace App\Modules\FAQs\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\FAQs\Requests\FAQPostRequest;
use App\Modules\FAQs\Services\FAQService;
use Illuminate\Support\Facades\DB;

class FAQUpdateController extends Controller
{
    public function __construct(private FAQService $faqService){}

    public function post(FAQPostRequest $request){
        $faq = $this->faqService->getById($request->id);
        DB::beginTransaction();
        try {
            //code...
            $data = $this->faqService->update([
                ...$request->safe()->except('id'),
            ], $faq);
            return response()->json(["url"=>empty($request->refreshUrl)?route('faq_view'):$request->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->intended(route('subadmin_edit',$faq->id))->with('error_status', 'Something went wrong. Please try again');
        } finally {
            DB::commit();
        }
    }
}
