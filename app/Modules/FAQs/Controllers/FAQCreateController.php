<?php

namespace App\Modules\FAQs\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\FAQs\Requests\FAQPostRequest;
use App\Modules\FAQs\Services\FAQService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FAQCreateController extends Controller
{
    public function __construct(private FAQService $faqService){}

    public function post(FAQPostRequest $request){

        DB::beginTransaction();
        try {
            //code...
            $data = $this->faqService->create([
                ...$request->validated(),
                'user_id' => Auth::guard('admin')->user()->id,
            ]);
            return response()->json(["url"=>empty($request->refreshUrl)?route('faq_view'):$request->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["error"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
}
