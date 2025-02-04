<?php

namespace App\Modules\Banners\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banners\Models\BannerModel;
use App\Modules\Banners\Services\BannerService;
use App\Services\FileService;

class BannerDeleteController extends Controller
{
    public function __construct(private BannerService $bannerService){}

    
    public function index($id){
        $banner = $this->bannerService->getById($id);

        try {
            //code...
            (new FileService)->remove_file($banner->image, ('app/public/'.(new BannerModel)->file_path));
            (new FileService)->remove_file('compressed-'.$banner->image, ('app/public/'.(new BannerModel)->file_path));
            $this->bannerService->forceDelete(
                $banner
            );
            return redirect()->intended(route('banner_view'))->with('success_status', 'Data Deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->intended(route('banner_view'))->with('error_status', 'Something went wrong. Please try again');
        }
    }

}
