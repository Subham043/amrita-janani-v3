<?php

namespace App\Modules\Languages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Languages\Requests\LanguagePostRequest;
use App\Modules\Languages\Services\LanguageService;
use Illuminate\Support\Facades\DB;

class LanguageUpdateController extends Controller
{
    public function __construct(private LanguageService $languageService){}

    public function get($id){
        $language = $this->languageService->getById($id);
        return view('pages.admin.language.edit')
        ->with('data',$language);
    }

    public function post(LanguagePostRequest $request, $id){
        $language = $this->languageService->getById($id);
        DB::beginTransaction();
        try {
            //code...
            $data = $this->languageService->update([
                ...$request->validated(),
                'status' => $request->status == "on" ? 1 : 0,
            ], $language);
            return response()->json(["url"=>empty($request->refreshUrl)?route('language_view'):$request->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["error"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
}
