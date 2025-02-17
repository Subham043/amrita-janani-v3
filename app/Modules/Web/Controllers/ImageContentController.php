<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Images\Models\ImageModel;
use App\Modules\Web\Requests\ContentPostRequest;
use App\Modules\Web\Requests\SearchPostRequest;
use App\Modules\Web\Services\WebImageContentService;
use App\Modules\Web\Services\WebPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageContentController extends Controller
{

    public function __construct(private WebImageContentService $webImageContentService, private WebPageService $webPageService){}

    public function index(Request $request){
        $data = $this->webImageContentService->paginate($request->total ?? 12);
        $languages = $this->webPageService->getLanguages();
        return view('pages.main.content.image')
        ->with('breadcrumb', 'Image')
        ->with('images',$data)
        ->with('languages', $languages)
        ->with([
            'sort' => $request->query('sort') ?? '-id',
            'favourite' => ($request->query('filter')['favourite'] ?? '')=="yes" ?? false,
            'selected_languages' => array_map('intval', explode('_', ($request->query('filter')['language'] ?? ''))) ?? [],
        ]);
    }

    public function view($uuid){
        $image = $this->webImageContentService->getByUuid($uuid);

        defer(function () use ($image) {
            $image->increment('views');
        });

        return view('pages.main.content.image_view')
        ->with('breadcrumb', 'Image'. ' - ' .$image->title)
        ->with('image', $image)
        ->with('imageAccess', null);
    }

    public function imageFile(Request $request, $uuid){
        if((auth()->guard('web')->check() || auth()->guard('admin')->check()) && $request->hasValidSignature()){
            if(!empty($request->header('referer')) && (str_contains($request->header('referer'), route('content_image_view', $uuid)) || str_contains($request->header('referer'), route('content_dashboard'))) && !empty($request->header('accept')) && !str_contains($request->header('accept'), 'text/html,application/xhtml+xml,application/xml')){
                $image = $this->webImageContentService->getFileByUuid($uuid);
        
                if($image->contentVisible()){
                    if(Storage::exists((new ImageModel)->file_path.$image->image)){
                        return response()->file(storage_path('app/private/'.(new ImageModel)->file_path.$image->image));
                    }
                }
            }
            return redirect()->intended(route('content_image_view', $uuid));
        }
        abort(404, "File not found.");
    }
    
    public function thumbnail(Request $request, $uuid){
        if((auth()->guard('web')->check() || auth()->guard('admin')->check()) && $request->hasValidSignature()){
            if(!empty($request->header('referer')) && str_contains($request->header('referer'), route('content_image_view', $uuid)) && !empty($request->header('accept')) && !str_contains($request->header('accept'), 'text/html,application/xhtml+xml,application/xml')){
                $image = $this->webImageContentService->getFileByUuid($uuid);

                if(Storage::exists((new ImageModel)->file_path.'compressed-'.$image->image)){
                    return response()->file(storage_path('app/private/'.(new ImageModel)->file_path.'compressed-'.$image->image));
                }
            }
            return redirect()->intended(route('content_image_view', $uuid));
        }
        abort(404, "File not found.");
    }

    public function makeFavourite($uuid){
        $image = $this->webImageContentService->getByUuid($uuid);
        $this->webImageContentService->toggleFavorite($image);
        return redirect()->intended(route('content_image_view', $uuid));
    }

    public function requestAccess(ContentPostRequest $req, $uuid){
        $image = $this->webImageContentService->getByUuid($uuid);
        $this->webImageContentService->requestAccess($image, $req->safe()->message);
        return response()->json(["message" => "Access requested successfully."], 201);
    }

    public function report(ContentPostRequest $req, $uuid){
        $image = $this->webImageContentService->getByUuid($uuid);
        $this->webImageContentService->report($image, $req->safe()->message);
        return response()->json(["message" => "Reported successfully."], 201);
    }

    public function search_query(SearchPostRequest $request){
        $data = $this->webPageService->imageSearchQueryList($request->safe()->phrase);
        return response()->json(["data"=>$data], 200);
    }
}
