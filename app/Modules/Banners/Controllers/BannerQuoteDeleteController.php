<?php

namespace App\Modules\Banners\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Banners\Services\BannerQuoteService;

class BannerQuoteDeleteController extends Controller
{
    public function __construct(private BannerQuoteService $bannerQuoteService){}

    
    public function index($id){
        $banner = $this->bannerQuoteService->getById($id);

        try {
            //code...
            $this->bannerQuoteService->forceDelete(
                $banner
            );
            return redirect()->intended(route('banner_quote_view'))->with('success_status', 'Data Deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->intended(route('banner_quote_view'))->with('error_status', 'Something went wrong. Please try again');
        }
    }

}
