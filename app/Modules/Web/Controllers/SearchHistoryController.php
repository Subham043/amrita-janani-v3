<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Web\Requests\SearchPostRequest;
use App\Modules\Web\Services\WebPageService;

class SearchHistoryController extends Controller
{

    public function __construct(private WebPageService $webPageService){}

    public function search_history(){
        $search_history = $this->webPageService->searchHistoryList();
        return view('pages.main.search_history')->with('breadcrumb','Search History')->with('search_history', $search_history);
    }
    
    public function search_query(SearchPostRequest $request){
        $data = $this->webPageService->searchQueryList($request->safe()->phrase);
        return response()->json(["data"=>$data], 200);
    }
}
