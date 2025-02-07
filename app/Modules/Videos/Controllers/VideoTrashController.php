<?php

namespace App\Modules\Videos\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Videos\Services\VideoTrashService;

class VideoTrashController extends Controller
{
    public function __construct(private VideoTrashService $videoTrashService){}

    public function viewTrash() {
        $data = $this->videoTrashService->paginate($request->total ?? 10);
        return view('pages.admin.video.list_trash')->with('data',$data);
    }

    public function restoreTrash($id){
        $data = $this->videoTrashService->getById($id);
        $data->restore();
        return redirect()->intended(route('video_view_trash'))->with('success_status', 'Data Restored successfully.');
    }

    public function restoreAllTrash(){
        $this->videoTrashService->model()->restore();
        return redirect()->intended(route('video_view_trash'))->with('success_status', 'Data Restored successfully.');
    }

    public function displayTrash($id) {
        $data = $this->videoTrashService->getById($id);
        return view('pages.admin.video.display_trash')->with('data',$data);
    }

    public function deleteTrash($id){
        $data = $this->videoTrashService->getById($id);
        $data->forceDelete();
        return redirect()->intended(route('video_view_trash'))->with('success_status', 'Data Deleted permanently.');
    }
}
