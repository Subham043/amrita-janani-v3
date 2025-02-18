<?php

namespace App\Modules\Images\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Images\Requests\ImageReportStatusRequest;
use App\Modules\Images\Services\ImageReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImageReportController extends Controller
{
    public function __construct(private ImageReportService $imageReportService){}

    public function viewreport(Request $request) {
        $data = $this->imageReportService->paginate($request->total ?? 10);
        return view('pages.admin.image.report_list')->with('data', $data)
        ->with('filter_status', $request->query('filter')['status'] ?? 'all')
        ->with('filter_search', $request->query('filter')['search'] ?? '');
    }

    public function deleteReport($id){
        $data = $this->imageReportService->getById($id);
        $this->imageReportService->forceDelete($data);
        return redirect()->intended(route('image_view_report'))->with('success_status', 'Data Deleted successfully.');
    }

    public function toggleReport(ImageReportStatusRequest $request, $id){
        $data = $this->imageReportService->getById($id);
        $this->imageReportService->toggleStatus([
            ...$request->validated(),
            'admin_id' => Auth::guard('admin')->id(),
        ], $data);
        return redirect()->back()->with('success_status', 'Status updated successfully.');
    }

    public function displayReport($id) {
        $data = $this->imageReportService->getById($id);
        return view('pages.admin.image.report_display')->with('data', $data);
    }

    
}
