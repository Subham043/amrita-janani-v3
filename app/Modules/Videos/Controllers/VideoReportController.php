<?php

namespace App\Modules\Videos\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Videos\Requests\VideoReportStatusRequest;
use App\Modules\Videos\Services\VideoReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoReportController extends Controller
{
    public function __construct(private VideoReportService $videoReportService){}

    public function viewreport(Request $request) {
        $data = $this->videoReportService->paginate($request->total ?? 10);
        return view('pages.admin.video.report_list')->with('data', $data)
        ->with('filter_status', $request->query('filter')['status'] ?? 'all')
        ->with('filter_search', $request->query('filter')['search'] ?? '');
    }

    public function deleteReport($id){
        $data = $this->videoReportService->getById($id);
        $this->videoReportService->forceDelete($data);
        return redirect()->intended(route('video_view_report'))->with('success_status', 'Data Deleted successfully.');
    }

    public function toggleReport(VideoReportStatusRequest $request, $id){
        $data = $this->videoReportService->getById($id);
        $this->videoReportService->toggleStatus([
            ...$request->validated(),
            'admin_id' => Auth::guard('admin')->id(),
        ], $data);
        return redirect()->back()->with('success_status', 'Status updated successfully.');
    }

    public function displayReport($id) {
        $data = $this->videoReportService->getById($id);
        return view('pages.admin.video.report_display')->with('data', $data);
    }

    
}
