<?php

namespace App\Modules\Videos\Controllers;

use App\Enums\Restricted;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Modules\Languages\Services\LanguageService;
use App\Modules\Videos\Models\VideoModel;
use App\Modules\Videos\Requests\VideoCreateRequest;
use App\Modules\Videos\Requests\VideoUpdateRequest;
use App\Modules\Videos\Services\VideoService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

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
        return view('pages.admin.video.list')->with('data',$data);
    }

    public function delete($id) {
        $data = $this->videoService->getById($id);
        $this->videoService->delete($data);
        return redirect()->intended(route('video_view'))->with('success_status', 'Data Deleted successfully.');
    }

    public function excel(){
        return $this->videoService->excel()->toBrowser();
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
}
