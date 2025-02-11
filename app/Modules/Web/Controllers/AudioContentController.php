<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Audios\Models\AudioModel;
use App\Modules\Web\Requests\ContentPostRequest;
use App\Modules\Web\Requests\SearchPostRequest;
use App\Modules\Web\Services\WebAudioContentService;
use App\Modules\Web\Services\WebPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AudioContentController extends Controller
{

    public function __construct(private WebAudioContentService $webAudioContentService, private WebPageService $webPageService){}

    public function index(Request $request){
        $data = $this->webAudioContentService->paginate($request->total ?? 12);
        $languages = $this->webPageService->getLanguages();
        return view('pages.main.content.audio')
        ->with('breadcrumb', 'Audio')
        ->with('audios',$data)
        ->with('languages', $languages)
        ->with([
            'sort' => $request->query('sort') ?? '-id',
            'favourite' => ($request->query('filter')['favourite'] ?? '')=="yes" ?? false,
            'selected_languages' => array_map('intval', explode('_', ($request->query('filter')['language'] ?? ''))) ?? [],
        ]);
    }

    public function view($uuid){
        $audio = $this->webAudioContentService->getByUuid($uuid);

        defer(function () use ($audio) {
            $audio->increment('views');
        });

        return view('pages.main.content.audio_view')
        ->with('breadcrumb', 'Audio'. ' - ' .$audio->title)
        ->with('audio', $audio)
        ->with('audioAccess', null);
    }

    public function audioFile(Request $request, $uuid){
        if((auth()->guard('web')->check() || auth()->guard('admin')->check()) && $request->hasValidSignature()){
            if(!empty($request->header('referer')) && str_contains($request->header('referer'), route('content_audio_view', $uuid)) && !empty($request->header('accept')) && !str_contains($request->header('accept'), 'text/html,application/xhtml+xml,application/xml')){
                $audio = $this->webAudioContentService->getFileByUuid($uuid);
        
                if($audio->contentVisible()){
                    if(Storage::exists((new AudioModel)->file_path.$audio->audio)){
                        return response()->file(storage_path('app/private/'.(new AudioModel)->file_path.$audio->audio));
                    }
                }
            }
            return redirect()->intended(route('content_audio_view', $uuid));
        }
        abort(404, "File not found.");
    }

    public function makeFavourite($uuid){
        $audio = $this->webAudioContentService->getByUuid($uuid);
        $this->webAudioContentService->toggleFavorite($audio);
        return redirect()->intended(route('content_audio_view', $uuid));
    }

    public function requestAccess(ContentPostRequest $req, $uuid){
        $audio = $this->webAudioContentService->getByUuid($uuid);
        $this->webAudioContentService->requestAccess($audio, $req->safe()->message);
        return response()->json(["message" => "Access requested successfully."], 201);
    }

    public function report(ContentPostRequest $req, $uuid){
        $audio = $this->webAudioContentService->getByUuid($uuid);
        $this->webAudioContentService->report($audio, $req->safe()->message);
        return response()->json(["message" => "Reported successfully."], 201);
    }

    public function search_query(SearchPostRequest $request){
        $data = $this->webAudioContentService->searchHandler($request->safe()->phrase);
        return response()->json(["data"=>$data], 200);
    }
}
