<?php

namespace App\Modules\Languages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Languages\Services\LanguageService;

class LanguageViewController extends Controller
{
    public function __construct(private LanguageService $languageService){}

    public function index($id){
        $language = $this->languageService->getById($id);
        return view('pages.admin.language.display')->with('data', $language);
    }
}
