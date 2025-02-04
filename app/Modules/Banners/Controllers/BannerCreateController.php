<?php

namespace App\Modules\Banners\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banners\Models\BannerModel;
use App\Modules\Banners\Requests\BannerPostRequest;
use App\Modules\Banners\Services\BannerService;
use App\Services\FileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BannerCreateController extends Controller
{
    public function __construct(private BannerService $bannerService){}

    public function post(BannerPostRequest $request){

        DB::beginTransaction();
        try {
            //code...
            if($request->hasFile('image') && $request->file('image')->isValid()){
                $data = $this->bannerService->create([
                    ...$request->except(['image']),
                    'user_id' => Auth::guard('admin')->user()->id,
                ]);
                $data->image = (new FileService)->save_public_image('image', (new BannerModel)->file_path);
                $data->save();
                $data->refresh();
                return response()->json(["url"=>empty($request->refreshUrl)?route('banner_view'):$request->refreshUrl, "message" => "Data Stored successfully.", "data" => $data], 201);
            }
            return response()->json(["error"=>"The image file is invalid"], 400);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
            return response()->json(["error"=>"something went wrong. Please try again"], 400);
        } finally {
            DB::commit();
        }
    }
}
