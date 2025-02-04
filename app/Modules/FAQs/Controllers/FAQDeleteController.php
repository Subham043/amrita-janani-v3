<?php

namespace App\Modules\FAQs\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\FAQs\Services\FAQService;

class FAQDeleteController extends Controller
{
    public function __construct(private FAQService $faqService){}

    
    public function index($id){
        $faq = $this->faqService->getById($id);

        try {
            //code...
            $this->faqService->forceDelete(
                $faq
            );
            return redirect()->intended(route('faq_view'))->with('success_status', 'Data Deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->intended(route('faq_view'))->with('error_status', 'Something went wrong. Please try again');
        }
    }

}
