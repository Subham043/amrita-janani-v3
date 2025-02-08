<?php

namespace App\Modules\Images\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Images\Requests\ImageAccessStatusRequest;
use App\Modules\Images\Services\ImageAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImageAccessController extends Controller
{
    public function __construct(private ImageAccessService $imageAccessService){}

    public function viewaccess(Request $request) {
        $data = $this->imageAccessService->paginate($request->total ?? 10);
        return view('pages.admin.image.access_list')->with('data', $data);
    }

    public function deleteAccess($id){
        $data = $this->imageAccessService->getById($id);
        $this->imageAccessService->forceDelete($data);
        return redirect()->intended(route('image_view_access'))->with('success_status', 'Data Deleted successfully.');
    }

    public function toggleAccess(ImageAccessStatusRequest $request, $id){
        $data = $this->imageAccessService->getById($id);
        $this->imageAccessService->toggleStatus([
            ...$request->validated(),
            'admin_id' => Auth::guard('admin')->id(),
        ], $data);
        return redirect()->back()->with('success_status', 'Status updated successfully.');
    }

    public function displayAccess($id) {
        $data = $this->imageAccessService->getById($id);
        return view('pages.admin.image.access_display')->with('data', $data);
    }

    
}
