<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Services\DashboardService;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService){}

    public function index(){
        $health = $this->dashboardService->getAppHealthResult();
        return view('pages.admin.dashboard.index')
        ->with([
            'user_count'=>$this->dashboardService->getUserCount(),
            'enquiry_count'=>$this->dashboardService->getEnquiryCount(),
            'media_count'=>$this->dashboardService->getMediaCount(),
            'health'=> $health,
            'lastRanAt'=>new Carbon($health?->finishedAt),
        ]);
    }

}
