<?php

namespace App\Modules\Languages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Languages\Services\LanguageService;

class LanguageDeleteController extends Controller
{
    public function __construct(private LanguageService $languageService){}

    
    public function index($id){
        $language = $this->languageService->getById($id);

        try {
            //code...
            $this->languageService->forceDelete(
                $language
            );
            return redirect()->intended(route('language_view'))->with('success_status', 'Data Deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->intended(route('language_view'))->with('error_status', 'Something went wrong. Please try again');
        }
    }

}
