<?php

namespace App\Modules\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Models\DocumentModel;
use App\Modules\Web\Requests\ContentPostRequest;
use App\Modules\Web\Requests\SearchPostRequest;
use App\Modules\Web\Services\WebDocumentContentService;
use App\Modules\Web\Services\WebPageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentContentController extends Controller
{

    public function __construct(private WebDocumentContentService $webDocumentContentService, private WebPageService $webPageService){}

    public function index(Request $request){
        $data = $this->webDocumentContentService->paginate($request->total ?? 12);
        $languages = $this->webPageService->getLanguages();
        return view('pages.main.content.document')
        ->with('breadcrumb', 'Document')
        ->with('documents',$data)
        ->with('languages', $languages)
        ->with([
            'sort' => $request->query('sort') ?? '-id',
            'favourite' => ($request->query('filter')['favourite'] ?? '')=="yes" ?? false,
            'selected_languages' => array_map('intval', explode('_', ($request->query('filter')['language'] ?? ''))) ?? [],
        ]);
    }

    public function view($uuid){
        $document = $this->webDocumentContentService->getByUuid($uuid);

        defer(function () use ($document) {
            $document->increment('views');
        });

        return view('pages.main.content.document_view')
        ->with('breadcrumb', 'Document'. ' - ' .$document->title)
        ->with('document', $document)
        ->with('documentAccess', null);
    }

    public function documentFile(Request $request, $uuid){
        if((auth()->guard('web')->check() || auth()->guard('admin')->check()) && $request->hasValidSignature()){
            if(!empty($request->header('referer')) && str_contains($request->header('referer'), route('content_document_reader', $uuid))){
                $document = $this->webDocumentContentService->getFileByUuid($uuid);
        
                if($document->contentVisible()){
                    if(Storage::exists((new DocumentModel)->file_path.$document->document)){
                        return response()->file(storage_path('app/private/'.(new DocumentModel)->file_path.$document->document));
                    }
                }
            }
            return redirect()->intended(route('content_document_view', $uuid));
        }
        abort(404, "File not found.");
    }
    
    public function documentReader(Request $request, $uuid){
        if(auth()->guard('web')->check()){
            if(!empty($request->header('referer')) && str_contains($request->header('referer'), route('content_document_view', $uuid))){
                $document = $this->webDocumentContentService->getFileByUuid($uuid);
        
                if($document->contentVisible()){
                    if(Storage::exists((new DocumentModel)->file_path.$document->document)){
                        return view('pdf_reader.reader')->with([
                            'document_link' => $document->content_document_link
                        ]);
                    }
                }
            }
            return redirect()->intended(route('content_document_view', $uuid));
        }
        abort(404, "File not found.");
    }

    public function makeFavourite($uuid){
        $document = $this->webDocumentContentService->getByUuid($uuid);
        $this->webDocumentContentService->toggleFavorite($document);
        return redirect()->intended(route('content_document_view', $uuid));
    }

    public function requestAccess(ContentPostRequest $req, $uuid){
        $document = $this->webDocumentContentService->getByUuid($uuid);
        $this->webDocumentContentService->requestAccess($document, $req->safe()->message);
        return response()->json(["message" => "Access requested successfully."], 201);
    }

    public function report(ContentPostRequest $req, $uuid){
        $document = $this->webDocumentContentService->getByUuid($uuid);
        $this->webDocumentContentService->report($document, $req->safe()->message);
        return response()->json(["message" => "Reported successfully."], 201);
    }

    public function search_query(SearchPostRequest $request){
        $data = $this->webDocumentContentService->searchHandler($request->safe()->phrase);
        return response()->json(["data"=>$data], 200);
    }
}
