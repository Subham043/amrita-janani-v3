<?php

namespace App\Modules\FAQs\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\FAQs\Services\FAQService;
use Illuminate\Http\Request;

class FAQPaginateController extends Controller
{
    public function __construct(private FAQService $faqService){}

    
    public function index(Request $request){
        $data = $this->faqService->paginate($request->total ?? 10);
        return view('pages.admin.faq.list')->with('data', $data);
    }

}
