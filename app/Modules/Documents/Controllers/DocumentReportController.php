<?php

namespace App\Modules\Documents\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Requests\DocumentReportStatusRequest;
use App\Modules\Documents\Services\DocumentReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentReportController extends Controller
{
    public function __construct(private DocumentReportService $documentReportService){}

    public function viewreport(Request $request) {
        $data = $this->documentReportService->paginate($request->total ?? 10);
        return view('pages.admin.document.report_list')->with('data', $data);
    }

    public function deleteReport($id){
        $data = $this->documentReportService->getById($id);
        $this->documentReportService->forceDelete($data);
        return redirect()->intended(route('document_view_report'))->with('success_status', 'Data Deleted successfully.');
    }

    public function toggleReport(DocumentReportStatusRequest $request, $id){
        $data = $this->documentReportService->getById($id);
        $this->documentReportService->toggleStatus([
            ...$request->validated(),
            'admin_id' => Auth::guard('admin')->id(),
        ], $data);
        return redirect()->back()->with('success_status', 'Status updated successfully.');
    }

    public function displayReport($id) {
        $data = $this->documentReportService->getById($id);
        return view('pages.admin.document.report_display')->with('data', $data);
    }

    
}
