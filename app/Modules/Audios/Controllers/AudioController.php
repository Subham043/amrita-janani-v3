<?php

namespace App\Modules\Audios\Controllers;

use App\Enums\Restricted;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Modules\Audios\Models\AudioModel;
use App\Modules\Audios\Requests\AudioCreateRequest;
use App\Modules\Audios\Requests\AudioMultiDeleteRequest;
use App\Modules\Audios\Requests\AudioMultiRestrictionRequest;
use App\Modules\Audios\Requests\AudioMultiStatusRequest;
use App\Modules\Audios\Requests\AudioUpdateRequest;
use App\Modules\Audios\Services\AudioService;
use App\Modules\Languages\Services\LanguageService;
use App\Services\FileService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class AudioController extends Controller
{
    public function __construct(private AudioService $audioService, private LanguageService $languageService){}

    public function create() {
        $tags_data = (new TagService(AudioModel::class))->get_tags();
        return view('pages.admin.audio.create')
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

    public function store(AudioCreateRequest $request){

        DB::beginTransaction();
        try {
            //code...
            if($request->hasFile('audio') && $request->file('audio')->isValid()){
                $data = $this->audioService->create([
                    ...$request->except(['audio']),
                    'uuid' => str()->uuid(),
                    'user_id' => Auth::guard('admin')->user()->id,
                ]);
                $data->audio = (new FileService)->save_file('audio', (new AudioModel)->file_path);
                $data->duration = (new FileService)->mp3_file_duration($data->audio);
                $data->save();
                $data->Languages()->sync($request->language);
                $data->refresh();
                return response()->json(["url"=>empty($request->refreshUrl)?route('audio_view'):$request->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 201);
            }
            return response()->json(["message"=>"The audio file is invalid"], 400);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }

    public function edit($id) {
        $data = $this->audioService->getById($id);
        $tags_data = (new TagService(AudioModel::class))->get_tags();
        return view('pages.admin.audio.edit')
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

    public function update(AudioUpdateRequest $request, $id){
        $data = $this->audioService->getById($id);
        DB::beginTransaction();
        try {
            //code...
            $this->audioService->update([
                ...$request->except(['audio']),
            ], $data);
            $data->Languages()->sync($request->language);
            $data->refresh();
            if($request->hasFile('audio') && $request->file('audio')->isValid()){
                (new FileService)->remove_file($data->audio, ('app/private/'.(new AudioModel)->file_path));
                $data->audio = (new FileService)->save_file('audio', (new AudioModel)->file_path);
                $data->duration = (new FileService)->mp3_file_duration($data->audio);
                $data->save();
                $data->refresh();
            }
            return response()->json(["url"=>empty($request->refreshUrl)?route('audio_view'):$request->refreshUrl, "message" => "Data Updated successfully.", "data" => $data], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function display($id) {
        $data = $this->audioService->getById($id);
        return view('pages.admin.audio.display')->with('data',$data);
    }
    
    public function view(Request $request) {
        $data = $this->audioService->paginate($request->total ?? 10);
        return view('pages.admin.audio.list')->with('data',$data)
        ->with('filter_status', $request->query('filter')['status'] ?? 'all')
        ->with('filter_restricted', $request->query('filter')['restricted'] ?? 'all');
    }

    public function delete($id) {
        $data = $this->audioService->getById($id);
        $this->audioService->delete($data);
        return redirect()->intended(route('audio_view'))->with('success_status', 'Data Deleted successfully.');
    }

    public function excel(){
        return $this->audioService->excel()->toBrowser();
    }

    public function file(Request $request, $uuid){
        if((auth()->guard('web')->check() || auth()->guard('admin')->check()) && $request->hasValidSignature()){
            $data = $this->audioService->getTrashedByUuid($uuid);
            if(Storage::exists((new AudioModel)->file_path.$data->audio)){
                return response()->file(storage_path('app/private/'.(new AudioModel)->file_path.$data->audio));
            }
        }
        abort(404, "Link has expired.");
    }

    public function multiStatusToggle(AudioMultiStatusRequest $request){
        $request->validated();
        $ids = $request->audios;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one audio to toggle status."], 400);
        }

        $status = $request->status ?? Status::Active->value;

        DB::beginTransaction();

        try {
            //code...
            AudioModel::whereIn('id', $ids)->update(['status' => $status]);
            return response()->json(["message"=>"Updated audio status successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function multiRestrictionToggle(AudioMultiRestrictionRequest $request){
        $request->validated();
        $ids = $request->audios;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one audio to toggle status."], 400);
        }

        $restricted = $request->restricted ?? Restricted::No->value;

        DB::beginTransaction();

        try {
            //code...
            AudioModel::whereIn('id', $ids)->update(['restricted' => $restricted]);
            return response()->json(["message"=>"Updated audio restriction updated successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function multiDelete(AudioMultiDeleteRequest $request){
        $request->validated();
        $ids = $request->audios;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one audio to toggle status."], 400);
        }

        DB::beginTransaction();

        try {
            //code...
            AudioModel::whereIn('id', $ids)->delete();
            return response()->json(["message"=>"Updated audio deleted successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
}
