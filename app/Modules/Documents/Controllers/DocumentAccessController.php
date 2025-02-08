<?php

namespace App\Modules\Documents\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Requests\DocumentAccessStatusRequest;
use App\Modules\Documents\Services\DocumentAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentAccessController extends Controller
{
    public function __construct(private DocumentAccessService $documentAccessService){}

    public function viewaccess(Request $request) {
        $data = $this->documentAccessService->paginate($request->total ?? 10);
        return view('pages.admin.document.access_list')->with('data', $data);
    }

    public function deleteAccess($id){
        $data = $this->documentAccessService->getById($id);
        $this->documentAccessService->forceDelete($data);
        return redirect()->intended(route('document_view_access'))->with('success_status', 'Data Deleted successfully.');
    }

    public function toggleAccess(DocumentAccessStatusRequest $request, $id){
        $data = $this->documentAccessService->getById($id);
        $this->documentAccessService->toggleStatus([
            ...$request->validated(),
            'admin_id' => Auth::guard('admin')->id(),
        ], $data);
        return redirect()->back()->with('success_status', 'Status updated successfully.');
    }

    public function displayAccess($id) {
        $data = $this->documentAccessService->getById($id);
        return view('pages.admin.document.access_display')->with('data', $data);
    }

    
}
