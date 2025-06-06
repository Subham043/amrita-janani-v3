<?php

namespace App\Modules\Documents\Controllers;

use App\Enums\Restricted;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Modules\Documents\Models\DocumentLanguage;
use App\Modules\Documents\Models\DocumentModel;
use App\Modules\Documents\Requests\DocumentCreateRequest;
use App\Modules\Documents\Requests\DocumentExcelRequest;
use App\Modules\Documents\Requests\DocumentMultiDeleteRequest;
use App\Modules\Documents\Requests\DocumentMultiRestrictionRequest;
use App\Modules\Documents\Requests\DocumentMultiStatusRequest;
use App\Modules\Documents\Requests\DocumentUpdateRequest;
use App\Modules\Documents\Services\DocumentService;
use App\Modules\Languages\Models\LanguageModel;
use App\Modules\Languages\Services\LanguageService;
use App\Services\FileService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

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
                    'uuid' => str()->uuid(),
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
        return view('pages.admin.document.list')->with('data',$data)
        ->with('filter_status', $request->query('filter')['status'] ?? 'all')
        ->with('filter_restricted', $request->query('filter')['restricted'] ?? 'all');
    }

    public function delete($id) {
        $data = $this->documentService->getById($id);
        $this->documentService->delete($data);
        return redirect()->intended(route('document_view'))->with('success_status', 'Data Deleted successfully.');
    }

    public function excel(){
        return $this->documentService->excel()->toBrowser();
    }

    public function bulk_upload(){
        return view('pages.admin.document.bulk_upload');
    }

    public function bulk_upload_store(DocumentExcelRequest $req) {
        $req->validated();

        $uuid = str()->uuid();
        $file = $uuid.'-'.$req->file('excel')->hashName();

        $req->file('excel')->storeAs('tmp_excel/', $file);

        $path = storage_path("app/private/tmp_excel/{$file}");
        $row_count = SimpleExcelReader::create($path)->getRows()->count();

        if($row_count == 0)
        {
            if(Storage::exists('tmp_excel/'.$file)){
                Storage::delete('tmp_excel/'.$file);
            }
            return response()->json(["errors"=>"Please enter atleast one row of data in the excel."], 400);
        }elseif($row_count > 30)
        {
            if(Storage::exists('tmp_excel/'.$file)){
                Storage::delete('tmp_excel/'.$file);
            }
            return response()->json(["errors"=>"Maximum 30 rows of data in the excel are allowed."], 400);
        }else{
            $documents = [];
            $languages = [];
            $languages_arr = [];
            SimpleExcelReader::create($path)->getRows()->each(function ($row) use (&$documents, &$languages, &$languages_arr) {
                if(Storage::exists('zip/documents/'.$row['document'])){
                    $uuid = str()->uuid();
                    $document_name = $uuid.'-'.str()->replace(' ', '-', str()->lower($row['document']));
                    $documents[] = [
                        'title' => $row['title'],
                        'description' => $row['description'],
                        'description_unformatted' => $row['description'],
                        'year' => $row['year'],
                        'deity' => $row['deity'],
                        'tags' => $row['tags'],
                        'topics' => $row['topics'],
                        'version' => $row['version'],
                        'document' => $document_name,
                        'restricted' => $row['restricted'] == true ? 1 : 0,
                        'status' => 1,
                        'user_id' => Auth::guard('admin')->user()->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'uuid' => $uuid,
                    ];
                    $languages[] = [
                        'uuid' => $uuid,
                        'languages' => array_map('strval', explode(',', $row['language'])),
                    ];
                    $languages_arr[] = array_map('strval', explode(',', $row['language']));
                    Storage::move('zip/documents'.'/'.$row['document'], 'upload/documents'.'/'.$document_name);
                }
            });
            try {
                //code...
                DB::beginTransaction();
                $unique_languages = array_unique(Arr::flatten($languages_arr));
                $language_ids = LanguageModel::whereIn('name', $unique_languages)->pluck('id','name')->toArray();
                DocumentModel::insert($documents);
                $document_uuids = array_map(function ($document) {
                    return $document['uuid'];
                }, $documents);
                $document_ids = DocumentModel::whereIn('uuid', $document_uuids)->pluck('id', 'uuid')->toArray();
                $language_insertions = [];
                foreach ($languages as $lang) {
                    $document_id = $document_ids[(string) $lang['uuid']] ?? null;
                    if($document_id){
                        foreach ($lang['languages'] as $language_name) {
                            $language_id = $language_ids[str()->title($language_name)] ?? null;
                            if($language_id){
                                $language_insertions[] = [
                                    'document_id' => $document_id,
                                    'language_id' => $language_id,
                                ];
                            }
                        }
                    }
                }
                DocumentLanguage::insert($language_insertions);
                if(Storage::exists('tmp_excel/'.$file)){
                    Storage::delete('tmp_excel/'.$file);
                }
                return response()->json(["url"=>empty($req->refreshUrl)?route('document_view'):$req->refreshUrl, "message" => "Data Stored successfully."], 201);
            } catch (\Throwable $th) {
                DB::rollBack();
                if(Storage::exists('tmp_excel/'.$file)){
                    Storage::delete('tmp_excel/'.$file);
                }
                return response()->json(["message"=>"something went wrong. Please try again"], 400);
            } finally {
                DB::commit();
            }
        }
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

    public function multiStatusToggle(DocumentMultiStatusRequest $request){
        $request->validated();
        $ids = $request->documents;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one document to toggle status."], 400);
        }

        $status = $request->status ?? Status::Active->value;

        DB::beginTransaction();

        try {
            //code...
            DocumentModel::whereIn('id', $ids)->update(['status' => $status]);
            return response()->json(["message"=>"Updated document status successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function multiRestrictionToggle(DocumentMultiRestrictionRequest $request){
        $request->validated();
        $ids = $request->documents;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one document to toggle status."], 400);
        }

        $restricted = $request->restricted ?? Restricted::No->value;

        DB::beginTransaction();

        try {
            //code...
            DocumentModel::whereIn('id', $ids)->update(['restricted' => $restricted]);
            return response()->json(["message"=>"Updated document restriction updated successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function multiDelete(DocumentMultiDeleteRequest $request){
        $request->validated();
        $ids = $request->documents;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one document to toggle status."], 400);
        }

        DB::beginTransaction();

        try {
            //code...
            DocumentModel::whereIn('id', $ids)->delete();
            return response()->json(["message"=>"Document deleted successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
}
