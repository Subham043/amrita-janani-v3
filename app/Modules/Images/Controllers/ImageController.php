<?php

namespace App\Modules\Images\Controllers;

use App\Enums\Restricted;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Modules\Images\Models\ImageModel;
use App\Modules\Images\Requests\ImageCreateRequest;
use App\Modules\Images\Requests\ImageUpdateRequest;
use App\Modules\Images\Services\ImageService;
use App\Modules\Images\Services\ImageTrashService;
use App\Services\FileService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class ImageController extends Controller
{
    public function __construct(private ImageService $imageService, private ImageTrashService $imageTrashService){}

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
        return view('pages.admin.image.list')->with('data',$data);
    }

    public function delete($id) {
        $data = $this->imageService->getById($id);
        $this->imageService->delete($data);
        return redirect()->intended(route('image_view'))->with('success_status', 'Data Deleted successfully.');
    }

    public function excel(){
        return $this->imageService->excel()->toBrowser();
    }
}
