<?php

namespace App\Modules\Banners\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banners\Requests\BannerQuotePostRequest;
use App\Modules\Banners\Services\BannerQuoteService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BannerQuoteCreateController extends Controller
{
    public function __construct(private BannerQuoteService $bannerQuoteService){}

    public function post(BannerQuotePostRequest $request){

        DB::beginTransaction();
        try {
            //code...
            $data = $this->bannerQuoteService->create([
                ...$request->validated(),
                'user_id' => Auth::guard('admin')->user()->id,
            ]);
            return response()->json(["url"=>empty($request->refreshUrl)?route('banner_quote_view'):$request->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
}
