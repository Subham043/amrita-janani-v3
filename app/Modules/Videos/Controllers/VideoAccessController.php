<?php

namespace App\Modules\Videos\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Videos\Requests\VideoAccessStatusRequest;
use App\Modules\Videos\Services\VideoAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoAccessController extends Controller
{
    public function __construct(private VideoAccessService $videoAccessService){}

    public function viewaccess(Request $request) {
        $data = $this->videoAccessService->paginate($request->total ?? 10);
        return view('pages.admin.video.access_list')->with('data', $data)
        ->with('filter_status', $request->query('filter')['status'] ?? 'all')
        ->with('filter_search', $request->query('filter')['search'] ?? '');
    }

    public function deleteAccess($id){
        $data = $this->videoAccessService->getById($id);
        $this->videoAccessService->forceDelete($data);
        return redirect()->intended(route('video_view_access'))->with('success_status', 'Data Deleted successfully.');
    }

    public function toggleAccess(VideoAccessStatusRequest $request, $id){
        $data = $this->videoAccessService->getById($id);
        $this->videoAccessService->toggleStatus([
            ...$request->validated(),
            'admin_id' => Auth::guard('admin')->id(),
        ], $data);
        return redirect()->back()->with('success_status', 'Status updated successfully.');
    }

    public function displayAccess($id) {
        $data = $this->videoAccessService->getById($id);
        return view('pages.admin.video.access_display')->with('data', $data);
    }

    
}
