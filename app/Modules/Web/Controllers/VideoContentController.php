<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Videos\Models\VideoModel;
use App\Modules\Web\Requests\ContentPostRequest;
use App\Modules\Web\Requests\SearchPostRequest;
use App\Modules\Web\Services\WebVideoContentService;
use App\Modules\Web\Services\WebPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoContentController extends Controller
{

    public function __construct(private WebVideoContentService $webVideoContentService, private WebPageService $webPageService){}

    public function index(Request $request){
        $data = $this->webVideoContentService->paginate($request->total ?? 12);
        $languages = $this->webPageService->getLanguages();
        return view('pages.main.content.video')
        ->with('breadcrumb', 'Video')
        ->with('videos',$data)
        ->with('languages', $languages)
        ->with([
            'sort' => $request->query('sort') ?? '-id',
            'favourite' => ($request->query('filter')['favourite'] ?? '')=="yes" ?? false,
            'selected_languages' => array_map('intval', explode('_', ($request->query('filter')['language'] ?? ''))) ?? [],
        ]);
    }

    public function view($uuid){
        $video = $this->webVideoContentService->getByUuid($uuid);

        defer(function () use ($video) {
            $video->increment('views');
        });

        return view('pages.main.content.video_view')
        ->with('breadcrumb', 'Video'. ' - ' .$video->title)
        ->with('video', $video)
        ->with('videoAccess', null);
    }

    public function makeFavourite($uuid){
        $video = $this->webVideoContentService->getByUuid($uuid);
        $this->webVideoContentService->toggleFavorite($video);
        return redirect()->intended(route('content_video_view', $uuid));
    }

    public function requestAccess(ContentPostRequest $req, $uuid){
        $video = $this->webVideoContentService->getByUuid($uuid);
        $this->webVideoContentService->requestAccess($video, $req->safe()->message);
        return response()->json(["message" => "Access requested successfully."], 201);
    }

    public function report(ContentPostRequest $req, $uuid){
        $video = $this->webVideoContentService->getByUuid($uuid);
        $this->webVideoContentService->report($video, $req->safe()->message);
        return response()->json(["message" => "Reported successfully."], 201);
    }

    public function search_query(SearchPostRequest $request){
        $data = $this->webPageService->videoSearchQueryList($request->safe()->phrase);
        return response()->json(["data"=>$data], 200);
    }
}
