<?php

namespace App\Modules\Audios\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Audios\Requests\AudioAccessStatusRequest;
use App\Modules\Audios\Services\AudioAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AudioAccessController extends Controller
{
    public function __construct(private AudioAccessService $audioAccessService){}

    public function viewaccess(Request $request) {
        $data = $this->audioAccessService->paginate($request->total ?? 10);
        return view('pages.admin.audio.access_list')->with('data', $data)
        ->with('filter_status', $request->query('filter')['status'] ?? 'all')
        ->with('filter_search', $request->query('filter')['search'] ?? '');
    }

    public function deleteAccess($id){
        $data = $this->audioAccessService->getById($id);
        $this->audioAccessService->forceDelete($data);
        return redirect()->intended(route('audio_view_access'))->with('success_status', 'Data Deleted successfully.');
    }

    public function toggleAccess(AudioAccessStatusRequest $request, $id){
        $data = $this->audioAccessService->getById($id);
        $this->audioAccessService->toggleStatus([
            ...$request->validated(),
            'admin_id' => Auth::guard('admin')->id(),
        ], $data);
        return redirect()->back()->with('success_status', 'Status updated successfully.');
    }

    public function displayAccess($id) {
        $data = $this->audioAccessService->getById($id);
        return view('pages.admin.audio.access_display')->with('data', $data);
    }

    
}
