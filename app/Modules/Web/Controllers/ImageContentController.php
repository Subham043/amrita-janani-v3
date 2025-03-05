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
use Illuminate\Support\Facades\Response;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;

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
        if(!$request->hasValidSignature()){
            abort(403, "Invalid File Signature");
        }
        if(!(auth()->guard('web')->check() || auth()->guard('admin')->check())){
            abort(401, "Unauthenticated");
        }
        // Get the Referer header
        $referer = $request->headers->get('referer');

        // Allowed referer URL (only allow requests from this page)
        $allowedReferer1 = route('content_image_view', $uuid);

        if (!($referer || ($referer == $allowedReferer1))) {
            return redirect()->intended(route('content_image_view', $uuid));
        }

        $accept = $request->headers->get('accept');

        if(!$accept || str_contains($accept, 'text/html,application/xhtml+xml,application/xml')){
            return redirect()->intended(route('content_image_view', $uuid));
        }

        $image = $this->webImageContentService->getFileByUuid($uuid);

        if(!$image->contentVisible()){
            abort(403, "Unauthorized Access");
        }

        if(!Storage::exists((new ImageModel)->file_path.$image->image)){
            abort(404, "File not found.");
        }
        

        return response()->file(storage_path('app/private/'.(new ImageModel)->file_path.$image->image), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Content-Disposition' => 'inline',
        ]);
    }

    public function imageThumbnail(Request $request, $uuid){
        if(!$request->has('compressed')){
            abort(404, "File not found.");
        }

        if(!$request->hasValidSignature()){
            abort(403, "Invalid File Signature");
        }
        if(!(auth()->guard('web')->check() || auth()->guard('admin')->check())){
            abort(401, "Unauthenticated");
        }
        // Get the Referer header
        $referer = $request->headers->get('referer');

        
        // Allowed referer URL (only allow requests from this page)
        $allowedReferer2 = route('content_image');
        $allowedReferer3 = route('content_dashboard');
        
        if (!($referer || ($referer == $allowedReferer2 || $referer == $allowedReferer3))) {
            return redirect()->intended(route('content_image_view', $uuid));
        }
        
        $accept = $request->headers->get('accept');

        if(!$accept || str_contains($accept, 'text/html,application/xhtml+xml,application/xml')){
            return redirect()->intended(route('content_image_view', $uuid));
        }

        $image = $this->webImageContentService->getFileByUuid($uuid);

        if(!Storage::exists((new ImageModel)->file_path.$image->image)){
            abort(404, "File not found.");
        }
        

        return response()->file(storage_path('app/private/'.(new ImageModel)->file_path.$image->image), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Content-Disposition' => 'inline',
        ]);
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
