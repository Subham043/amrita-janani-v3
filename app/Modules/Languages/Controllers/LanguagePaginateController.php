<?php

namespace App\Modules\Languages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Languages\Services\LanguageService;
use Illuminate\Http\Request;

class LanguagePaginateController extends Controller
{
    public function __construct(private LanguageService $languageService){}

    
    public function index(Request $request){
        $data = $this->languageService->paginate($request->total ?? 10);
        return view('pages.admin.language.list')->with('data', $data);
    }

}
