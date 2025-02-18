<?php

namespace App\Modules\Images\Controllers;

use App\Enums\Restricted;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Modules\Images\Models\ImageModel;
use App\Modules\Images\Requests\ImageCreateRequest;
use App\Modules\Images\Requests\ImageMultiDeleteRequest;
use App\Modules\Images\Requests\ImageMultiRestrictionRequest;
use App\Modules\Images\Requests\ImageMultiStatusRequest;
use App\Modules\Images\Requests\ImageUpdateRequest;
use App\Modules\Images\Services\ImageService;
use App\Services\FileService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function __construct(private ImageService $imageService){}

    public function create() {
        $tags_data = (new TagService(ImageModel::class))->get_tags();
        return view('pages.admin.image.create')
        ->with([
            'restrictions' => Arr::mapWithKeys(Restricted::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
            'statuses' => Arr::mapWithKeys(Status::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
            "tags_exist" => $tags_data['tags_exist'],
            "topics_exist" => $tags_data['topics_exist']
        ]);
    }

    public function store(ImageCreateRequest $request){

        DB::beginTransaction();
        try {
            //code...
            if($request->hasFile('image') && $request->file('image')->isValid()){
                $data = $this->imageService->create([
                    ...$request->except(['image']),
                    'user_id' => Auth::guard('admin')->user()->id,
                ]);
                $data->image = (new FileService)->save_private_image('image', (new ImageModel)->file_path);
                $data->save();
                $data->refresh();
                return response()->json(["url"=>empty($request->refreshUrl)?route('image_view'):$request->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 201);
            }
            return response()->json(["message"=>"The image file is invalid"], 400);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }

    public function edit($id) {
        $data = $this->imageService->getById($id);
        $tags_data = (new TagService(ImageModel::class))->get_tags();
        return view('pages.admin.image.edit')
        ->with([
            'data' => $data,
            'restrictions' => Arr::mapWithKeys(Restricted::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
            'statuses' => Arr::mapWithKeys(Status::cases(), function ($enum) {
                return [$enum->name => $enum->value];
            }),
            "tags_exist" => $tags_data['tags_exist'],
            "topics_exist" => $tags_data['topics_exist']
        ]);
    }

    public function update(ImageUpdateRequest $request, $id){
        $data = $this->imageService->getById($id);
        DB::beginTransaction();
        try {
            //code...
            $this->imageService->update([
                ...$request->except(['image']),
            ], $data);
            if($request->hasFile('image') && $request->file('image')->isValid()){
                (new FileService)->remove_file($data->image, ('app/private/'.(new ImageModel)->file_path));
                (new FileService)->remove_file('compressed-'.$data->image, ('app/private/'.(new ImageModel)->file_path));
                $data->image = (new FileService)->save_private_image('image', (new ImageModel)->file_path);
                $data->save();
                $data->refresh();
            }
            return response()->json(["url"=>empty($request->refreshUrl)?route('image_view'):$request->refreshUrl, "message" => "Data Updated successfully.", "data" => $data], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function display($id) {
        $data = $this->imageService->getById($id);
        return view('pages.admin.image.display')->with('data',$data);
    }
    
    public function view(Request $request) {
        $data = $this->imageService->paginate($request->total ?? 10);
        return view('pages.admin.image.list')->with('data',$data)
        ->with('filter_status', $request->query('filter')['status'] ?? 'all')
        ->with('filter_restricted', $request->query('filter')['restricted'] ?? 'all');
    }

    public function delete($id) {
        $data = $this->imageService->getById($id);
        $this->imageService->delete($data);
        return redirect()->intended(route('image_view'))->with('success_status', 'Data Deleted successfully.');
    }

    public function excel(){
        return $this->imageService->excel()->toBrowser();
    }

    public function file(Request $request, $uuid){
        if((auth()->guard('web')->check() || auth()->guard('admin')->check()) && $request->hasValidSignature()){
            $data = $this->imageService->getTrashedByUuid($uuid);
            if($request->compressed && Storage::exists((new ImageModel)->file_path.'compressed-'.$data->image)){
                return response()->file(storage_path('app/private/'.(new ImageModel)->file_path.'compressed-'.$data->image));
            }
            if(Storage::exists((new ImageModel)->file_path.$data->image)){
                return response()->file(storage_path('app/private/'.(new ImageModel)->file_path.$data->image));
            }
        }
        abort(404, "Link has expired.");
    }

    public function multiStatusToggle(ImageMultiStatusRequest $request){
        $request->validated();
        $ids = $request->images;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one image to toggle status."], 400);
        }

        $status = $request->status ?? Status::Active->value;

        DB::beginTransaction();

        try {
            //code...
            ImageModel::whereIn('id', $ids)->update(['status' => $status]);
            return response()->json(["message"=>"Updated image status successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function multiRestrictionToggle(ImageMultiRestrictionRequest $request){
        $request->validated();
        $ids = $request->images;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one image to toggle status."], 400);
        }

        $restricted = $request->restricted ?? Restricted::No->value;

        DB::beginTransaction();

        try {
            //code...
            ImageModel::whereIn('id', $ids)->update(['restricted' => $restricted]);
            return response()->json(["message"=>"Updated image restriction updated successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
    
    public function multiDelete(ImageMultiDeleteRequest $request){
        $request->validated();
        $ids = $request->images;

        if($ids && count($ids)<1){
            return response()->json(["message"=>"Please select at least one image to toggle status."], 400);
        }

        DB::beginTransaction();

        try {
            //code...
            ImageModel::whereIn('id', $ids)->delete();
            return response()->json(["message"=>"Updated image deleted successfully."], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
}
