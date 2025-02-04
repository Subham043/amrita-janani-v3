<?php

namespace App\Modules\Languages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Languages\Services\LanguageService;

class LanguageExportController extends Controller
{
    public function __construct(private LanguageService $languageService){}

    public function index(){
        return $this->languageService->excel()->toBrowser();
    }
}
