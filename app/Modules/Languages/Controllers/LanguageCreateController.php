<?php

namespace App\Modules\Languages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Languages\Requests\LanguagePostRequest;
use App\Modules\Languages\Services\LanguageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LanguageCreateController extends Controller
{
    public function __construct(private LanguageService $languageService){}

    public function get(){
        return view('pages.admin.language.create');
    }

    public function post(LanguagePostRequest $request){

        DB::beginTransaction();
        try {
            //code...
            $data = $this->languageService->create([
                ...$request->validated(),
                'status' => $request->status == "on" ? 1 : 0,
                'user_id' => Auth::guard('admin')->user()->id,
            ]);
            return response()->json(["url"=>empty($request->refreshUrl)?route('language_view'):$request->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["error"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
}
