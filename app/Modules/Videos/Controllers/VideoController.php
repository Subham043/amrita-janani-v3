<?php

namespace App\Modules\Videos\Controllers;

use App\Enums\Restricted;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Modules\Languages\Models\LanguageModel;
use App\Modules\Languages\Services\LanguageService;
use App\Modules\Videos\Models\VideoLanguage;
use App\Modules\Videos\Models\VideoModel;
use App\Modules\Videos\Requests\VideoCreateRequest;
use App\Modules\Videos\Requests\VideoExcelRequest;
use App\Modules\Videos\Requests\VideoMultiDeleteRequest;
use App\Modules\Videos\Requests\VideoMultiRestrictionRequest;
use App\Modules\Videos\Requests\VideoMultiStatusRequest;
use App\Modules\Videos\Requests\VideoUpdateRequest;
use App\Modules\Videos\Services\VideoService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class VideoController extends Controller
{
    public function __construct(private VideoService $videoService, private LanguageService $languageService){}

    public function create() {
        $tags_data = (new TagService(VideoModel::class))->get_tags();
        return view('pages.admin.video.create')
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

    public function store(VideoCreateRequest $request){

        DB::beginTransaction();
        try {
            //code...
            $data = $this->videoService->create([
                ...$request->except(['language']),
                'uuid' => str()->uuid(),
                'user_id' => Auth::guard('admin')->user()->id,
            ]);
            $data->Languages()->sync($request->language);
            $data->refresh();
            return response()->json(["url"=>empty($request->refreshUrl)?route('video_view'):$request->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }

    public function edit($id) {
        $data = $this->videoService->getById($id);
        $tags_data = (new TagService(VideoModel::class))->get_tags();
        return view('pages.admin.video.edit')
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

    public function update(VideoUpdateRequest $request, $id){
        $data = $this->videoService->getById($id);
        DB::beginTransaction();
        try {
            //code...
            $this->videoService->update([
                ...$request->except(['language']),
            ], $data);
            $data->Languages()->sync($request->language);
            $data->refresh();
            return response()->json(["url"=>empty($request->refreshUrl)?route('video_view'):$request->refreshUrl, "message" => "Data Updated successfully.", "data" => $data], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function display($id) {
        $data = $this->videoService->getById($id);
        return view('pages.admin.video.display')->with('data',$data);
    }
    
    public function view(Request $request) {
        $data = $this->videoService->paginate($request->total ?? 10);
        return view('pages.admin.video.list')->with('data',$data)
        ->with('filter_status', $request->query('filter')['status'] ?? 'all')
        ->with('filter_restricted', $request->query('filter')['restricted'] ?? 'all');
    }

    public function delete($id) {
        $data = $this->videoService->getById($id);
        $this->videoService->delete($data);
        return redirect()->intended(route('video_view'))->with('success_status', 'Data Deleted successfully.');
    }

    public function excel(){
        return $this->videoService->excel()->toBrowser();
    }

    public function bulk_upload(){
        return view('pages.admin.video.bulk_upload');
    }

    public function bulk_upload_store(VideoExcelRequest $req) {
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
            $videos = [];
            $languages = [];
            $languages_arr = [];
            SimpleExcelReader::create($path)->getRows()->each(function ($row) use (&$videos, &$languages, &$languages_arr) {
                $uuid = str()->uuid();
                $videos[] = [
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'description_unformatted' => $row['description'],
                    'year' => $row['year'],
                    'deity' => $row['deity'],
                    'tags' => $row['tags'],
                    'topics' => $row['topics'],
                    'version' => $row['version'],
                    'video' => $row['video'],
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
            });
            try {
                //code...
                DB::beginTransaction();
                $unique_languages = array_unique(Arr::flatten($languages_arr));
                $language_ids = LanguageModel::whereIn('name', $unique_languages)->pluck('id','name')->toArray();
                VideoModel::insert($videos);
                $video_uuids = array_map(function ($video) {
                    return $video['uuid'];
                }, $videos);
                $video_ids = VideoModel::whereIn('uuid', $video_uuids)->pluck('id', 'uuid')->toArray();
                $language_insertions = [];
                foreach ($languages as $lang) {
                    $video_id = $video_ids[(string) $lang['uuid']] ?? null;
                    if($video_id){
                        foreach ($lang['languages'] as $language_name) {
                            $language_id = $language_ids[str()->title($language_name)] ?? null;
                            if($language_id){
                                $language_insertions[] = [
                                    'video_id' => $video_id,
                                    'language_id' => $language_id,
                                ];
                            }
                        }
                    }
                }
                VideoLanguage::insert($language_insertions);
                if(Storage::exists('tmp_excel/'.$file)){
                    Storage::delete('tmp_excel/'.$file);
                }
                return response()->json(["url"=>empty($req->refreshUrl)?route('video_view'):$req->refreshUrl, "message" => "Data Stored successfully."], 201);
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
            $data = $this->videoService->getTrashedByUuid($uuid);
            if($request->compressed && Storage::exists((new VideoModel)->file_path.'compressed-'.$data->video)){
                return response()->file(storage_path('app/private/'.(new VideoModel)->file_path.'compressed-'.$data->video));
            }
            if(Storage::exists((new VideoModel)->file_path.$data->video)){
                return response()->file(storage_path('app/private/'.(new VideoModel)->file_path.$data->video));
            }
        }
        abort(404, "Link has expired.");
    }

    public function multiStatusToggle(VideoMultiStatusRequest $request){
        $request->validated();
        $ids = $request->videos;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one video to toggle status."], 400);
        }

        $status = $request->status ?? Status::Active->value;

        DB::beginTransaction();

        try {
            //code...
            VideoModel::whereIn('id', $ids)->update(['status' => $status]);
            return response()->json(["message"=>"Updated video status successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function multiRestrictionToggle(VideoMultiRestrictionRequest $request){
        $request->validated();
        $ids = $request->videos;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one video to toggle status."], 400);
        }

        $restricted = $request->restricted ?? Restricted::No->value;

        DB::beginTransaction();

        try {
            //code...
            VideoModel::whereIn('id', $ids)->update(['restricted' => $restricted]);
            return response()->json(["message"=>"Updated video restriction updated successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function multiDelete(VideoMultiDeleteRequest $request){
        $request->validated();
        $ids = $request->videos;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one video to toggle status."], 400);
        }

        DB::beginTransaction();

        try {
            //code...
            VideoModel::whereIn('id', $ids)->delete();
            return response()->json(["message"=>"Updated video deleted successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
}
