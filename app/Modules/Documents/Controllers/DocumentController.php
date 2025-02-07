<?php

namespace App\Modules\Documents\Controllers;

use App\Enums\Restricted;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Modules\Documents\Models\DocumentModel;
use App\Modules\Documents\Requests\DocumentCreateRequest;
use App\Modules\Documents\Requests\DocumentUpdateRequest;
use App\Modules\Documents\Services\DocumentService;
use App\Modules\Languages\Services\LanguageService;
use App\Services\FileService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct(private DocumentService $documentService, private LanguageService $languageService){}

    public function create() {
        $tags_data = (new TagService(DocumentModel::class))->get_tags();
        return view('pages.admin.document.create')
        ->with([
            'restrictions' => Arr::mapWithKeys(Restricted::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
            'statuses' => Arr::mapWithKeys(Status::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
            "tags_exist" => $tags_data['tags_exist'],
            "topics_exist" => $tags_data['topics_exist'],
            "languages" => $this->languageService->all()
        ]);
    }

    public function store(DocumentCreateRequest $request){

        DB::beginTransaction();
        try {
            //code...
            if($request->hasFile('document') && $request->file('document')->isValid()){
                $data = $this->documentService->create([
                    ...$request->except(['document']),
                    'user_id' => Auth::guard('admin')->user()->id,
                ]);
                $data->document = (new FileService)->save_file('document', (new DocumentModel)->file_path);
                $data->page_number = (new FileService)->document_page_number($data->document);
                $data->save();
                $data->Languages()->sync($request->language);
                $data->refresh();
                return response()->json(["url"=>empty($request->refreshUrl)?route('document_view'):$request->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 201);
            }
            return response()->json(["message"=>"The document file is invalid"], 400);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }

    public function edit($id) {
        $data = $this->documentService->getById($id);
        $tags_data = (new TagService(DocumentModel::class))->get_tags();
        return view('pages.admin.document.edit')
        ->with([
            'data' => $data,
            'restrictions' => Arr::mapWithKeys(Restricted::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
            'statuses' => Arr::mapWithKeys(Status::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
            "tags_exist" => $tags_data['tags_exist'],
            "topics_exist" => $tags_data['topics_exist'],
            "languages" => $this->languageService->all()
        ]);
    }

    public function update(DocumentUpdateRequest $request, $id){
        $data = $this->documentService->getById($id);
        DB::beginTransaction();
        try {
            //code...
            $this->documentService->update([
                ...$request->except(['document']),
            ], $data);
            $data->Languages()->sync($request->language);
            $data->refresh();
            if($request->hasFile('document') && $request->file('document')->isValid()){
                (new FileService)->remove_file($data->document, ('app/private/'.(new DocumentModel)->file_path));
                $data->document = (new FileService)->save_file('document', (new DocumentModel)->file_path);
                $data->page_number = (new FileService)->document_page_number($data->document);
                $data->save();
                $data->refresh();
            }
            return response()->json(["url"=>empty($request->refreshUrl)?route('document_view'):$request->refreshUrl, "message" => "Data Updated successfully.", "data" => $data], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function display($id) {
        $data = $this->documentService->getById($id);
        return view('pages.admin.document.display')->with('data',$data);
    }
    
    public function view(Request $request) {
        $data = $this->documentService->paginate($request->total ?? 10);
        return view('pages.admin.document.list')->with('data',$data);
    }

    public function delete($id) {
        $data = $this->documentService->getById($id);
        $this->documentService->delete($data);
        return redirect()->intended(route('document_view'))->with('success_status', 'Data Deleted successfully.');
    }

    public function excel(){
        return $this->documentService->excel()->toBrowser();
    }

    public function file(Request $request, $uuid){
        if((auth()->guard('web')->check() || auth()->guard('admin')->check()) && $request->hasValidSignature()){
            $data = $this->documentService->getTrashedByUuid($uuid);
            if(Storage::exists((new DocumentModel)->file_path.$data->document)){
                return response()->file(storage_path('app/private/'.(new DocumentModel)->file_path.$data->document));
            }
        }
        abort(404, "Link has expired.");
    }
}
