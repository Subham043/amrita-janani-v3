<?php

namespace App\Modules\Audios\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Audios\Requests\AudioReportStatusRequest;
use App\Modules\Audios\Services\AudioReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AudioReportController extends Controller
{
    public function __construct(private AudioReportService $audioReportService){}

    public function viewreport(Request $request) {
        $data = $this->audioReportService->paginate($request->total ?? 10);
        return view('pages.admin.audio.report_list')->with('data', $data);
    }

    public function deleteReport($id){
        $data = $this->audioReportService->getById($id);
        $this->audioReportService->forceDelete($data);
        return redirect()->intended(route('audio_view_report'))->with('success_status', 'Data Deleted successfully.');
    }

    public function toggleReport(AudioReportStatusRequest $request, $id){
        $data = $this->audioReportService->getById($id);
        $this->audioReportService->toggleStatus([
            ...$request->validated(),
            'admin_id' => Auth::guard('admin')->id(),
        ], $data);
        return redirect()->back()->with('success_status', 'Status updated successfully.');
    }

    public function displayReport($id) {
        $data = $this->audioReportService->getById($id);
        return view('pages.admin.audio.report_display')->with('data', $data);
    }

    
}
